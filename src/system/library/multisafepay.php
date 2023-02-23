<?php

class Multisafepay {

    public const FIXED_TYPE = 'F';
    public const PERCENTAGE_TYPE = 'P';
    public const OC_VERSION = VERSION;
    public const CONFIGURABLE_PAYMENT_COMPONENT = array('AMEX', 'CREDITCARD', 'MAESTRO', 'MASTERCARD', 'VISA', 'BNPL_INSTM');
    public const CONFIGURABLE_TOKENIZATION = array('AMEX', 'CREDITCARD', 'MAESTRO', 'MASTERCARD', 'VISA');
    public const CONFIGURABLE_RECURRING_PAYMENT_METHODS = array('AMEX', 'MAESTRO', 'MASTERCARD', 'VISA');
    public const CONFIGURABLE_TYPE_SEARCH = array('AFTERPAY', 'DIRDEB', 'EINVOICE', 'IN3', 'IDEAL', 'MYBANK', 'PAYAFTER', 'SANTANDER');
    public const CONFIGURABLE_GATEWAYS_WITH_ISSUERS = array('IDEAL', 'MYBANK');

    public function __construct($registry) {
        $this->registry = $registry;
        $this->registry->set('multisafepay_version_control', new Multisafepayversioncontrol($registry));
        $this->route = $this->multisafepay_version_control->getExtensionRoute();
        $this->oc_version = $this->multisafepay_version_control->getOcVersion();
        $this->key_prefix = $this->multisafepay_version_control->getKeyPrefix();
        $this->shipping_key_prefix = $this->multisafepay_version_control->getShippingKeyPrefix();
        $this->model_call = $this->multisafepay_version_control->getStandartModelCall();
        $this->non_standart_model_call = $this->multisafepay_version_control->getNonStandartModelCall();
        $this->total_extension_key_prefix = $this->multisafepay_version_control->getTotalExtensionPrefix();
        $this->extension_directory_route = $this->multisafepay_version_control->getExtensionDirectoryRoute();
        $this->configurable_payment_component = self::CONFIGURABLE_PAYMENT_COMPONENT;
        $this->configurable_tokenization = self::CONFIGURABLE_TOKENIZATION;
        $this->configurable_type_search = self::CONFIGURABLE_TYPE_SEARCH;
        $this->configurable_recurring_payment_methods = self::CONFIGURABLE_RECURRING_PAYMENT_METHODS;
        $this->configurable_gateways_with_issuers = self::CONFIGURABLE_GATEWAYS_WITH_ISSUERS;
    }

    /**
     * Magic method that returns any object used in OpenCart from registry object
     * when has not been found inside this class
     *
     * @param string $name
     * @return object
     *
     */
    public function __get($name) {
        return $this->registry->get($name);
    }

    /**
     * Returns the plugin version .
     *
     * @return string $plugin_version
     *
     */
    public function getPluginVersion() {
        $plugin_version = '3.17.0';
        return $plugin_version;
    }

    /**
     * Returns a ShoppingCart object to be used in .
     *
     * @param int $order_id
     * @return ShoppingCart  object
     * @phpcs:disabled ObjectCalisthenics.Metrics.MaxNestingLevel
     */
    public function getShoppingCartItems($order_id) {
        $order_info = $this->getOrderInfo($order_id);
        $order_products = $this->getOrderProducts($order_id);
        $coupon_info = $this->getCouponInfo($order_id);
        $shopping_cart_items = array();

        // Order Products
        foreach ($order_products as $product) {
            $shopping_cart_item = $this->getCartItem($product, $order_id, $order_products);
            $shopping_cart_items[$this->config->get($this->total_extension_key_prefix . 'sub_total_sort_order')][] = $shopping_cart_item;
        }

        // Gift Cards - Vouchers
	    $vouchers_in_cart = $this->getOrderVouchersItemsInCart($order_id);
	    if ($vouchers_in_cart) {
		    foreach ($vouchers_in_cart as $voucher_in_cart) {
			    $shopping_cart_items[$this->config->get($this->total_extension_key_prefix . 'sub_total_sort_order')][] = $this->getOrderVouchersItem($order_id, $voucher_in_cart);
		    }
	    }

        // Shipping Cost
        $shipping_info = $this->getShippingInfo($order_id);
        if ($shipping_info) {
            $shipping_cart_item = $this->getShippingItem($order_id);
            $shopping_cart_items[$this->config->get($this->total_extension_key_prefix . 'shipping_sort_order')][] = $shipping_cart_item;
        }

        // Fixed Coupons applied after taxes
        if ($coupon_info) {
            $coupon_cart_item = $this->getCouponItem($order_id);
            if ($coupon_cart_item) {
                $shopping_cart_items[$this->config->get($this->total_extension_key_prefix . 'coupon_sort_order')][] = $coupon_cart_item;
            }
        }

        // Handling Fee
        $handling_fee_info = $this->getHandlingFeeInfo($order_id);
        if ($handling_fee_info) {
            $handling_fee_cart_item = $this->getHandlingFeeItem($order_id);
            $shopping_cart_items[$this->config->get($this->total_extension_key_prefix . 'handling_sort_order')][] = $handling_fee_cart_item;
        }

        // Low Order Fee
        $low_order_fee_info = $this->getLowOrderFeeInfo($order_id);
        if ($low_order_fee_info) {
            $low_order_fee_info_cart_item = $this->getLowOrderFeeItem($order_id);
            $shopping_cart_items[$this->config->get($this->total_extension_key_prefix . 'low_order_fee_sort_order')][] = $low_order_fee_info_cart_item;
        }

        // Fixed Taxes
        $fixed_taxes_items = $this->getFixedTaxesItems($order_id);
        if (!empty($fixed_taxes_items)) {
            $fixed_taxes_items = $this->getFixedTaxesItems($order_id);
            $shopping_cart_items[$this->config->get($this->total_extension_key_prefix . 'tax_sort_order')] = $fixed_taxes_items;
        }

        // Customer Balance - Credit
        $customer_additional_data = $this->getAdditionalCustomerData();
        // Customer Balance included in the order
        $customer_balance_info = $this->getCustomerBalanceInfo($order_id);
        if ($customer_additional_data['customer_balance'] > 0 && $customer_balance_info) {
            $customer_balance_item = $this->getCustomerBalanceItem($order_id);
            if($customer_balance_item) {
                $shopping_cart_items[$this->config->get($this->total_extension_key_prefix . 'credit_sort_order')][] = $customer_balance_item;
            }
        }

        // Vouchers Gift Cards
        $vouchers_info = $this->getVoucherInfo($order_id);
        if ($vouchers_info) {
	        $voucher_info_cart_item = $this->getVouchersItem($order_id);
	        $shopping_cart_items[$this->config->get($this->total_extension_key_prefix . 'voucher_sort_order')] = $voucher_info_cart_item;
        }

        // Custom Order Totals
        $detected_order_total_keys = $this->checkForThirdPartyPluginsOrderTotals();
        if(!empty($detected_order_total_keys)) {
            foreach ($detected_order_total_keys as $custom_order_total_key) {
                $custom_order_total_key = trim($custom_order_total_key);
                $custom_order_total_info = $this->getCustomOrderTotalInfo($order_id, $custom_order_total_key);
                if ($custom_order_total_info) {
                    $custom_order_total_cart_item = $this->getCustomOrderTotalItem($order_id, $custom_order_total_key);
                    $shopping_cart_items[$this->config->get($this->total_extension_key_prefix . $custom_order_total_key . '_sort_order')][] = $custom_order_total_cart_item;
                }
            }
        }

        // Sort Order Shopping Cart Items
        $cart_items = $this->reOrderShoppingCartItems($shopping_cart_items);

        $shopping_cart = new \MultiSafepay\Api\Transactions\OrderRequest\Arguments\ShoppingCart($cart_items);

        return $shopping_cart;
    }

    /**
     * Compare the result of the order totals keys found in database; and
     * return a result
     */
    public function checkForThirdPartyPluginsOrderTotals() {
        $default_order_total_keys = array('sub_total', 'shipping', 'total', 'coupon', 'tax', 'handling', 'voucher', 'credit', 'low_order_fee', 'reward', 'klarna_fee');
        $detected_order_total_keys = $this->{$this->model_call}->getDetectedOrderTotalsKeys();

        // Custom order totals keys, after remove default ones included in OpenCart
        $custom_order_total_keys = array_diff($detected_order_total_keys, $default_order_total_keys);

        // Custom order totals keys defined in settings to be excluded
        $exclude_order_total_keys = explode(",", ($this->config->get($this->key_prefix . 'multisafepay_custom_order_total_keys') ?? ''));
        return array_diff($custom_order_total_keys, $exclude_order_total_keys);
    }

    /**
     * Returns the tax rate value applied for a item in the cart.
     *
     * @param float $total
     * @param int $tax_class_id
     * @return float
     *
     */
    private function getItemTaxRate($total, $tax_class_id) {
        $tax_rate = 0;
        $rates = $this->tax->getRates($total, $tax_class_id);
        foreach ($rates as $oc_tax_rate) {
            if ($oc_tax_rate['type'] == self::PERCENTAGE_TYPE) {
                $tax_rate = $tax_rate + $oc_tax_rate['rate'];
            }
        }
        return $tax_rate;
    }

    /**
     * Returns boolean if sort order module provided is lower than the one setup for taxes,
     * used to determined if necessary calculated taxes for those modules.
     *
     * @return bool
     *
     */
    private function isSortOrderLowerThanTaxes($module_sort_order) {
        $tax_sort_order = $this->config->get($this->total_extension_key_prefix . 'tax_sort_order');
        if ((int)$tax_sort_order > (int)$module_sort_order) {
            return true;
        }
        return false;
    }

    /**
     * Returns a Sdk object
     *
     * @param int $store_id
     *
     * @return Sdk object
     * @throws InvalidApiKeyException
     *
     */
    public function getSdkObject($store_id = 0) {

        $this->language->load($this->route);
	    $this->load->model($this->route);

        require_once(DIR_SYSTEM . 'library/multisafepay/vendor/autoload.php');

        $environment = $this->{$this->model_call}->getSettingValue($this->key_prefix . 'multisafepay_environment', $store_id);
        $environment = (empty($environment) ? true : false);
        $api_key = (($environment) ? $this->{$this->model_call}->getSettingValue($this->key_prefix . 'multisafepay_api_key', $store_id) : $this->{$this->model_call}->getSettingValue($this->key_prefix . 'multisafepay_sandbox_api_key', $store_id));

        try {
            $sdk = new \MultiSafepay\Sdk($api_key, $environment);
        }
        catch (\MultiSafepay\Exception\InvalidApiKeyException $invalidApiKeyException ) {
            if ($this->{$this->model_call}->getSettingValue($this->key_prefix . 'multisafepay_debug_mode', $store_id)) {
                $this->log->write($invalidApiKeyException->getMessage());
            }
            $this->session->data['error'] = $this->language->get('text_error');
            $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
        }

        return $sdk;

    }

