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

class Multisafepayevents {

    const ROUTE = 'extension/payment/multisafepay';

    public function __construct($registry) {
        $this->registry = $registry;
    }

    /**
     * Magic method that returns any object used in OpenCart from registry object
     * when has not been found inside this class
     *
     * @param string $name
     * @return object
     */
    public function __get($name) {
        return $this->registry->get($name);
    }

    /**
     * Trigger that is called after catalog/controller/checkout/payment/method
     * using OpenCart events system and overwrites it
     */
    public function catalogControllerCheckoutPaymentMethodAfter() {

        if (!isset($this->session->data['payment_address'])) {
            $data = $this->baseCatalogControllerCheckoutPaymentMethodAfter();
            $this->response->setOutput($this->load->view('checkout/payment_method', $data));
        }

        $totals = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;

        $total_data = array(
            'totals' => &$totals,
            'taxes'  => &$taxes,
            'total'  => &$total
        );

        $this->load->model('setting/extension');

        $sort_order = array();

        $results = $this->model_setting_extension->getExtensions('total');

        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            if ($this->config->get('total_' . $result['code'] . '_status')) {
                $this->load->model('extension/total/' . $result['code']);
                $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
            }
        }

        $method_data = array();
        $this->load->model('setting/extension');
        $results = $this->model_setting_extension->getExtensions('payment');
        $recurring = $this->cart->hasRecurringProducts();

        foreach ($results as $result) {
            if ($this->config->get('payment_' . $result['code'] . '_status')) {
                $this->load->model('extension/payment/' . $result['code']);
                $method = $this->{'model_extension_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);
                $method_data = $this->extractPaymentMethodsArray($method, $result, $total, $recurring, $method_data);
            }
        }

        $method_data = $this->sortMethods($method_data);
        $this->session->data['payment_methods'] = $method_data;
        $data = $this->baseCatalogControllerCheckoutPaymentMethodAfter();
        $this->response->setOutput($this->load->view('checkout/payment_method', $data));
    }

    /**
     * Return payment methods ordered according with natural language criteria
     *
     * @param array $method_data
     * @return array
     */
    private function sortMethods($method_data) {
        $sort_order = array();
        foreach($method_data as $key => $value) {
            if(strpos($key, 'multisafepay') !== false && $value['sort_order']) {
                $sort_order[$key] = $this->config->get('payment_multisafepay_sort_order') . '.' . $value['sort_order'];
            }
            if(strpos($key, 'multisafepay') !== false && !$value['sort_order']) {
                $sort_order[$key] = $this->config->get('payment_multisafepay_sort_order');
            }
            if(strpos($key, 'multisafepay') === false) {
                $sort_order[$key] = $value['sort_order'];
            }
        }
        array_multisort($sort_order, SORT_ASC, SORT_NATURAL, $method_data);
        return $method_data;
    }

    /**
     * Extract payment methods from a loop for each payment extension
     *
     * @param array $method
     * @param array $extension
     * @param double $total
     * @param bool $recurring
     * @param array $method_data
     * @return array
     */
    private function extractPaymentMethodsArray($method, $extension, $total, $recurring = false, $method_data = array()) {
        if ($method && $extension['code'] === 'multisafepay' && !$recurring) {
            $methods = $this->{'model_extension_payment_'.$extension['code']}->getMethods($this->session->data['payment_address'],
                $total);
            foreach ($methods as $msp_method) {
                $method_data[$msp_method['code']] = $msp_method;
            }
        }
        if ($method && $extension['code'] !== 'multisafepay' && $recurring) {
            if (property_exists($this->{'model_extension_payment_'.$extension['code']},
                    'recurringPayments') && $this->{'model_extension_payment_'.$extension['code']}->recurringPayments()) {
                $method_data[$method['code']] = $method;
            }
        }
        if ($method && $extension['code'] !== 'multisafepay' && !$recurring) {
            $method_data[$method['code']] = $method;
        }
        return $method_data;
    }

