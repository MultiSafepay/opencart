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


use PHPUnit\Framework\TestCase;

class OpenCartMultiSafepayTest extends TestCase {

    private static $loaded = false;
    private static $has_orders = false;
    private static $created_orders = null;
    private static $is_admin = null;
    public static $registry;


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

            self::$registry = $registry;
            self::$registry->set('controller', $route);
            self::$loaded = true;
            self::$has_orders = $this->hasOrders();
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
            $this->load->model('checkout/order');
             foreach (self::$created_orders as $order_id) {
                 $this->model_checkout_order->deleteOrder($order_id);
             }
        }
        if($this->cart->getProducts()) {
            $this->cart->clear();
        }
        self::$is_admin = null;
        self::$loaded = false;
        self::$has_orders = false;
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
            self::$is_admin = is_int(strpos(get_called_class(), "AdminTest"));
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
    public function multisafepay() {
        return self::$registry->set('multisafepay', new Multisafepay($this->registry));
    }

    /**
     * Get Random Order ID from database, and if no orders are registered in database
     * creates a few ones.
     *
     */
    public function getRandomOrderId() {
        $sql = "SELECT order_id FROM `" . DB_PREFIX . "order` ORDER BY RAND() LIMIT 1";
        $query = $this->db->query($sql);
        if($query->rows) {
            return $query->row['order_id'];
        }
    }

    /**
     * Check if there are orders in database to make tests over them
     *
     */
    public function hasOrders() {
        $sql = "SELECT order_id FROM `" . DB_PREFIX . "order` ORDER BY RAND() LIMIT 1";
        $query = $this->db->query($sql);
        if(!$query->rows) {
           return false;
        }
        if($query->rows) {
            return true;
        }
    }

    /**
     * Creates orders information if there are not in database
     *
     */
    public function generateOrdersInformation() {
        $number_of_orders = 2;
        $this->load->model('checkout/order');
        self::$created_orders = array();
        for ($i = 1; $i <= $number_of_orders; $i++) {
            $data = $this->generateOrderData();
            self::$created_orders[] = $this->model_checkout_order->addOrder($data);
        }
    }

    /**
     * Generates order data array.
     *
     */
    public function generateOrderData() {
        $orderFixture = new OrdersTest(1);
        $order_information  = $orderFixture->getOrderInformation();
        $customerFixture = new CustomersTest(0);
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
        $sessionFixture = new SessionTest();
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
        $orderFixture = new OrdersTest($order_id, $order_key);
        $order_information = $orderFixture->getOrderInformation();
        $customerFixture = new CustomersTest($customer_key);
        $customer_information  = $customerFixture->getCustomer();
        $order_information = array_merge($order_information, $customer_information);

        // Set Address for taxes.
        $this->tax->setPaymentAddress($customer_information['payment_country_id'], $customer_information['payment_zone_id']);
        $this->tax->setShippingAddress($customer_information['shipping_country_id'], $customer_information['shipping_zone_id']);
        $this->tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));

        $mock = $this->getMockBuilder(Multisafepay::class)
            ->setConstructorArgs(array(self::$registry))
            ->setMethods(array('getOrderInfo', 'getOrderTotals', 'getOrderProducts'))
            ->getMock();

        $mock->method('getOrderInfo')
            ->withAnyParameters()
            ->willReturn($order_information);

        $mock->method('getOrderTotals')
            ->withAnyParameters()
            ->willReturn($orderFixture->getTotals());

        $mock->method('getOrderProducts')
            ->withAnyParameters()
            ->willReturn($orderFixture->getProducts());

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
        $customerFixture = new CustomersTest(0);
        $customer_payment_information  = $customerFixture->getCustomerPayment();
        return $customer_payment_information;

    }
}