    /**
     * Return Order Request Object
     *
     * @param int $order_id
     * @return OrderRequest object
     * @throws ApiException
     *
     */
    public function getOrderRequestObject($data) {

        $this->language->load($this->route);

        $order_info = $this->getOrderInfo($data['order_id']);

        // Order Request
        $sdk = $this->getSdkObject($order_info['store_id']);

        $multisafepay_order = new \MultiSafepay\Api\Transactions\OrderRequest();
        $multisafepay_order->addOrderId($data['order_id']);

        if (isset($data['gateway']) && empty($data['issuer_id']) && in_array($data['gateway'], $this->configurable_gateways_with_issuers, true)) {
            $data['type'] = 'redirect';
            $data['gateway_info'] = '';
        }

        $multisafepay_order->addType($data['type']);

        // Order Request: Gateway
        if (!empty($data['gateway'])) {
            $multisafepay_order->addGatewayCode($data['gateway']);
        }

        if (isset($data['gateway_info']) && $data['gateway_info'] != '') {
            $gateway_info = $this->getGatewayInfoInterfaceObject($data);
            $multisafepay_order->addGatewayInfo($gateway_info);
        }

        // If order goes through Payment Component
        if (isset($data['payload']) && $data['payload'] !== '') {
            $multisafepay_order->addData(array('payment_data' => array('payload' => $data['payload'])));
        }

        // Order Request: Plugin details
        $plugin_details = $this->getPluginDetailsObject();
        $multisafepay_order->addPluginDetails($plugin_details);

        // Order Request: Money
        $order_total = $this->getMoneyObjectOrderAmount($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $multisafepay_order->addMoney($order_total);

        // Order Request: Description
        $description = $this->getOrderDescriptionObject($data['order_id']);
        $multisafepay_order->addDescription($description);

        // Order Request: Payment Options
        $payment_options = $this->getPaymentOptionsObject();
        $multisafepay_order->addPaymentOptions($payment_options);

        // Order Request: Second Chance
        $payment_multisafepay_second_chance = ($this->config->get($this->key_prefix . 'multisafepay_second_chance')) ? true : false;
        $second_chance = $this->getSecondChanceObject($payment_multisafepay_second_chance);
        $multisafepay_order->addSecondChance($second_chance);

        // Order Request: Shopping Cart Items - Products
        if(!(bool)$this->config->get($this->key_prefix . 'multisafepay_shopping_cart_disabled')) {
            $shopping_cart = $this->getShoppingCartItems($data['order_id']);
            $multisafepay_order->addShoppingCart($shopping_cart);
        }

        // Order Request: Customer
        $customer_payment = $this->getCustomerObject($data['order_id'], 'payment');
        $multisafepay_order->addCustomer($customer_payment);

        // Order Request: Customer Delivery. Only if the order requires delivery.
        if ($order_info['shipping_method'] != '') {
            $customer_shipping = $this->getCustomerObject($data['order_id'], 'shipping');
            $multisafepay_order->addDelivery($customer_shipping);
        }

        // Order Request: Lifetime of payment link.

        if ($this->config->get($this->key_prefix . 'multisafepay_days_active') && $this->config->get($this->key_prefix . 'multisafepay_unit_lifetime_payment_link')) {
            $payment_multisafepay_unit_lifetime_payment_link = $this->config->get($this->key_prefix . 'multisafepay_unit_lifetime_payment_link');
            switch ($payment_multisafepay_unit_lifetime_payment_link) {
                case 'days':
                    $multisafepay_order->addDaysActive((int)$this->config->get($this->key_prefix . 'multisafepay_days_active'));
                    break;
                case 'hours':
                    $hours = (int)$this->config->get($this->key_prefix . 'multisafepay_days_active') * 60 * 60;
                    $multisafepay_order->addSecondsActive((int)$hours);
                    break;
                case 'seconds':
                    $multisafepay_order->addSecondsActive((int)$this->config->get($this->key_prefix . 'multisafepay_days_active'));
                    break;
            }

        }

        return $multisafepay_order;

    }

    /**
     * Process an Order Request
     *
     * @param OrderRequest $multisafepay_order
     * @return OrderRequest object
     * @throws ApiException
     *
     */
    public function processOrderRequestObject($multisafepay_order) {
        if (!$multisafepay_order) {
            return false;
        }
        $this->language->load($this->route);
	    $order_id = $multisafepay_order->getOrderId();
	    $order_info = $this->getOrderInfo($order_id);
        $sdk = $this->getSdkObject($order_info['store_id']);
        $transaction_manager = $sdk->getTransactionManager();
        try {
            $order_request = $transaction_manager->create($multisafepay_order);
            return $order_request;
        }
        catch (\MultiSafepay\Exception\ApiException $apiException ) {
            if ($this->{$this->model_call}->getSettingValue($this->key_prefix . 'multisafepay_debug_mode', $order_info['store_id'])) {
                $this->log->write($apiException->getMessage());
            }
            $this->session->data['error'] = $this->language->get('text_error');
            $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
        }

    }

    /**
     * Process a Refund Request
     *
     * @param RefundRequest $multisafepay_order
     * @return RefundRequest object
     * @throws ApiException
     *
     */
    public function processRefundRequestObject($multisafepay_order, $refund_request) {
        if (!$multisafepay_order || !$refund_request) {
            return false;
        }

	    $order_id = $multisafepay_order->getOrderId();
	    $order_info = $this->getAdminOrderInfo($order_id);
        $sdk = $this->getSdkObject($order_info['store_id']);
        $transaction_manager = $sdk->getTransactionManager();
        try {
            $process_refund = $transaction_manager->refund($multisafepay_order, $refund_request);
            return $process_refund;
        }
        catch (\MultiSafepay\Exception\ApiException $apiException ) {
            if ($this->{$this->model_call}->getSettingValue($this->key_prefix . 'multisafepay_debug_mode', $order_info['store_id'])) {
                $this->log->write($apiException->getMessage());
            }
            return false;
        }
    }

    /**
     * Create an Refund Request
     *
     * @param RefundRequest $multisafepay_order
     * @return RefundRequest object
     * @throws ApiException
     *
     */
    public function createRefundRequestObject($multisafepay_order) {
        if (!$multisafepay_order) {
            return false;
        }
	    $order_id = $multisafepay_order->getOrderId();
        $order_info = $this->getAdminOrderInfo($order_id);
        $sdk = $this->getSdkObject($order_info['store_id']);
        $transaction_manager = $sdk->getTransactionManager();

        try {
            $refund_request = $transaction_manager->createRefundRequest($multisafepay_order);
            return $refund_request;
        }
        catch (\MultiSafepay\Exception\ApiException $apiException ) {
            if ($this->{$this->model_call}->getSettingValue($this->key_prefix . 'multisafepay_debug_mode', $order_info['store_id'])) {
                $this->log->write($apiException->getMessage());
            }
            return false;
        }
    }

    /**
     * Return Issuers by gateway code
     *
     * @param string $gateway_code
     * @return array Issuers
     *
     */
    public function getIssuersByGatewayCode($gateway_code) {
        $sdk = $this->getSdkObject($this->config->get('config_store_id'));
        try {
            $issuer_manager = $sdk->getIssuerManager();
            $issuers = $issuer_manager->getIssuersByGatewayCode($gateway_code);
        }
        catch (InvalidArgumentException $invalidArgumentException ) {
            if ($this->config->get($this->key_prefix . 'multisafepay_debug_mode')) {
                $this->log->write($invalidArgumentException->getMessage());
            }
            return false;
        }

        $data_issuers = array();
        foreach ($issuers as $issuer) {
            $data_issuers[] = array(
                'code' => $issuer->getCode(),
                'description' => $issuer->getDescription()
            );
        }
        return $data_issuers;
    }

    /**
     * Return Gateway object by code
     *
     * @param string $gateway_code
     * @return Gateway object
     *
     */
    public function getGatewayObjectByCode($gateway_code) {
        $this->language->load($this->route);
        $sdk = $this->getSdkObject($this->config->get('config_store_id'));
        try {
            $gateway_manager = $sdk->getGatewayManager();
            $gateway = $gateway_manager->getByCode($gateway_code);
        }
        catch (\MultiSafepay\Exception\ApiException $apiException ) {
            if ($this->config->get($this->key_prefix . 'multisafepay_debug_mode')) {
                $this->log->write($apiException->getMessage());
            }
            $this->session->data['error'] = $this->language->get('text_error');
            $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
        }

        return $gateway;
    }

    /**
     * Returns a CustomerDetails object used to build the order request object,
     * in addCustomer and addDelivery methods.
     *
     * @param array $order_info Order information.
     * @param string $type Used to build the object with the order`s shipping or payment information.
     * @return CustomerDetails object
     *
     */
    public function getCustomerObject($order_id, $type = 'payment') {
	    $order_info   = $this->getOrderInfo( $order_id );
	    $customer_obj = new \MultiSafepay\Api\Transactions\OrderRequest\Arguments\CustomerDetails();
	    $customer_obj->addIpAddressAsString( $order_info['ip'] );
	    if ( $order_info['forwarded_ip'] ) {
		    $customer_obj->addForwardedIpAsString( $order_info['forwarded_ip'] );
	    }
        if(isset($order_info[ $type . '_company']) && !empty($order_info[ $type . '_company'])) {
	        $customer_obj->addCompanyName($order_info[ $type . '_company']);
        }
        $customer_obj->addUserAgent($order_info['user_agent']);
        $customer_obj->addPhoneNumberAsString($order_info['telephone']);
        $customer_obj->addLocale($this->getLocale());
        $customer_obj->addEmailAddressAsString($order_info['email']);
        $customer_obj->addFirstName($order_info[$type . '_firstname']);
        $customer_obj->addLastName($order_info[$type . '_lastname']);

        $customer_address_parser_obj = new \MultiSafepay\ValueObject\Customer\AddressParser();
        $parsed_address = $customer_address_parser_obj->parse($order_info[$type . '_address_1'], $order_info[$type . '_address_2']);

        $customer_address_obj = new \MultiSafepay\ValueObject\Customer\Address();
        $customer_address_obj->addStreetName($parsed_address[0]);
        $customer_address_obj->addHouseNumber($parsed_address[1]);
        $customer_address_obj->addZipCode($order_info[$type . '_postcode']);
        $customer_address_obj->addCity($order_info[$type . '_city']);
        $customer_address_obj->addState($order_info[$type . '_zone']);
        $customer_address_obj->addCountryCode($order_info[$type . '_iso_code_2']);
        $customer_obj->addAddress($customer_address_obj);

        return $customer_obj;
    }

    /**
     * Returns SecondChance object to be used in OrderRequest transaction
     *
     * @param bool $second_chance_status
     * @return SecondChance object
     *
     */
    public function getSecondChanceObject($second_chance_status) {
        $second_chance_details = new \MultiSafepay\Api\Transactions\OrderRequest\Arguments\SecondChance();
        $second_chance_details->addSendEmail($second_chance_status);
        return $second_chance_details;
    }

    /**
     * Returns PluginDetails object to be used in OrderRequest transaction
     *
     * @return PluginDetails object
     *
     */
    public function getPluginDetailsObject() {
        $plugin_details = new \MultiSafepay\Api\Transactions\OrderRequest\Arguments\PluginDetails();
        $plugin_details->addApplicationName('OpenCart');
        $plugin_details->addApplicationVersion(self::OC_VERSION);
        $plugin_details->addPluginVersion($this->getPluginVersion());
        $plugin_details->addShopRootUrl($this->getShopUrl());
        return $plugin_details;
    }

    /**
     * Returns a PaymentOptions object used to build the order request object
     *
     * @return PaymentOptions object
     *
     */
    public function getPaymentOptionsObject() {
        $payment_options_details = new \MultiSafepay\Api\Transactions\OrderRequest\Arguments\PaymentOptions();
        $payment_options_details->addNotificationUrl($this->url->link($this->route . '/postCallback', '', 'SSL'));
        $payment_options_details->addRedirectUrl($this->url->link('checkout/success', '', 'SSL'));
        $payment_options_details->addCancelUrl($this->url->link('checkout/failure', '', 'SSL'));
        return $payment_options_details;
    }

    /**
     * Returns a Description object used to build the order request object
     *
     * @param int $order_id
     * @return Description object
     *
     */
    public function getOrderDescriptionObject($order_id) {

    	$this->load->language($this->route);
	    $description = sprintf($this->language->get('text_order_description'), $order_id, $this->config->get('config_name'), date($this->language->get('datetime_format')) );

	    if($this->config->get($this->key_prefix . 'multisafepay_order_description')) {
			$description = $this->config->get($this->key_prefix . 'multisafepay_order_description');
			$description = str_replace('{order_id}', $order_id, $description);
		}

        $description_details = new \MultiSafepay\Api\Transactions\OrderRequest\Arguments\Description();
        $description_details->addDescription($description);
        return $description_details;
    }

    /**
     * Returns GatewayInfoInterface object to be used in OrderRequest transaction
     *
     * @return mixed boolean|GatewayInfoInterface object
     *
     */
    public function getGatewayInfoInterfaceObject($data) {

        if (!isset($data['gateway_info']) && empty($data['gateway_info'])) {
            return false;
        }

        switch ($data['gateway_info']) {
            case "Issuer":
                if (!isset($data['issuer_id']) && !empty($data['issuer_id'])) {
                    return false;
                }
                $gateway_info = new \MultiSafepay\Api\Transactions\OrderRequest\Arguments\GatewayInfo\Issuer();
                $gateway_info->addIssuerId($data['issuer_id']);
                break;
            case "QrCode":
                $gateway_info = new \MultiSafepay\Api\Transactions\OrderRequest\Arguments\GatewayInfo\QrCode();
                $gateway_info->addQrSize(250);
                $gateway_info->addAllowChangeAmount(false);
                $gateway_info->addAllowMultiple(false);
                break;
            case "Account":
                $gateway_info = new \MultiSafepay\Api\Transactions\OrderRequest\Arguments\GatewayInfo\Account();
                $gateway_info->addAccountHolderName($data['account_holder_name']);
	            $gateway_info->addAccountIdAsString($data['account_holder_iban']);
	            $gateway_info->addAccountHolderIbanAsString($data['account_holder_iban']);
                $gateway_info->addEmanDate($data['emandate']);
                break;
            case "Meta":
                $order_info = $this->getOrderInfo($data['order_id']);
                $gateway_info = new \MultiSafepay\Api\Transactions\OrderRequest\Arguments\GatewayInfo\Meta();
	            $gateway_info->addPhoneAsString($order_info['telephone']);
                $gateway_info->addEmailAddressAsString($order_info['email']);
                if(isset($data['gender']) && !empty($data['gender'])) {
                    $gateway_info->addGenderAsString($data['gender']);
                }
                if(isset($data['birthday']) && !empty($data['birthday'])) {
                    $gateway_info->addBirthdayAsString($data['birthday']);
                }
                if(isset($data['bankaccount']) && !empty($data['bankaccount'])) {
                    $gateway_info->addBankAccountAsString($data['bankaccount']);
                }
                break;
        }

        return $gateway_info;

    }

    /**
     * Returns a CartItem object used to build the order request object
     *
     * @param float $amount
     * @param string $currency_code
     * @param float $currency_value
     * @param bool $is_negative
     * @param string $name
     * @param int $quantity
     * @param string $merchant_item_id
     * @param string $tax_table_selector
     * @param string $description
     * @param string $weight_unit
     * @param float $weight_value
     * @return CartItem object
     *
     */
    private function getCartItemObject($price, $order_info, $name, $quantity, $merchant_item_id,
        $tax_rate, $description = '', $weight_unit = false, $weight_value = false) {
        $unit_price = $this->getMoneyObject($price, $order_info['currency_code'], $order_info['currency_value']);
        $cart_item = new \MultiSafepay\ValueObject\CartItem();
        $cart_item->addName($name);
        $cart_item->addUnitPrice($unit_price);
        $cart_item->addQuantity((int)$quantity);
        $cart_item->addMerchantItemId($merchant_item_id);
        $cart_item->addTaxRate((float)$tax_rate);
        $cart_item->addDescription($description);
        if ($weight_unit && $weight_value) {
            $cart_item_weight = $this->getWeightObject($weight_unit, (float)$weight_value);
            $cart_item->addWeight($cart_item_weight);
        }
        return $cart_item;
    }

    /**
     * Returns a negative CartItem object used to build the order request object
     *
     * @param float $amount
     * @param string $currency_code
     * @param float $currency_value
     * @param bool $is_negative
     * @param string $name
     * @param int $quantity
     * @param string $merchant_item_id
     * @param string $tax_table_selector
     * @param string $description
     * @param string $weight_unit
     * @param float $weight_value
     * @return CartItem object
     *
     */
    private function getNegativeCartItemObject($price, $order_info, $name, $quantity, $merchant_item_id,
        $tax_rate, $description = '', $weight_unit = false, $weight_value = false) {

        $unit_price = $this->getMoneyObject($price, $order_info['currency_code'], $order_info['currency_value']);
        $unit_price = $unit_price->negative();

        $cart_item = new \MultiSafepay\ValueObject\CartItem();
        $cart_item->addName($name);
        $cart_item->addUnitPrice($unit_price);
        $cart_item->addQuantity($quantity);
        $cart_item->addMerchantItemId($merchant_item_id);
        $cart_item->addTaxRate((float)$tax_rate);
        $cart_item->addDescription($description);
        if ($weight_unit && $weight_value) {
            $cart_item_weight = $this->getWeightObject($weight_unit, $weight_value);
            $cart_item->addWeight($cart_item_weight);
        }
        return $cart_item;
    }

    /**
     * Returns a Weight object used to build the order request object
     *
     * @param string $weight_unit
     * @param float $weight_value
     * @return Weight object
     *
     */
    private function getWeightObject($weight_unit, $weight_value) {
        $cart_item_weight = new \MultiSafepay\ValueObject\Weight(strtoupper($weight_unit), $weight_value);
        return $cart_item_weight;
    }

    /**
     * Returns an amount convert into another currency
     *
     * @param float $number
     * @param string $currency
     * @param float $value
     * @return Money object
     *
     */
    public function formatByCurrency($number, $currency, $value = '') {

        $this->load->model('localisation/currency');

        $currencies = $this->model_localisation_currency->getCurrencies();

        $decimal_place = 10;

        if (!$value) {
            $value = $currencies[$currency]['value'];
        }

        $amount = ($value) ? (float)$number * $value : (float)$number;

        $amount = round($amount, (int)$decimal_place);

        return $amount;

    }

    /**
     * Returns a Money object used to build the order request object addMoney method
     *
     * @param float $amount
     * @param string $currency_code
     * @param float $currency_value
     * @return Money object
     *
     */
    public function getMoneyObjectOrderAmount($amount, $currency_code, $currency_value) {
        $amount = $this->formatByCurrency($amount, $currency_code, $currency_value);
        $amount = $amount * 100;
        $amount = new  \MultiSafepay\ValueObject\Money($amount, $currency_code);
        return $amount;
    }

    /**
     * Returns a Money object used to build the order request object in shopping cart unit prices
     *
     * @param float $amount
     * @param string $currency_code
     * @param float $currency_value
     * @return Money object
     *
     */
    public function getMoneyObject($amount, $currency_code, $currency_value) {
        $amount =  round(($amount * 100), 10);
        $amount = $this->formatByCurrency($amount, $currency_code, $currency_value);
        $amount = new  \MultiSafepay\ValueObject\Money($amount, $currency_code);
        return $amount;
    }

	/**
	 * Returns an Order object called from the admin side
	 *
	 * @param int $order_id
	 * @return Order object
	 *
	 */
	public function getAdminOrderObject($order_id) {
		$order_info = $this->getAdminOrderInfo($order_id);
		$sdk = $this->multisafepay->getSdkObject($order_info['store_id']);
		$transaction_manager = $sdk->getTransactionManager();
		try {
			$order = $transaction_manager->get($order_id);
		}
		catch (\MultiSafepay\Exception\ApiException $apiException ) {
			return false;
		}
		return $order;
	}

    /**
     * Returns an Order object
     *
     * @param int $order_id
     * @return Order object
     *
     */
    public function getOrderObject($order_id) {
        $sdk = $this->multisafepay->getSdkObject($this->config->get('config_store_id'));
        $transaction_manager = $sdk->getTransactionManager();
        try {
            $order = $transaction_manager->get($order_id);
        }
        catch (\MultiSafepay\Exception\ApiException $apiException ) {
            return false;
        }
        return $order;
    }

    /**
     * Returns bool after validates IBAN format
     *
     * @return bool
     *
     */
    public function validateIban($iban) {
        require_once(DIR_SYSTEM . 'library/multisafepay/vendor/autoload.php');
        try {
            $iban = new \MultiSafepay\ValueObject\IbanNumber($iban);
            return true;
        }
        catch (\MultiSafepay\Exception\InvalidArgumentException $invalidArgumentException ) {
            return false;
        }
    }

    /**
     * Set the MultiSafepay order status as shipped or cancelled.
     *
     * @param int $order_id
     * @param string $status allowed values are shipped and cancelled
     * @return Order object
     *
     */
    public function changeMultiSafepayOrderStatusTo($order_id, $status) {
    	$order_info = $this->getAdminOrderInfo($order_id);
        $sdk = $this->getSdkObject($order_info['store_id']);
        $transaction_manager = $sdk->getTransactionManager();
        $update_order = new MultiSafepay\Api\Transactions\UpdateRequest();
        $update_order->addId($order_id);
        $update_order->addStatus($status);

        try {
            $transaction_manager->update($order_id, $update_order);
        }
        catch (\MultiSafepay\Exception\ApiException $apiException ) {
            die($apiException->getMessage());
        }

    }

    /**
     * Returns an array with additional information of the customer, to be used as additional
     * customer information in the order transaction
     *
     * @return array
     *
     */
    private function getAdditionalCustomerData() {
        if (!$this->customer->isLogged()) {
            $customer_additional_data = array(
                'customer_id' => 0,
                'customer_group_id' => $this->config->get('config_customer_group_id'),
                'customer_balance' => 0,
                'customer_reward_points' => 0,
            );
            return $customer_additional_data;
        }

        $customer_additional_data = array(
            'customer_id' => $this->customer->getId(),
            'customer_group_id' => $this->customer->getGroupId(),
            'customer_balance' => $this->customer->getBalance(),
            'customer_reward_points' => $this->customer->getRewardPoints(),
        );
        return $customer_additional_data;

    }

    /**
     * Returns the language code required by MultiSafepay.
     * Language code concatenated with the country code. Format: ab_CD.
     *
     * @return string
     *
     */
    public function getLocale() {
        $this->load->model('localisation/language');
        $language = $this->model_localisation_language->getLanguage($this->config->get('config_language_id'));

        if ((strlen($language['code']) !== 5 && strlen($language['code']) !== 2)) {
            return 'en_US';
        }

        if (strlen($language['code']) == 5) {
            $locale_strings = explode('-', $language['code']);
            $locale = $locale_strings[0] . '_' . strtoupper($locale_strings[1]);
        }

        if (strlen($language['code']) == 2) {
            $locale = $language['code'] . '_' . strtoupper($language['code']);
        }

        if($locale === 'en_EN') {
            return 'en_US';
        }

        return $locale;
    }

    /**
     * Returns the shop url according with the selected protocol
     *
     * @return string
     *
     */
    public function getShopUrl() {
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            return $this->config->get('config_ssl');
        }
        return  $this->config->get('config_url');
    }

