<?php

/**
 * Multisafepay  Payment Integration
 * Date 31-11-2014
 * @version 2.0
 * @package 2.0
 * @copyright 2014 MultiSafePay.
 */
class ControllerPaymentMultiSafePay extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('payment/multisafepay');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('multisafepay', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['text_set_order_status'] = $this->language->get('text_set_order_status');
        $data['text_free_account'] = $this->language->get('text_free_account');
        $data['entry_multisafepay_account_type'] = $this->language->get('entry_multisafepay_account_type');
        $data['entry_multisafepay_merchantid'] = $this->language->get('entry_multisafepay_merchantid');
        $data['entry_multisafepay_siteid'] = $this->language->get('entry_multisafepay_siteid');
        $data['entry_multisafepay_secure_code'] = $this->language->get('entry_multisafepay_secure_code');
        $data['entry_multisafepay_storeid'] = $this->language->get('entry_multisafepay_storeid');
        $data['entry_multisafepay_merchantid'] = $this->language->get('bno_multisafepay_merchantid');
        $data['entry_multisafepay_siteid'] = $this->language->get('bno_multisafepay_siteid');
        $data['entry_multisafepay_secure_code'] = $this->language->get('bno_multisafepay_secure_code');
        $data['entry_environment'] = $this->language->get('entry_environment');
        $data['yes'] = $this->language->get('yes');
        $data['no'] = $this->language->get('no');
        $data['select_methods'] = $this->language->get('select_methods');
        $data['entry_multisafepay_redirect_url'] = $this->language->get('entry_multisafepay_redirect_url');
        $data['entry_multisafepay_b2b'] = $this->language->get('entry_multisafepay_b2b');
        $data['entry_multisafepay_gateway_selection'] = $this->language->get('entry_multisafepay_gateway_selection');
        $data['entry_multisafepay_order_status_id_initialized'] = $this->language->get('entry_multisafepay_order_status_id_initialized');
        $data['entry_multisafepay_order_status_id_completed'] = $this->language->get('entry_multisafepay_order_status_id_completed');
        $data['entry_multisafepay_order_status_id_uncleared'] = $this->language->get('entry_multisafepay_order_status_id_uncleared');
        $data['entry_multisafepay_order_status_id_reserved'] = $this->language->get('entry_multisafepay_order_status_id_reserved');
        $data['entry_multisafepay_order_status_id_void'] = $this->language->get('entry_multisafepay_order_status_id_void');
        $data['entry_multisafepay_order_status_id_refunded'] = $this->language->get('entry_multisafepay_order_status_id_refunded');
        $data['entry_multisafepay_order_status_id_declined'] = $this->language->get('entry_multisafepay_order_status_id_declined');
        $data['entry_multisafepay_order_status_id_expired'] = $this->language->get('entry_multisafepay_order_status_id_expired');
        $data['entry_multisafepay_order_status_id_shipped'] = $this->language->get('entry_multisafepay_order_status_id_shipped');
        $data['entry_multisafepay_order_status_id_partial_refunded'] = $this->language->get('entry_multisafepay_order_status_id_partial_refunded');
        //start bno data
        $data['text_min_amount'] = $this->language->get('text_min_amount');
        $data['text_max_amount'] = $this->language->get('text_max_amount');
        $data['text_set_bno_data'] = $this->language->get('text_set_bno_data');
        $data['text_bno_product_fee_id'] = $this->language->get('text_bno_product_fee_id');
        $data['text_bno_ip_validation_option'] = $this->language->get('text_bno_ip_validation_option');
        $data['text_bno_ip_validation_address'] = $this->language->get('text_bno_ip_validation_address');

        $data['text_set_fco_shipping'] = $this->language->get('text_set_fco_shipping');
        $data['entry_multisafepay_fco_tax'] = $this->language->get('entry_multisafepay_fco_tax');
        $data['entry_multisafepay_fco_free_ship'] = $this->language->get('entry_multisafepay_fco_free_ship');
        $data['entry_multisafepay_days_active'] = $this->language->get('entry_multisafepay_days_active');


        //end bno data
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['enable_checkout_button'] = $this->language->get('enable_checkout_button');


        if (isset($this->request->post['multisafepay_max_amount'])) {
            $data['multisafepay_max_amount'] = $this->request->post['multisafepay_max_amount'];
        } else {
            $data['multisafepay_max_amount'] = $this->config->get('multisafepay_max_amount');
        }
        if (isset($this->request->post['multisafepay_min_amount'])) {
            $data['multisafepay_min_amount'] = $this->request->post['multisafepay_min_amount'];
        } else {
            $data['multisafepay_min_amount'] = $this->config->get('multisafepay_min_amount');
        }



        if (isset($this->request->post['bno_product_fee_id'])) {
            $data['bno_product_fee_id'] = $this->request->post['bno_product_fee_id'];
        } else {
            $data['bno_product_fee_id'] = $this->config->get('bno_product_fee_id');
        }
        if (isset($this->request->post['multisafepay_fco_tax_percent'])) {
            $data['multisafepay_fco_tax_percent'] = $this->request->post['multisafepay_fco_tax_percent'];
        } else {
            $data['multisafepay_fco_tax_percent'] = $this->config->get('multisafepay_fco_tax_percent');
        }

        if (isset($this->request->post['multisafepay_fco_free_ship'])) {
            $data['multisafepay_fco_free_ship'] = $this->request->post['multisafepay_fco_free_ship'];
        } else {
            $data['multisafepay_fco_free_ship'] = $this->config->get('multisafepay_fco_free_ship');
        }

        if (isset($this->request->post['multisafepay_days_active'])) {
            $data['multisafepay_days_active'] = $this->request->post['multisafepay_days_active'];
        } else {
            $data['multisafepay_days_active'] = $this->config->get('multisafepay_days_active');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['multisafepay_geo_zone_id'])) {
            $data['multisafepay_geo_zone_id'] = $this->request->post['multisafepay_geo_zone_id'];
        } else {
            $data['multisafepay_geo_zone_id'] = $this->config->get('multisafepay_geo_zone_id');
        }




        if (isset($this->request->post['bno_ip_validation_address'])) {
            $data['bno_ip_validation_address'] = $this->request->post['bno_ip_validation_address'];
        } else {
            $data['bno_ip_validation_address'] = $this->config->get('bno_ip_validation_address');
        }


        if (isset($this->request->post['bno_ip_validation_enabler'])) {
            $data['bno_ip_validation_enabler'] = $this->request->post['bno_ip_validation_enabler'];
        } else {
            $data['bno_ip_validation_enabler'] = $this->config->get('bno_ip_validation_enabler');
        }

        if (isset($this->request->post['max_bno_amount'])) {
            $data['max_bno_amount'] = $this->request->post['max_bno_amount'];
        } else {
            $data['max_bno_amount'] = $this->config->get('max_bno_amount');
        }
        if (isset($this->request->post['min_bno_amount'])) {
            $data['min_bno_amount'] = $this->request->post['min_bno_amount'];
        } else {
            $data['min_bno_amount'] = $this->config->get('min_bno_amount');
        }



        //main store config
        if (isset($this->request->post['multisafepay_merchant_id'])) {
            $data['multisafepay_merchant_id'] = $this->request->post['multisafepay_merchant_id'];
        } else {
            $data['multisafepay_merchant_id'] = $this->config->get('multisafepay_merchant_id');
        }

        if (isset($this->request->post['multisafepay_site_id'])) {
            $data['multisafepay_site_id'] = $this->request->post['multisafepay_site_id'];
        } else {
            $data['multisafepay_site_id'] = $this->config->get('multisafepay_site_id');
        }

        if (isset($this->request->post['multisafepay_secure_code'])) {
            $data['multisafepay_secure_code'] = $this->request->post['multisafepay_secure_code'];
        } else {
            $data['multisafepay_secure_code'] = $this->config->get('multisafepay_secure_code');
        }


        //multistore site config 1
        if (isset($this->request->post['multisafepay_merchant_id_1'])) {
            $data['multisafepay_merchant_id_1'] = $this->request->post['multisafepay_merchant_id_1'];
        } else {
            $data['multisafepay_merchant_id_1'] = $this->config->get('multisafepay_merchant_id_1');
        }

        if (isset($this->request->post['multisafepay_site_id_1'])) {
            $data['multisafepay_site_id_1'] = $this->request->post['multisafepay_site_id_1'];
        } else {
            $data['multisafepay_site_id_1'] = $this->config->get('multisafepay_site_id_1');
        }

        if (isset($this->request->post['multisafepay_secure_code_1'])) {
            $data['multisafepay_secure_code_1'] = $this->request->post['multisafepay_secure_code_1'];
        } else {
            $data['multisafepay_secure_code_1'] = $this->config->get('multisafepay_secure_code_1');
        }

        if (isset($this->request->post['msp_storeid_1'])) {
            $data['msp_storeid_1'] = $this->request->post['msp_storeid_1'];
        } else {
            $data['msp_storeid_1'] = $this->config->get('msp_storeid_1');
        }


        //multistore site config 2
        if (isset($this->request->post['multisafepay_merchant_id_2'])) {
            $data['multisafepay_merchant_id_2'] = $this->request->post['multisafepay_merchant_id_2'];
        } else {
            $data['multisafepay_merchant_id_2'] = $this->config->get('multisafepay_merchant_id_2');
        }

        if (isset($this->request->post['multisafepay_site_id_2'])) {
            $data['multisafepay_site_id_2'] = $this->request->post['multisafepay_site_id_2'];
        } else {
            $data['multisafepay_site_id_2'] = $this->config->get('multisafepay_site_id_2');
        }

        if (isset($this->request->post['multisafepay_secure_code_2'])) {
            $data['multisafepay_secure_code_2'] = $this->request->post['multisafepay_secure_code_2'];
        } else {
            $data['multisafepay_secure_code_2'] = $this->config->get('multisafepay_secure_code_2');
        }

        if (isset($this->request->post['msp_storeid_2'])) {
            $data['msp_storeid_2'] = $this->request->post['msp_storeid_2'];
        } else {
            $data['msp_storeid_2'] = $this->config->get('msp_storeid_2');
        }


        //multistore site config 3
        if (isset($this->request->post['multisafepay_merchant_id_3'])) {
            $data['multisafepay_merchant_id_3'] = $this->request->post['multisafepay_merchant_id_3'];
        } else {
            $data['multisafepay_merchant_id_3'] = $this->config->get('multisafepay_merchant_id_3');
        }

        if (isset($this->request->post['multisafepay_site_id_3'])) {
            $data['multisafepay_site_id_3'] = $this->request->post['multisafepay_site_id_3'];
        } else {
            $data['multisafepay_site_id_3'] = $this->config->get('multisafepay_site_id_3');
        }

        if (isset($this->request->post['multisafepay_secure_code_3'])) {
            $data['multisafepay_secure_code_3'] = $this->request->post['multisafepay_secure_code_3'];
        } else {
            $data['multisafepay_secure_code_3'] = $this->config->get('multisafepay_secure_code_3');
        }

        if (isset($this->request->post['msp_storeid_3'])) {
            $data['msp_storeid_3'] = $this->request->post['msp_storeid_3'];
        } else {
            $data['msp_storeid_3'] = $this->config->get('msp_storeid_3');
        }

        //multistore site config 4
        if (isset($this->request->post['multisafepay_merchant_id_4'])) {
            $data['multisafepay_merchant_id_4'] = $this->request->post['multisafepay_merchant_id_4'];
        } else {
            $data['multisafepay_merchant_id_4'] = $this->config->get('multisafepay_merchant_id_4');
        }

        if (isset($this->request->post['multisafepay_site_id_4'])) {
            $data['multisafepay_site_id_4'] = $this->request->post['multisafepay_site_id_4'];
        } else {
            $data['multisafepay_site_id_4'] = $this->config->get('multisafepay_site_id_4');
        }

        if (isset($this->request->post['multisafepay_secure_code_4'])) {
            $data['multisafepay_secure_code_4'] = $this->request->post['multisafepay_secure_code_4'];
        } else {
            $data['multisafepay_secure_code_4'] = $this->config->get('multisafepay_secure_code_4');
        }

        if (isset($this->request->post['msp_storeid_4'])) {
            $data['msp_storeid_4'] = $this->request->post['msp_storeid_4'];
        } else {
            $data['msp_storeid_4'] = $this->config->get('msp_storeid_4');
        }


        if (isset($this->request->post['bno_multisafepay_merchant_id'])) {
            $data['bno_multisafepay_merchant_id'] = $this->request->post['bno_multisafepay_merchant_id'];
        } else {
            $data['bno_multisafepay_merchant_id'] = $this->config->get('bno_multisafepay_merchant_id');
        }

        if (isset($this->request->post['bno_multisafepay_site_id'])) {
            $data['bno_multisafepay_site_id'] = $this->request->post['bno_multisafepay_site_id'];
        } else {
            $data['bno_multisafepay_site_id'] = $this->config->get('bno_multisafepay_site_id');
        }

        if (isset($this->request->post['bno_multisafepay_secure_code'])) {
            $data['bno_multisafepay_secure_code'] = $this->request->post['bno_multisafepay_secure_code'];
        } else {
            $data['bno_multisafepay_secure_code'] = $this->config->get('bno_multisafepay_secure_code');
        }

        if (isset($this->request->post['bno_multisafepay_environment'])) {
            $data['bno_multisafepay_environment'] = $this->request->post['bno_multisafepay_environment'];
        } else {
            $data['bno_multisafepay_environment'] = $this->config->get('bno_multisafepay_environment');
        }




        if (isset($this->request->post['multisafepay_environment'])) {
            $data['multisafepay_environment'] = $this->request->post['multisafepay_environment'];
        } else {
            $data['multisafepay_environment'] = $this->config->get('multisafepay_environment');
        }

        if (isset($this->request->post['multisafepay_enable_checkout_button_connect'])) {
            $data['multisafepay_enable_checkout_button_connect'] = $this->request->post['multisafepay_enable_checkout_button_connect'];
        } else {
            $data['multisafepay_enable_checkout_button_connect'] = $this->config->get('multisafepay_enable_checkout_button_connect');
        }


        if (isset($this->request->post['multisafepay_account_type'])) {
            $data['multisafepay_account_type'] = $this->request->post['multisafepay_account_type'];
        } else {
            $data['multisafepay_account_type'] = $this->config->get('multisafepay_account_type');
        }

        if (isset($this->request->post['multisafepay_redirect_url'])) {
            $data['multisafepay_redirect_url'] = $this->request->post['multisafepay_redirect_url'];
        } else {
            $data['multisafepay_redirect_url'] = $this->config->get('multisafepay_redirect_url');
        }
        
        
        
        if (isset($this->request->post['multisafepay_b2b'])) {
            $data['multisafepay_b2b'] = $this->request->post['multisafepay_b2b'];
        } else {
            $data['multisafepay_b2b'] = $this->config->get('multisafepay_b2b');
        }
        
        

        if (isset($this->request->post['multisafepay_gateway_selection'])) {
            $data['multisafepay_gateway_selection'] = $this->request->post['multisafepay_gateway_selection'];
        } else {
            $data['multisafepay_gateway_selection'] = $this->config->get('multisafepay_gateway_selection');
        }

        if (isset($this->request->post['MSP_MISTERCASH'])) {
            $data['MSP_MISTERCASH'] = $this->request->post['MSP_MISTERCASH'];
        } else {
            $data['MSP_MISTERCASH'] = $this->config->get('MSP_MISTERCASH');
        }

        if (isset($this->request->post['MSP_DIRDEB'])) {
            $data['MSP_DIRDEB'] = $this->request->post['MSP_DIRDEB'];
        } else {
            $data['MSP_DIRDEB'] = $this->config->get('MSP_DIRDEB');
        }

        if (isset($this->request->post['MSP_WALLET'])) {
            $data['MSP_WALLET'] = $this->request->post['MSP_WALLET'];
        } else {
            $data['MSP_WALLET'] = $this->config->get('MSP_WALLET');
        }

        if (isset($this->request->post['MSP_BANKTRANS'])) {
            $data['MSP_BANKTRANS'] = $this->request->post['MSP_BANKTRANS'];
        } else {
            $data['MSP_BANKTRANS'] = $this->config->get('MSP_BANKTRANS');
        }

        if (isset($this->request->post['MSP_IDEAL'])) {
            $data['MSP_IDEAL'] = $this->request->post['MSP_IDEAL'];
        } else {
            $data['MSP_IDEAL'] = $this->config->get('MSP_IDEAL');
        }

        if (isset($this->request->post['MSP_VISA'])) {
            $data['MSP_VISA'] = $this->request->post['MSP_VISA'];
        } else {
            $data['MSP_VISA'] = $this->config->get('MSP_VISA');
        }

        if (isset($this->request->post['MSP_MASTERCARD'])) {
            $data['MSP_MASTERCARD'] = $this->request->post['MSP_MASTERCARD'];
        } else {
            $data['MSP_MASTERCARD'] = $this->config->get('MSP_MASTERCARD');
        }

        if (isset($this->request->post['MSP_DIRECTBANK'])) {
            $data['MSP_DIRECTBANK'] = $this->request->post['MSP_DIRECTBANK'];
        } else {
            $data['MSP_DIRECTBANK'] = $this->config->get('MSP_DIRECTBANK');
        }

        if (isset($this->request->post['MSP_GIROPAY'])) {
            $data['MSP_GIROPAY'] = $this->request->post['MSP_GIROPAY'];
        } else {
            $data['MSP_GIROPAY'] = $this->config->get('MSP_GIROPAY');
        }

        if (isset($this->request->post['MSP_MAESTRO'])) {
            $data['MSP_MAESTRO'] = $this->request->post['MSP_MAESTRO'];
        } else {
            $data['MSP_MAESTRO'] = $this->config->get('MSP_MAESTRO');
        }
        if (isset($this->request->post['MSP_PAYAFTER'])) {
            $data['MSP_PAYAFTER'] = $this->request->post['MSP_PAYAFTER'];
        } else {
            $data['MSP_PAYAFTER'] = $this->config->get('MSP_PAYAFTER');
        }


        if (isset($this->request->post['multisafepay_order_status_id_completed'])) {
            $data['multisafepay_order_status_id_completed'] = $this->request->post['multisafepay_order_status_id_completed'];
        } else {
            $data['multisafepay_order_status_id_completed'] = $this->config->get('multisafepay_order_status_id_completed');
        }


        if (isset($this->request->post['multisafepay_order_status_id_initialized'])) {
            $data['multisafepay_order_status_id_initialized'] = $this->request->post['multisafepay_order_status_id_initialized'];
        } else {
            $data['multisafepay_order_status_id_initialized'] = $this->config->get('multisafepay_order_status_id_initialized');
        }

        if (isset($this->request->post['multisafepay_order_status_id_uncleared'])) {
            $data['multisafepay_order_status_id_uncleared'] = $this->request->post['multisafepay_order_status_id_uncleared'];
        } else {
            $data['multisafepay_order_status_id_uncleared'] = $this->config->get('multisafepay_order_status_id_uncleared');
        }

        if (isset($this->request->post['multisafepay_order_status_id_reserved'])) {
            $data['multisafepay_order_status_id_reserved'] = $this->request->post['multisafepay_order_status_id_reserved'];
        } else {
            $data['multisafepay_order_status_id_reserved'] = $this->config->get('multisafepay_order_status_id_reserved');
        }

        if (isset($this->request->post['multisafepay_order_status_id_void'])) {
            $data['multisafepay_order_status_id_void'] = $this->request->post['multisafepay_order_status_id_void'];
        } else {
            $data['multisafepay_order_status_id_void'] = $this->config->get('multisafepay_order_status_id_void');
        }

        if (isset($this->request->post['multisafepay_order_status_id_refunded'])) {
            $data['multisafepay_order_status_id_refunded'] = $this->request->post['multisafepay_order_status_id_refunded'];
        } else {
            $data['multisafepay_order_status_id_refunded'] = $this->config->get('multisafepay_order_status_id_refunded');
        }

        if (isset($this->request->post['multisafepay_order_status_id_declined'])) {
            $data['multisafepay_order_status_id_declined'] = $this->request->post['multisafepay_order_status_id_declined'];
        } else {
            $data['multisafepay_order_status_id_declined'] = $this->config->get('multisafepay_order_status_id_declined');
        }

        if (isset($this->request->post['multisafepay_order_status_id_expired'])) {
            $data['multisafepay_order_status_id_expired'] = $this->request->post['multisafepay_order_status_id_expired'];
        } else {
            $data['multisafepay_order_status_id_expired'] = $this->config->get('multisafepay_order_status_id_expired');
        }

        if (isset($this->request->post['multisafepay_order_status_id_shipped'])) {
            $data['multisafepay_order_status_id_shipped'] = $this->request->post['multisafepay_order_status_id_shipped'];
        } else {
            $data['multisafepay_order_status_id_shipped'] = $this->config->get('multisafepay_order_status_id_shipped');
        }
        
        if (isset($this->request->post['multisafepay_order_status_id_partial_refunded'])) {
            $data['multisafepay_order_status_id_partial_refunded'] = $this->request->post['multisafepay_order_status_id_partial_refunded'];
        } else {
            $data['multisafepay_order_status_id_partial_refunded'] = $this->config->get('multisafepay_order_status_id_partial_refunded');
        }


        if (isset($this->request->post['multisafepay_status'])) {
            $data['multisafepay_status'] = $this->request->post['multisafepay_status'];
        } else {
            $data['multisafepay_status'] = $this->config->get('multisafepay_status');
        }


        if (isset($this->request->post['multisafepay_sort_order'])) {
            $data['multisafepay_sort_order'] = $this->request->post['multisafepay_sort_order'];
        } else {
            $data['multisafepay_sort_order'] = $this->config->get('multisafepay_sort_order');
        }

        // callback url
        $data['callback'] = HTTP_CATALOG . 'index.php?route=payment/multisafepay/fastcheckout';
        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['merchant'])) {
            $data['error_merchant'] = $this->error['merchant'];
        } else {
            $data['error_merchant'] = '';
        }

        if (isset($this->error['password'])) {
            $data['error_password'] = $this->error['password'];
        } else {
            $data['error_password'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('payment/multisafepay', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action'] = $this->url->link('payment/multisafepay', 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/multisafepay.tpl', $data));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'payment/multisafepay')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['multisafepay_merchant_id']) {
            $this->error['merchant'] = $this->language->get('error_merchant');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function setup_link($route) {
        return $this->url->link($route, 'token=' . $this->session->data['token'], 'SSL');
    }

}

?>