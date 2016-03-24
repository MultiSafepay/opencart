<?php

/**
 * Multisafepay Connect Plugin
 * Date 06-09-2012
 * @version 1.6.1
 * @package 1.5
 * @copyright 2012 MultiSafePay.
 */
class ControllerPaymentMultiSafePayIdeal extends Controller {

    public function index() {
	    $storeid = $this->config->get('config_store_id');
        $this->load->language('payment/multisafepay');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['button_back'] = $this->language->get('button_back');
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        //$this->load->library('encryption');
        $data['action'] = $this->url->link('payment/multisafepay/multisafepayProcess', '', 'SSL');
        $data['back'] = $this->url->link('checkout/checkout', '', 'SSL');
        $data['gateway'] = 'IDEAL';
        $data['MSP_CARTID'] = $this->session->data['order_id'];



        $data['order_id'] = $this->session->data['order_id'];



        //GET IDEAL ISSUERS
        require_once(dirname(__FILE__) . '/MultiSafepay.combined.php');
        $msp = new MultiSafepay();
        $msp->test = $this->config->get('multisafepay_environment_'.$storeid);
        $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id_'.$storeid);
        $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id_'.$storeid);
        $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code_'.$storeid);
        $iDealIssuers = $msp->getIdealIssuers();
		

        $idealselect = '<div id="issuerselect">';// . $this->language->get('text_select_bank');
        $idealselect .= '<select name="issuer">';
		$idealselect .= '<option value="">' . $this->language->get('text_select_bank'). '</option>';
        if ($this->config->get('multisafepay_environment_'.$storeid)) {
            foreach ($iDealIssuers['issuers'] as $issuer) {
                $idealselect .= '<option value="' . $issuer['code']['VALUE'] . '">' . $issuer['description']['VALUE'] . '</option>';
            }
        } else {
            foreach ($iDealIssuers['issuers']['issuer'] as $issuer) {
                $idealselect .= '<option value="' . $issuer['code']['VALUE'] . '">' . $issuer['description']['VALUE'] . '</option>';
            }
        }
        $idealselect .= '</select></div>';
        //END ideal issuers request data
        $data['ISSUER_SELECT'] = $idealselect;
        
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/multisafepay_ideal.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/payment/multisafepay_ideal.tpl', $data);
        } elseif(file_exists(DIR_TEMPLATE . 'default/template/payment/multisafepay_ideal.tpl')) {
            return $this->load->view('/payment/multisafepay_ideal.tpl', $data);
        }else{
            return $this->load->view('payment/multisafepay_ideal.tpl', $data);
        }
    }

}

?>