    /**
     * Data to be include in catalogControllerCheckoutPaymentMethodAfter function
     * and be able to return early
     *
     * @return array
     */
    private function baseCatalogControllerCheckoutPaymentMethodAfter() {

        $this->load->language('checkout/checkout');

        if (empty($this->session->data['payment_methods'])) {
            $data['error_warning'] = sprintf($this->language->get('error_no_payment'), $this->url->link('information/contact', '', true));
        }
        if (!empty($this->session->data['payment_methods'])) {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['payment_methods'])) {
            $data['payment_methods'] = $this->session->data['payment_methods'];
        }
        if (!isset($this->session->data['payment_methods'])) {
            $data['payment_methods'] = array();
        }

        if (isset($this->session->data['payment_method']['code'])) {
            $data['code'] = $this->session->data['payment_method']['code'];
        }
        if (!isset($this->session->data['payment_method']['code'])) {
            $data['code'] = '';
        }

        if (isset($this->session->data['comment'])) {
            $data['comment'] = $this->session->data['comment'];
        }
        if (!isset($this->session->data['comment'])) {
            $data['comment'] = '';
        }

        $data['scripts'] = $this->document->getScripts();

        if ($this->config->get('config_checkout_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

            if ($information_info) {
                $data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_checkout_id'), true), $information_info['title'], $information_info['title']);
            }
            if (!$information_info) {
                $data['text_agree'] = '';
            }
        }

        if (!$this->config->get('config_checkout_id')) {
            $data['text_agree'] = '';
        }

        if (isset($this->session->data['agree'])) {
            $data['agree'] = $this->session->data['agree'];
        }
        if (!isset($this->session->data['agree'])) {
            $data['agree'] = '';
        }

        return $data;
    }

    /**
     * Extract payment methods from a loop for MultiSafepay payment extension
     *
     * @param array $extension
     * @param array $address
     * @param double $total
     * @param array $payment_methods
     * @return array
     */
    private function extractPaymentMethodsJsonFromMultisafePay($extension, $address, $total, $payment_methods) {
        $this->load->model('extension/payment/' . $extension['code']);
        $methods = $this->{'model_extension_payment_' . $extension['code']}->getMethods($address, $total);
        if ($methods) {
            foreach ($methods as $msp_method) {
                $payment_methods[$msp_method['code']] = $msp_method;
            }
        }
        return $payment_methods;
    }

    /**
     * Extract payment methods from a loop for OpenCart payment extension
     *
     * @param array $extension
     * @param array $address
     * @param double $total
     * @param array $payment_methods
     * @param bool $recurring
     * @return array
     */
    private function extractPaymentMethodsJsonFromOpenCart($extension, $address, $total, $payment_methods, $recurring) {
        $this->load->model('extension/payment/' . $extension['code']);
        $method = $this->{'model_extension_payment_' . $extension['code']}->getMethod($address, $total);
        if ($method && $recurring) {
            if (property_exists($this->{'model_extension_payment_'.$extension['code']},
                    'recurringPayments') && $this->{'model_extension_payment_'.$extension['code']}->recurringPayments()) {
                $payment_methods[$extension['code']] = $method;
            }
        }

        if ($method && !$recurring) {
            $payment_methods[$extension['code']] = $method;
        }

        return $payment_methods;
    }

    /**
     * Trigger that is called after catalog/controller/api/payment/methods
     * using OpenCart events system and overwrites it
     */
    public function catalogControllerApiPaymentMethodsAfter() {
        $this->load->language('api/payment');

        unset($this->session->data['payment_methods']);
        unset($this->session->data['payment_method']);

        $json = array();

        if (!isset($this->session->data['api_id'])) {
            $json['error'] = $this->language->get('error_permission');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        }

        if (!isset($this->session->data['payment_address'])) {
            $json['error'] = $this->language->get('error_address');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        }

        $totals = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;

        $total_data = array(
            'totals' => &$totals,
            'taxes'  => &$taxes,
            'total'  => &$total
        );

        $this->load->model('setting/extension');

        $sort_order = array();

        $results = $this->model_setting_extension->getExtensions('total');

        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            if ($this->config->get('total_' . $result['code'] . '_status')) {
                $this->load->model('extension/total/' . $result['code']);
                $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
            }
        }

