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
 * @author      TechSupport <techsupport@multisafepay.com>
 * @copyright   Copyright (c) 2017 MultiSafepay, Inc. (http://www.multisafepay.com)
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

class ControllerExtensionPaymentMultiSafePay extends Controller
{

    private $error = array();

    public function index()
    {
        $this->load->language('extension/payment/multisafepay');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('setting/store');
        $this->load->model('localisation/geo_zone');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_multisafepay', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL'));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['stores'] = $this->model_setting_store->getStores();
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
        $data['entry_confirm_order'] = $this->language->get('entry_confirm_order');
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
        $data['text_bno_ip_validation_option'] = $this->language->get('text_bno_ip_validation_option');
        $data['text_bno_ip_validation_address'] = $this->language->get('text_bno_ip_validation_address');

        $data['text_set_fco_shipping'] = $this->language->get('text_set_fco_shipping');
        $data['entry_multisafepay_fco_tax'] = $this->language->get('entry_multisafepay_fco_tax');
        $data['entry_multisafepay_fco_free_ship'] = $this->language->get('entry_multisafepay_fco_free_ship');
        $data['entry_multisafepay_days_active'] = $this->language->get('entry_multisafepay_days_active');
        $data['entry_multisafepay_use_payment_logo'] = $this->language->get('entry_multisafepay_use_payment_logo');


        //end bno data
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['enable_checkout_button'] = $this->language->get('enable_checkout_button');


        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        /**
         * 	Start Default configuration
         */
        if (isset($this->request->post['payment_multisafepay_max_amount_0'])) {
            $data['payment_multisafepay_max_amount'] = $this->request->post['payment_multisafepay_max_amount_0'];
        } else {
            $data['payment_multisafepay_max_amount'] = $this->config->get('payment_multisafepay_max_amount_0');
        }

        if (isset($this->request->post['payment_multisafepay_confirm_order_0'])) {
            $data['payment_multisafepay_confirm_order'] = $this->request->post['payment_multisafepay_confirm_order_0'];
        } else {
            $data['payment_multisafepay_confirm_order'] = $this->config->get('payment_multisafepay_confirm_order_0');
        }

        if (isset($this->request->post['payment_multisafepay_min_amount_0'])) {
            $data['payment_multisafepay_min_amount'] = $this->request->post['payment_multisafepay_min_amount_0'];
        } else {
            $data['payment_multisafepay_min_amount'] = $this->config->get('payment_multisafepay_min_amount_0');
        }

        if (isset($this->request->post['payment_multisafepay_fco_tax_percent_0'])) {
            $data['payment_multisafepay_fco_tax_percent'] = $this->request->post['payment_multisafepay_fco_tax_percent_0'];
        } else {
            $data['payment_multisafepay_fco_tax_percent'] = $this->config->get('payment_multisafepay_fco_tax_percent_0');
        }

        if (isset($this->request->post['payment_multisafepay_fco_free_ship_0'])) {
            $data['payment_multisafepay_fco_free_ship'] = $this->request->post['payment_multisafepay_fco_free_ship_0'];
        } else {
            $data['payment_multisafepay_fco_free_ship'] = $this->config->get('payment_multisafepay_fco_free_ship_0');
        }

        if (isset($this->request->post['payment_multisafepay_days_active_0'])) {
            $data['payment_multisafepay_days_active'] = $this->request->post['payment_multisafepay_days_active_0'];
        } else {
            $data['payment_multisafepay_days_active'] = $this->config->get('payment_multisafepay_days_active_0');
        }

        if (isset($this->request->post['payment_multisafepay_use_payment_logo_0'])) {
            $data['payment_multisafepay_use_payment_logo'] = $this->request->post['payment_multisafepay_use_payment_logo_0'];
        } else {
            $data['payment_multisafepay_use_payment_logo'] = $this->config->get('payment_multisafepay_use_payment_logo_0');
        }

        if (isset($this->request->post['payment_multisafepay_status'])) {
            $data['payment_multisafepay_status'] = $this->request->post['payment_multisafepay_status'];
        } else {
            $data['payment_multisafepay_status'] = $this->config->get('payment_multisafepay_status');
        }

        if (isset($this->request->post['payment_multisafepay_environment_0'])) {
            $data['payment_multisafepay_environment'] = $this->request->post['payment_multisafepay_environment_0'];
        } else {
            $data['payment_multisafepay_environment'] = $this->config->get('payment_multisafepay_environment_0');
        }

        if (isset($this->request->post['payment_multisafepay_account_type_0'])) {
            $data['payment_multisafepay_account_type'] = $this->request->post['payment_multisafepay_account_type_0'];
        } else {
            $data['payment_multisafepay_account_type'] = $this->config->get('payment_multisafepay_account_type_0');
        }


        if (isset($this->request->post['payment_multisafepay_enable_checkout_button_connect_0'])) {
            $data['payment_multisafepay_enable_checkout_button_connect'] = $this->request->post['payment_multisafepay_enable_checkout_button_connect_0'];
        } else {
            $data['payment_multisafepay_enable_checkout_button_connect'] = $this->config->get('payment_multisafepay_enable_checkout_button_connect_0');
        }


        if (isset($this->request->post['payment_multisafepay_geo_zone_id_0'])) {
            $data['payment_multisafepay_geo_zone_id'] = $this->request->post['payment_multisafepay_geo_zone_id_0'];
        } else {
            $data['payment_multisafepay_geo_zone_id'] = $this->config->get('payment_multisafepay_geo_zone_id_0');
        }

        //main store config
        if (isset($this->request->post['payment_multisafepay_merchant_id_0'])) {
            $data['payment_multisafepay_merchant_id'] = $this->request->post['payment_multisafepay_merchant_id_0'];
        } else {
            $data['payment_multisafepay_merchant_id'] = $this->config->get('payment_multisafepay_merchant_id_0');
        }

        if (isset($this->request->post['payment_multisafepay_site_id_0'])) {
            $data['payment_multisafepay_site_id'] = $this->request->post['payment_multisafepay_site_id_0'];
        } else {
            $data['payment_multisafepay_site_id'] = $this->config->get('payment_multisafepay_site_id_0');
        }

        if (isset($this->request->post['payment_multisafepay_secure_code_0'])) {
            $data['payment_multisafepay_secure_code'] = $this->request->post['payment_multisafepay_secure_code_0'];
        } else {
            $data['payment_multisafepay_secure_code'] = $this->config->get('payment_multisafepay_secure_code_0');
        }


        if (isset($this->request->post['payment_multisafepay_redirect_url_0'])) {
            $data['payment_multisafepay_redirect_url'] = $this->request->post['payment_multisafepay_redirect_url_0'];
        } else {
            $data['payment_multisafepay_redirect_url'] = $this->config->get('payment_multisafepay_redirect_url_0');
        }

        if (isset($this->request->post['payment_multisafepay_b2b_0'])) {
            $data['payment_multisafepay_b2b'] = $this->request->post['payment_multisafepay_b2b_0'];
        } else {
            $data['payment_multisafepay_b2b'] = $this->config->get('payment_multisafepay_b2b_0');
        }

        if (isset($this->request->post['payment_multisafepay_order_status_id_completed_0'])) {
            $data['payment_multisafepay_order_status_id_completed'] = $this->request->post['payment_multisafepay_order_status_id_completed_0'];
        } else {
            $data['payment_multisafepay_order_status_id_completed'] = $this->config->get('payment_multisafepay_order_status_id_completed_0');
        }

        if (isset($this->request->post['payment_multisafepay_order_status_id_initialized_0'])) {
            $data['payment_multisafepay_order_status_id_initialized'] = $this->request->post['payment_multisafepay_order_status_id_initialized_0'];
        } else {
            $data['payment_multisafepay_order_status_id_initialized'] = $this->config->get('payment_multisafepay_order_status_id_initialized_0');
        }

        if (isset($this->request->post['payment_multisafepay_order_status_id_uncleared_0'])) {
            $data['payment_multisafepay_order_status_id_uncleared'] = $this->request->post['payment_multisafepay_order_status_id_uncleared_0'];
        } else {
            $data['payment_multisafepay_order_status_id_uncleared'] = $this->config->get('payment_multisafepay_order_status_id_uncleared_0');
        }

        if (isset($this->request->post['payment_multisafepay_order_status_id_reserved_0'])) {
            $data['payment_multisafepay_order_status_id_reserved'] = $this->request->post['payment_multisafepay_order_status_id_reserved_0'];
        } else {
            $data['payment_multisafepay_order_status_id_reserved'] = $this->config->get('payment_multisafepay_order_status_id_reserved_0');
        }

        if (isset($this->request->post['payment_multisafepay_order_status_id_void_0'])) {
            $data['payment_multisafepay_order_status_id_void'] = $this->request->post['payment_multisafepay_order_status_id_void_0'];
        } else {
            $data['payment_multisafepay_order_status_id_void'] = $this->config->get('payment_multisafepay_order_status_id_void_0');
        }

        if (isset($this->request->post['payment_multisafepay_order_status_id_refunded_0'])) {
            $data['payment_multisafepay_order_status_id_refunded'] = $this->request->post['payment_multisafepay_order_status_id_refunded_0'];
        } else {
            $data['payment_multisafepay_order_status_id_refunded'] = $this->config->get('payment_multisafepay_order_status_id_refunded_0');
        }

        if (isset($this->request->post['payment_multisafepay_order_status_id_declined_0'])) {
            $data['payment_multisafepay_order_status_id_declined'] = $this->request->post['payment_multisafepay_order_status_id_declined_0'];
        } else {
            $data['payment_multisafepay_order_status_id_declined'] = $this->config->get('payment_multisafepay_order_status_id_declined_0');
        }

        if (isset($this->request->post['payment_multisafepay_order_status_id_expired_0'])) {
            $data['payment_multisafepay_order_status_id_expired'] = $this->request->post['payment_multisafepay_order_status_id_expired_0'];
        } else {
            $data['payment_multisafepay_order_status_id_expired'] = $this->config->get('payment_multisafepay_order_status_id_expired_0');
        }

        if (isset($this->request->post['payment_multisafepay_order_status_id_shipped_0'])) {
            $data['payment_multisafepay_order_status_id_shipped'] = $this->request->post['payment_multisafepay_order_status_id_shipped_0'];
        } else {
            $data['payment_multisafepay_order_status_id_shipped'] = $this->config->get('payment_multisafepay_order_status_id_shipped_0');
        }

        if (isset($this->request->post['payment_multisafepay_order_status_id_partial_refunded_0'])) {
            $data['payment_multisafepay_order_status_id_partial_refunded'] = $this->request->post['payment_multisafepay_order_status_id_partial_refunded_0'];
        } else {
            $data['payment_multisafepay_order_status_id_partial_refunded'] = $this->config->get('payment_multisafepay_order_status_id_partial_refunded_0');
        }

        if (isset($this->request->post['payment_multisafepay_sort_order_0'])) {
            $data['payment_multisafepay_sort_order'] = $this->request->post['payment_multisafepay_sort_order_0'];
        } else {
            $data['payment_multisafepay_sort_order'] = $this->config->get('payment_multisafepay_sort_order_0');
        }
//     echo '<pre>';  print_r ($data);  die('Klaar');

        /*
         * Start multistore configuration
         */
        foreach ($this->model_setting_store->getStores() as $store) {

            if (isset($this->request->post['payment_multisafepay_confirm_order_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_confirm_order_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_confirm_order_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_confirm_order_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_confirm_order_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_max_amount_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_max_amount_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_max_amount_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_max_amount_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_max_amount_' . $store['store_id']);
            }
            if (isset($this->request->post['payment_multisafepay_min_amount_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_min_amount_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_min_amount_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_min_amount_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_min_amount_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_fco_tax_percent_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_fco_tax_percent_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_fco_tax_percent_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_fco_tax_percent_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_fco_tax_percent_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_fco_free_ship_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_fco_free_ship_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_fco_free_ship_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_fco_free_ship_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_fco_free_ship_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_days_active_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_days_active_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_days_active_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_days_active_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_days_active_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_use_payment_logo_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_use_payment_logo_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_use_payment_logo_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_use_payment_logo_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_use_payment_logo_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_geo_zone_id_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_geo_zone_id_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_geo_zone_id_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_geo_zone_id_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_geo_zone_id_' . $store['store_id']);
            }

            //main store config
            if (isset($this->request->post['payment_multisafepay_merchant_id_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_merchant_id_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_merchant_id_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_merchant_id_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_merchant_id_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_site_id_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_site_id_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_site_id_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_site_id_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_site_id_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_secure_code_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_secure_code_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_secure_code_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_secure_code_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_secure_code_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_environment_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_environment_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_environment_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_environment_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_environment_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_enable_checkout_button_connect_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_enable_checkout_button_connect_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_enable_checkout_button_connect_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_enable_checkout_button_connect_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_enable_checkout_button_connect_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_account_type_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_account_type_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_account_type_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_account_type_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_account_type_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_redirect_url_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_redirect_url_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_redirect_url_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_redirect_url_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_redirect_url_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_b2b_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_b2b_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_b2b_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_b2b_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_b2b_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_order_status_id_completed_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_order_status_id_completed_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_order_status_id_completed_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_order_status_id_completed_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_order_status_id_completed_' . $store['store_id']);
            }


            if (isset($this->request->post['payment_multisafepay_order_status_id_initialized_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_order_status_id_initialized_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_order_status_id_initialized_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_order_status_id_initialized_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_order_status_id_initialized_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_order_status_id_uncleared_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_order_status_id_uncleared_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_order_status_id_uncleared_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_order_status_id_uncleared_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_order_status_id_uncleared_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_order_status_id_reserved_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_order_status_id_reserved_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_order_status_id_reserved_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_order_status_id_reserved_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_order_status_id_reserved_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_order_status_id_void_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_order_status_id_void_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_order_status_id_void_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_order_status_id_void_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_order_status_id_void_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_order_status_id_refunded_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_order_status_id_refunded_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_order_status_id_refunded_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_order_status_id_refunded_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_order_status_id_refunded_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_order_status_id_declined_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_order_status_id_declined_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_order_status_id_declined_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_order_status_id_declined_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_order_status_id_declined_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_order_status_id_expired_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_order_status_id_expired_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_order_status_id_expired_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_order_status_id_expired_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_order_status_id_expired_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_order_status_id_shipped_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_order_status_id_shipped_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_order_status_id_shipped_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_order_status_id_shipped_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_order_status_id_shipped_' . $store['store_id']);
            }

            if (isset($this->request->post['payment_multisafepay_order_status_id_partial_refunded_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_order_status_id_partial_refunded_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_order_status_id_partial_refunded_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_order_status_id_partial_refunded_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_order_status_id_partial_refunded_' . $store['store_id']);
            }


            if (isset($this->request->post['payment_multisafepay_status_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_status_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_status_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_status_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_status_' . $store['store_id']);
            }


            if (isset($this->request->post['payment_multisafepay_sort_order_' . $store['store_id'] . ''])) {
                $data['payment_multisafepay_sort_order_' . $store['store_id'] . ''] = $this->request->post['payment_multisafepay_sort_order_' . $store['store_id'] . ''];
            } else {
                $data['payment_multisafepay_sort_order_' . $store['store_id'] . ''] = $this->config->get('payment_multisafepay_sort_order_' . $store['store_id']);
            }
        }

        /*
         * End multistore configuration
         */

        // callback url
        $data['callback'] = HTTP_CATALOG . 'index.php?route=payment/multisafepay/fastcheckout';

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
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/multisafepay', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );

        $data['action'] = $this->url->link('extension/payment/multisafepay', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/multisafepay', $data));
    }

    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/multisafepay')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_multisafepay_merchant_id_0']) {
            $this->error['merchant'] = $this->language->get('error_merchant');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function setup_link($route)
    {
        return $this->url->link($route, 'user_token=' . $this->session->data['user_token'], 'SSL');
    }

}

?>