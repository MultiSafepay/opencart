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
class ControllerExtensionPaymentMultiSafePayGezondheidsbon extends Controller
{

    private $error = array();

    public function index()
    {
        $this->load->language('extension/payment/multisafepay');
        $this->load->language('extension/payment/multisafepay_gezondheidsbon');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('multisafepay_gezondheidsbon', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL'));
        }

        $this->load->model('setting/store');
        $data['stores'] = $this->model_setting_store->getStores();

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        // Geo Zone
        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();


        $data['action'] = $this->setup_link('extension/payment/multisafepay_gezondheidsbon');
        $data['cancel'] = $this->setup_link('extension/extension');
        $data['text_set_order_status'] = $this->language->get('text_set_order_status');
        $data['heading_title'] = $this->language->get('heading_title');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['text_min_amount'] = $this->language->get('text_min_amount');
        $data['text_max_amount'] = $this->language->get('text_max_amount');


        if (isset($this->request->post['multisafepay_gezondheidsbon_status'])) {
            $data['multisafepay_gezondheidsbon_status'] = $this->request->post['multisafepay_gezondheidsbon_status'];
        } else {
            $data['multisafepay_gezondheidsbon_status'] = $this->config->get('multisafepay_gezondheidsbon_status');
        }
        if (isset($this->request->post['multisafepay_gezondheidsbon_geo_zone_id_0'])) {
            $data['multisafepay_gezondheidsbon_geo_zone_id'] = $this->request->post['multisafepay_gezondheidsbon_geo_zone_id_0'];
        } else {
            $data['multisafepay_gezondheidsbon_geo_zone_id'] = $this->config->get('multisafepay_gezondheidsbon_geo_zone_id_0');
        }
        if (isset($this->request->post['multisafepay_gezondheidsbon_sort_order_0'])) {
            $data['multisafepay_gezondheidsbon_sort_order'] = $this->request->post['multisafepay_gezondheidsbon_sort_order_0'];
        } else {
            $data['multisafepay_gezondheidsbon_sort_order'] = $this->config->get('multisafepay_gezondheidsbon_sort_order_0');
        }
        if (isset($this->request->post['multisafepay_gezondheidsbon_max_amount_0'])) {
            $data['multisafepay_gezondheidsbon_max_amount'] = $this->request->post['multisafepay_gezondheidsbon_max_amount_0'];
        } else {
            $data['multisafepay_gezondheidsbon_max_amount'] = $this->config->get('multisafepay_gezondheidsbon_max_amount_0');
        }
        if (isset($this->request->post['multisafepay_gezondheidsbon_min_amount_0'])) {
            $data['multisafepay_gezondheidsbon_min_amount'] = $this->request->post['multisafepay_gezondheidsbon_min_amount_0'];
        } else {
            $data['multisafepay_gezondheidsbon_min_amount'] = $this->config->get('multisafepay_gezondheidsbon_min_amount_0');
        }


        foreach ($this->model_setting_store->getStores() as $store) {
            if (isset($this->request->post['multisafepay_gezondheidsbon_geo_zone_id_' . $store['store_id'] . ''])) {
                $data['multisafepay_gezondheidsbon_geo_zone_id_' . $store['store_id'] . ''] = $this->request->post['multisafepay_gezondheidsbon_geo_zone_id_' . $store['store_id'] . ''];
            } else {
                $data['multisafepay_gezondheidsbon_geo_zone_id_' . $store['store_id'] . ''] = $this->config->get('multisafepay_gezondheidsbon_geo_zone_id_' . $store['store_id']);
            }
            if (isset($this->request->post['multisafepay_gezondheidsbon_sort_order_' . $store['store_id'] . ''])) {
                $data['multisafepay_gezondheidsbon_sort_order_' . $store['store_id'] . ''] = $this->request->post['multisafepay_gezondheidsbon_sort_order_' . $store['store_id'] . ''];
            } else {
                $data['multisafepay_gezondheidsbon_sort_order_' . $store['store_id'] . ''] = $this->config->get('multisafepay_gezondheidsbon_sort_order_' . $store['store_id']);
            }
            if (isset($this->request->post['multisafepay_gezondheidsbon_max_amount_' . $store['store_id'] . ''])) {
                $data['multisafepay_gezondheidsbon_max_amount_' . $store['store_id'] . ''] = $this->request->post['multisafepay_gezondheidsbon_max_amount_' . $store['store_id'] . ''];
            } else {
                $data['multisafepay_gezondheidsbon_max_amount_' . $store['store_id'] . ''] = $this->config->get('multisafepay_gezondheidsbon_max_amount_' . $store['store_id']);
            }
            if (isset($this->request->post['multisafepay_gezondheidsbon_min_amount_' . $store['store_id'] . ''])) {
                $data['multisafepay_gezondheidsbon_min_amount_' . $store['store_id'] . ''] = $this->request->post['multisafepay_gezondheidsbon_min_amount_' . $store['store_id'] . ''];
            } else {
                $data['multisafepay_gezondheidsbon_min_amount_' . $store['store_id'] . ''] = $this->config->get('multisafepay_gezondheidsbon_min_amount_' . $store['store_id']);
            }
        }

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['tab_general'] = $this->language->get('tab_general');

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->setup_link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->setup_link('extension/extension'),
            'separator' => ' :: '
        );
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->setup_link('extension/payment/multisafepay_gezondheidsbon'),
            'separator' => ' :: '
        );


        $this->template = 'extension/payment/multisafepay_gezondheidsbon.tpl';
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this->template, $data));
    }

    private function setup_link($route)
    {
        return $link = $this->url->link($route, 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL');
    }

}

?>