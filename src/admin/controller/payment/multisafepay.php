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
 * @author      TechSupport <integration@multisafepay.com>
 * @copyright   Copyright (c) MultiSafepay, Inc. (https://www.multisafepay.com)
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

class ControllerExtensionPaymentMultiSafePay extends Controller {

    private $error = array();

    public function __construct($registry) {
        parent::__construct($registry);
        $this->registry->set('multisafepay_version_control', new Multisafepayversioncontrol($registry));
        $this->oc_version = $this->multisafepay_version_control->getOcVersion();
        $this->key_prefix = $this->multisafepay_version_control->getKeyPrefix();
        $this->route = $this->multisafepay_version_control->getExtensionRoute();
        $this->view_extension_file = $this->multisafepay_version_control->getFileExtensionView();
        $this->extension_list_route = $this->multisafepay_version_control->getExtensionListRoute();
        $this->token_name = $this->multisafepay_version_control->getTokenName();
        $this->model_call = $this->multisafepay_version_control->getStandartModelCall();
        $this->language_autoload_support = $this->multisafepay_version_control->hasLanguageAutoloadSupport();
        $this->oc_events_system_support = $this->multisafepay_version_control->hasEventsSystemSupportSupport();
        $this->extension_directory_route = $this->multisafepay_version_control->getExtensionDirectoryRoute();
        $this->customer_model_route = $this->multisafepay_version_control->getCustomerModelRoute();
        $this->customer_model_call = $this->multisafepay_version_control->getCustomerModelCall();
        $this->customer_group_model_route = $this->multisafepay_version_control->getCustomerGroupModelRoute();
        $this->customer_group_model_call = $this->multisafepay_version_control->getCustomerGroupModelCall();
    }

    /**
     * Handles the settings form page for MultiSafepay payment extension
     *
     */
    public function index() {

        $this->registry->set('multisafepay', new Multisafepay($this->registry));

        $this->load->language($this->route);

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addStyle('view/stylesheet/multisafepay/multisafepay.css');

        $this->document->addScript('/admin/view/javascript/multisafepay/dragula.js');

        $this->load->model('setting/setting');

        $this->load->model($this->route);

        $data = $this->getTexts();

        $data['heading_title'] = $this->language->get('heading_title') . ' <small>v.' . $this->multisafepay->getPluginVersion() . '</small>';

        $data['store_id'] = 0;

        $data['maintenance'] = $this->{$this->model_call}->removeOldExtensionsAndFiles();

        if($data['maintenance']) {
            $this->error['maintenance'] = $this->language->get('text_maintenance_warning');
            $data['files'] = $this->{$this->model_call}->getOldFilesThatCurrentlyExist();
        }

        $data['error_php_version'] = $this->checkPhpVersion();

        $data['needs_upgrade'] = $this->{$this->model_call}->checkForNewVersions();
        if($data['needs_upgrade']) {
            $data['text_needs_upgrade_warning'] = sprintf($this->language->get('text_needs_upgrade_warning'), 'https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=39960');
        }

        if(isset($this->request->get['store_id'])) {
            $data['store_id'] = $this->request->get['store_id'];
        }

        $data['stores'] = $this->getStores();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if ($this->request->post[$this->key_prefix . 'multisafepay_status']) {
                $this->addMultiSafepayEvents();
            }
            if (!$this->request->post[$this->key_prefix . 'multisafepay_status']) {
                $this->deleteMultiSafepayEvents();
            }

            $this->model_setting_setting->editSetting($this->key_prefix . 'multisafepay', $this->request->post, $data['store_id']);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link($this->extension_list_route, $this->token_name . '=' . $this->session->data[$this->token_name] . '&type=payment', true));
        }

        $data['token'] = $this->session->data[$this->token_name];
        $data['token_name'] = $this->token_name;
        $data['key_prefix'] = $this->key_prefix;

        $error_keys = $this->getErrorsKeysAndTypes();
        foreach ($error_keys as $key => $type) {
            if(isset($this->error[$key])) {
                $data['error_' . $key] = $this->error[$key];
            }
            if(!isset($this->error[$key]) && $type === 'string') {
                $data['error_' . $key] = '';
            }
            if(!isset($this->error[$key]) && $type === 'array') {
                $data['error_' . $key] = array();
            }
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $this->token_name . '=' . $this->session->data[$this->token_name], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link($this->extension_list_route, $this->token_name . '=' . $this->session->data[$this->token_name] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->route, $this->token_name . '=' . $this->session->data[$this->token_name], true)
        );

        if(isset($this->request->get['store_id'])) {
            $data['action']     = $this->url->link($this->route, $this->token_name . '=' . $this->session->data[$this->token_name] . '&store_id=' . $data['store_id'], true);
        }
        if(!isset($this->request->get['store_id'])) {
            $data['action']     = $this->url->link($this->route, $this->token_name . '=' . $this->session->data[$this->token_name], true);
        }

        $data['cancel']     = $this->url->link($this->extension_list_route, $this->token_name . '=' . $this->session->data[$this->token_name] . '&type=payment', true);

        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $gateways = $this->multisafepay->getOrderedGateways($data['store_id']);

        foreach ($gateways as $key => $gateway) {
            if(!file_exists(DIR_IMAGE . 'catalog/multisafepay/' . $gateway['image'] . '.png')) {
                $gateways[$key]['image'] = '';
            }
        }

        $data['gateways'] = $gateways;

        $this->load->model($this->customer_group_model_route);
        $data['customer_groups'] = $this->{$this->customer_group_model_call}->getCustomerGroups(array('sort' => 'cg.sort_order'));



        $this->load->model('localisation/currency');
        $data['currencies'] = $this->model_localisation_currency->getCurrencies();

        $fields = $this->getFields();
        $data['payment_methods_fields_values'] = $this->getPaymentMethodsFieldsValues($data['store_id']);

        foreach ($fields as $field) {
            if (isset($this->request->post[$field])) {
                $data[$field] = $this->request->post[$field];
                continue;
            }
            if (!isset($this->request->post[$field])) {
                $data[$field] = $this->{$this->model_call}->getSettingValue($field, $data['store_id']);
                continue;
            }
        }

        $data['header']     = $this->load->controller('common/header');
        $data['column_left']= $this->load->controller('common/column_left');
        $data['footer']     = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this->route . $this->view_extension_file, $data));
    }

    /**
     * Return error message is PHP Version is not supported by the extension
     *
     * @return mixed string|bool
     */
    public function checkPhpVersion() {
        $this->load->language($this->route);
        if(version_compare(PHP_VERSION, '7.1.0', '<')) {
            return $this->language->get('error_php_version');
        }
        return false;
    }

    /**
     * Remove old files from the old extension if the user is upgrading
     *
     */
    public function removeOldFiles() {

        $this->load->language($this->route);

        $this->load->model($this->route);

        $json = array();

        $remove = $this->{$this->model_call}->removeOldFiles();

        if($remove) {
            $json['success'] = $this->language->get('text_remove_old_files_success');
        }
        if(!$remove) {
            $json['error'] = $this->language->get('text_remove_old_files_error');
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }


    /**
     * Uninstall default action for this admin extension controller
     *
     */
    public function uninstall() {
        $this->deleteMultiSafepayEvents();
    }

    /**
     * Add the events to OpenCart; when the extension is enable
     *
     */
    private function addMultiSafepayEvents() {

        if(!$this->oc_version || $this->oc_version == '2.1' || $this->oc_version == '2.0' ) {
            return false;
        }

        $this->load->model($this->route);

        // All payment methods in catalog checkout: msp_all_methods_at_front

        $event_multisafepay_multiple_gateways = $this->{$this->model_call}->getEventByCode('msp_all_methods_at_front');
        if(!$event_multisafepay_multiple_gateways) {
            $this->{$this->model_call}->addEvent('msp_all_methods_at_front',
                'catalog/controller/checkout/payment_method/after',
                $this->extension_directory_route . 'payment/multisafepay/catalogControllerCheckoutPaymentMethodAfter');
        }

        // All payment methods in admin checkout: msp_all_methods_at_back
        $event_api_multisafepay_multiple_gateways = $this->{$this->model_call}->getEventByCode('msp_all_methods_at_back');
        if(!$event_api_multisafepay_multiple_gateways) {
            $this->{$this->model_call}->addEvent('msp_all_methods_at_back',
                'catalog/controller/api/payment/methods/after',
                $this->extension_directory_route . 'payment/multisafepay/catalogControllerApiPaymentMethodsAfter');
        }

        // Set as invoiced the order in MSP: msp_set_invoiced_to_msp
        $event_multisafepay_create_invoice = $this->{$this->model_call}->getEventByCode('msp_set_invoiced_to_msp');
        if(!$event_multisafepay_create_invoice) {
            $this->{$this->model_call}->addEvent('msp_set_invoiced_to_msp',
                'admin/model/sale/order/createInvoiceNo/before',
                $this->extension_directory_route . 'payment/multisafepay/adminModelSaleOrderCreateInvoiceNoBefore');
        }

        // Set MSP tab in admin order view page: msp_set_order_tab
        $event_multisafepay_order_tabs = $this->{$this->model_call}->getEventByCode('msp_set_order_tab');
        if(!$event_multisafepay_order_tabs) {
            $this->{$this->model_call}->addEvent('msp_set_order_tab',
                'admin/view/sale/order_info/before',
                $this->extension_directory_route . 'payment/multisafepay/adminViewSaleOrderInfoBefore');
        }

        // Attach the payment link into the customers email and in the order history: msp_payment_links_at_email
        $event_multisafepay_add_payment_link_order_email = $this->{$this->model_call}->getEventByCode('msp_payment_links_at_email');
        if(!$event_multisafepay_add_payment_link_order_email) {
            if($this->oc_version == '3.0') {
                $this->{$this->model_call}->addEvent('msp_payment_links_at_email',
                    'catalog/view/mail/order_add/before',
                    $this->extension_directory_route . 'payment/multisafepay/catalogViewMailOrderAddBefore');
            }
            if($this->oc_version == '2.3' || $this->oc_version == '2.2') {
                $this->{$this->model_call}->addEvent('msp_payment_links_at_email',
                    'catalog/view/mail/order/before',
                    $this->extension_directory_route . 'payment/multisafepay/catalogViewMailOrderAddBefore');
            }
        }
    }

    /**
     * Delete the events from OpenCart; when the extension is disable or uninstalled
     *
     */
    private function deleteMultiSafepayEvents() {

        if(!$this->oc_version || $this->oc_version == '2.1' || $this->oc_version == '2.0' ) {
            return false;
        }

        $this->load->model($this->route);
        $this->{$this->model_call}->deleteEventByCode('msp_all_methods_at_front');
        $this->{$this->model_call}->deleteEventByCode('msp_all_methods_at_back');
        $this->{$this->model_call}->deleteEventByCode('msp_set_invoiced_to_msp');
        $this->{$this->model_call}->deleteEventByCode('msp_set_order_tab');
        $this->{$this->model_call}->deleteEventByCode('msp_payment_links_at_email');
    }

    /**
     * Define the common fields for each payment method
     *
     * @return array
     *
     */
    private function getPaymentMethodsFields() {
        return array(
            'status',
            'min_amount',
            'max_amount',
            'currency',
            'geo_zone_id',
            'customer_group_id',
            'order_status_id_initialized',
            'sort_order'
        );
    }

    /**
     * Return the values of fields for each payment methods keys
     *
     * @param int $store_id
     * @return array
     *
     */
    private function getPaymentMethodsFieldsValues($store_id = 0) {
        $this->registry->set('multisafepay', new Multisafepay($this->registry));
        $gateways = $this->multisafepay->getGateways();
        $fields = array();
        foreach($gateways as $gateway) {
            $fields = $this->extractFieldsByGateway($gateway, $fields, $store_id);
        }
        return $fields;
    }

    /**
     * Extract the values of fields for each payment methods keys for each gateway
     *
     * @param string $gateway
     * @param array $fields
     * @param int $store_id
     * @return array
     *
     */
    private function extractFieldsByGateway($gateway, $fields, $store_id) {
        $payment_fields = $this->getPaymentMethodsFields();
        foreach ($payment_fields as $payment_field) {
            if (isset($this->request->post[$this->key_prefix . 'multisafepay_'.$gateway['code'].'_'.$payment_field])) {
                $fields[$gateway['code']][$payment_field] = $this->request->post[$this->key_prefix . 'multisafepay_'.$gateway['code'].'_'.$payment_field];
                continue;
            }
            if (!isset($this->request->post[$this->key_prefix . 'multisafepay_'.$gateway['code'].'_'.$payment_field])) {
                $fields[$gateway['code']][$payment_field] = $this->{$this->model_call}->getSettingValue($this->key_prefix . 'multisafepay_'.$gateway['code'].'_'.$payment_field, $store_id);
                continue;
            }
        }
        return $fields;
    }

    /**
     * Return an array of stores to provide multi store support
     *
     * @return array
     *
     */
    public function getStores() {

        $shops = array();

        $shops[0] = array(
            'store_id' => 0,
            'name'     => $this->config->get('config_name'),
             'href'      => $this->url->link($this->route, $this->token_name . '=' . $this->session->data[$this->token_name] . '&store_id=0', true)
        );

        $this->load->model('setting/store');
        $stores = $this->model_setting_store->getStores();

        if($stores) {
            foreach ($stores as $store) {
                $shops[$store['store_id']] = array(
                    'store_id' => $store['store_id'],
                    'name'     => $store['name'],
                    'href'      => $this->url->link($this->route, $this->token_name . '=' . $this->session->data[$this->token_name] . '&store_id=' . $store['store_id'], true)
                );
            }
        }
        return $shops;
    }

    /**
     * Return an array of fields to process the form quickly on submit
     *
     * @return array
     *
     */
    private function getFields() {
        return array(
            $this->key_prefix . 'multisafepay_status',
            $this->key_prefix . 'multisafepay_sort_order',
            $this->key_prefix . 'multisafepay_environment',
            $this->key_prefix . 'multisafepay_debug_mode',
            $this->key_prefix . 'multisafepay_account_type',
            $this->key_prefix . 'multisafepay_sandbox_api_key',
            $this->key_prefix . 'multisafepay_api_key',
            $this->key_prefix . 'multisafepay_google_analytics_account_id',
            $this->key_prefix . 'multisafepay_days_active',
            $this->key_prefix . 'multisafepay_unit_lifetime_payment_link',
            $this->key_prefix . 'multisafepay_second_chance',
            $this->key_prefix . 'multisafepay_use_payment_logo',
            $this->key_prefix . 'multisafepay_generate_payment_links_status',
            $this->key_prefix . 'multisafepay_order_status_id_initialized',
            $this->key_prefix . 'multisafepay_order_status_id_completed',
            $this->key_prefix . 'multisafepay_order_status_id_uncleared',
            $this->key_prefix . 'multisafepay_order_status_id_reserved',
            $this->key_prefix . 'multisafepay_order_status_id_void',
            $this->key_prefix . 'multisafepay_order_status_id_refunded',
            $this->key_prefix . 'multisafepay_order_status_id_declined',
            $this->key_prefix . 'multisafepay_order_status_id_expired',
            $this->key_prefix . 'multisafepay_order_status_id_shipped',
            $this->key_prefix . 'multisafepay_order_status_id_partial_refunded',
            $this->key_prefix . 'multisafepay_order_status_id_cancelled',
            $this->key_prefix . 'multisafepay_order_status_id_initialize_payment_request'
        );
    }

    /**
     * Return an array of error keys to be used in validation functions
     *
     * @return array
     *
     */
    private function getErrorsKeysAndTypes() {
        return array(
            'warning'           => 'string',
            'maintenance'       => 'string',
            'api_key'           => 'string',
            'sandbox_api_key'   => 'string',
            'days_active'       => 'string',
            'gateway'           => 'array',
        );
    }

    /**
     * Compare if the gateway is available in a call to API.
     *
     * @param string $gateway
     * @param array $available_gateways
     * @return mixed null|string
     *
     */
    private function isGatewayAvailable($gateway, $available_gateways) {

        $this->load->language($this->route);

        if(!in_array($gateway['id'], $available_gateways)) {
            return sprintf($this->language->get('error_gateway_not_available'), $gateway['description']);
        }

        return null;

    }

    /**
     * Handles the form validation in setting page.
     *
     * @return mixed bool|array
     *
     */
    protected function validate() {

        $this->load->model($this->route);

        if (!$this->user->hasPermission('modify', $this->route)) {
            $this->error['warning'] = $this->language->get('error_check_form');
            return !$this->error;
        }

        $old_files = $this->{$this->model_call}->removeOldExtensionsAndFiles();
        if($old_files) {
            $this->error['maintenance'] = $this->language->get('text_maintenance_warning');
            return !$this->error;
        }

        if (($this->request->post[$this->key_prefix . 'multisafepay_environment'] == '1') && $this->request->post[$this->key_prefix . 'multisafepay_sandbox_api_key'] === '') {
            $this->error['sandbox_api_key'] = $this->language->get('error_empty_api_key');
        }
        if (($this->request->post[$this->key_prefix . 'multisafepay_environment'] == '0') && $this->request->post[$this->key_prefix . 'multisafepay_api_key'] === '') {
            $this->error['api_key'] = $this->language->get('error_empty_api_key');
        }

        $this->registry->set('multisafepay', new Multisafepay($this->registry));
        $gateways = $this->multisafepay->getGateways();
        $enviroment = (empty($this->request->post[$this->key_prefix . 'multisafepay_environment'])) ? true : false;
        $enviroment_key = (empty($this->request->post[$this->key_prefix . 'multisafepay_environment'])) ? '' : 'sandbox_';
        $api_key = $this->request->post[$this->key_prefix . 'multisafepay_' . $enviroment_key . 'api_key'];
        $available_gateways = $this->multisafepay->getAvailableGateways($enviroment, $api_key);

        if(!$available_gateways) {
            $this->error[$enviroment_key . 'api_key'] = $this->language->get('error_invalid_api_key');
            $this->error['warning'] = $this->language->get('error_check_form');
            return !$this->error;
        }

        foreach($gateways as $gateway) {
            if ($this->request->post[$this->key_prefix . 'multisafepay_'.$gateway['code'].'_status'] && $this->isGatewayAvailable($gateway, $available_gateways) !== null) {
                $this->error['gateway'][$gateway['code']] = $this->isGatewayAvailable($gateway, $available_gateways);
            }
        }

        if(!empty($this->error['gateway'])) {
            $this->error['warning'] = $this->language->get('error_gateways_not_available');
        }

        if (!isset($this->request->post[$this->key_prefix . 'multisafepay_days_active']) || $this->request->post[$this->key_prefix . 'multisafepay_days_active'] < 1) {
            $this->error['days_active'] = $this->language->get('error_days_active');
        }

        if($this->error) {
            $this->error['warning'] = $this->language->get('error_check_form');
        }

        return !$this->error;
    }

    /**
     * Return additional language strings keys
     *
     * @return array $data
     *
     */
    private function getAdditionalTextsKeys() {
        $additional_keys = array(
            'button_save',
            'text_enabled',
            'text_disabled',
            'text_all_zones',
            'text_yes',
            'text_no'
        );
        return $additional_keys;
    }

    /**
     * Load all language strings values and keys into $this->data
     *
     * */
    public function getTexts() {
        $data = $this->multisafepay_version_control->getLanguageKeys($this->route, $this->getAdditionalTextsKeys());
        $support_variables = $this->getSupportTabData();
        $data = array_merge($data, $support_variables);
        return $data;
    }

    /**
     * Return array of texts used in support tab.
     *
     * @return array
     *
     */
    private function getSupportTabData() {

        $this->registry->set('multisafepay', new Multisafepay($this->registry));
        $plugin_version = $this->multisafepay->getPluginVersion();

        $support_variables =  array(
            'support_row_value_multisafepay_version' => $plugin_version,
            'support_row_value_multisafepay_version_oc_supported' => '2.0.0.0, 2.0.1.0, 2.0.1.1, 2.0.2.0, 2.0.3.1, 2.1.0.1, 2.1.0.2, 2.2.0.0, 2.3.0.0, 2.3.0.1, 2.3.0.2, 3.0.0.0, 3.0.0.1, 3.0.0.2, 3.0.1.0, 3.0.1.1, 3.0.1.2, 3.0.2.0, 3.0.3.0, 3.0.3.1, 3.0.3.2, 3.0.3.3, 3.0.3.4, 3.0.3.5, 3.0.3.6',
            'support_manual_link' => 'https://docs.multisafepay.com/integrations/plugins/opencart/?utm_source=opencart&utm_medium=opencart-cms&utm_campaign=opencart-cms',
            'support_changelog_link' => 'https://github.com/MultiSafepay/OpenCart/blob/master/CHANGELOG.md',
            'support_faq_link' => 'https://docs.multisafepay.com/integrations/plugins/opencart/faq/?utm_source=opencart&utm_medium=opencart-cms&utm_campaign=opencart-cms',
            'support_api_documentation_link' => 'https://docs.multisafepay.com/api/?utm_source=opencart&utm_medium=opencart-cms&utm_campaign=opencart-cms',
            'support_multisafepay_github_link' => 'https://github.com/MultiSafepay/OpenCart',
            'support_create_test_account' => 'https://testmerchant.multisafepay.com/signup',
            'support_telephone' => 'tel:+31208500501',
            'support_readable_telephone' => '+31 (0)20 - 8500501',
            'support_email' => 'mailto:sales@multisafepay.com',
            'support_readable_email' => 'sales@multisafepay.com',
            'support_assistance_telephone' => 'tel:+31208500500',
            'support_assistance_readable_telephone' => '+31 (0)20 - 8500500',
            'support_assistance_readable_email' => 'integration@multisafepay.com',
            'support_assistance_email' => 'mailto:integration@multisafepay.com'
        );

        $data['text_row_value_multisafepay_version'] = sprintf(
            $this->language->get('text_row_value_multisafepay_version'),
            $support_variables['support_row_value_multisafepay_version']
        );

        $data['text_row_value_multisafepay_version_oc_supported'] = sprintf(
            $this->language->get('text_row_value_multisafepay_version_oc_supported'),
            $support_variables['support_row_value_multisafepay_version_oc_supported']
        );

        $data['text_manual_link'] = sprintf(
            $this->language->get('text_manual_link'),
            $support_variables['support_manual_link']
        );

        $data['text_changelog_link'] = sprintf(
            $this->language->get('text_changelog_link'),
            $support_variables['support_changelog_link']
        );

        $data['text_faq_link'] = sprintf(
            $this->language->get('text_faq_link'),
            $support_variables['support_faq_link']
        );

        $data['text_api_documentation_link'] = sprintf(
            $this->language->get('text_api_documentation_link'),
            $support_variables['support_api_documentation_link']
        );

        $data['text_multisafepay_github_link'] = sprintf(
            $this->language->get('text_multisafepay_github_link'),
            $support_variables['support_multisafepay_github_link']
        );

        $data['text_create_test_account'] = sprintf(
            $this->language->get('text_create_test_account'),
            $support_variables['support_create_test_account']
        );

        $data['text_telephone'] = sprintf(
            $this->language->get('text_telephone'),
            $support_variables['support_telephone'],
            $support_variables['support_readable_telephone']
        );

        $data['text_email'] = sprintf(
            $this->language->get('text_email'),
            $support_variables['support_email'],
            $support_variables['support_readable_email']
        );

        $data['text_assistance_telephone'] = sprintf(
            $this->language->get('text_assistance_telephone'),
            $support_variables['support_assistance_telephone'],
            $support_variables['support_assistance_readable_telephone']
        );

        $data['text_assistance_email'] = sprintf(
            $this->language->get('text_assistance_email'),
            $support_variables['support_assistance_email'],
            $support_variables['support_assistance_readable_email']
        );

        return $data;
    }

    /**
     * Return array of result of the refund request transaction in JSON format
     *
     * @return mixed bool|json
     *
     */
    public function refundOrder() {

        if (!isset($this->request->get['order_id'])) {
            return false;
        }

        $this->load->language($this->route);

        $json = array();

        $this->registry->set('multisafepay', new Multisafepay($this->registry));

        $msp_order = $this->multisafepay->getOrderObject($this->request->get['order_id']);
        $data['status'] = $msp_order->getStatus();
        $refund_request = $this->multisafepay->createRefundRequestObject($msp_order);
        $refund_request->addMoney($msp_order->getMoney());
        $description = sprintf($this->language->get('text_description_refunded'), $this->request->get['order_id'], date($this->language->get('datetime_format')));
        $refund_request->addDescriptionText($description);

        $payment_details = $msp_order->getPaymentDetails();
        $gateway_id = $payment_details->getType();
        $gateways_with_shopping_cart = array('AFTERPAY', 'KLARNA', 'EINVOICE', 'PAYAFTER');
        if(in_array($gateway_id, $gateways_with_shopping_cart)) {
            $msp_shopping_cart = $msp_order->getShoppingCart();
            $msp_shopping_cart_data = $msp_shopping_cart->getData();
            foreach ($msp_shopping_cart_data['items'] as $msp_cart_item) {
                $checkout_data = $refund_request->getCheckoutData();
                $checkout_data->refundByMerchantItemId($msp_cart_item['merchant_item_id'], $msp_cart_item['quantity']);
            }
        }

        $process_refund = $this->multisafepay->processRefundRequestObject($msp_order, $refund_request);

        if(!$process_refund) {
            $json['error'] = $this->language->get('text_refund_error');
        }

        if($process_refund) {
            $this->load->model($this->route);
            $this->model_extension_payment_multisafepay->addOrderHistory($this->request->get['order_id'], $this->config->get('payment_multisafepay_order_status_id_refunded'), $description);
            $json['success'] = $this->language->get('text_refund_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

    }

    /**
     * Return array of result of the cancel or shipped order update transaction in JSON format
     *
     * @return mixed bool|json
     *
     */
    public function changeMultiSafepayOrderStatusTo() {

        if (!isset($this->request->get['order_id']) || !isset($this->request->get['type']) ) {
            return false;
        }

        $order_id = $this->request->get['order_id'];
        $type = $this->request->get['type'];
        $this->load->language($this->route);
        $json = array();

        // Set Order Status
        $this->registry->set('multisafepay', new Multisafepay($this->registry));
        $this->multisafepay->changeMultiSafepayOrderStatusTo($order_id, $type);
        if($type === 'cancelled') {
            $order_status_id = $this->config->get($this->key_prefix . 'multisafepay_order_status_id_cancelled');
        }
        if($type === 'shipped') {
            $order_status_id = $this->config->get($this->key_prefix . 'multisafepay_order_status_id_shipped');
        }

        if ($this->config->get($this->key_prefix . 'multisafepay_debug_mode')) {
            $this->log->write('OpenCart set the transaction to ' . $type . ' in MultiSafepay, for order ID ' . $order_id . ' and status ID ' . $order_status_id);
        }

        // Update Order Status
        $this->load->model($this->route);
        $description = sprintf($this->language->get('text_description_shipped_or_cancelled'), $type);
        $this->{$this->model_call}->addOrderHistory($order_id, $order_status_id, $description);
        $json['success'] = $this->language->get('text_' . $type . '_success');

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

    }

    /**
     * Returns Order Tab view to be called in custom payment order tabs.
     *
     * @return string
     *
     */
    public function order() {

        if (!isset($this->request->get['order_id'])) {
            return false;
        }

        if($this->oc_version == '3.0') {
            $this->load->language($this->route);
        }

        if($this->oc_version == '2.3' || $this->oc_version == '2.2' || $this->oc_version == '2.1' || $this->oc_version == '2.0') {
            $data = $this->getTexts();
        }

        $this->registry->set('multisafepay', new Multisafepay($this->registry));
        $msp_order = $this->multisafepay->getOrderObject($this->request->get['order_id']);

        if(!$msp_order || !$msp_order->getTransactionId()) {
            return false;
        }

        $data['token_name'] = $this->token_name;
        $data['token'] = $this->session->data[$this->token_name];
        $data['status'] = $msp_order->getStatus();
        $data['order_id'] = $this->request->get['order_id'];
        $data['extension_route'] = $this->route;
        $data[$this->token_name] = $this->session->data[$this->token_name];
        $msp_shopping_cart = $msp_order->getShoppingCart();
        $msp_shopping_cart_data = $msp_shopping_cart->getData();
        $data['items'] = array();
        $subtotal = 0;

        foreach ($msp_shopping_cart_data['items'] as $msp_cart_item) {
            $subtotal = $subtotal + ($msp_cart_item['unit_price'] * $msp_cart_item['quantity']);
            $data['items'][] = array(
                'product_id' => $msp_cart_item['merchant_item_id'],
                'product_name' => $msp_cart_item['name'],
                'product_description' => $msp_cart_item['description'],
                'product_quantity' => $msp_cart_item['quantity'],
                'product_price' => $this->currency->format($msp_cart_item['unit_price'], $msp_order->getCurrency(), 1.00000000, true),
                'product_total' => $this->currency->format(($msp_cart_item['unit_price'] * $msp_cart_item['quantity']), $msp_order->getCurrency(), 1.00000000, true),
            );
        }

        $data['subtotal'] = $this->currency->format($subtotal, $msp_order->getCurrency(), 1.00000000, true);
        $order_adjustment = $msp_order->getOrderAdjustment();
        $total_taxes = $order_adjustment->getTotalTax();
        $data['taxes'] = $this->currency->format($order_adjustment->getTotalTax(), $msp_order->getCurrency(), 1.00000000, true);
        $total = $subtotal + (($total_taxes) ? $total_taxes : 0);
        $data['total'] = $this->currency->format($total, $msp_order->getCurrency(), 1.00000000, true);
        $total = $msp_order->getMoney();
        $data['total'] = $this->currency->format($total->__toString(), $msp_order->getCurrency(), 1.00000000, true);
        return $this->load->view($this->route . '_order' . $this->view_extension_file, $data);

    }

    /**
     * Returns Order Tab Output from order function
     *
     * @return string
     *
     */
    public function refreshOrderTab() {
        $this->response->setOutput($this->order());
    }

    /**
     * Trigger that is called after admin/controller/sale/order/createInvoiceNo
     * using OpenCart events system and overwrites it
     *
     * @param string $route
     * @param array $args
     * @param string $output
     *
     */
    public function adminModelSaleOrderCreateInvoiceNoBefore($route, $args) {
        $this->registry->set('multisafepayevents', new Multisafepayevents($this->registry));
        $this->multisafepayevents->adminModelSaleOrderCreateInvoiceNoBefore($route, $args);
    }

    /**
     * Trigger that is called before admin/view/sale/order_info
     * using OpenCart events system and overwrites it
     *
     * @param string $route
     * @param array $args
     *
     */
    public function adminViewSaleOrderInfoBefore(&$route, &$args) {
        $this->registry->set('multisafepayevents', new Multisafepayevents($this->registry));
        $this->multisafepayevents->adminViewSaleOrderInfoBefore($route, $args);
    }
}

class ControllerPaymentMultiSafePay extends ControllerExtensionPaymentMultiSafePay { }