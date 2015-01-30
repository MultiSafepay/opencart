<?php

/**
 * Multisafepay Connect Plugin
 * Date 06-09-2012
 * @version 1.6.1
 * @package 1.5
 * @copyright 2012 MultiSafePay.
 */
class ControllerPaymentMultiSafePayAmex extends Controller {

    public function index() {
        $this->load->language('payment/multisafepay');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['button_back'] = $this->language->get('button_back');
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $this->load->library('encryption');
        $data['action'] = $this->url->link('payment/multisafepay/multisafepayProcess', '', 'SSL');
        $data['back'] = $this->url->link('checkout/payment', '', 'SSL');
        $data['gateway'] = 'AMEX';
        $data['MSP_CARTID'] = $this->session->data['order_id'];



        $data['order_id'] = $this->session->data['order_id'];

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/multisafepay_default.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/multisafepay_default.tpl', $data);
        } else {
            return $this->load->view('default/template/payment/multisafepay_default.tpl', $data);
        }
    }

}

?>