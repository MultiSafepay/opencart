<?php

use PHPUnit\Framework\TestCase;

class MultisafepayTestSuiteForOpenCart extends TestCase {

    private static $loaded = false;
    private static $has_orders = false;
    private static $created_orders = null;
    private static $created_customer_id = null;
    private static $created_geo_zone_id = null;
    private static $created_tax_rate_id = null;
    private static $created_tax_class_id = null;
    private static $previous_products_tax_class_id = null;
    private static $created_coupon_id = null;
    private static $shipping_method_flat_rate_tax_class_id = null;
    private static $is_admin = null;
    public static $registry;
    public static $helper;

    /**
     * Overrides setUp method in TestCase
     *
     */
    public function setUp() {
        if (!self::$loaded) {
            $application_config = getenv('TEST_CONFIG');
            $_SERVER['SERVER_PORT'] = 80;
            $_SERVER['SERVER_PROTOCOL'] = 'CLI';
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

            ob_start();
            $this->loadConfiguration();
            require_once(DIR_SYSTEM . 'startup.php');
            require(DIR_SYSTEM . 'framework.php');
            ob_end_clean();

            if(!defined('VERSION')) {
                define('VERSION', $this->getOcVersion());
            }

            self::$registry = $registry;
            self::$registry->set('controller', $route);
            self::$loaded = true;
            self::$helper = new Helper(self::$registry);
            self::$has_orders = self::$helper->hasOrders();
            if(!self::$has_orders) {
                $this->generateOrdersInformation();
            }

        }
    }

    /**
     * Overrides tearDown method in TestCase
     *
     */
    public function tearDown() {

        if(self::$created_orders) {
            foreach (self::$created_orders as $order_id) {
                self::$helper->deleteOrder($order_id);
            }
        }

        if(self::$created_customer_id) {
            self::$helper->deleteCustomer(self::$created_customer_id);
        }

        if(self::$created_geo_zone_id) {
            self::$helper->deleteGeoZone(self::$created_geo_zone_id);
        }
        if(self::$created_tax_rate_id) {
            self::$helper->deleteTaxRate(self::$created_tax_rate_id);
        }
        if(self::$created_tax_class_id) {
            self::$helper->deleteTaxClass(self::$created_tax_class_id);
        }

        if(self::$previous_products_tax_class_id) {
            foreach (self::$previous_products_tax_class_id as $product_id => $tax_class_id) {
                self::$helper->editProductTaxClassId($product_id, $tax_class_id);
            }
        }

        if(self::$shipping_method_flat_rate_tax_class_id) {
            self::$helper->editTaxClassFromFlatShippingMethod(self::$shipping_method_flat_rate_tax_class_id);
        }

        if(self::$created_coupon_id) {
            self::$helper->deleteCoupon(self::$created_coupon_id);
        }

        if($this->cart->getProducts()) {
            $this->cart->clear();
        }

        self::$is_admin = null;
        self::$loaded = false;
        self::$has_orders = false;
        self::$created_orders = null;
        self::$created_customer_id = null;
        self::$created_geo_zone_id = null;
        self::$created_tax_rate_id = null;
        self::$created_tax_class_id = null;
        self::$previous_products_tax_class_id = null;
        self::$created_coupon_id = null;
    }

    /**
     * Generate OpenCart geo zones and setup tax rates and tax classes
     * needed to run some tests related with transactions.
     *
     */
    public function generateGeoZoneAndTaxes() {
        // Add GeoZone.
        $fixture_geo_zones = new GeoZones();
        $data_geo_zone = $fixture_geo_zones->getGeoZones();
        self::$created_geo_zone_id = self::$helper->addGeoZone($data_geo_zone);

        // Add TaxRate.
        $fixture_tax_rates = new TaxRates();
        $data_tax_rate = $fixture_tax_rates->getTaxRates(self::$created_geo_zone_id);
        self::$created_tax_rate_id = self::$helper->addTaxRate($data_tax_rate);

        // Add TaxClass.
        $fixture_tax_classes = new TaxClasses();
        $data_tax_class = $fixture_tax_classes->getTaxClasses(self::$created_tax_rate_id);
        self::$created_tax_class_id = self::$helper->addTaxClass($data_tax_class);

        // Assign Tax Class to products including in the tests.
        $products_ids = array('30', '49', '28', '29', '41', '42', '40', '43');
        self::$previous_products_tax_class_id = array();
        foreach ($products_ids as $product_id) {
            self::$previous_products_tax_class_id[$product_id] = self::$helper->getTaxClassIdByProduct($product_id);
        }
        foreach ($products_ids as $product_id) {
            self::$helper->editProductTaxClassId($product_id, self::$created_tax_class_id);
        }
        $this->unSetTaxRateOfShippingMethod();

    }