    /**
     * Returns a unique product ID, formed with the product id concatenated with the id
     * of the products options, selected in the order.
     *
     * @param int $order_id The order id.
     * @param array $product The product from order information.
     * @return string
     *
     */
    private function getUniqueProductId($order_id, $product) {
        $unique_product_id = $product['product_id'];

        $option_data = $this->getProductOptionsData($order_id, $product);

        if (!empty($option_data)) {
            foreach($option_data as $option) {
                $unique_product_id .= '-' .  $option['product_option_id'];
            }
        }

        return (string)$unique_product_id;
    }

    /**
     * Returns product's name, according with order information,
     * including quantity and options selected.
     *
     * @param int $order_id The order id.
     * @param array $product The product from order information.
     * @return string
     *
     */
    private function getProductName($order_id, $product) {
        $option_data = $this->getProductOptionsData($order_id, $product);
        $product_name = $this->htmlEntityDecode($product['name']);

        if (empty($option_data)) {
            return $product['quantity'] . ' x ' . $product_name;
        }

        $option_output = '';

        foreach($option_data as $option) {
            $option_output .= $this->htmlEntityDecode($option['name']) . ': ' . $option['value'] . ', ';
        }
        $option_output = ' (' . substr($option_output, 0, -2) . ')';
        $product_name = $product['quantity'] . ' x ' . $product_name . $option_output;

        return $product_name;
    }

