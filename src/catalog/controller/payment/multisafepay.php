<?php

/**
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the MultiSafepay plugin
 * to newer versions in the future. If you wish to customize the plugin for your
 * needs please document your changes and make backups before you update.
 *
 * @category    MultiSafepay
 * @package     Connect
 * @author      TechSupport <integration@multisafepay.com>
 * @copyright   Copyright (c) MultiSafepay, Inc. (https://www.multisafepay.com)
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

class ControllerExtensionPaymentMultiSafePay extends Controller {

    const ROUTE = 'extension/payment/multisafepay';

    public function __construct($registry) {
        parent::__construct($registry);
        $this->registry->set('multisafepay_version_control', new Multisafepayversioncontrol($registry));
        $this->key_prefix = $this->multisafepay_version_control->getKeyPrefix();
        $this->route = $this->multisafepay_version_control->getExtensionRoute();
        $this->view_extension_file = $this->multisafepay_version_control->getFileExtensionView();
        $this->model_call = $this->multisafepay_version_control->getStandartModelCall();
    }

    /**
     * Return additional language strings keys
     *
     * @return array $data
     *
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
     *
     * */
    public function getTexts() {
        $data = $this->multisafepay_version_control->getLanguageKeys($this->route, $this->getAdditionalTextsKeys());
        return $data;
    }

    /**
     * Data to be include in each payment method as base
     *
     */
    private function paymentMethodBase() {
        $data = $this->getTexts();
        $data['issuers'] = array();
        $data['fields'] = array();
        $data['gateway_info'] = array();
        $data['gateway'] = '';
        $data['order_id'] = $this->session->data['order_id'];
        $data['action'] = $this->url->link($this->route . '/confirm', '', true);
        $data['back'] = $this->url->link('checkout/checkout', '', true);
        $data['type'] = 'redirect';
        $data['route'] = $this->route;
        $data['test_mode'] = ($this->config->get($this->key_prefix . 'multisafepay_environment')) ? true : false;
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
     * Handles the confirm order form for Afterpay payment method
     *
     */
    public function afterPay() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'AFTERPAY';
        $data['type'] = 'direct';
        $data['gateway_info'] = 'Meta';
        $data['fields'] = array(
            'gender' => true,
            'birthday' => true
        );
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Amex payment method
     *
     */
    public function amex() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'AMEX';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Alipay payment method
     *
     */
    public function aliPay() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'ALIPAY';
        $data['type'] = 'direct';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Apple Pay payment method
     *
     */
    public function applePay() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'APPLEPAY';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Baby Cadeaubon payment method
     *
     */
    public function babycad() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'BABYCAD';

        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Bancontact payment method
     *
     */
    public function bancontact() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'MISTERCASH';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Bank Transfer payment method
     *
     */
    public function bankTransfer() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'BANKTRANS';
        $data['type'] = 'direct';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Beauty & Wellness payment method
     *
     */
    public function beautyWellness() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'BEAUTYWELL';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Belfius payment method
     *
     */
    public function belfius() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'BELFIUS';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Boekenbon payment method
     *
     */
    public function boekenbon() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'BOEKENBON';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for CBC payment method
     *
     */
    public function cbc() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'CBC';
        $data['type'] = 'direct';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for CreditCard payment method
     *
     */
    public function creditCard() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'CREDITCARD';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Request to Pay powered by Deutsche Bank payment method
     *
     */
    public function dbrtp() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'DBRTP';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Direct Bank payment method
     *
     */
    public function directBank() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'DIRECTBANK';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Dotpay payment method
     *
     */
    public function dotpay() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'DOTPAY';
        $data['gateway_info'] = 'Meta';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for E-Invoicing payment method
     *
     */
    public function eInvoice() {
        $data = $this->paymentMethodBase();
        $data['fields'] = array(
            'birthday' => true,
            'bankaccount' => true
        );
        $data['type'] = 'direct';
        $data['gateway'] = 'EINVOICE';
        $data['gateway_info'] = 'Meta';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for EPS payment method
     *
     */
    public function eps() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'EPS';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for fashionCheque payment method
     *
     */
    public function fashionCheque() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'FASHIONCHQ';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for fashionGiftCard payment method
     *
     */
    public function fashionGiftCard() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'FASHIONGFT';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Fietsenbon payment method
     *
     */
    public function fietsenbon() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'FIETSENBON';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for GivaCard payment method
     *
     */
    public function givaCard() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'GIVACARD';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Good4fun Giftcard payment method
     *
     */
    public function good4fun() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'GOOD4FUN';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Good Card payment method
     *
     */
    public function goodCard() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'GOODCARD';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for in3 payment method
     *
     */
    public function in3() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'IN3';
        $data['type'] = 'direct';
        $data['gateway_info'] = 'Meta';
        $data['fields'] = array(
            'gender' => true,
            'birthday' => true
        );
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Gezondheidsbon payment method
     *
     */
    public function gezondheidsbon() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'GEZONDHEID';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for giroPay payment method
     *
     */
    public function giroPay() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'GIROPAY';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for iDEAL payment method
     *
     */
    public function ideal() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'IDEAL';
        $this->registry->set('multisafepay', new Multisafepay($this->registry));
        $issuers = $this->multisafepay->getIssuersByGatewayCode($data['gateway']);
        if($issuers) {
            $data['issuers'] = $issuers;
            $data['type'] = 'direct';
            $data['gateway_info'] = 'Ideal';
        }
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for iDEAL QR payment method
     *
     */
    public function idealQr() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'IDEALQR';
        $data['gateway_info'] = 'QrCode';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for ING Home\'Pay payment method
     *
     */
    public function ing() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'INGHOME';
        $data['type'] = 'direct';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for KBC payment method
     *
     */
    public function kbc() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'KBC';
        $data['type'] = 'direct';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Klarna payment method
     *
     */
    public function klarna() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'KLARNA';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Visa payment method
     *
     */
    public function maestro() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'MAESTRO';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Mastercard payment method
     *
     */
    public function mastercard() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'MASTERCARD';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Nationale Tuinbon payment method
     *
     */
    public function nationaleTuinbon() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'NATNLETUIN';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Parfum Cadeaukaart payment method
     *
     */
    public function parfumCadeaukaart() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'PARFUMCADE';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Pay After Delivery payment method
     *
     */
    public function payAfterDelivery() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'PAYAFTER';
        $data['type'] = 'direct';
        $data['gateway_info'] = 'Meta';
        $data['fields'] = array(
            'birthday' => true,
            'bankaccount' => true
        );
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for PayPal payment method
     *
     */
    public function payPal() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'PAYPAL';
        $data['type'] = 'direct';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for paysafecard payment method
     *
     */
    public function paysafecard() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'PSAFECARD';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Podium payment method
     *
     */
    public function podium() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'PODIUM';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for betaalplan payment method
     *
     */
    public function betaalplan() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'SANTANDER';
        $data['type'] = 'direct';
        $data['gateway_info'] = 'Meta';
        $data['fields'] = array(
            'sex' => true,
            'birthday' => true,
            'bankaccount' => true
        );
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for SEPA Direct Debt payment method
     *
     */
    public function dirDeb() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'DIRDEB';
        $data['type'] = 'direct';
        $data['gateway_info'] = 'Account';
        $data['fields'] = array(
            'account_holder_name' => true,
            'account_holder_iban' => true,
            'emandate'            => true,
        );
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Sport & Fit payment method
     *
     */
    public function sportFit() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'SPORTENFIT';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Trustly payment method
     *
     */
    public function trustly() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'TRUSTLY';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Visa payment method
     *
     */
    public function visa() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'VISA';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for VVV Cadeaukaart payment method
     *
     */
    public function vvvGiftCard() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'VVVGIFTCRD';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Webshop Giftcard payment method
     *
     */
    public function webshopGiftCard() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'WEBSHOPGIFTCARD';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Wellness gift card payment method
     *
     */
    public function wellnessGiftCard() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'WELLNESSGIFTCARD';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Wijncadeau payment method
     *
     */
    public function wijnCadeau() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'WIJNCADEAU';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for Winkelcheque payment method
     *
     */
    public function winkelCheque() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'WINKELCHEQUE';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the confirm order form for YourGift payment method
     *
     */
    public function yourGift() {
        $data = $this->paymentMethodBase();
        $data['gateway'] = 'YOURGIFT';
        return $this->multisafepay_version_control->getViewAccordingWithOcVersion($this->route . $this->view_extension_file, $data);
    }

    /**
     * Handles the form validation before submit and return errors if exist.
     *
     */
    public function validateForm() {

        $this->load->language($this->route);

        $this->registry->set('multisafepay', new Multisafepay($this->registry));

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

        $this->registry->set('multisafepay', new Multisafepay($this->registry));
        $order_id = $this->request->post['order_id'];
        $msp_order = $this->multisafepay->getOrderRequestObject($this->request->post);
        $order_request = $this->multisafepay->processOrderRequestObject($msp_order);

        if ($order_request->getPaymentLink()) {

            if ($this->config->get($this->key_prefix . 'multisafepay_debug_mode')) {
                $this->log->write('Start transaction in MSP for order ID ' . $order_id . ' on ' . date($this->language->get('datetime_format')));
                $this->log->write('Payment Link: '. $order_request->getPaymentLink());
            }
            $this->response->redirect($order_request->getPaymentLink());
        }
    }

    /**
     *  Handles the callback from MultiSafepay
     *
     * @return bool|void
     */
    public function callback() {

        if (!isset($this->request->get['transactionid']) || empty($this->request->get['transactionid'])) {
            return false;
        }

        if ($this->config->get($this->key_prefix . 'multisafepay_debug_mode')) {
            $this->log->write('Callback received for Order ID ' . $this->request->get['transactionid']);
        }

        $this->load->model('checkout/order');
        $this->load->model($this->route);

        // Start transaction in MSP.
        $this->registry->set('multisafepay', new Multisafepay($this->registry));
        $sdk = $this->multisafepay->getSdkObject();

        $order_id = $this->request->get['transactionid'];
        $timestamp = date($this->language->get('datetime_format'));
        $order_info = $this->model_checkout_order->getOrder($order_id);
        $current_order_status = $order_info['order_status_id'];
        $transaction_manager = $sdk->getTransactionManager();
        $transaction = $transaction_manager->get($order_id);
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

        if($gateway_details['route'] != $order_info['payment_code']) {
            $this->log->write('Callback received with a different payment method for ' . $order_id . ' on ' . $timestamp . ' with status: ' . $status . ', and PSP ID: ' . $psp_id . '. and payment method pass from ' . $order_info['payment_method'] . ' to '. $gateway_details['description'] .'.');
            $this->{$this->model_call}->editOrderPaymentMethod($order_id, $gateway_details);
        }

        if ($order_status_id && $order_status_id != $current_order_status) {
            if ($this->config->get($this->key_prefix . 'multisafepay_debug_mode')) {
                $this->log->write('Callback received for Order ID ' . $order_id . ' on ' . $timestamp . ' with status: ' . $status . ', and PSP ID: ' . $psp_id . '.');
            }
            $comment = '';
            if($current_order_status != 0) {
                $comment .= sprintf($this->language->get('text_comment_callback'), $order_id, $timestamp, $status, $psp_id);
            }
            $this->model_checkout_order->addOrderHistory($order_id, $order_status_id, $comment, true);
            $this->response->addHeader('Content-type: text/plain');
            $this->response->setOutput('OK');
        }

        if (!$order_status_id && $this->config->get($this->key_prefix . 'multisafepay_debug_mode')) {
            $this->log->write('Callback received for Order ID ' . $order_id . ', has not been process for some reason.');
        }
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
            return $this->config->get('payment_multisafepay_order_status_id_initialized');
        }

        $order_status_id_initialized_key = 'payment_multisafepay_' . $gateway_details['code'] . '_order_status_id_initialized';
        $custom_order_status_id_initialized = $this->config->get($order_status_id_initialized_key);

        if(!$custom_order_status_id_initialized) {
            return $this->config->get('payment_multisafepay_order_status_id_initialized');
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
     * Trigger that is called before catalog/mail/order/before
     * using OpenCart events system and overwrites it
     *
     * @param string $route
     * @param array $args
     *
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
     *
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
     *
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
     *
     */
    public function catalogModelCheckoutOrderEditBefore(&$route, &$args) {
        $this->registry->set('multisafepayevents', new Multisafepayevents($this->registry));
        $this->multisafepayevents->catalogModelCheckoutOrderEditBefore($route, $args);
    }

}

class ControllerPaymentMultiSafePay extends ControllerExtensionPaymentMultiSafePay { }