    public function unSetTaxRateOfShippingMethod() {
        self::$shipping_method_flat_rate_tax_class_id = self::$helper->getTaxClassFromFlatShippingMethod();
        self::$helper->editTaxClassFromFlatShippingMethod(0);
    }

    /**
     * Check if dependencies are loaded; also load OpenCart`s config files.
     *
     */
    public static function loadConfiguration() {
        // Load OpenCart config files.
        $config_path = getenv('OC_ROOT') . (self::isAdmin() === false ? '' : 'admin/') . 'config.php';
        if (file_exists($config_path)) {
            require_once($config_path);
        } else {
            echo "\033[0;31mIt seems OpenCart`s config files are missing\033[0;m" . PHP_EOL;
            exit( 1 );
        }
    }

    /**
     * Check if test is on admin side checking if class names contains AdminTest at the end.
     *
     */
    public static function isAdmin() {
        if (is_null(self::$is_admin)) {
            self::$is_admin = is_int(strpos(get_called_class(), "Admin"));
        }
        return self::$is_admin;
    }

    /**
     * Magic method that returns any object used in OpenCart from registry object
     * when has not been found inside this class
     *
     * @param string $name
     * @return object
     */
    public function __get($name)  {
        return self::$registry->get($name);
    }

    /**
     * Register Multisafepay library file as object
     *
     */
    public function multisafepay(){
        return self::$registry->set('multisafepay', new Multisafepay($this->registry));
    }

    /**
     * Register a customer to execute test with him
     *
     */
    public function generateCustomerInformation() {
        $customerFixture = new Customers(0, self::$created_customer_id);
        $customer_data = $customerFixture->getCustomerAccountData();
        self::$created_customer_id = self::$helper->addCustomer($customer_data);
        self::$helper->addReward(self::$created_customer_id, 'Reward PHPUnit', 100, 0);
    }

    /**
     * Creates orders information if there are not in database
     *
     */
    public function generateOrdersInformation() {
        $number_of_orders = 1;
        self::$created_orders = array();
        for ($i = 1; $i <= $number_of_orders; $i++) {
            $data = $this->generateOrderData();
            self::$created_orders[] = self::$helper->addOrder($data);
        }
    }

    /**
     * Generates order data array.
     *
     */
    public function generateOrderData() {
        $orderFixture = new Orders(0,0);
        $order_information  = $orderFixture->getOrderInformation();
        $customerFixture = new Customers(0, 1);
        $customer_information  = $customerFixture->getCustomer();
        $order = array_merge($order_information, $customer_information);
        return $order;
    }

    /**
     * Return OpenCart admin username defined in phpunit.xml file
     *
     */
    public function getAdminUserName() {
        return getenv('OC_ADMIN_USERNAME');
    }

    /**
     * Return OpenCart admin password defined in phpunit.xml file
     *
     */
    public function getAdminPassword() {
        return getenv('OC_ADMIN_PASSWORD');
    }

    /**
     * Return OpenCart OC VERSION  defined in phpunit.xml file
     *
     */
    public function getOcVersion() {
        return getenv('OC_VERSION');
    }

    /**
     * Login user if is in admin or catalog side, then is able to use dispatchAction function
     *
     */
    public function login($username, $password, $override = false)  {
        $logged = false;
        if (!self::isAdmin() && ($logged = $this->customer->login($username, $password, $override))) {
            $this->session->data['customer_id'] = $this->customer->getId();
        } elseif ($logged = $this->user->login($username, $password)) {
            $this->session->data['user_id'] = $this->user->getId();
            $this->session->data['user_token'] = bin2hex(openssl_random_pseudo_bytes(16));
        }
        return $logged;
    }

    /**
     * Logout user
     *
     */
    public function logout() {
        if (self::isAdmin()) {
            $this->user->logout();
            unset($this->session->data['user_id']);
            unset($this->session->data['user_token']);
        } else {
            $this->customer->logout();
            unset($this->session->data['customer_id']);
        }
    }

    /**
     * With this function is possible to send request to any OpenCart controller method
     *
     */
    public function dispatchAction($route, $request_method = 'GET', $data = array())  {
        if ($request_method != 'GET' && $request_method != 'POST') {
            $request_method = 'GET';
        }

        foreach ($data as $key => $value) {
            $this->request->{strtolower($request_method)}[$key] = $value;
        }

        if (self::isAdmin() && isset($this->session->data['user_token'])) {
            $this->request->get['user_token'] = $this->session->data['user_token'];
        }

        $this->request->cookie['language'] = getenv('LANGUAGE_CODE');
        $this->request->cookie['currency'] = getenv('CURRENCY_CODE');

        $this->request->get['route'] = $route;
        $this->request->server['REQUEST_METHOD'] = $request_method;
        $this->controller->dispatch(new \Action($route), new \Action($this->config->get('action_error')));

        return $this->response;
    }

