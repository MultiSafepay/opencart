<?php

/**
 * Multisafepay Payment Integration
 * Date 15-07-2011
 * @version 1.5
 * @package 1.5
 * @copyright 2011 MultiSafePay.
 */
class ControllerPaymentMultiSafePayPaypal extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('payment/multisafepay');
        $this->load->language('payment/multisafepay_paypal');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('multisafepay_paypal', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }
        
        $this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();


        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['action'] = $this->setup_link('payment/multisafepay_paypal');
        $data['cancel'] = $this->setup_link('extension/payment');
        $data['text_set_order_status'] = $this->language->get('text_set_order_status');
        $data['heading_title'] = $this->language->get('heading_title');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        // Geo Zone
        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
        

        $data['text_min_amount'] = $this->language->get('text_min_amount');
        $data['text_max_amount'] = $this->language->get('text_max_amount');
        
        if (isset($this->request->post['multisafepay_paypal_geo_zone_id_0'])) {
            $data['multisafepay_paypal_geo_zone_id'] = $this->request->post['multisafepay_paypal_geo_zone_id_0'];
        } else {
            $data['multisafepay_paypal_geo_zone_id'] = $this->config->get('multisafepay_paypal_geo_zone_id_0');
        }
        if (isset($this->request->post['multisafepay_paypal_max_amount_0'])) {
            $data['multisafepay_paypal_max_amount'] = $this->request->post['multisafepay_paypal_max_amount_0'];
        } else {
            $data['multisafepay_paypal_max_amount'] = $this->config->get('multisafepay_paypal_max_amount_0');
        }
        if (isset($this->request->post['multisafepay_paypal_min_amount_0'])) {
            $data['multisafepay_paypal_min_amount'] = $this->request->post['multisafepay_paypal_min_amount_0'];
        } else {
            $data['multisafepay_paypal_min_amount'] = $this->config->get('multisafepay_paypal_min_amount_0');
        }
        if (isset($this->request->post['multisafepay_paypal_status'])) {
            $data['multisafepay_paypal_status'] = $this->request->post['multisafepay_paypal_status'];
        } else {
            $data['multisafepay_paypal_status'] = $this->config->get('multisafepay_paypal_status');
        }
        if (isset($this->request->post['multisafepay_paypal_sort_order_0'])) {
            $data['multisafepay_paypal_sort_order'] = $this->request->post['multisafepay_paypal_sort_order_0'];
        } else {
            $data['multisafepay_paypal_sort_order'] = $this->config->get('multisafepay_paypal_sort_order_0');
        }
        
        
        foreach($this->model_setting_store->getStores() as $store ){
	        if (isset($this->request->post['multisafepay_paypal_geo_zone_id_'.$store['store_id'].''])) {
	            $data['multisafepay_paypal_geo_zone_id_'.$store['store_id'].''] = $this->request->post['multisafepay_paypal_geo_zone_id_'.$store['store_id'].''];
	        } else {
	            $data['multisafepay_paypal_geo_zone_id_'.$store['store_id'].''] = $this->config->get('multisafepay_paypal_geo_zone_id_0');
	        }
	        if (isset($this->request->post['multisafepay_paypal_max_amount_'.$store['store_id'].''])) {
	            $data['multisafepay_paypal_max_amount_'.$store['store_id'].''] = $this->request->post['multisafepay_paypal_max_amount_'.$store['store_id'].''];
	        } else {
	            $data['multisafepay_paypal_max_amount_'.$store['store_id'].''] = $this->config->get('multisafepay_paypal_max_amount_0');
	        }
	        if (isset($this->request->post['multisafepay_paypal_min_amount_'.$store['store_id'].''])) {
	            $data['multisafepay_paypal_min_amount_'.$store['store_id'].''] = $this->request->post['multisafepay_paypal_min_amount_'.$store['store_id'].''];
	        } else {
	            $data['multisafepay_paypal_min_amount_'.$store['store_id'].''] = $this->config->get('multisafepay_paypal_min_amount_0');
	        }
	        if (isset($this->request->post['multisafepay_paypal_sort_order_'.$store['store_id'].''])) {
	            $data['multisafepay_paypal_sort_order_'.$store['store_id'].''] = $this->request->post['multisafepay_paypal_sort_order_'.$store['store_id'].''];
	        } else {
	            $data['multisafepay_paypal_sort_order_'.$store['store_id'].''] = $this->config->get('multisafepay_paypal_sort_order_0');
	        }
	    }
        

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['tab_general'] = $this->language->get('tab_general');

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->setup_link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->setup_link('extension/payment'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->setup_link('payment/multisafepay_paypal'),
            'separator' => ' :: '
        );


        $this->template = 'payment/multisafepay_paypal.tpl';
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this->template, $data));
    }

   private function setup_link($route) {
        return  $link = $this->url->link($route, 'token=' . $this->session->data['token'], 'SSL');
    }

}

?>