    /**
     * Returns product's options selected in the order
     *
     * @param int $order_id The order id.
     * @param array $product The product from order information.
     * @return array
     *
     */
    private function getProductOptionsData($order_id, $product) {
        $this->load->model($this->route);

        $option_data = array();

        $options = $this->{$this->model_call}->getOrderOptions($order_id, $product['product_id']);

        foreach ($options as $option) {
            if ($option['type'] !== 'file') {
                $option_data[] = $this->extractOptionsData($option);
            }
            if ($option['type'] === 'file') {
                $option_data[] = $this->extractOptionsFileData($option);
            }
        }
        return $option_data;
    }

    /**
     * Extract product's options data from options array
     *
     * @param array $option
     * @return array
     *
     */
    private function extractOptionsData($option) {
        $option_data = array(
            'name'               => $option['name'],
            'value'              => $option['value'],
            'product_option_id'  => $option['product_option_id'],
            'order_option_id'    => $option['order_option_id']
        );
        return $option_data;
    }

    /**
     * Extract product's options data file from options array
     *
     * @param array $option
     * @return array
     *
     */
    private function extractOptionsFileData($option) {
        $this->load->model('tool/upload');
        $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
        if ($upload_info) {
            $option_data = array(
                'name'               => $option['name'],
                'value'              => $upload_info['name'],
                'product_option_id'  => $option['product_option_id'],
                'order_option_id'    => $option['order_option_id']
            );
        }
        return $option_data;
    }

    /**
     * Extract fixed rates from taxes that might be related to handling and low order fee total modules,
     * used as helper in the function getFixedTaxesItems
     *
     * @param array $product
     * @param int $quantity
     * @param array $fixed_taxes_items
     * @return array $fixed_taxes_items
     *
     */
    private function extractFixedTaxesRatesFromProducts($oc_tax_rate, $quantity, $fixed_taxes_items) {
        if ($oc_tax_rate['type'] == self::FIXED_TYPE) {
            for ($i = 1; $i <= $quantity; $i++) {
                $fixed_taxes_items[] = $oc_tax_rate;
            }
        }
        return $fixed_taxes_items;
    }

    /**
     * Extract fixed rates from taxes that might be related to handling and low order fee total modules,
     * used as helper in the function getFixedTaxesItems
     *
     * @param array $order_totals
     * @param array $fixed_taxes_items
     * @param string $key
     * @param string $type
     * @return array $fixed_taxes_items
     *
     */
    private function extractFixedTaxesFromHandlingLowOrderFee($order_totals, $fixed_taxes_items, $key, $type) {
        $tax_class_id  = $this->config->get($this->total_extension_key_prefix . $type . '_tax_class_id');
        $is_order_lower_than_taxes = $this->isSortOrderLowerThanTaxes($this->config->get($this->total_extension_key_prefix . $type . '_sort_order'));
        if ($tax_class_id && $is_order_lower_than_taxes) {
            $fixed_taxes_items = $this->addToArrayOfFixedTaxes($order_totals[$key]['value'], $tax_class_id, $fixed_taxes_items);
        }
        return $fixed_taxes_items;

    }

    /**
     * Returns an array with fixed taxes; which will becomes in cart items.
     *
     * @param int $order_id
     * @return array $fixed_taxes_items
     *
     */
    private function getFixedTaxesItems($order_id) {
        $order_totals = $this->getOrderTotals($order_id);
        $order_info = $this->getOrderInfo($order_id);
        $order_products = $this->getOrderProducts($order_id);
        $coupon_info = $this->getCouponInfo($order_id);
        $detected_third_party_order_total_keys = $this->checkForThirdPartyPluginsOrderTotals();

        $has_handling = array_search('handling', array_column($order_totals, 'code'));
        $has_low_order_fee = array_search('low_order_fee', array_column($order_totals, 'code'));
        $has_shipping = array_search('shipping', array_column($order_totals, 'code'));
        $has_coupons = array_search('coupon', array_column($order_totals, 'code'));

        $fixed_taxes_items = array();

        foreach ($order_products as $product) {
            $product_info = $this->getProductInfo($product['product_id']);
            $oc_tax_rates = $this->tax->getRates($product['price'], $product_info['tax_class_id']);
            foreach ($oc_tax_rates as $oc_tax_rate) {
                $fixed_taxes_items = $this->extractFixedTaxesRatesFromProducts($oc_tax_rate, $product['quantity'], $fixed_taxes_items);
            }
        }

        if ($has_shipping !== false) {
            $shipping_tax_class_id = $this->getShippingTaxClassId($order_info['shipping_code']);
            if ($shipping_tax_class_id) {
                $fixed_taxes_items = $this->addToArrayOfFixedTaxes($order_totals[$has_shipping]['value'], $shipping_tax_class_id, $fixed_taxes_items);
            }
        }

        if ($has_handling !== false) {
            $fixed_taxes_items = $this->extractFixedTaxesFromHandlingLowOrderFee($order_totals, $fixed_taxes_items, $has_handling, 'handling');
        }

        if ($has_low_order_fee !== false) {
            $fixed_taxes_items = $this->extractFixedTaxesFromHandlingLowOrderFee($order_totals, $fixed_taxes_items, $has_low_order_fee, 'low_order_fee');
        }

        if(!empty($detected_third_party_order_total_keys)) {
            foreach ($detected_third_party_order_total_keys as $custom_order_total_key) {
                $custom_order_total_key = trim($custom_order_total_key);
                $has_custom_order_total = array_search($custom_order_total_key, array_column($order_totals, 'code'));
                if ($has_custom_order_total) {
                  $fixed_taxes_items = $this->extractFixedTaxesFromHandlingLowOrderFee($order_totals, $fixed_taxes_items, $has_custom_order_total, $custom_order_total_key);
                }
            }
        }

        if (empty($fixed_taxes_items)) {
            return false;
        }

        if (!empty($fixed_taxes_items)) {
            $shopping_cart_items = array();
            // If there are more than once with the same id; must be grouped, then counted
            foreach ($fixed_taxes_items as $fixed_taxes_item) {
                $fixed_taxes_items_ungrouped[$fixed_taxes_item['tax_rate_id']][] = $fixed_taxes_item;
            }
            foreach ($fixed_taxes_items_ungrouped as $fixed_taxes_item) {
                $fixed_taxes_item_quantity = count($fixed_taxes_item);
                $shopping_cart_item = $this->getCartItemObject(
                    $fixed_taxes_item[0]['amount'],
                    $order_info,
                    sprintf($this->language->get('text_fixed_product_name'), $fixed_taxes_item[0]['name']),
                    $fixed_taxes_item_quantity,
                    'TAX-' . $fixed_taxes_item[0]['tax_rate_id'],
                    0
                );
                $shopping_cart_items[] = $shopping_cart_item;
            }
            return $shopping_cart_items;
        }
    }

    /**
     * Search into the array of tax rates that belongs to shipping, handling or low order fees
     * an add the rates found to an array, to be return an added to the transaction as items.
     *
     * @param float $total
     * @param int $tax_class_id
     * @param array $array_taxes
     * @return array
     *
     */
    private function addToArrayOfFixedTaxes($total, $tax_class_id, $array_taxes) {
        $rate = $this->tax->getRates($total, $tax_class_id);
        foreach ($rate as $oc_tax_rate) {
            if ($oc_tax_rate['type'] == self::FIXED_TYPE) {
                $array_taxes[] = $oc_tax_rate;
            }
        }
        return $array_taxes;
    }

