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
class ControllerExtensionPaymentMultiSafePayKlarna extends Controller
{

    private $error = array();

    public function index()
    {
        $this->load->language('extension/payment/multisafepay');
        $this->load->language('extension/payment/multisafepay_klarna');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        $this->load->model('setting/store');
        $data['stores'] = $this->model_setting_store->getStores();


        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('multisafepay_klarna', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', 'SSL'));
        }


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');


        /* $do = false;
          $this->load->model('setting/extension');
          $totals = $this->model_setting_extension->getInstalled('total');
          foreach ($totals as $total) {
          if ($total == 'multisafepayklarnafee') {
          $do = true;
          break;
          }
          }
          if (!$do) {
          $this->model_setting_extension->install('total', 'multisafepayklarnafee');
          $post['multisafepayklarnafee_sort_order'] = 4;
          $post['multisafepayklarnafee_status'] = 1;
          $this->model_setting_setting->editSetting('multisafepayklarnafee', $post);
          } */


        $data['entry_paymentfee'] = $this->language->get('entry_paymentfee');
        $data['entry_multisafepay_klarna_tax'] = $this->language->get('entry_multisafepay_klarna_tax');
        $this->load->model('localisation/tax_class');
        $data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
        $data['text_none'] = $this->language->get('text_none');

        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        // Geo Zone
        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $data['action'] = $this->setup_link('extension/payment/multisafepay_klarna');
        $data['cancel'] = $this->setup_link('extension/extension');

        $data['text_set_order_status'] = $this->language->get('text_set_order_status');
        $data['text_free_account'] = $this->language->get('text_free_account');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['entry_multisafepay_merchantid'] = $this->language->get('klarna_multisafepay_merchantid');
        $data['entry_multisafepay_siteid'] = $this->language->get('klarna_multisafepay_siteid');
        $data['entry_multisafepay_secure_code'] = $this->language->get('klarna_multisafepay_secure_code');
        $data['entry_environment'] = $this->language->get('entry_environment');

        //start klarna data
        $data['text_multisafepay_klarna_min_amount'] = $this->language->get('text_min_klarna_amount');
        $data['text_multisafepay_klarna_max_amount'] = $this->language->get('text_max_klarna_amount');
        $data['text_set_klarna_data'] = $this->language->get('text_set_klarna_data');
        $data['text_multisafepay_klarna_ip_validation_option'] = $this->language->get('text_klarna_ip_validation_option');
        $data['text_multisafepay_klarna_ip_validation_address'] = $this->language->get('text_klarna_ip_validation_address');

        //end klarna data
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');


        if (isset($this->request->post['multisafepay_klarna_geo_zone_id_0'])) {
            $data['multisafepay_klarna_geo_zone_id'] = $this->request->post['multisafepay_klarna_geo_zone_id_0'];
        } else {
            $data['multisafepay_klarna_geo_zone_id'] = $this->config->get('multisafepay_klarna_geo_zone_id_0');
        }

        if (isset($this->request->post['multisafepay_klarna_ip_validation_address_0'])) {
            $data['multisafepay_klarna_ip_validation_address'] = $this->request->post['multisafepay_klarna_ip_validation_address_0'];
        } else {
            $data['multisafepay_klarna_ip_validation_address'] = $this->config->get('multisafepay_klarna_ip_validation_address_0');
        }


        if (isset($this->request->post['multisafepay_klarna_ip_validation_enabler_0'])) {
            $data['multisafepay_klarna_ip_validation_enabler'] = $this->request->post['multisafepay_klarna_ip_validation_enabler_0'];
        } else {
            $data['multisafepay_klarna_ip_validation_enabler'] = $this->config->get('multisafepay_klarna_ip_validation_enabler_0');
        }

        if (isset($this->request->post['multisafepay_klarna_max_amount_0'])) {
            $data['multisafepay_klarna_max_amount'] = $this->request->post['multisafepay_klarna_max_amount_0'];
        } else {
            $data['multisafepay_klarna_max_amount'] = $this->config->get('multisafepay_klarna_max_amount_0');
        }
        if (isset($this->request->post['multisafepay_klarna_min_amount_0'])) {
            $data['multisafepay_klarna_min_amount'] = $this->request->post['multisafepay_klarna_min_amount_0'];
        } else {
            $data['multisafepay_klarna_min_amount'] = $this->config->get('multisafepay_klarna_min_amount_0');
        }

        if (isset($this->request->post['multisafepay_klarna_merchant_id_0'])) {
            $data['multisafepay_klarna_merchant_id'] = $this->request->post['multisafepay_klarna_merchant_id_0'];
        } else {
            $data['multisafepay_klarna_merchant_id'] = $this->config->get('multisafepay_klarna_merchant_id_0');
        }

        if (isset($this->request->post['multisafepay_klarna_site_id_0'])) {
            $data['multisafepay_klarna_site_id'] = $this->request->post['multisafepay_klarna_site_id_0'];
        } else {
            $data['multisafepay_klarna_site_id'] = $this->config->get('multisafepay_klarna_site_id_0');
        }

        if (isset($this->request->post['multisafepay_klarna_secure_code_0'])) {
            $data['multisafepay_klarna_secure_code'] = $this->request->post['multisafepay_klarna_secure_code_0'];
        } else {
            $data['multisafepay_klarna_secure_code'] = $this->config->get('multisafepay_klarna_secure_code_0');
        }

        if (isset($this->request->post['multisafepay_klarna_environment_0'])) {
            $data['multisafepay_klarna_environment'] = $this->request->post['multisafepay_klarna_environment_0'];
        } else {
            $data['multisafepay_klarna_environment'] = $this->config->get('multisafepay_klarna_environment_0');
        }




        if (isset($this->request->post['multisafepay_klarna_status'])) {
            $data['multisafepay_klarna_status'] = $this->request->post['multisafepay_klarna_status'];
        } else {
            $data['multisafepay_klarna_status'] = $this->config->get('multisafepay_klarna_status');
        }


        if (isset($this->request->post['multisafepay_klarna_sort_order_0'])) {
            $data['multisafepay_klarna_sort_order'] = $this->request->post['multisafepay_klarna_sort_order_0'];
        } else {
            $data['multisafepay_klarna_sort_order'] = $this->config->get('multisafepay_klarna_sort_order_0');
        }



        foreach ($this->model_setting_store->getStores() as $store) {
            if (isset($this->request->post['multisafepay_klarna_geo_zone_id_' . $store['store_id'] . ''])) {
                $data['multisafepay_klarna_geo_zone_id_' . $store['store_id'] . ''] = $this->request->post['multisafepay_klarna_geo_zone_id_' . $store['store_id'] . ''];
            } else {
                $data['multisafepay_klarna_geo_zone_id_' . $store['store_id'] . ''] = $this->config->get('multisafepay_klarna_geo_zone_id_' . $store['store_id']);
            }
            if (isset($this->request->post['multisafepay_klarna_ip_validation_address_' . $store['store_id'] . ''])) {
                $data['multisafepay_klarna_ip_validation_address_' . $store['store_id'] . ''] = $this->request->post['multisafepay_klarna_ip_validation_address_' . $store['store_id'] . ''];
            } else {
                $data['multisafepay_klarna_ip_validation_address_' . $store['store_id'] . ''] = $this->config->get('multisafepay_klarna_ip_validation_address_' . $store['store_id']);
            }
            if (isset($this->request->post['multisafepay_klarna_ip_validation_enabler_' . $store['store_id'] . ''])) {
                $data['multisafepay_klarna_ip_validation_enabler_' . $store['store_id'] . ''] = $this->request->post['multisafepay_klarna_ip_validation_enabler_' . $store['store_id'] . ''];
            } else {
                $data['multisafepay_klarna_ip_validation_enabler_' . $store['store_id'] . ''] = $this->config->get('multisafepay_klarna_ip_validation_enabler_' . $store['store_id']);
            }
            if (isset($this->request->post['multisafepay_klarna_max_amount_' . $store['store_id'] . ''])) {
                $data['multisafepay_klarna_max_amount_' . $store['store_id'] . ''] = $this->request->post['multisafepay_klarna_max_amount_' . $store['store_id'] . ''];
            } else {
                $data['multisafepay_klarna_max_amount_' . $store['store_id'] . ''] = $this->config->get('multisafepay_klarna_max_amount_' . $store['store_id']);
            }
            if (isset($this->request->post['multisafepay_klarna_min_amount_' . $store['store_id'] . ''])) {
                $data['multisafepay_klarna_min_amount_' . $store['store_id'] . ''] = $this->request->post['multisafepay_klarna_min_amount_' . $store['store_id'] . ''];
            } else {
                $data['multisafepay_klarna_min_amount_' . $store['store_id'] . ''] = $this->config->get('multisafepay_klarna_min_amount_' . $store['store_id']);
            }
            if (isset($this->request->post['multisafepay_klarna_merchant_id_' . $store['store_id'] . ''])) {
                $data['multisafepay_klarna_merchant_id_' . $store['store_id'] . ''] = $this->request->post['multisafepay_klarna_merchant_id_' . $store['store_id'] . ''];
            } else {
                $data['multisafepay_klarna_merchant_id_' . $store['store_id'] . ''] = $this->config->get('multisafepay_klarna_merchant_id_' . $store['store_id']);
            }
            if (isset($this->request->post['multisafepay_klarna_site_id_' . $store['store_id'] . ''])) {
                $data['multisafepay_klarna_site_id_' . $store['store_id'] . ''] = $this->request->post['multisafepay_klarna_site_id_' . $store['store_id'] . ''];
            } else {
                $data['multisafepay_klarna_site_id_' . $store['store_id'] . ''] = $this->config->get('multisafepay_klarna_site_id_' . $store['store_id']);
            }
            if (isset($this->request->post['multisafepay_klarna_secure_code_' . $store['store_id'] . ''])) {
                $data['multisafepay_klarna_secure_code_' . $store['store_id'] . ''] = $this->request->post['multisafepay_klarna_secure_code_' . $store['store_id'] . ''];
            } else {
                $data['multisafepay_klarna_secure_code_' . $store['store_id'] . ''] = $this->config->get('multisafepay_klarna_secure_code_' . $store['store_id']);
            }

            if (isset($this->request->post['multisafepay_klarna_environment_' . $store['store_id'] . ''])) {
                $data['multisafepay_klarna_environment_' . $store['store_id'] . ''] = $this->request->post['multisafepay_klarna_environment_' . $store['store_id'] . ''];
            } else {
                $data['multisafepay_klarna_environment_' . $store['store_id'] . ''] = $this->config->get('multisafepay_klarna_environment_' . $store['store_id']);
            }
            if (isset($this->request->post['multisafepay_klarna_sort_order_' . $store['store_id'] . ''])) {
                $data['multisafepay_klarna_sort_order_' . $store['store_id'] . ''] = $this->request->post['multisafepay_klarna_sort_order_' . $store['store_id'] . ''];
            } else {
                $data['multisafepay_klarna_sort_order_' . $store['store_id'] . ''] = $this->config->get('multisafepay_klarna_sort_order_' . $store['store_id']);
            }
        }


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->setup_link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->setup_link('extension/extension'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->setup_link('extension/payment/multisafepay_klarna'),
            'separator' => ' :: '
        );


        $this->template = 'extension/payment/multisafepay_klarna.tpl';
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this->template, $data));
    }

    private function setup_link($route)
    {
        $link = $this->url->link($route, 'token=' . $this->session->data['token'], 'SSL');
        return $link;
    }

}

?>