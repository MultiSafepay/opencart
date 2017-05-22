<?php

/**
 * Multisafepay Connect Plugin
 * Date 06-09-2012
 * @version 1.6.1
 * @package 1.5
 * @copyright 2012 MultiSafePay.
 */
class ControllerExtensionPaymentMultiSafePayBoekenbon extends Controller
{

    public function index()
    {
        $this->load->language('extension/payment/multisafepay');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['button_back'] = $this->language->get('button_back');
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        //$this->load->library('encryption');
        $data['action'] = $this->url->link('extension/payment/multisafepay/multisafepayProcess', '', 'SSL');
        $data['back'] = $this->url->link('checkout/checkout', '', 'SSL');
        $data['gateway'] = 'BOEKENBON';
        $data['MSP_CARTID'] = $this->session->data['order_id'];



        $data['order_id'] = $this->session->data['order_id'];

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/multisafepay_default.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/extension/payment/multisafepay_default.tpl', $data);
        } elseif (file_exists(DIR_TEMPLATE . 'default/template/extension/payment/multisafepay_default.tpl') && VERSION < '2.2.0.0') {
            return $this->load->view('default/template/extension/payment/multisafepay_default.tpl', $data);
        } else {
            return $this->load->view('extension/payment/multisafepay_default', $data);
        }
    }

}

?>