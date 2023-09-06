<?php

class ControllerExtensionPaymentMultiSafePay extends Controller {

    const ROUTE = 'extension/payment/multisafepay';

    public function __construct($registry) {
        parent::__construct($registry);
        $this->registry->set('multisafepay_version_control', new Multisafepayversioncontrol($registry));
        $this->registry->set('multisafepay', new Multisafepay($registry));
        $this->key_prefix = $this->multisafepay_version_control->getKeyPrefix();
        $this->route = $this->multisafepay_version_control->getExtensionRoute();
        $this->view_extension_file = $this->multisafepay_version_control->getFileExtensionView();
        $this->model_call = $this->multisafepay_version_control->getStandartModelCall();
    }

    /**
     * Return additional language strings keys
     *
     * @return array $data
     */
    private function getAdditionalTextsKeys() {
        $additional_keys = array(
            'button_confirm',
            'text_testmode',
            'text_select'
        );
        return $additional_keys;
    }

    /**
     * Load all language strings values and keys into $this->data
     */
    public function getTexts() {
        $data = $this->multisafepay_version_control->getLanguageKeys($this->route, $this->getAdditionalTextsKeys());
        return $data;
    }

    /**
     * Get Extra Data for Payment Component
     */
    public function getDataForPaymentComponent($data) {
        $order_info = $this->multisafepay->getOrderInfo($data['order_id']);

        $data['type'] = 'direct';

        $data['fields']['payment_component_enabled'] = (bool)$this->config->get($this->key_prefix . 'multisafepay_' . strtolower($data['gateway']) . '_payment_component');
        $data['fields']['tokenization'] = (bool)$this->config->get($this->key_prefix . 'multisafepay_' . strtolower($data['gateway']) . '_tokenization');
        $data['currency'] = $order_info['currency_code'];
        $data['amount'] = (float)$order_info['total'] * 100;
        $data['locale'] = $this->multisafepay->getLocale();
        $data['country'] = $order_info['payment_iso_code_2'];
        $data['apiToken'] = $this->multisafepay->getUserApiToken();

        $data['env'] = 'live';
        if ($data['test_mode']) {
            $data['env'] = 'test';
        }

        $order_template = array(
            'currency' => $data['currency'],
            'amount' => $data['amount'],
            'customer' => array(
                'locale' => $data['locale'],
                'country' => $data['country'],
            ),
            'template' => array(
                'settings' => array(
                    'embed_mode' => true
                )
            )
        );

        // Recurring model is just working when payment components and tokenization are enabled at the same time, and for some specific credit cards
        if ($this->customer->isLogged() && $data['fields']['tokenization'] && in_array($data['gateway'], $this->multisafepay->configurable_recurring_payment_methods, true)) {
            $order_template['recurring']['model'] = 'cardOnFile';
            $order_template['customer']['reference'] = $order_info['customer_id'];
        }
        $data['order_data'] = json_encode($order_template);

        return $data;
    }

    /**
     * Data to be included in each payment method as base
     */
    private function paymentMethodBase($gateway = '') {
        $data = $this->getTexts();
        $data['issuers'] = array();
        $data['fields'] = array();
        $data['gateway_info'] = array();
        $data['gateway'] = $gateway;
        $data['order_id'] = $this->session->data['order_id'];
        $data['action'] = $this->url->link($this->route . '/confirm', '', true);
        $data['back'] = $this->url->link('checkout/checkout', '', true);
        $data['type'] = 'redirect';
        $data['route'] = $this->route;
        $data['test_mode'] = ($this->config->get($this->key_prefix . 'multisafepay_environment')) ? true : false;

        if (in_array($gateway, $this->multisafepay->configurable_type_search, true)) {
            $data['type'] = $this->config->get($this->key_prefix . 'multisafepay_' . strtolower($gateway) . '_redirect') ? 'redirect' : 'direct';
        }

        // Payment component enabled both with and without tokenization
        if (in_array($gateway, $this->multisafepay->configurable_payment_component, true) && (bool)$this->config->get($this->key_prefix . 'multisafepay_' . strtolower($gateway) . '_payment_component')) {
            $data = $this->getDataForPaymentComponent($data);
        }

        return $data;
    }

