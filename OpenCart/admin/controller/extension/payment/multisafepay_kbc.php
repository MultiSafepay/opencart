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

class ControllerExtensionPaymentMultiSafePaykbc extends Controller
{

    private $error = array();

    public function index()
    {
        $this->load->language('extension/payment/multisafepay');
        $this->load->language('extension/payment/multisafepay_kbc');

        $this->load->model('setting/store');
        $data['stores'] = $this->model_setting_store->getStores();
        $data['stores'][] = array(
            'store_id' => 0,
            'name'     => $this->config->get('config_name'),
            'url'      => HTTP_SERVER . 'index.php?route=common/home&session_id=' . $this->session->getId()
        );

        usort($data['stores'], function($a, $b) {
            return $a['store_id'] - $b['store_id'];
        });


        // Geo Zone
        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == "POST")){
            foreach($data['stores'] as $store) {
                $this->model_setting_setting->editSetting('payment_multisafepay_kbc', $this->request->post['stores'][$store['store_id']], $store['store_id']);
            }
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL'));
            $this->session->data['success'] = $this->language->get('text_success');
        }


        $data['text_edit']              = $this->language->get('text_edit');
        $data['text_enabled']           = $this->language->get('text_enabled');
        $data['text_disabled']          = $this->language->get('text_disabled');
        $data['text_all_zones']         = $this->language->get('text_all_zones');

        $data['action']                 = $this->setup_link('extension/payment/multisafepay_kbc');
        $data['cancel']                 = $this->setup_link('marketplace/extension');
        $data['text_set_order_status']  = $this->language->get('text_set_order_status');
        $data['heading_title']          = $this->language->get('heading_title');
        $data['entry_status']           = $this->language->get('entry_status');
        $data['entry_sort_order']       = $this->language->get('entry_sort_order');
        $data['entry_min_amount']       = $this->language->get('entry_min_amount');
        $data['entry_max_amount']       = $this->language->get('entry_max_amount');

        $data['button_save']            = $this->language->get('button_save');
        $data['button_cancel']          = $this->language->get('button_cancel');
        $data['tab_general']            = $this->language->get('tab_general');

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->setup_link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->setup_link('marketplace/extension'),
            'separator' => ' :: '
        );
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->setup_link('extension/payment/multisafepay_kbc'),
            'separator' => ' :: '
        );


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $fields = array (
            'payment_multisafepay_kbc_status',
            'payment_multisafepay_kbc_min_amount',
            'payment_multisafepay_kbc_max_amount',
            'payment_multisafepay_kbc_geo_zone_id',
            'payment_multisafepay_kbc_sort_order');

        foreach ($data['stores'] as $store) {
            $store_id = $store['store_id'];
            $config = $this->model_setting_setting->getSetting('payment_multisafepay_kbc', $store_id);
            foreach ($fields as $field) {
                if (isset($this->request->post['stores'][$store_id][$field])) {
                    $data['stores'][$store_id][$field] = $this->request->post['stores'][$store_id][$field];
                    continue;
                }
                if (isset($config[$field])) {
                    $data['stores'][$store_id][$field] = $config[$field];
                    continue;
                }
                $data['stores'][$store_id][$field] = null;
            }
        }
        
        $this->template = 'extension/payment/multisafepay_kbc';
        $this->response->setOutput($this->load->view($this->template, $data));
    }

    private function setup_link($route)
    {
        return $link = $this->url->link($route, 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL');
    }

}

?>