    /**
     * Returns the shipping tax class id if exist from the order shipping code.
     *
     * @param string $shipping_code
     * @return mixed boolean|int
     *
     */
    private function getShippingTaxClassId($shipping_code) {
        $shipping_code = explode('.', $shipping_code);
        $shipping_tax_class_id_key = $this->shipping_key_prefix . $shipping_code['0'] . '_tax_class_id';
        $shipping_tax_class_id = $this->config->get($shipping_tax_class_id_key);
        return $shipping_tax_class_id;
    }

    /**
     * Returns order totals information.
     *
     * @param int $order_id
     * @return array $order_totals
     *
     */
    public function getOrderTotals($order_id) {
        $this->load->model($this->route);
        $order_totals = $this->{$this->model_call}->getOrderTotals($order_id);
        return $order_totals;
    }

	/**
	 * Returns order using model from admin.
	 *
	 * @param int $order_id
	 * @return array $order_info
	 *
	 */
	public function getAdminOrderInfo($order_id) {
		$this->load->model('sale/order');
		$order_info = $this->model_sale_order->getOrder($order_id);
		return $order_info;
	}

    /**
     * Returns order information.
     *
     * @param int $order_id
     * @return array $order_info
     *
     */
    public function getOrderInfo($order_id) {
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);
        return $order_info;
    }

	/**
	 * Returns vouchers information to be included as discount.
	 *
	 * @param int $order_id
	 * @return array $order_vouchers
	 *
	 */
    public function getOrderVouchers($order_id) {
	    $order_totals = $this->getOrderTotals($order_id);
	    $has_vouchers = array_search('voucher', array_column($order_totals, 'code'));
	    if ($has_vouchers === false) {
		    return false;
	    }
	    $vouchers_info = array();
	    $vouchers_info[] = array(
		    'amount'           => $order_totals[$has_vouchers]['value'],
		    'description'      => $this->htmlEntityDecode($order_totals[$has_vouchers]['title'])
	    );
	    return $vouchers_info;
    }

    /**
     * Returns product information.
     *
     * @param int $product_id
     * @return array $product_info
     *
     */
    private function getProductInfo($product_id) {
        $this->load->model('catalog/product');
        $product_info = $this->model_catalog_product->getProduct($product_id);
        return $product_info;
    }

    /**
     * Returns order`s products.
     *
     * @param int $order_id
     * @return array $order_products
     *
     */
    public function getOrderProducts($order_id) {
        $this->load->model($this->route);
        $order_products = $this->{$this->model_call}->getOrderProducts($order_id);
        return $order_products;
    }

    /**
     * Returns coupon info if exist, or false.
     *
     * @param int $order_id
     * @return mixed false|$coupon_info
     *
     */
    public function getCouponInfo($order_id) {
        $order_totals = $this->getOrderTotals($order_id);
        $has_coupons = array_search('coupon', array_column($order_totals, 'code'));
        if ($has_coupons === false) {
            return false;
        }
        if (!isset($this->session->data['coupon']) || empty($this->session->data['coupon'])) {
            return false;
        }
        $this->load->model($this->route);
        $coupon_info = $this->{$this->model_call}->getCoupon($this->session->data['coupon']);
        $coupon_info['name'] = $this->htmlEntityDecode($coupon_info['name']);
        $coupon_info['is_order_lower_than_taxes'] = $this->isSortOrderLowerThanTaxes($this->config->get($this->total_extension_key_prefix . 'coupon_sort_order'));
        return $coupon_info;
    }

    /**
     * Returns shipping method info if exist, or false.
     *
     * @param int $order_id
     * @return mixed false|$shipping_info
     *
     */
    private function getShippingInfo($order_id) {
        $order_totals = $this->getOrderTotals($order_id);
        $has_shipping = array_search('shipping', array_column($order_totals, 'code'));
        if ($has_shipping === false) {
            return false;
        }
        $order_info = $this->getOrderInfo($order_id);
        $shipping_tax_class_id = $this->getShippingTaxClassId($order_info['shipping_code']);
        $tax_rate = $this->getItemTaxRate($order_totals[$has_shipping]['value'], $shipping_tax_class_id);
        $shipping_info = array(
            'value' => $order_totals[$has_shipping]['value'],
            'title' => $this->htmlEntityDecode($order_totals[$has_shipping]['title']),
            'tax_rate' => $tax_rate
        );
        return $shipping_info;
    }

    /**
     * Returns CartItem object with shipping information.
     *
     * @param int $order_id
     * @return CartItem object
     *
     */
    private function getShippingItem($order_id) {
        $this->load->language($this->route);
        $order_info = $this->getOrderInfo($order_id);
        $coupon_info = $this->getCouponInfo($order_id);
        $shipping_info = $this->getShippingInfo($order_id);

        if (($coupon_info && isset($coupon_info['shipping']) && $coupon_info['shipping'])) {
            return $this->getCartItemObject(
                0,
                $order_info,
                sprintf($this->language->get('text_coupon_applied_to_shipping'), $shipping_info['title'], $coupon_info['name']),
                1,
                'msp-shipping',
                0
            );
        }

        if ((!$coupon_info) || ($coupon_info && !$coupon_info['shipping'])) {
            return $this->getCartItemObject(
                $shipping_info['value'],
                $order_info,
                $shipping_info['title'],
                1,
                'msp-shipping',
                $shipping_info['tax_rate']
            );
        }
    }

    /**
     * Returns CartItem object with product information.
     *
     * @param array $product
     * @param int $order_id
     * @param array $order_products
     * @return CartItem object
     *
     */
	// phpcs:ignore ObjectCalisthenics.Metrics.MaxNestingLevel
    private function getCartItem($product, $order_id, $order_products) {
        $this->load->language($this->route);
        $order_info = $this->getOrderInfo($order_id);
        $product_info =  $this->getProductInfo($product['product_id']);
        $product_name = $this->getProductName($order_id, $product);
        $product_price = $product['price'];
        $product_description = '';
        $merchant_item_id = $this->getUniqueProductId($order_id, $product);

        $tax_rate = $this->getItemTaxRate($product['price'], $product_info['tax_class_id']);

        // Some third party extensions could set the product taxes to 0, even when the product has valid tax class id assigned
        if (isset($product['tax']) && $product['tax'] == 0) {
            $tax_rate = 0;
        }

        $reward_info = $this->getRewardInfo($order_id);
        $coupon_info = $this->getCouponInfo($order_id);

        if($reward_info) {
            $discount_by_product = $this->getRewardPointsDiscountByProduct($order_id);
            if(isset($discount_by_product[$product['product_id']]['discount_per_product'])) {
                $product_price -= $discount_by_product[$product['product_id']]['discount_per_product'];
                $discount =  $this->currency->format($discount_by_product[$product['product_id']]['discount_per_products'], $order_info['currency_code'], $order_info['currency_value'], true);
                $product_name .= sprintf($this->language->get('text_reward_applied'), $discount, strtolower($reward_info['title']));
                $product_description .= sprintf($this->language->get('text_reward_applied'), $discount, strtolower($reward_info['title']));
            }
        }

		// Coupons is fixed type and apply just to a few items in the order before taxes
	    if (
		    $coupon_info &&
		    isset($coupon_info['type']) &&
		    $coupon_info['type'] == self::FIXED_TYPE &&
		    $coupon_info['is_order_lower_than_taxes'] &&
		    !empty($coupon_info['product']) &&
		    $coupon_info['discount'] > 0 &&
		    in_array($product['product_id'], $coupon_info['product'])
	    ) {
	    	$count = 0;
	    	foreach ($order_products as $order_product) {
	    		if(in_array($order_product['product_id'], $coupon_info['product'])) {
	    			$count++;
			    }
		    }
		    $discount_by_product =  ($coupon_info['discount'] / $count / $product['quantity']);
		    $product_price -= $discount_by_product;
		    $product_name .= ' - '.sprintf($this->language->get('text_coupon_applied'), $coupon_info['name']);
		    $product_description .= sprintf(
			    $this->language->get('text_price_before_coupon'),
			    $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value'], true),
			    $coupon_info['name']
		    );
	    }

	    // Coupons is fixed type and apply to all items in the order before taxes
	    if (
	    	$coupon_info &&
		    isset($coupon_info['type']) &&
		    $coupon_info['type'] == self::FIXED_TYPE &&
		    $coupon_info['is_order_lower_than_taxes'] &&
		    empty($coupon_info['product']) &&
		    $coupon_info['discount'] > 0
	    ) {
		    // Coupon discount is distributed in the same way for each product in the cart
	    	$discount_by_product =  ($coupon_info['discount'] / count($order_products) / $product['quantity']);
		    $product_price -= $discount_by_product;
		    $product_name .= ' - '.sprintf($this->language->get('text_coupon_applied'), $coupon_info['name']);
		    $product_description .= sprintf(
			    $this->language->get('text_price_before_coupon'),
			    $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value'], true),
			    $coupon_info['name']
		    );
	    }

        // Coupons is percentage type and apply just to a few items in the order.
        if ($coupon_info
            && isset($coupon_info['type']) && $coupon_info['type'] == self::PERCENTAGE_TYPE
            && $coupon_info['is_order_lower_than_taxes']
            && !empty($coupon_info['product'])
            && in_array($product['product_id'], $coupon_info['product'])) {
        	$discount_by_product = ($product['price'] * ($coupon_info['discount'] / 100));
            $product_price -= $discount_by_product;
            // If coupon is just for free shipping, the name and description is not modified
            if ($coupon_info['discount'] > 0) {
                $product_name .= ' - '.sprintf($this->language->get('text_coupon_applied'), $coupon_info['name']);
                $product_description .= sprintf(
                    $this->language->get('text_price_before_coupon'),
                    $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value'], true),
                    $coupon_info['name']
                );
            }
        }

        // Coupons is percentage type and apply for all items in the order.
        if ($coupon_info && isset($coupon_info['type']) && $coupon_info['type'] == self::PERCENTAGE_TYPE && $coupon_info['is_order_lower_than_taxes'] && empty($coupon_info['product'])) {
	        $discount_by_product = ($product['price'] * ($coupon_info['discount']/100));
        	$product_price -= $discount_by_product;
            // If coupon is just for free shipping, the name and description is not modified
            if ($coupon_info['discount'] > 0) {
                $product_name .= ' - ' . sprintf($this->language->get('text_coupon_applied'),
                        $coupon_info['name']);
                $product_description .= sprintf($this->language->get('text_price_before_coupon'),
                    $this->currency->format($product['price'], $order_info['currency_code'],
                        $order_info['currency_value'], true), $coupon_info['name']);
            }
        }

        $shopping_cart_item = $this->getCartItemObject(
            $product_price,
            $order_info,
            $product_name,
            $product['quantity'],
            $merchant_item_id,
            $tax_rate,
            $product_description,
            $this->weight->getUnit($product_info['weight_class_id']),
            $product_info['weight']
        );

        return $shopping_cart_item;
    }

    /**
     * Returns CartItem object with product information.
     *
     * @param int $order_id
     * @return CartItem object
     *
     */
    private function getCouponItem($order_id) {
        $coupon_info = $this->getCouponInfo($order_id);
        $order_info = $this->getOrderInfo($order_id);

	    if (
		    (!$coupon_info) ||
		    (isset($coupon_info['type']) && $coupon_info['type'] !== self::FIXED_TYPE) ||
		    (isset($coupon_info['type']) && $coupon_info['type'] == self::FIXED_TYPE && $coupon_info['discount'] == 0) ||
	        ($coupon_info['is_order_lower_than_taxes'])
        ) {
		    return false;
	    }


        return $this->getNegativeCartItemObject(
            $coupon_info['discount'],
            $order_info,
            $coupon_info['name'],
            1,
            'COUPON',
            0
        );

    }

    /**
     * Returns handling fee information if exist, or false.
     *
     * @param int $order_id
     * @return mixed false|array
     *
     */
    private function getHandlingFeeInfo($order_id) {
        $order_totals = $this->getOrderTotals($order_id);
        $has_handling_fee = array_search('handling', array_column($order_totals, 'code'));
        if ($has_handling_fee === false) {
            return false;
        }

        $handling_tax_class_id  = $this->config->get($this->total_extension_key_prefix . 'handling_tax_class_id');
        $tax_rate = $this->getItemTaxRate($order_totals[$has_handling_fee]['value'], $handling_tax_class_id);

        $handling_fee_info = array(
            'value' => $order_totals[$has_handling_fee]['value'],
            'title' => $this->htmlEntityDecode($order_totals[$has_handling_fee]['title']),
            'is_order_lower_than_taxes' => $this->isSortOrderLowerThanTaxes($this->config->get($this->total_extension_key_prefix . 'handling_sort_order')),
            'tax_rate' => $tax_rate
        );
        return $handling_fee_info;
    }

    /**
     * Returns CartItem object with handling fee information.
     *
     * @param int $order_id
     * @return CartItem object
     *
     */
    private function getHandlingFeeItem($order_id) {
        $handling_fee_info = $this->getHandlingFeeInfo($order_id);
        $order_info = $this->getOrderInfo($order_id);
        if (!$handling_fee_info) {
            return false;
        }

        return $this->getCartItemObject(
            $handling_fee_info['value'],
            $order_info,
            $handling_fee_info['title'],
            1,
            'HANDLING',
            $handling_fee_info['tax_rate']
        );
    }

    /**
     * Returns low order fee information.
     *
     * @param int $order_id
     * @return array $low_order_fee_info
     *
     */
    private function getLowOrderFeeInfo($order_id) {
        $order_totals = $this->getOrderTotals($order_id);
        $has_low_order_fee = array_search('low_order_fee', array_column($order_totals, 'code'));
        if ($has_low_order_fee === false) {
            return false;
        }

        $low_order_fee_tax_class_id  = $this->config->get($this->total_extension_key_prefix . 'low_order_fee_tax_class_id');
        $tax_rate = $this->getItemTaxRate($order_totals[$has_low_order_fee]['value'], $low_order_fee_tax_class_id);
        $low_order_fee_info = array(
            'value' => $order_totals[$has_low_order_fee]['value'],
            'title' => $this->htmlEntityDecode($order_totals[$has_low_order_fee]['title']),
            'is_order_lower_than_taxes' => $this->isSortOrderLowerThanTaxes($this->config->get($this->total_extension_key_prefix . 'low_order_fee_sort_order')),
            'tax_rate' => $tax_rate
        );
        return $low_order_fee_info;
    }

    /**
     * Returns CartItem object with low order fee.
     *
     * @param int $order_id
     * @return CartItem object
     *
     */
    private function getLowOrderFeeItem($order_id) {
        $low_order_fee_info = $this->getLowOrderFeeInfo($order_id);
        $order_info = $this->getOrderInfo($order_id);
        if ($low_order_fee_info) {
            return $this->getCartItemObject(
                $low_order_fee_info['value'],
                $order_info,
                $low_order_fee_info['title'],
                1,
                'LOWORDERFEE',
                $low_order_fee_info['tax_rate']
            );
        }
    }

    /**
     * Returns reward info if exist, or false.
     *
     * @param int $order_id
     * @return array $reward_info
     */
    private function getRewardInfo($order_id) {
        $order_totals = $this->getOrderTotals($order_id);
        $has_reward = array_search('reward', array_column($order_totals, 'code'));
        if ($has_reward === false) {
            return false;
        }

        $reward_info = array(
            'value' => $order_totals[$has_reward]['value'],
            'title' => $this->htmlEntityDecode($order_totals[$has_reward]['title']),
        );
        return $reward_info;
    }

    /**
     * Returns reward discount by product id.
     *
     * @param int $order_id
     * @return array $discounts
     *
     */
    public function getRewardPointsDiscountByProduct($order_id) {
        $order_products = $this->getOrderProducts($order_id);
        $points_total = 0;

        foreach ($order_products as $product) {
            $product_info = $this->getProductInfo($product['product_id']);
            if ($product_info['points']) {
                $points_total += ($product_info['points'] * $product['quantity']);
            }
        }

        $discounts = array();
        foreach ($order_products as $product) {
            $product_info = $this->getProductInfo($product['product_id']);
            if ($product_info['points']) {
                $discount_per_products = $product['total'] * ($this->session->data['reward'] / $points_total);
                $discount_per_product = $discount_per_products / $product['quantity'];
                $discounts[$product['product_id']]['discount_per_product'] = $discount_per_product;
                $discounts[$product['product_id']]['discount_per_products'] = $discount_per_products;
            }
        }
        return $discounts;
    }

    /**
     * Return if the order contains a credit - customer balance item
     *
     * @param int $order_id
     * @return mixed
     */
    private function getCustomerBalanceInfo($order_id) {
        $this->load->language($this->route);

        $order_totals = $this->getOrderTotals($order_id);
        $has_credit = array_search('credit', array_column($order_totals, 'code'));
        if ($has_credit === false) {
            return false;
        }

        $credit_info = array(
            'value' => $order_totals[$has_credit]['value'],
            'title' => $this->htmlEntityDecode($this->language->get('text_customer_balance')),
        );
        return $credit_info;
    }

    /**
     * Returns CartItem object with customer balance.
     *
     * @param int $order_id
     * @return CartItem object
     *
     */
    private function getCustomerBalanceItem($order_id) {
        $customer_balance_item = $this->getCustomerBalanceInfo($order_id);
        $order_info = $this->getOrderInfo($order_id);
        return $this->getNegativeCartItemObject(
            -$customer_balance_item['value'],
            $order_info,
            $customer_balance_item['title'],
            1,
            'CREDIT',
            0
        );
    }

	/**
	 * Returns vouchers information to be included as product.
	 *
	 * @param int $order_id
	 * @return array $order_vouchers
	 *
	 */
	public function getOrderVouchersItemsInCart($order_id) {
		$this->load->model( $this->route );
		$order_vouchers = $this->{$this->model_call}->getOrderVouchers( $order_id );
		$voucher_info = array();
		foreach ($order_vouchers as $order_voucher) {
			$voucher_info[] = array(
				'order_voucher_id' => $order_voucher['order_voucher_id'],
				'value'            => $order_voucher['amount'],
				'title'            => $this->htmlEntityDecode($order_voucher['description']),
			);
		}
		return $voucher_info;
	}

    /**
     * Returns voucher information if exist, or false.
     *
     * @param int $order_id
     * @return mixed false|array
     *
     */
    private function getVoucherInfo($order_id) {
        $order_vouchers = $this->getOrderVouchers($order_id);
        if (!$order_vouchers) {
            return false;
        }
	    $voucher_info = array();
        foreach ($order_vouchers as $order_voucher) {
	        $voucher_info[] = array(
	        	'value'            => $order_voucher['amount'],
		        'title'            => $this->htmlEntityDecode($order_voucher['description']),
	        );
        }
        return $voucher_info;
    }

    /**
     * Returns CartItem object with voucher.
     *
     * @param int $order_id
     * @return CartItem[] object
     *
     */
    private function getVouchersItem($order_id) {
        $vouchers_info = $this->getVoucherInfo($order_id);
        $order_info = $this->getOrderInfo($order_id);
        $cart_items = array();
        foreach ($vouchers_info as $voucher_info) {
	        $cart_items[] = $this->getCartItemObject(
		        $voucher_info['value'],
		        $order_info,
		        $voucher_info['title'],
		        1,
		        'VOUCHER',
		        0
	        );
        }
        return $cart_items;
    }

    /**
     * Returns voucher information if exist, or false.
     *
     * @param int $order_id
     * @return mixed false|array
     *
     */
    private function getOrderVouchersItem($order_id, $voucher_info) {
	    $order_info = $this->getOrderInfo($order_id);
	    return $this->getCartItemObject(
		    $voucher_info['value'],
		    $order_info,
		    $voucher_info['title'],
		    1,
		    $voucher_info['order_voucher_id'],
		    0
	    );
    }

    /**
     * Returns Custom Order Total information if exist, or false.
     *
     * @param int $order_id
     * @return mixed false|array
     *
     */
    private function getCustomOrderTotalInfo($order_id, $custom_order_total_key) {
        $order_totals = $this->getOrderTotals($order_id);
        $has_custom_order_total = array_search($custom_order_total_key, array_column($order_totals, 'code'));
        if ($has_custom_order_total === false) {
            return false;
        }

        $custom_order_total_tax_class_id  = $this->config->get($this->total_extension_key_prefix . $custom_order_total_key . '_tax_class_id');
        $tax_rate = $this->getItemTaxRate($order_totals[$has_custom_order_total]['value'], $custom_order_total_tax_class_id);
        $custom_order_total_info = array(
            'value' => $order_totals[$has_custom_order_total]['value'],
            'title' => $this->htmlEntityDecode($order_totals[$has_custom_order_total]['title']),
            'is_order_lower_than_taxes' => $this->isSortOrderLowerThanTaxes($this->config->get($this->total_extension_key_prefix . $custom_order_total_key . '_sort_order')),
            'tax_rate' => $tax_rate
        );
        return $custom_order_total_info;
    }

    /**
     * Returns Custom Order Total Cart Item object
     *
     * @param int $order_id
     * @return CartItem object
     *
     */
    private function getCustomOrderTotalItem($order_id, $custom_order_total_key) {
        $custom_order_total_info = $this->getCustomOrderTotalInfo($order_id, $custom_order_total_key);
        $order_info = $this->getOrderInfo($order_id);
        if (!$custom_order_total_info) {
            return false;
        }

        if(!$custom_order_total_info['is_order_lower_than_taxes']) {
            $custom_order_total_info['tax_rate'] = 0;
        }

        return $this->getCartItemObject(
            $custom_order_total_info['value'],
            $order_info,
            $custom_order_total_info['title'],
            1,
            $custom_order_total_key,
            $custom_order_total_info['tax_rate']
        );
    }

    /**
     * Return the string decoded, when contains html entities.
     *
     * @param $string
     * @return string
     */
    private function htmlEntityDecode($string) {
        return html_entity_decode($string, ENT_COMPAT, 'UTF-8');
    }

    /**
     * Returns cart items reordered.
     *
     * @param array $shopping_cart_items
     * @return array $cart_items
     *
     */
    private function reOrderShoppingCartItems($shopping_cart_items) {
        ksort($shopping_cart_items);
        $cart_items = array();
        foreach ($shopping_cart_items as $key => $value) {
            foreach ($value as $item) {
                $cart_items[] = $item;
            }
        }
        return $cart_items;
    }

    /**
     * Return all gateways
     *
     * @return array $gateways
     * phpcs:disable ObjectCalisthenics.Files.FunctionLength
     */
    public function getGateways() {
        $this->language->load($this->route);
        $this->load->model('setting/setting');
        $gateways = array(
            array(
                'id' => 'MULTISAFEPAY',
                'code' => 'multisafepay',
                'route' => 'multisafepay',
                'description' => $this->language->get('text_title_multisafepay'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_multisafepay'),
                'image' => 'multisafepay'
            ),
            array(
                'id' => 'AFTERPAY',
                'code' => 'afterpay',
                'route' => 'multisafepay/afterPay',
                'description' => $this->language->get('text_title_afterpay'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_afterpay'),
                'image' => 'afterpay'
            ),
            array(
                'id' => 'ALIPAY',
                'code' => 'alipay',
                'route' => 'multisafepay/alipay',
                'description' => $this->language->get('text_title_alipay'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_alipay'),
                'image' => 'alipay'
            ),
            array(
                'id' => 'ALIPAYPLUS',
                'code' => 'alipayplus',
                'route' => 'multisafepay/alipayplus',
                'description' => $this->language->get('text_title_alipayplus'),
                'type' => 'gateway',
                'redirect_switch' => false,
                'brief_description' => $this->language->get('text_brief_description_alipay'),
                'image' => 'alipayplus'
            ),
            array(
                'id' => 'AMAZONBTN',
                'code' => 'amazonbtn',
                'route' => 'multisafepay/amazonPay',
                'description' => $this->language->get('text_title_amazon_pay'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_amazonpay'),
                'image' => 'amazonpay'
            ),
            array(
                'id' => 'AMEX',
                'code' => 'amex',
                'route' => 'multisafepay/amex',
                'description' => $this->language->get('text_title_american_express'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_amex'),
                'image' => 'amex'
            ),
            array(
                'id' => 'APPLEPAY',
                'code' => 'applepay',
                'route' => 'multisafepay/applePay',
                'description' => $this->language->get('text_title_apple_pay'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_applepay'),
                'image' => 'applepay'
            ),
            array(
                'id' => 'MISTERCASH',
                'code' => 'mistercash',
                'route' => 'multisafepay/bancontact',
                'description' => $this->language->get('text_title_bancontact'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_mistercash'),
                'image' => 'bancontact'
            ),
            array(
                'id' => 'BABYCAD',
                'code' => 'babycad',
                'route' => 'multisafepay/babyCad',
                'description' => $this->language->get('text_title_baby_cad'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_babycad'),
                'image' => 'babycad'
            ),
            array(
                'id' => 'BANKTRANS',
                'code' => 'banktrans',
                'route' => 'multisafepay/bankTransfer',
                'description' => $this->language->get('text_title_bank_transfer'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_banktrans'),
                'image' => 'banktrans'
            ),
            array(
                'id' => 'BEAUTYWELL',
                'code' => 'beautywellness',
                'route' => 'multisafepay/beautyWellness',
                'description' => $this->language->get('text_title_beauty_wellness'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_beautywellness'),
                'image' => 'beautywellness'
            ),
            array(
                'id' => 'BELFIUS',
                'code' => 'belfius',
                'route' => 'multisafepay/belfius',
                'description' => $this->language->get('text_title_belfius'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_belfius'),
                'image' => 'belfius'
            ),
            array(
                'id' => 'BOEKENBON',
                'code' => 'boekenbon',
                'route' => 'multisafepay/boekenbon',
                'description' => $this->language->get('text_title_boekenbon'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_boekenbon'),
                'image' => 'boekenbon'
            ),
            array(
                'id' => 'CBC',
                'code' => 'cbc',
                'route' => 'multisafepay/cbc',
                'description' => $this->language->get('text_title_cbc'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_cbc'),
                'image' => 'cbc'
            ),
            array(
                'id' => 'CREDITCARD',
                'code' => 'creditcard',
                'route' => 'multisafepay/creditCard',
                'description' => $this->language->get('text_title_credit_card'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_creditcard'),
                'image' => 'creditcard'
            ),
            array(
                'id' => 'DBRTP',
                'code' => 'dbrtp',
                'route' => 'multisafepay/dbrtp',
                'description' => $this->language->get('text_title_dbrtp'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_dbrtp'),
                'image' => 'dbrtp'
            ),
            array(
                'id' => 'DIRECTBANK',
                'code' => 'directbank',
                'route' => 'multisafepay/directBank',
                'description' => $this->language->get('text_title_direct_bank'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_directbank'),
                'image' => 'sofort'
            ),
            array(
                'id' => 'DOTPAY',
                'code' => 'dotpay',
                'route' => 'multisafepay/dotpay',
                'description' => $this->language->get('text_title_dotpay'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_dotpay'),
                'image' => 'Dotpay'
            ),
            array(
                'id' => 'EPS',
                'code' => 'eps',
                'route' => 'multisafepay/eps',
                'description' => $this->language->get('text_title_eps'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_eps'),
                'image' => 'eps'
            ),
            array(
                'id' => 'EINVOICE',
                'code' => 'einvoice',
                'route' => 'multisafepay/eInvoice',
                'description' => $this->language->get('text_title_e_invoicing'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_einvoice'),
                'image' => 'einvoice'
            ),
            array(
                'id' => 'FASHIONCHQ',
                'code' => 'fashioncheque',
                'route' => 'multisafepay/fashionCheque',
                'description' => $this->language->get('text_title_fashion_cheque'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_fashioncheque'),
                'image' => 'fashioncheque'
            ),
            array(
                'id' => 'FASHIONGFT',
                'code' => 'fashiongiftcard',
                'route' => 'multisafepay/fashionGiftCard',
                'description' => $this->language->get('text_title_fashion_gift_card'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_fashiongiftcard'),
                'image' => 'fashiongiftcard'
            ),
            array(
                'id' => 'FIETSENBON',
                'code' => 'fietsenbon',
                'route' => 'multisafepay/fietsenbon',
                'description' => $this->language->get('text_title_fietsenbon'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_fietsenbon'),
                'image' => 'fietsenbon'
            ),
            array(
                'id' => 'GEZONDHEID',
                'code' => 'gezondheidsbon',
                'route' => 'multisafepay/gezondheidsbon',
                'description' => $this->language->get('text_title_gezondheidsbon'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_gezondheidsbon'),
                'image' => 'gezondheidsbon'
            ),
            array(
                'id' => 'GIVACARD',
                'code' => 'givacard',
                'route' => 'multisafepay/givaCard',
                'description' => $this->language->get('text_title_giva_card'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_givacard'),
                'image' => 'givacard'
            ),
            array(
                'id' => 'GIROPAY',
                'code' => 'giropay',
                'route' => 'multisafepay/giroPay',
                'description' => $this->language->get('text_title_giropay'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_giropay'),
                'image' => 'giropay'
            ),
            array(
                'id' => 'GOOD4FUN',
                'code' => 'good4fun',
                'route' => 'multisafepay/good4fun',
                'description' => $this->language->get('text_title_good4fun'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_good4fun'),
                'image' => 'good4fun'
            ),
            array(
                'id' => 'GOODCARD',
                'code' => 'goodcard',
                'route' => 'multisafepay/goodCard',
                'description' => $this->language->get('text_title_good_card'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_goodcard'),
                'image' => 'goodcard'
            ),
            array(
                'id' => 'GOOGLEPAY',
                'code' => 'googlepay',
                'route' => 'multisafepay/googlePay',
                'description' => $this->language->get('text_title_google_pay'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_googlepay'),
                'image' => 'googlepay'
            ),
            array(
                'id' => 'IN3',
                'code' => 'in3',
                'route' => 'multisafepay/in3',
                'description' => $this->language->get('text_title_in3'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_in3'),
                'image' => 'in3',
            ),
            array(
                'id' => 'IDEAL',
                'code' => 'ideal',
                'route' => 'multisafepay/ideal',
                'description' => $this->language->get('text_title_ideal'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_ideal'),
                'image' => 'ideal'
            ),
            array(
                'id' => 'IDEALQR',
                'code' => 'idealqr',
                'route' => 'multisafepay/idealQr',
                'description' => $this->language->get('text_title_ideal_qr'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_idealqr'),
                'image' => 'ideal-qr'
            ),
            array(
                'id' => 'KBC',
                'code' => 'kbc',
                'route' => 'multisafepay/kbc',
                'description' => $this->language->get('text_title_kbc'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_kbc'),
                'image' => 'kbc'
            ),
            array(
                'id' => 'KLARNA',
                'code' => 'klarna',
                'route' => 'multisafepay/klarna',
                'description' => $this->language->get('text_title_klarna'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_klarna'),
                'image' => 'klarna'
            ),
            array(
                'id' => 'MAESTRO',
                'code' => 'maestro',
                'route' => 'multisafepay/maestro',
                'description' => $this->language->get('text_title_maestro'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_maestro'),
                'image' => 'maestro'
            ),
            array(
                'id' => 'MASTERCARD',
                'code' => 'mastercard',
                'route' => 'multisafepay/mastercard',
                'description' => $this->language->get('text_title_mastercard'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_mastercard'),
                'image' => 'mastercard'
            ),
            array(
                'id' => 'MYBANK',
                'code' => 'mybank',
                'route' => 'multisafepay/mybank',
                'description' => $this->language->get('text_title_mybank'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_mybank'),
                'image' => 'mybank'
            ),
            array(
                'id' => 'NATNLETUIN',
                'code' => 'nationaletuinbon',
                'route' => 'multisafepay/nationaleTuinbon',
                'description' => $this->language->get('text_title_nationale_tuinbon'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_nationaletuinbon'),
                'image' => 'nationaletuinbon'
            ),
            array(
                'id' => 'PARFUMCADE',
                'code' => 'parfumcadeaukaart',
                'route' => 'multisafepay/parfumCadeaukaart',
                'description' => $this->language->get('text_title_parfum_cadeaukaart'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_parfumcadeaukaart'),
                'image' => 'parfumcadeaukaart'
            ),
            array(
                'id' => 'PAYAFTER',
                'code' => 'payafter',
                'route' => 'multisafepay/payAfterDelivery',
                'description' => $this->language->get('text_title_pay_after_delivery'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_payafter'),
                'image' => 'payafter'
            ),
            array(
                'id' => 'BNPL_INSTM',
                'code' => 'bnpl_instm',
                'route' => 'multisafepay/payAfterDeliveryInstallments',
                'description' => $this->language->get('text_title_pay_after_delivery_installments'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_payafter_installments'),
                'image' => 'payafterinstallments'
            ),
            array(
                'id' => 'PAYPAL',
                'code' => 'paypal',
                'route' => 'multisafepay/payPal',
                'description' => $this->language->get('text_title_paypal'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_paypal'),
                'image' => 'paypal'
            ),
            array(
                'id' => 'PODIUM',
                'code' => 'podium',
                'route' => 'multisafepay/podium',
                'description' => $this->language->get('text_title_podium'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_podium'),
                'image' => 'podium'
            ),
            array(
                'id' => 'PSAFECARD',
                'code' => 'paysafecard',
                'route' => 'multisafepay/paysafecard',
                'description' => $this->language->get('text_title_paysafecard'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_paysafecard'),
                'image' => 'paysafecard'
            ),
            array(
                'id' => 'SANTANDER',
                'code' => 'santander',
                'route' => 'multisafepay/betaalplan',
                'description' => $this->language->get('text_title_santander_betaalplan'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_santander'),
                'image' => 'betaalplan'
            ),
            array(
                'id' => 'DIRDEB',
                'code' => 'dirdeb',
                'route' => 'multisafepay/dirDeb',
                'description' => $this->language->get('text_title_sepa_direct_debit'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_dirdeb'),
                'image' => 'dirdeb'
            ),
            array(
                'id' => 'SPORTENFIT',
                'code' => 'sportfit',
                'route' => 'multisafepay/sportFit',
                'description' => $this->language->get('text_title_sport_fit'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_sportfit'),
                'image' => 'sportenfit'
            ),
            array(
                'id' => 'TRUSTLY',
                'code' => 'trustly',
                'route' => 'multisafepay/trustly',
                'description' => $this->language->get('text_title_trustly'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_trustly'),
                'image' => 'trustly'
            ),
            array(
                'id' => 'VISA',
                'code' => 'visa',
                'route' => 'multisafepay/visa',
                'description' => $this->language->get('text_title_visa'),
                'type' => 'gateway',
                'brief_description' => $this->language->get('text_brief_description_visa'),
                'image' => 'visa'
            ),
            array(
                'id' => 'VVVGIFTCRD',
                'code' => 'vvv',
                'route' => 'multisafepay/vvvGiftCard',
                'description' => $this->language->get('text_title_vvv_cadeaukaart'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_vvv'),
                'image' => 'vvv'
            ),
            array(
                'id' => 'WEBSHOPGIFTCARD',
                'code' => 'webshopgiftcard',
                'route' => 'multisafepay/webshopGiftCard',
                'description' => $this->language->get('text_title_webshop_giftcard'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_webshopgiftcard'),
                'image' => 'webshopgiftcard'
            ),
            array(
                'id' => 'WELLNESSGIFTCARD',
                'code' => 'wellnessgiftcard',
                'route' => 'multisafepay/wellnessGiftCard',
                'description' => $this->language->get('text_title_wellness_giftcard'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_wellnessgiftcard'),
                'image' => 'wellnessgiftcard'
            ),
            array(
                'id' => 'WIJNCADEAU',
                'code' => 'wijncadeau',
                'route' => 'multisafepay/wijnCadeau',
                'description' => $this->language->get('text_title_wijncadeau'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_wijncadeau'),
                'image' => 'wijncadeau'
            ),
            array(
                'id' => 'WINKELCHEQUE',
                'code' => 'winkelcheque',
                'route' => 'multisafepay/winkelCheque',
                'description' => $this->language->get('text_title_winkel_cheque'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_winkelcheque'),
                'image' => 'winkelcheque'
            ),
            array(
                'id' => 'YOURGIFT',
                'code' => 'yourgift',
                'route' => 'multisafepay/yourGift',
                'description' => $this->language->get('text_title_yourgift'),
                'type' => 'giftcard',
                'brief_description' => $this->language->get('text_brief_description_yourgift'),
                'image' => 'yourgift'
            ),
	        array(
		        'id' => 'GENERIC',
		        'code' => 'generic',
		        'route' => 'multisafepay/generic',
		        'description' => $this->language->get('text_title_generic'),
		        'type' => 'generic',
		        'brief_description' => $this->language->get('text_brief_description_generic'),
		        'image' => ''
	        )
        );

        return $gateways;
    }

    /**
     * Return gateway by gateway id
     *
     * @param string $gateway_id
     * @return mixed bool|array
     *
     */
    public function getGatewayById($gateway_id) {
        $gateways = $this->getGateways();
        $gateway_key = array_search($gateway_id, array_column($gateways, 'id'));

        if($gateway_key === false) {
            return false;
        }

        return $gateways[$gateway_key];

    }

    /**
     * Return gateway by gateway payment code
     *
     * @param string $gateway_id
     * @return mixed bool|array
     *
     */
    public function getGatewayByPaymentCode($payment_code) {
        $gateways = $this->getGateways();
        $gateway_key = array_search($payment_code, array_column($gateways, 'route'));

        if($gateway_key === false) {
            return false;
        }

        return $gateways[$gateway_key];

    }

	/**
	 * Return gateway by gateway type
	 *
	 * @param string $type
	 * @return mixed array
	 *
	 */
	public function getGatewayByType( $type ) {
		$gateways = $this->getGateways();
		$gateways_requested = array();
		foreach ($gateways as $key => $gateway) {
			if($gateway['type'] === $type) {
				$gateways_requested[] = $gateways[$key];
			}
		}
		return $gateways_requested;
	}

    /**
     * Include in the gateways array the configurable type of transaction
     *
     * @return array
     */
	public function getGatewaysWithRedirectSwitchProperty() {
        $gateways = $this->getGateways();
        foreach($gateways as $key => $gateway) {
            $gateways[$key]['redirect_switch'] = in_array($gateway['id'], $this->configurable_type_search, true);
        }
        return $gateways;
    }

    /**
     * Return ordered gateways
     *
     * @param int $store_id
     * @return array $gateways
     *
     */
    public function getOrderedGateways($store_id = 0) {
        $gateways = $this->getGatewaysWithRedirectSwitchProperty();
        $this->load->model('setting/setting');
        $settings = $this->model_setting_setting->getSetting($this->key_prefix . 'multisafepay', $store_id);
        $sort_order = array();
        foreach($gateways as $key => $gateway) {
            if(!isset($settings[$this->key_prefix . 'multisafepay_' . $gateway['code'] . '_sort_order'])) {
                $sort_order[$key] = 0;
            }

            if(isset($settings[$this->key_prefix . 'multisafepay_' . $gateway['code'] . '_sort_order'])) {
                $sort_order[$key] = $settings[$this->key_prefix . 'multisafepay_'. $gateway['code']. '_sort_order'];
            }
        }
        array_multisort($sort_order, SORT_ASC, $gateways);
        return $gateways;
    }

    /**
     * Return gateways from the API available for the merchant.
     *
     * @return array $gateways
     *
     */
    public function getAvailableGateways($enviroment = false, $api_key = false) {

        if(!$api_key) {
            return false;
        }

        require_once(DIR_SYSTEM . 'library/multisafepay/vendor/autoload.php');

        try {
            $sdk = new \MultiSafepay\Sdk($api_key, $enviroment);
        }
        catch (\MultiSafepay\Exception\InvalidApiKeyException $invalidApiKeyException ) {
            return false;
        }

        try {
            $gateway_manager = $sdk->getGatewayManager();
        }
        catch (\MultiSafepay\Exception\ApiException $apiException ) {
            return false;
        }

        try {
            $gateways = $gateway_manager->getGateways( true );
        }
        catch (\MultiSafepay\Exception\ApiException $apiException ) {
            return false;
        }

        if(!$gateways) {
            return false;
        }

        $available_gateways = array();

        // This methods has been hardcoded, since are availables but it doesn`t comes in the request.
        $available_gateways[] = 'MULTISAFEPAY';
        $available_gateways[] = 'CREDITCARD';

        foreach ($gateways as $gateway) {
            $available_gateways[] = $gateway->getId();
        }

        return $available_gateways;
    }

	/**
	 * Verify the signature of a POST notification
	 *
	 * @param $body
	 * @param $auth
	 * @param $api_key
	 *
	 * @return bool
	 */
    public function verifyNotification($body, $auth, $api_key) {
	    require_once(DIR_SYSTEM . 'library/multisafepay/vendor/autoload.php');
	    if (\MultiSafepay\Util\Notification::verifyNotification($body, $auth, $api_key)) {
	    	return true;
	    }
	    return false;
    }

	/**
	 * @param $body
	 *
	 * @return false|\MultiSafepay\Api\Transactions\TransactionResponse
	 */
    public function getTransactionFromPostNotification($body) {
	    require_once(DIR_SYSTEM . 'library/multisafepay/vendor/autoload.php');
    	try {
		    $transaction = new MultiSafepay\Api\Transactions\TransactionResponse(json_decode($body, true), $body);
		    return $transaction;
	    } catch (\MultiSafepay\Exception\ApiException $apiException) {
		    return false;
	    }
	}

    /**
     * @return false|\MultiSafepay\Api\ApiTokenManager
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getUserApiTokenManager() {
        $sdk = $this->getSdkObject($this->config->get('config_store_id'));
        try {
            return $sdk->getApiTokenManager();
        } catch (ClientExceptionInterface $client_exception) {
            $this->log->write($client_exception->getMessage());
        }
        return false;
    }

    /**
     * @return string
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * phpcs:disable ObjectCalisthenics.CodeAnalysis.OneObjectOperatorPerLine
     */
    public function getUserApiToken() {
        $token = '';
        $api_token_manager = $this->getUserApiTokenManager();
        if ($api_token_manager !== false) {
            try {
                $token = $api_token_manager->get()->getApiToken();
            } catch (ClientExceptionInterface $client_exception) {
                $this->log->write($client_exception->getMessage());
            }
        }
        return $token;
    }
}