    /**
     * Handles the confirm order form for MultiSafepay payment method
     *
     */
    public function index() {
        $this->language->load($this->route);
        $data = $this->paymentMethodBase();
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Change the terms and conditions links for Riverty / Afterpay - Riverty payment
     * according to the selected language and the billing country of the customer
     *
     * @phpcs:disabled ObjectCalisthenics.ControlStructures.NoElse
     *
     * @return string
     */
    public function afterPayGeoTerms() {
        $terms = $this->language->get('entry_afterpay_terms');

        if ($this->session->data['payment_address']['country_id']) {
            $this->load->model('localisation/country');
            $billing_country = $this->model_localisation_country->getCountry($this->session->data['payment_address']['country_id']);
            $billing_code = strtolower($billing_country['iso_code_2']);
            $language_code = strtolower($this->language->get('code'));

            if (($billing_code === 'de') && (strpos($language_code, 'en') !== false)) {
                $terms = str_replace('/nl_en/', '/de_en/', $terms);
            } else if (($billing_code === 'at') && (strpos($language_code, 'de') !== false)) {
                $terms = str_replace('/de_de/', '/at_de/', $terms);
            } else if ($billing_code === 'at') {
                $terms = str_replace('/nl_en/', '/at_en/', $terms);
            } else if (($billing_code === 'ch') && (strpos($language_code, 'de') !== false)) {
                $terms = str_replace('/de_de/', '/ch_de/', $terms);
            } else if (($billing_code === 'ch') && (strpos($language_code, 'fr') !== false)) {
                $terms = str_replace('/nl_en/', '/ch_fr/', $terms);
            } else if ($billing_code === 'ch') {
                $terms = str_replace('/nl_en/', '/ch_en/', $terms);
            } else if (($billing_code === 'be') && (strpos($language_code, 'nl') !== false)) {
                $terms = str_replace('/nl_nl/', '/be_nl/', $terms);
            } else if (($billing_code === 'be') && (strpos($language_code, 'fr') !== false)) {
                $terms = str_replace('/nl_en/', '/be_fr/', $terms);
            } else if ($billing_code === 'be') {
                $terms = str_replace('/nl_en/', '/be_en/', $terms);
            }
        }
        return $terms;
    }

    /**
     * Handles the confirm order form for Riverty / Afterpay - Riverty payment method
     */
    public function afterPay() {
        $data = $this->paymentMethodBase('AFTERPAY');
	    if($data['type'] === 'direct') {
		    $data['gateway_info'] = 'Meta';
		    $data['fields'] = array(
			    'gender' => true,
			    'birthday' => true,
                'afterpay_terms' => true
		    );
	    }
        $data['entry_afterpay_terms'] = $this->afterPayGeoTerms();

        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Amazon Pay payment method
     */
    public function amazonPay() {
        $data = $this->paymentMethodBase('AMAZONBTN');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Amex payment method
     */
    public function amex() {
        $data = $this->paymentMethodBase('AMEX');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Alipay payment method
     */
    public function aliPay() {
        $data = $this->paymentMethodBase('ALIPAY');
        $data['type'] = 'direct';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Alipay+ payment method
     */
    public function alipayplus() {
        $data = $this->paymentMethodBase('ALIPAYPLUS');
        $data['type'] = 'direct';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Apple Pay payment method
     */
    public function applePay() {
        $data = $this->paymentMethodBase('APPLEPAY');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Baby Cadeaubon payment method
     */
    public function babycad() {
        $data = $this->paymentMethodBase('BABYCAD');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Bancontact payment method
     */
    public function bancontact() {
        $data = $this->paymentMethodBase('MISTERCASH');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Bank Transfer payment method
     */
    public function bankTransfer() {
        $data = $this->paymentMethodBase('BANKTRANS');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Beauty & Wellness payment method
     */
    public function beautyWellness() {
        $data = $this->paymentMethodBase('BEAUTYWELL');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Belfius payment method
     */
    public function belfius() {
        $data = $this->paymentMethodBase('BELFIUS');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Boekenbon payment method
     */
    public function boekenbon() {
        $data = $this->paymentMethodBase('BOEKENBON');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for CBC payment method
     */
    public function cbc() {
        $data = $this->paymentMethodBase('CBC');
        $data['type'] = 'direct';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for CreditCard payment method
     */
    public function creditCard() {
        $data = $this->paymentMethodBase('CREDITCARD');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Request to Pay powered by Deutsche Bank payment method
     */
    public function dbrtp() {
        $data = $this->paymentMethodBase('DBRTP');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Direct Bank payment method
     */
    public function directBank() {
        $data = $this->paymentMethodBase('DIRECTBANK');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Dotpay payment method
     */
    public function dotpay() {
        $data = $this->paymentMethodBase('DOTPAY');
        $data['gateway_info'] = 'Meta';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for E-Invoicing payment method
     */
    public function eInvoice() {
        $data = $this->paymentMethodBase('EINVOICE');
	    if($data['type'] === 'direct') {
		    $data['fields'] = array(
			    'birthday' => true,
			    'bankaccount' => true
		    );
		    $data['gateway_info'] = 'Meta';
	    }
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for EPS payment method
     */
    public function eps() {
        $data = $this->paymentMethodBase('EPS');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for fashionCheque payment method
     */
    public function fashionCheque() {
        $data = $this->paymentMethodBase('FASHIONCHQ');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for fashionGiftCard payment method
     */
    public function fashionGiftCard() {
        $data = $this->paymentMethodBase('FASHIONGFT');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Fietsenbon payment method
     */
    public function fietsenbon() {
        $data = $this->paymentMethodBase('FIETSENBON');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for GivaCard payment method
     */
    public function givaCard() {
        $data = $this->paymentMethodBase('GIVACARD');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Good Card payment method
     */
    public function goodCard() {
        $data = $this->paymentMethodBase('GOODCARD');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Google Pay payment method
     */
    public function googlePay() {
        $data = $this->paymentMethodBase('GOOGLEPAY');
        $data['mode_string'] = 'PRODUCTION';

        if ($data['test_mode']) {
            $data['mode_string'] = 'TEST';
        }
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for in3 payment method
     */
    public function in3() {
        $data = $this->paymentMethodBase('IN3');
	    if($data['type'] === 'direct') {
		    $data['gateway_info'] = 'Meta';
		    $data['fields'] = array(
			    'gender' => true,
			    'birthday' => true
		    );
	    }
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Gezondheidsbon payment method
     */
    public function gezondheidsbon() {
        $data = $this->paymentMethodBase('GEZONDHEID');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for giroPay payment method
     */
    public function giroPay() {
        $data = $this->paymentMethodBase('GIROPAY');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Good4fun Giftcard payment method
     */
    public function good4fun() {
        $data = $this->paymentMethodBase('GOOD4FUN');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for iDEAL payment method
     */
    public function ideal() {
        $data = $this->paymentMethodBase('IDEAL');
	    if($data['type'] === 'direct') {
		    $issuers = $this->multisafepay->getIssuersByGatewayCode($data['gateway']);
		    if($issuers) {
			    $data['issuers'] = $issuers;
			    $data['type'] = 'direct';
			    $data['gateway_info'] = 'Issuer';
		    }
	    }
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for iDEAL QR payment method
     */
    public function idealQr() {
        $data = $this->paymentMethodBase('IDEALQR');
        $data['gateway_info'] = 'QrCode';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for KBC payment method
     */
    public function kbc() {
        $data = $this->paymentMethodBase('KBC');
        $data['type'] = 'direct';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Klarna payment method
     */
    public function klarna() {
        $data = $this->paymentMethodBase('KLARNA');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Maestro payment method
     */
    public function maestro() {
        $data = $this->paymentMethodBase('MAESTRO');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Mastercard payment method
     */
    public function mastercard() {
        $data = $this->paymentMethodBase('MASTERCARD');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Mybank payment method
     */
    public function mybank() {
        $data = $this->paymentMethodBase('MYBANK');
        if ($data['type'] === 'direct') {
            $issuers = $this->multisafepay->getIssuersByGatewayCode($data['gateway']);
            if ($issuers) {
                $data['issuers'] = $issuers;
                $data['gateway_info'] = 'Issuer';
            }
        }
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Nationale Tuinbon payment method
     */
    public function nationaleTuinbon() {
        $data = $this->paymentMethodBase('NATNLETUIN');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Parfum Cadeaukaart payment method
     */
    public function parfumCadeaukaart() {
        $data = $this->paymentMethodBase('PARFUMCADE');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Pay After Delivery payment method
     */
    public function payAfterDelivery() {
        $data = $this->paymentMethodBase('PAYAFTER');
	    if($data['type'] === 'direct') {
		    $data['gateway_info'] = 'Meta';
		    $data['fields'] = array(
			    'birthday' => true,
			    'bankaccount' => true
		    );
	    }
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Pay After Delivery Installments payment method
     *
     * @return string
     */
    public function payAfterDeliveryInstallments(): string
    {
        $data = $this->paymentMethodBase('BNPL_INSTM');
        return $this->load->view($this->route, $data);
    }

    /**
     * Handles the confirm order form for PayPal payment method
     */
    public function payPal() {
        $data = $this->paymentMethodBase('PAYPAL');
        $data['type'] = 'direct';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for paysafecard payment method
     */
    public function paysafecard() {
        $data = $this->paymentMethodBase('PSAFECARD');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Podium payment method
     */
    public function podium() {
        $data = $this->paymentMethodBase('PODIUM');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for betaalplan payment method
     */
    public function betaalplan() {
        $data = $this->paymentMethodBase('SANTANDER');
	    if($data['type'] === 'direct') {
		    $data['gateway_info'] = 'Meta';
		    $data['fields'] = array(
			    'sex' => true,
			    'birthday' => true,
			    'bankaccount' => true
		    );
	    }
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for SEPA Direct Debt payment method
     */
    public function dirDeb() {
        $data = $this->paymentMethodBase('DIRDEB');
	    if($data['type'] === 'direct') {
		    $data['gateway_info'] = 'Account';
		    $data['fields'] = array(
			    'account_holder_name' => true,
			    'account_holder_iban' => true,
			    'emandate'            => true,
		    );
	    }
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Sport & Fit payment method
     */
    public function sportFit() {
        $data = $this->paymentMethodBase('SPORTENFIT');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Trustly payment method
     */
    public function trustly() {
        $data = $this->paymentMethodBase('TRUSTLY');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Visa payment method
     */
    public function visa() {
        $data = $this->paymentMethodBase('VISA');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Zinia payment method
     */
    public function zinia() {
        $data = $this->paymentMethodBase('ZINIA');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for VVV Cadeaukaart payment method
     */
    public function vvvGiftCard() {
        $data = $this->paymentMethodBase('VVVGIFTCRD');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Webshop Giftcard payment method
     */
    public function webshopGiftCard() {
        $data = $this->paymentMethodBase('WEBSHOPGIFTCARD');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Wellness gift card payment method
     */
    public function wellnessGiftCard() {
        $data = $this->paymentMethodBase('WELLNESSGIFTCARD');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Wijncadeau payment method
     */
    public function wijnCadeau() {
        $data = $this->paymentMethodBase('WIJNCADEAU');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Winkelcheque payment method
     */
    public function winkelCheque() {
        $data = $this->paymentMethodBase('WINKELCHEQUE');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for YourGift payment method
     */
    public function yourGift() {
        $data = $this->paymentMethodBase('YOURGIFT');
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

	/**
	 * Handles the confirm order form the generic payment method
	 */
	public function generic() {
		$data = $this->paymentMethodBase($this->config->get($this->key_prefix . 'multisafepay_generic_code'));
		return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
	}

    /**
     * Handles the form validation before submit and return errors if exist.
     */
    public function validateForm() {

        $this->load->language($this->route);

        $json = array();

        if ( (isset($this->request->post['gender'])) && $this->request->post['gender'] == '') {
            $json['error']['gender'] = $this->language->get('text_error_empty_gender');
        }

        if (isset($this->request->post['birthday']) && $this->request->post['birthday'] == '' ) {
            $json['error']['birthday'] = $this->language->get('text_error_empty_date_of_birth');
        }

        if (isset($this->request->post['bankaccount']) && $this->request->post['bankaccount'] == '' ) {
            $json['error']['bankaccount'] = $this->language->get('text_error_empty_bank_account');
        }

        if (isset($this->request->post['bankaccount']) && $this->request->post['bankaccount'] !== '' ) {
            if (!$this->multisafepay->validateIban($this->request->post['bankaccount'])) {
                $json['error']['bankaccount'] = $this->language->get('text_error_not_valid_iban');
            }
        }
        if (isset($this->request->post['account_holder_name']) && $this->request->post['account_holder_name'] == '' ) {
            $json['error']['account-holder-name'] = $this->language->get('text_error_empty_account_holder_name');
        }

        if (isset($this->request->post['account_holder_iban']) && $this->request->post['account_holder_iban'] == '' ) {
            $json['error']['account-holder-iban'] = $this->language->get('text_error_empty_account_holder_iban');
        }

        if (isset($this->request->post['account_holder_iban']) && $this->request->post['account_holder_iban'] !== '' ) {
            if (!$this->multisafepay->validateIban($this->request->post['account_holder_iban'])) {
                $json['error']['account-holder-iban'] = $this->language->get('text_error_not_valid_iban');
            }
        }

        if(isset($this->request->post['afterpay_terms']) && $this->request->post['afterpay_terms'] !== "1" ) {
            $json['error']['afterpay-terms'] = $this->language->get('text_error_empty_afterpay_terms');
        }


        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

    }

    /**
     * Handles the confirm order form in OpenCart checkout page
     *
     * @return bool|void
     */
    public function confirm() {

        if (!isset($this->request->post['order_id']) || !isset($this->request->post['type'])) {
            return false;
        }

        $order_id = $this->request->post['order_id'];
        $multisafepay_order = $this->multisafepay->getOrderRequestObject($this->request->post);
        $order_request = $this->multisafepay->processOrderRequestObject($multisafepay_order);

        if ($order_request->getPaymentUrl()) {

            if ($this->config->get($this->key_prefix . 'multisafepay_debug_mode')) {
                $this->log->write('Start transaction in MultiSafepay for order ID ' . $order_id . ' on ' . date($this->language->get('datetime_format')));
                $this->log->write('Payment Link: '. $order_request->getPaymentUrl());
            }
            $this->response->redirect($order_request->getPaymentUrl());
        }
    }

	/**
	 * Process POST and GET notifications.
	 *
	 * @param array $order_info
	 * @param MultiSafepay\Api\Transactions\TransactionResponse $transaction
	 */
    private function processCallBack($order_id, $transaction) {

	    $timestamp = date($this->language->get('datetime_format'));
	    $this->load->model('checkout/order');
	    $this->load->model($this->route);
	    $order_info = $this->model_checkout_order->getOrder($order_id);

	    $current_order_status = $order_info['order_status_id'];
	    $psp_id = $transaction->getTransactionId();
	    $payment_details = $transaction->getPaymentDetails();
	    $gateway_id = $payment_details->getType();
	    $gateway_details = $this->multisafepay->getGatewayById($gateway_id);
	    $status = $transaction->getStatus();

	    switch ($status) {
		    case 'completed':
			    $order_status_id = $this->config->get($this->key_prefix . 'multisafepay_order_status_id_completed');
			    break;
		    case 'uncleared':
			    $order_status_id = $this->config->get($this->key_prefix . 'multisafepay_order_status_id_uncleared');
			    break;
		    case 'reserved':
			    $order_status_id = $this->config->get($this->key_prefix . 'multisafepay_order_status_id_reserved');
			    break;
		    case 'void':
			    $order_status_id = $this->config->get($this->key_prefix . 'multisafepay_order_status_id_void');
			    break;
		    case 'cancelled':
			    $order_status_id = $this->config->get($this->key_prefix . 'multisafepay_order_status_id_cancelled');
			    break;
		    case 'declined':
			    $order_status_id = $this->config->get($this->key_prefix . 'multisafepay_order_status_id_declined');
			    break;
		    case 'reversed':
			    $order_status_id = $this->config->get($this->key_prefix . 'multisafepay_order_status_id_reversed');
			    break;
		    case 'refunded':
			    $order_status_id = $this->config->get($this->key_prefix . 'multisafepay_order_status_id_refunded');
			    break;
		    case 'partial_refunded':
			    $order_status_id = $this->config->get($this->key_prefix . 'multisafepay_order_status_id_partial_refunded');
			    break;
		    case 'expired':
			    $order_status_id = $this->config->get($this->key_prefix . 'multisafepay_order_status_id_expired');
			    break;
		    case 'shipped':
			    $order_status_id = $this->config->get($this->key_prefix . 'multisafepay_order_status_id_shipped');
			    break;
		    case 'initialized':
			    $order_status_id = $this->getOrderStatusInitialized($gateway_details);
			    break;
		    default:
			    $order_status_id = $this->config->get($this->key_prefix . 'multisafepay_order_status_id_initialized');
			    break;
	    }

	    if($gateway_details && $gateway_details['route'] != $order_info['payment_code']) {
		    $this->log->write('Callback received with a different payment method for ' . $order_id . ' on ' . $timestamp . ' with status: ' . $status . ', and PSP ID: ' . $psp_id . '. and payment method pass from ' . $order_info['payment_method'] . ' to '. $gateway_details['description'] .'.');
		    $this->{$this->model_call}->editOrderPaymentMethod($order_id, $gateway_details);
	    }

	    if(!$gateway_details) {
		    $this->log->write('Callback received with a non registered payment method for ' . $order_id . ' on ' . $timestamp . ' with status: ' . $status . ', and PSP ID: ' . $psp_id );
	    }

	    if ($order_status_id !== '0' && $order_status_id != $current_order_status) {
		    if ($this->config->get($this->key_prefix . 'multisafepay_debug_mode')) {
			    $this->log->write('Callback received for Order ID ' . $order_id . ' on ' . $timestamp . ' with status: ' . $status . ', and PSP ID: ' . $psp_id . '.');
		    }
		    $comment = '';
		    if($current_order_status != 0) {
			    $comment .= sprintf($this->language->get('text_comment_callback'), $order_id, $timestamp, $status, $psp_id);
		    }
		    $this->model_checkout_order->addOrderHistory($order_id, $order_status_id, $comment, true);
	    }

        // If $order_status_id is 0, it means do nothing. Callback will not trigger order status change
	    if ($order_status_id == '0' && $order_status_id != $current_order_status && $this->config->get($this->key_prefix . 'multisafepay_debug_mode')) {
		    $comment = sprintf($this->language->get('text_comment_callback'), $order_id, $timestamp, $status, $psp_id);
		    $this->model_checkout_order->addOrderHistory($order_id, $current_order_status, $comment, false);
		    $this->log->write('Callback received for Order ID ' . $order_id . ', has not been process.');
	    }

	    $this->response->addHeader('Content-type: text/plain');
	    $this->response->setOutput('OK');

    }

	/**
	 * Handles the callback from MultiSafepay using POST method
	 *
	 * @return bool|void
	 */
	public function postCallback() {
		// Check for required query arguments
		if(!$this->checkRequiredArgumentsInNotification()) {
			return;
		}

        // Check if the notification is related with "pre-transactions", in which case is ignored
		if($this->isPreTransactionNotification()) {
			return;
		}

		// Check if the order exist in the shop and belongs to MultiSafepay.
		if(!$this->checkIfOrderExistAndBelongsToMultiSafepay()) {
			return;
		}

		// Check if POST notification is empty
		if(!$this->checkIfPostBodyNotEmpty()) {
			return;
		}

		$body = file_get_contents('php://input');

		// Check if signature is valid
		if(!$this->checkIfSignatureIsValid($body)) {
			return;
		}

		$transaction = $this->multisafepay->getTransactionFromPostNotification($body);

		$this->processCallBack($this->request->get['transactionid'], $transaction);

	}

    /**
     * Handles the callback from MultiSafepay
     *
     * @return bool|void
     */
    public function callback() {
    	// Check for required query arguments
	    if(!$this->checkRequiredArgumentsInNotification()) {
	    	return;
	    }

	    // Check if the order exist in the shop and belongs to MultiSafepay.
	    if(!$this->checkIfOrderExistAndBelongsToMultiSafepay()) {
		    return;
	    }

        // Check if the notification is related with "pre-transactions", in which case is ignored
		if($this->isPreTransactionNotification()) {
			return;
		}

		// Get the transaction information from MultiSafepay via API request
	    $sdk = $this->multisafepay->getSdkObject($this->config->get('config_store_id'));
	    $transaction_manager = $sdk->getTransactionManager();
	    $transaction = $transaction_manager->get($this->request->get['transactionid']);

	    // Process the notification
	    $this->processCallBack($this->request->get['transactionid'], $transaction);
    }

    /**
     * Returns true if the notification is a "pretransaction" notification
     *
     * @return bool|void
     */
    public function isPreTransactionNotification() {
        if (isset($this->request->get['payload_type']) && $this->request->get['payload_type'] === 'pretransaction') {
            $this->log->write("A pre-transaction notification has been received but is not going to be processed.");
            return true;
        }

        return false;
    }

	/**
	 * Check required query arguments are present in the notification
	 *
	 * @return bool
	 */
    private function checkRequiredArgumentsInNotification() {
	    $required_arguments = array('transactionid', 'timestamp');
	    foreach ($required_arguments as $required_argument) {
		    if (empty($this->request->get[$required_argument])) {
			    $this->log->write("It seems the notification URL has been triggered but does't contain the required query arguments");
			    $this->response->addHeader('Content-type: text/plain');
			    $this->response->setOutput('OK');
			    return false;
		    }
	    }
	    return true;
    }

	/**
	 * Check if the order exist in the shop and belongs to MultiSafepay.
	 *
	 * @return bool
	 */
	private function checkIfOrderExistAndBelongsToMultiSafepay() {
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->request->get['transactionid']);
		if ( strpos( $order_info['payment_code'], 'multisafepay' ) === false ) {
			$this->log->write('Callback received for an order which currently do not have a MultiSafepay payment method assigned.');
			$this->response->addHeader('Content-type: text/plain');
			$this->response->setOutput('OK');
			return false;
		}
		return true;
	}

	/**
	 * Check if POST notification is empty
	 *
	 * @return bool
	 */
	private function checkIfPostBodyNotEmpty() {
		if (empty(file_get_contents('php://input'))) {
			$message = "It seems the notification URL has been triggered but doesn't contain a body in the POST request";
			$this->log->write($message);
			$this->response->addHeader('Content-type: text/plain');
			$this->response->setOutput('OK');
			return false;
		}
		return true;
	}

	/**
	 * Check if the signature of a POST request is valid
	 *
	 * @return bool
	 */
	private function checkIfSignatureIsValid($body) {
		$this->load->model($this->route);
		$environment = $this->{$this->model_call}->getSettingValue($this->key_prefix . 'multisafepay_environment', $this->config->get('config_store_id'));
		$environment = (empty($environment) ? true : false);
		$api_key = (($environment) ? $this->{$this->model_call}->getSettingValue($this->key_prefix . 'multisafepay_api_key', $this->config->get('config_store_id')) : $this->{$this->model_call}->getSettingValue($this->key_prefix . 'multisafepay_sandbox_api_key', $this->config->get('config_store_id')));
        if (!$this->multisafepay->verifyNotification($body, $_SERVER['HTTP_AUTH'], $api_key)) {
			$message = "Notification for transaction ID " . $this->request->get['transactionid'] . " has been received but is not valid";
			$this->log->write($message);
			$this->response->addHeader('Content-type: text/plain');
			$this->response->setOutput('OK');
			return false;
		}
		return true;
	}

    /**
     * Return custom order status id initialized when this one has been set
     * for a payment method
     *
     * @param array $gateway_details
     * @return int $custom_order_status_id_initialized
     */
    public function getOrderStatusInitialized($gateway_details = false) {

        if(!$gateway_details) {
            return $this->config->get($this->key_prefix . 'multisafepay_order_status_id_initialized');
        }

        $order_status_id_initialized_key = $this->key_prefix . 'multisafepay_' . $gateway_details['code'] . '_order_status_id_initialized';
        $custom_order_status_id_initialized = $this->config->get($order_status_id_initialized_key);

        if(!$custom_order_status_id_initialized) {
            return $this->config->get($this->key_prefix . 'multisafepay_order_status_id_initialized');
        }

        return $custom_order_status_id_initialized;
    }

    /**
     * Trigger that is called after catalog/controller/api/payment/methods
     * using OpenCart events system and overwrites it
     */
    public function catalogControllerApiPaymentMethodsAfter() {
        $this->registry->set('multisafepayevents', new Multisafepayevents($this->registry));
        $this->multisafepayevents->catalogControllerApiPaymentMethodsAfter();
    }

    /**
     * Trigger that is called after catalog/controller/checkout/payment/method
     * using OpenCart events system and overwrites it
     */
    public function catalogControllerCheckoutPaymentMethodAfter() {
        $this->registry->set('multisafepayevents', new Multisafepayevents($this->registry));
        $this->multisafepayevents->catalogControllerCheckoutPaymentMethodAfter();
	}

    /**
     * Trigger that is called before catalog/view/common/header/before
     * using OpenCart events system and overwrites it
     *
     * @param string $route
     * @param array $args
     */
    public function catalogViewCommonHeaderBefore(&$route, &$args) {
        $this->registry->set('multisafepayevents', new Multisafepayevents($this->registry));
        $this->multisafepayevents->catalogViewCommonHeaderFooterBefore($route, $args, 'header');
    }

    /**
     * Trigger that is called before catalog/view/common/footer/before
     * using OpenCart events system and overwrites it
     *
     * @param string $route
     * @param array $args
     */
    public function catalogViewCommonFooterBefore(&$route, &$args) {
        $this->registry->set('multisafepayevents', new Multisafepayevents($this->registry));
        $this->multisafepayevents->catalogViewCommonHeaderFooterBefore($route, $args, 'footer');
    }

    /**
     * Trigger that is called before catalog/mail/order/before
     * using OpenCart events system and overwrites it
     *
     * @param string $route
     * @param array $args
     */
    public function catalogViewMailOrderAddBefore(&$route, &$args) {
        $this->registry->set('multisafepayevents', new Multisafepayevents($this->registry));
        $this->multisafepayevents->catalogViewMailOrderAddBefore($route, $args);
    }

    /**
     * Trigger that is called before catalog/model/checkout/order/addOrderHistory/before
     * using OpenCart events system and overwrites it
     *
     * @param string $route
     * @param array $args
     */
    public function catalogModelCheckoutOrderAddOrderHistoryBefore(&$route, &$args) {
        $this->registry->set('multisafepayevents', new Multisafepayevents($this->registry));
        $this->multisafepayevents->catalogModelCheckoutOrderAddOrderHistoryBefore($route, $args);
    }

    /**
     * Trigger that is called before: catalog/model/checkout/order/addOrder
     *
     * using OpenCart events system and overwrites it
     *
     * @param string $route
     * @param array $args
     */
    public function catalogModelCheckoutOrderAddBefore(&$route, &$args) {
        $this->registry->set('multisafepayevents', new Multisafepayevents($this->registry));
        $this->multisafepayevents->catalogModelCheckoutOrderAddBefore($route, $args);
    }

    /**
     * Trigger that is called before: catalog/model/checkout/order/editOrder
     *
     * using OpenCart events system and overwrites it
     *
     * @param string $route
     * @param array $args
     */
    public function catalogModelCheckoutOrderEditBefore(&$route, &$args) {
        $this->registry->set('multisafepayevents', new Multisafepayevents($this->registry));
        $this->multisafepayevents->catalogModelCheckoutOrderEditBefore($route, $args);
    }

}

class ControllerPaymentMultiSafePay extends ControllerExtensionPaymentMultiSafePay { }
