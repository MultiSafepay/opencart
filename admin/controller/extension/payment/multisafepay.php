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
        $this->load->model('localisation/geo_zone');
        $this->load->model('setting/store');

        $stores = $this->model_setting_store->getStores();

        $data['stores'][0] = array(
            'store_id' => 0,
            'name'     => $this->config->get('config_name'),
            'url'      => HTTP_SERVER . 'index.php?route=common/home&session_id=' . $this->session->getId()
        );

        foreach ($stores as $store){
            $data['stores'][$store['store_id']] = array(
                'store_id' => $store['store_id'],
                'name'     => $store['name'],
                'url'      => $store['url']
            );
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            foreach($data['stores'] as $store) {
                $this->model_setting_setting->editSetting('payment_multisafepay', $this->request->post['stores'][$store['store_id']], $store['store_id']);
            }
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL'));
            $this->session->data['success'] = $this->language->get('text_success');
        }


        $data['button_save']    = $this->language->get('button_save');
        $data['button_cancel']  = $this->language->get('button_cancel');

        $data['heading_title']  = $this->language->get('heading_title');

        $data['text_edit']      = $this->language->get('text_edit');
        $data['text_all_zones'] = $this->language->get('text_all_zones');

        $data['After']          = $this->language->get('After');
        $data['Before']         = $this->language->get('Before');
        $data['Disabled']       = $this->language->get('Disabled');
        $data['Enabled']        = $this->language->get('Enabled');
        $data['Connect']        = $this->language->get('Connect');
        $data['FastCheckout']   = $this->language->get('FastCheckout');
        $data['Test']           = $this->language->get('Test');
        $data['Live']           = $this->language->get('Live');
        $data['yes']            = $this->language->get('yes');
        $data['no']             = $this->language->get('no');

        $data['entry_account_type']                 = $this->language->get('entry_account_type');
        $data['entry_confirm_order']                = $this->language->get('entry_confirm_order');
        $data['entry_environment']                  = $this->language->get('entry_environment');
        $data['entry_min_amount']                   = $this->language->get('entry_min_amount');
        $data['entry_max_amount']                   = $this->language->get('entry_max_amount');
        $data['entry_multisafepay_b2b']             = $this->language->get('entry_multisafepay_b2b');
        $data['entry_multisafepay_checkout_button'] = $this->language->get('entry_multisafepay_checkout_button');
        $data['entry_multisafepay_days_active']     = $this->language->get('entry_multisafepay_days_active');
        $data['entry_multisafepay_fco_free_ship']   = $this->language->get('entry_multisafepay_fco_free_ship');
        $data['entry_multisafepay_merchantid']      = $this->language->get('entry_multisafepay_merchantid');
        $data['entry_multisafepay_siteid']          = $this->language->get('entry_multisafepay_siteid');
        $data['entry_multisafepay_secure_code']     = $this->language->get('entry_multisafepay_secure_code');
        $data['entry_multisafepay_use_payment_logo']= $this->language->get('entry_multisafepay_use_payment_logo');
        $data['entry_sort_order']                   = $this->language->get('entry_sort_order');
        $data['entry_status']                       = $this->language->get('entry_status');

        $data['entry_multisafepay_order_status_id_initialized']     = $this->language->get('entry_multisafepay_order_status_id_initialized');
        $data['entry_multisafepay_order_status_id_completed']       = $this->language->get('entry_multisafepay_order_status_id_completed');
        $data['entry_multisafepay_order_status_id_uncleared']       = $this->language->get('entry_multisafepay_order_status_id_uncleared');
        $data['entry_multisafepay_order_status_id_reserved']        = $this->language->get('entry_multisafepay_order_status_id_reserved');
        $data['entry_multisafepay_order_status_id_void']            = $this->language->get('entry_multisafepay_order_status_id_void');
        $data['entry_multisafepay_order_status_id_refunded']        = $this->language->get('entry_multisafepay_order_status_id_refunded');
        $data['entry_multisafepay_order_status_id_declined']        = $this->language->get('entry_multisafepay_order_status_id_declined');
        $data['entry_multisafepay_order_status_id_expired']         = $this->language->get('entry_multisafepay_order_status_id_expired');
        $data['entry_multisafepay_order_status_id_shipped']         = $this->language->get('entry_multisafepay_order_status_id_shipped');
        $data['entry_multisafepay_order_status_id_partial_refunded']= $this->language->get('entry_multisafepay_order_status_id_partial_refunded');


        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();


        $fields = array (
            'payment_multisafepay_status',
            'payment_multisafepay_environment',
            'payment_multisafepay_account_type',
            'payment_multisafepay_merchant_id',
            'payment_multisafepay_site_id',
            'payment_multisafepay_secure_code',
            'payment_multisafepay_redirect_url',
            'payment_multisafepay_days_active',
            'payment_multisafepay_use_payment_logo',
            'payment_multisafepay_confirm_order',
            'payment_multisafepay_min_amount',
            'payment_multisafepay_max_amount',
            'payment_multisafepay_geo_zone_id',
            'payment_multisafepay_sort_order',
            'payment_multisafepay_enable_checkout_button_connect',
            'payment_multisafepay_b2b',
            'payment_multisafepay_fco_free_ship',
            'payment_multisafepay_order_status_id_initialized',
            'payment_multisafepay_order_status_id_completed',
            'payment_multisafepay_order_status_id_uncleared',
            'payment_multisafepay_order_status_id_reserved',
            'payment_multisafepay_order_status_id_void',
            'payment_multisafepay_order_status_id_refunded',
            'payment_multisafepay_order_status_id_declined',
            'payment_multisafepay_order_status_id_expired',
            'payment_multisafepay_order_status_id_shipped',
            'payment_multisafepay_order_status_id_partial_refunded');


        foreach ($data['stores'] as $store) {
            $store_id = $store['store_id'];
            $config = $this->model_setting_setting->getSetting('payment_multisafepay', $store_id);
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



        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
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

        $data['action']     = $this->url->link('extension/payment/multisafepay', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['cancel']     = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL');

        $data['header']     = $this->load->controller('common/header');
        $data['column_left']= $this->load->controller('common/column_left');
        $data['footer']     = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/multisafepay', $data));
    }

    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/multisafepay')) {
            $this->error['warning'] = $this->language->get('error_permission');
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