    /**
     * Return the necessary information in session to call payment methods forms
     *
     */
    public function getSessionData() {
        $this->generateCustomerInformation();
        $sessionFixture = new SessionData(self::$created_customer_id);
        $session_information = $sessionFixture->getSessionInformation();
        $session_information['payment_methods'] = array();
        $gateways = $this->multisafepay->getGateways();
        foreach ($gateways as $gateway) {
            $session_information['payment_methods'][$gateway['route']] = array(
                'code' => $gateway['route'],
                'title' => $gateway['description'],
                'terms' => '',
                'sort_order' => ''
            );
        }
        return $session_information;
    }

    /**
     * Return stub of the MultiSafepay library object
     *
     */
    public function getMockMultiSafepayGetOrderRequestObject($order_id, $order_key, $customer_key) {
        $orderFixture = new Orders($order_id, $order_key);
        $order_information = $orderFixture->getOrderInformation();
        $customerFixture = new Customers($customer_key, 1);
        $customer_information  = $customerFixture->getCustomer();
        $order_information = array_merge($order_information, $customer_information);

        // Set Address for taxes.
        $this->tax->setPaymentAddress($customer_information['payment_country_id'], $customer_information['payment_zone_id']);
        $this->tax->setShippingAddress($customer_information['shipping_country_id'], $customer_information['shipping_zone_id']);
        $this->tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));

        $api_key = getenv('API_KEY');
        $sdk = self::$helper->getSdkObject(false, $api_key);

        $mock = $this->getMockBuilder(Multisafepay::class)
            ->setConstructorArgs(array(self::$registry))
            ->setMethods(array('getSdkObject', 'getOrderInfo', 'getOrderTotals', 'getOrderProducts', 'getRewardPointsDiscountByProduct'))
            ->getMock();

        $mock->method('getSdkObject')
            ->withAnyParameters()
            ->willReturn($sdk);

        $mock->method('getOrderInfo')
            ->withAnyParameters()
            ->willReturn($order_information);

        $mock->method('getOrderTotals')
            ->withAnyParameters()
            ->willReturn($orderFixture->getTotals());

        $mock->method('getOrderProducts')
            ->withAnyParameters()
            ->willReturn($orderFixture->getProducts());

        $mock->method('getRewardPointsDiscountByProduct')
            ->withAnyParameters()
            ->willReturn(array('40' => array('discount_per_product' => '33.666666666667', 'discount_per_products' => '67.333333333333')));

        return $mock;
    }

    /**
     * Set the necessary information in session to call payment methods forms
     *
     */
    public function buildSessionPaymentData($payment_method_code, $payment_method_title) {
        $session_data = $this->getSessionData();
        foreach ($session_data as $key => $value) {
            $this->session->data[$key] = $value;
        }
        $this->session->data['payment_method'] = array(
            'code' => $payment_method_code,
            'title' => $payment_method_title,
            'terms' => '',
            'sort_order' => 1
        );
        $post_data = array(
            'payment_method' => $payment_method_code,
            'comment' => '',
            'agree' => 1
        );
        $this->dispatchAction('checkout/payment_method/save', 'POST', $post_data);
        return $this->session->data;
    }

    /**
     * Return fixture of customer payment information
     *
     */
    public function getCustomerPaymentInformation() {
        $customerFixture = new Customers(0, 1);
        $customer_payment_information  = $customerFixture->getCustomerPayment();
        return $customer_payment_information;
    }

    /**
     * Return fixture of customer information
     *
     */
    public function getCustomerAccountInformation() {
        $customerFixture = new Customers(0, 1);
        $customer_information  = $customerFixture->getCustomerAccountData();
        return $customer_information;
    }

    /**
     * Generates a coupon into the database
     * Will be delete in tearDown
     *
     */
    public function generateCoupons() {
        $couponFixture = new Coupons();
        $data_coupon = $couponFixture->getCoupon();
        self::$created_coupon_id =  self::$helper->addCoupon($data_coupon);
    }

    /**
     * Get Random Order ID from database, and if no orders are registered in database
     * creates a few ones.
     *
     */
    public function getRandomOrderId() {
        $order_id = self::$helper->getRandomOrderId();
        return $order_id;
    }

}