        $json['payment_methods'] = array();

        $this->load->model('setting/extension');

        $results = $this->model_setting_extension->getExtensions('payment');

        $recurring = $this->cart->hasRecurringProducts();

        foreach ($results as $result) {

            if ($this->config->get('payment_' . $result['code'] . '_status') && $result['code'] !== 'multisafepay') {
                $json['payment_methods'] = $this->extractPaymentMethodsJsonFromOpenCart($result, $this->session->data['payment_address'], $total, $json['payment_methods'], $recurring);
            }

            if ($this->config->get('payment_' . $result['code'] . '_status') && $result['code'] === 'multisafepay') {
                $json['payment_methods'] = $this->extractPaymentMethodsJsonFromMultisafePay($result, $this->session->data['payment_address'], $total, $json['payment_methods']);
            }

        }

        $sort_order = array();

        foreach ($json['payment_methods'] as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $json['payment_methods']);

        if ($json['payment_methods']) {
            $this->session->data['payment_methods'] = $json['payment_methods'];
        }
        if (!$json['payment_methods']) {
            $json['error'] = $this->language->get('error_no_payment');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     *
     * Trigger that is called after admin/model/sale/order/createInvoiceNo
     * using OpenCart events system and overwrites it
     *
     * @param string $route
     * @param array $args
     * @param string $output
     *
     */
    public function adminModelSaleOrderCreateInvoiceNoAfter(&$route, &$args, &$output) {
        if ($output && $args) {
            $this->load->model('sale/order');
            $order_info = $this->model_sale_order->getOrder($args[0]);

            if(strpos($order_info['payment_code'], 'multisafepay') !== false) {
                $order_id = $order_info['order_id'];
                $invoice_id = $output;
                $this->registry->set('multisafepay', new Multisafepay($this->registry));
                $sdk = $this->multisafepay->getSdkObject();
                $transaction_manager = $sdk->getTransactionManager();
                $update_order = new MultiSafepay\Api\Transactions\UpdateRequest();
                $update_order->addData(array('invoice_id' => $invoice_id));
                $transaction_manager->update($order_id, $update_order);
            }

            if( (strpos($order_info['payment_code'], 'multisafepay') !== false) && $this->config->get('payment_multisafepay_debug_mode')) {
                $this->log->write('OpenCart Event to send invoice ID to MSP, for order ID '.$order_id);
            }

        }
    }

    /**
     *
     * Trigger that is called before admin/view/sale/order_info
     * using OpenCart events system and overwrites it
     *
     * @param string $route
     * @param array $args
     *
     */
    public function adminViewSaleOrderInfoBefore(&$route, &$args) {

        unset($args['tabs']);
        $args['tabs'] = array();

        $this->load->model('sale/order');
        $order_info = $this->model_sale_order->getOrder($args['order_id']);

        $this->registry->set('multisafepay', new Multisafepay($this->registry));
        $msp_order = $this->multisafepay->getOrderObject($args['order_id']);

        if($msp_order && $msp_order->getTransactionId()) {

            if( (strpos($order_info['payment_code'], 'multisafepay') !== false) && $this->user->hasPermission('access', self::ROUTE) ) {

                $this->load->language(self::ROUTE);

                $content = $this->load->controller(self::ROUTE . '/order');

                $args['tabs'][] = array(
                    'code'    => 'multisafepay-order',
                    'title'   => $this->language->get('tab_order'),
                    'content' => $content
                );

            }
        }

        if( (strpos($order_info['payment_code'], 'multisafepay') === false) && $this->user->hasPermission('access', 'extension/payment/' . $order_info['payment_code']) ) {

            if (is_file(DIR_CATALOG . 'controller/extension/payment/' . $order_info['payment_code'] . '.php')) {
                $content = $this->load->controller('extension/payment/' . $order_info['payment_code'] . '/order');
            }
            if (!is_file(DIR_CATALOG . 'controller/extension/payment/' . $order_info['payment_code'] . '.php')) {
                $content = '';
            }
            if ($content) {
                $this->load->language('extension/payment/' . $order_info['payment_code']);
                $args['tabs'][] = array(
                    'code'    => $order_info['payment_code'],
                    'title'   => $this->language->get('text_title'),
                    'content' => $content
                );
            }
        }

        $this->load->model('setting/extension');
        $extensions = $this->model_setting_extension->getInstalled('fraud');
        foreach ($extensions as $extension) {
            if ($this->config->get('fraud_' . $extension . '_status') && $this->load->controller('extension/fraud/' . $extension . '/order')) {
                $this->load->language('extension/fraud/' . $extension, 'extension');
                $content = $this->load->controller('extension/fraud/' . $extension . '/order');
                $args['tabs'][] = array(
                    'code'    => $extension,
                    'title'   => $this->language->get('extension')->get('heading_title'),
                    'content' => $content
                );
            }
        }
    }

    /**
     *
     * Trigger that is called before catalog/mail/order/before
     * using OpenCart events system and overwrites it
     *
     * @param string $route
     * @param array $args
     * @return mixed bool|array
     */
    public function catalogViewMailOrderAddBefore(&$route, &$args) {

        // Check if we have the needed params and the setting to send payment link is enable
        if (!isset($args['order_id']) || !isset($args['order_status']) || !$this->config->get('payment_multisafepay_generate_payment_links_status')) {
            return false;
        }

        $this->load->language(self::ROUTE);
        $this->load->model('checkout/order');
        $this->load->model(self::ROUTE);
        $this->load->model('localisation/order_status');

        // Check if the order has been paid with some MultiSafepay method
        $order_id = $args['order_id'];
        $order_info = $this->model_checkout_order->getOrder($order_id);

        if ((strpos($order_info['payment_code'], 'multisafepay') === false)) {
            return false;
        }

        // Check if the status is the one
        $order_statuses = $this->model_localisation_order_status->getOrderStatuses();
        $payment_request_order_status_id = $this->config->get('payment_multisafepay_order_status_id_initialize_payment_request');
        $order_status_key = array_search($payment_request_order_status_id, array_column($order_statuses, 'order_status_id'));

        if($args['order_status'] != $order_statuses[$order_status_key]['name']) {
            return false;
        }

        // Generate Payment Link. Order from admin. Include payment link in the order email.
        $this->registry->set('multisafepay', new Multisafepay($this->registry));
        $gateways = $this->multisafepay->getGateways();
        $order_payment_method = $args['payment_method'];
        $gateway_key = array_search($order_payment_method, array_column($gateways, 'description'));

        $gateway = (($gateway_key) ? $gateways[$gateway_key]['id'] : '');
        $order_request = array(
            'order_id' => $order_id,
            'action'   => $this->url->link(self::ROUTE . '/confirm', '', true),
            'back'     => $this->url->link('checkout/checkout', '', true),
            'test_mode'=> (($this->config->get('payment_multisafepay_environment')) ? true : false),
            'type'     => 'paymentlink',
            'gateway'  => $gateway
        );

        $msp_order = $this->multisafepay->getOrderRequestObject($order_request);
        $order_request = $this->multisafepay->processOrderRequestObject($msp_order);

        if ($order_request->getPaymentLink()) {

            $payment_link = $order_request->getPaymentLink();

            if ($this->config->get('payment_multisafepay_debug_mode')) {
                $this->log->write('Start transaction in MSP for order ID ' . $order_id . ' on ' . date($this->language->get('datetime_format')));
            }

            $args['text_instruction'] = $this->language->get('text_instructions');
            $args['comment'] = sprintf($this->language->get('text_payment_link'), $payment_link, $payment_link);
            $order_history_comment = sprintf($this->language->get('text_payment_link_admin_order_history'), $payment_link, $payment_link);
            $this->model_extension_payment_multisafepay->addPaymentLinkToOrderHistory($order_id, $payment_request_order_status_id, $order_history_comment, false);

            return $args;

        }

        return false;

    }

}
