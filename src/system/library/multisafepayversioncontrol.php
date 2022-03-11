<?php

class Multisafepayversioncontrol {

    public function __construct($registry) {
        $this->registry = $registry;
        $this->oc_version = VERSION;
        $this->extension_type = 'payment';
        $this->extension_key  = 'multisafepay';
        $this->lowest_version_supported = '2.0.0.0';
        $this->higher_version_supported = '3.0.3.8';
    }

    /**
     * Magic method that returns any object used in OpenCart from registry object
     * when has not been found inside this class
     *
     * @param string $name
     * @return object
     *
     */
    public function __get($name) {
        return $this->registry->get($name);
    }

    /**
     * Return OpenCart version needed to compare what to do on each version
     * or false is version is not supported by the plugin
     *
     * @return mixed bool|string
     *
     */
    public function getOcVersion() {
        if((version_compare($this->oc_version, '3.0.0.0', '>=') && version_compare($this->oc_version, $this->higher_version_supported, '<='))) {
            return '3.0';
        }
        if((version_compare($this->oc_version, '2.3.0.0', '>=') && version_compare($this->oc_version, '2.3.0.2', '<='))) {
            return '2.3';
        }
        if(version_compare($this->oc_version, '2.2.0.0', '==')) {
            return '2.2';
        }
        if((version_compare($this->oc_version, '2.1.0.0', '>=') && version_compare($this->oc_version, '2.1.0.2', '<='))) {
            return '2.1';
        }
        if((version_compare($this->oc_version, '2.0.0.0', '>=') && version_compare($this->oc_version, '2.0.3.1', '<='))) {
            return '2.0';
        }
        if((version_compare($this->oc_version, '2.0.0.0', '<') && version_compare($this->oc_version, $this->higher_version_supported, '>'))) {
            return false;
        }
    }

    /**
     * Return OpenCart Key Prefix to be attached into the variables for payment extensions.
     * Since OpenCart 3, extensions must use a prefix for each of their variables.
     * Some acceptable values are: payment_, shipping_, module_, captcha_, analytics_, dashboard_, themes_.
     *
     * @return string
     *
     */
    public function getKeyPrefix() {
        $oc_version = $this->getOcVersion();
        if($oc_version === '3.0') {
            return $this->extension_type . '_';
        }
        if($oc_version === '2.3' || $oc_version === '2.2' || $oc_version === '2.1' || $oc_version === '2.0') {
            return '';
        }
    }


    /**
     * Return OpenCart Key Prefix to be attached into the variables for shipping extensions.
     * Since OpenCart 3, extensions must use a prefix for each of their variables.
     * Some acceptable values are: payment_, shipping_, module_, captcha_, analytics_, dashboard_, themes_.
     *
     * @return string
     *
     */
    public function getShippingKeyPrefix() {
        $oc_version = $this->getOcVersion();
        if($oc_version === '3.0') {
            return 'shipping_';
        }
        if($oc_version === '2.3' || $oc_version === '2.2' || $oc_version === '2.1' || $oc_version === '2.0') {
            return '';
        }
    }

    /**
     * Return Extension Route.
     * Since OpenCart 2.3, there was an important change in folder structure
     *
     * For example:
     * Prior to 2.3, the controller of a payment extension was located in controller/payment/
     * Since 2.3, the controller of a payment extension will pass to be controller/extension/payment/
     *
     * @return string
     *
     */
    public function getExtensionRoute() {
        $oc_version = $this->getOcVersion();
        if($oc_version === '2.3' || $oc_version === '3.0') {
            return 'extension/' . $this->extension_type . '/' . $this->extension_key;
        }
        if($oc_version === '2.2' || $oc_version === '2.1' || $oc_version === '2.0') {
            return $this->extension_type . '/' . $this->extension_key;
        }
    }


    /**
     * Return Extension Route to print the view.
     * Since OpenCart 2.2, there was an important change in the function that render the view
     *
     * For example:
     * Prior to 2.2, the file extension should pass in the load->view function
     * Since 2.2, there is no need to pass the extension of the file in the load->view function
     *
     * @return string
     *
     */
    public function getFileExtensionView() {
        $oc_version = $this->getOcVersion();
        if($oc_version === '2.1' || $oc_version === '2.0') {
            return '.tpl';
        }
        return '';
    }

    /**
     * Return Extension Directory Route.
     * Since OpenCart 2.3, there was an important change in folder structure
     *
     * For example:
     * Prior to 2.3, the controller of a payment extension was located in controller/payment/
     * Since 2.3, the controller of a payment extension will pass to be controller/extension/payment/
     *
     * @return string
     *
     */
    public function getExtensionDirectoryRoute() {
        $oc_version = $this->getOcVersion();
        if($oc_version === '2.3' || $oc_version === '3.0') {
            return 'extension/';
        }
        if($oc_version === '2.2' || $oc_version === '2.1' || $oc_version === '2.0') {
            return '';
        }
    }


    /**
     * Return the route for the extension list page in admin side.
     * This route is important because is used in breadcrumbs and redirect after process the form
     * Since OpenCart 2.3 there are changes in this. It changes again in OpenCart 3.0
     *
     * For example:
     * Prior to 2.3, there was an extension list for each type of extension. i.e., extension/payment
     * In OpenCart 2.3 there was a list for all extensions and a selector to filter i.e., extension/extension
     * In OpenCart 3.0 the name extension was substituted because the introduction of new features. i.e., marketplace/extension
     *
     * @return string
     *
     */
    public function getExtensionListRoute() {
        $oc_version = $this->getOcVersion();
        if($oc_version === '3.0') {
            return 'marketplace/extension';
        }
        if($oc_version === '2.3') {
            return 'extension/extension';
        }
        if($oc_version === '2.2' || $oc_version === '2.1' || $oc_version === '2.0') {
            return 'extension/' . $this->extension_type;
        }
    }

    /**
     * Return the name for the variable token according with OpenCart version.
     * Since OpenCart 3.0 token name is "user_token"; before that is "token"
     *
     *
     * @return string
     *
     */
    public function getTokenName() {
        $oc_version = $this->getOcVersion();
        if($oc_version === '3.0') {
            return 'user_token';
        }
        if($oc_version === '2.3' || $oc_version === '2.2' || $oc_version === '2.1' || $oc_version === '2.0') {
            return 'token';
        }
    }

    /**
     * Return the route to make calls to the model of the plugin
     * Model calls are conditioned by the structure of the directories
     *
     * @return string
     *
     */
    public function getStandartModelCall() {
        $oc_version = $this->getOcVersion();
        if($oc_version === '2.3' || $oc_version === '3.0') {
            return 'model_extension_' . $this->extension_type . '_' . $this->extension_key;
        }
        if($oc_version === '2.2' || $oc_version === '2.1' || $oc_version === '2.0') {
            return 'model_' . $this->extension_type . '_' . $this->extension_key;
        }
    }


    /**
     * Return the route to make calls to the model of other plugins
     * Model calls are conditioned by the structure of the directories
     *
     * @return string
     *
     */
    public function getNonStandartModelCall() {
        $oc_version = $this->getOcVersion();
        if($oc_version === '2.3' || $oc_version === '3.0') {
            return 'model_extension';
        }
        if($oc_version === '2.2' || $oc_version === '2.1' || $oc_version === '2.0') {
            return 'model';
        }
    }



    /**
     * Return the total extension prefix
     * Settings key variables are conditioned by the structure of the directories
     *
     * @return string
     *
     */
    public function getTotalExtensionPrefix() {
        $oc_version = $this->getOcVersion();
        if($oc_version === '3.0') {
            return 'total_';
        }
        if($oc_version === '2.3' || $oc_version === '2.2' || $oc_version === '2.1' || $oc_version === '2.0') {
            return '';
        }
    }

    /**
     * Return if current OpenCart version support autoload language strings
     *
     * @return bool
     *
     */
    public function hasLanguageAutoloadSupport() {
        $oc_version = $this->getOcVersion();
        if($oc_version === '3.0') {
            return true;
        }
        return false;
    }

    /**
     * Return if current OpenCart version support autoload language strings
     *
     * @return bool
     *
     */
    public function hasEventsSystemSupportSupport() {
        $oc_version = $this->getOcVersion();
        if($oc_version === '2.3' || $oc_version === '2.2'|| $oc_version === '3.0') {
            return true;
        }
        return false;
    }

    /**
     * Return view according with OC version
     *
     * @return string $data
     *
     */
    public function getViewAccordingWithOcVersion($route, $data) {
        $oc_version = $this->getOcVersion();
        if($oc_version === '2.0' || $oc_version === '2.1') {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/' . $route)) {
                return $this->load->view($this->config->get('config_template') . '/template/' . $route, $data);
            }
            if (!file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/' . $route)) {
                return $this->load->view('default/template/' . $route, $data);
            }
        }
        if($oc_version === '2.2' || $oc_version === '2.3'|| $oc_version === '3.0') {
            return $this->load->view($this->route . $this->view_extension_file, $data);
        }
    }

    /**
     * Return the keys of each language string.
     * Since OpenCart 3.0 language strings loads automatically
     * Prior of OpenCart 3.0 each language string must pass to controller to view using the Language->get($key) function
     * after include the file.
     *
     * @param array $additional_keys
     * @return array $data
     *
     */
    public function getLanguageKeys($route, $additional_keys = array()) {
        $oc_version = $this->getOcVersion();

        if($oc_version === '3.0') {
            $this->load->language($this->getExtensionRoute());
            return array();
        }
        if($oc_version === '2.0' || $oc_version === '2.1' || $oc_version === '2.2' || $oc_version === '2.3') {
            $this->load->language($this->getExtensionRoute());
            $language_code = $this->getCurrentLanguageCode();

            $default = 'en-gb';

            if((version_compare($this->oc_version, '2.0.0.0', '>=') && version_compare($this->oc_version, '2.1.0.2', '<='))) {
                $default = 'english';
            }

            // phpcs:ignore ObjectCalisthenics.NamingConventions.ElementNameMinimalLength
            $_ = array();

            $file = DIR_LANGUAGE . $default . '/' . $route . '.php';
            if (is_file($file)) {
                require($file);
            }

            $file = DIR_LANGUAGE . $language_code . '/' . $route . '.php';
            if (is_file($file)) {
                require($file);
            }

            // phpcs:ignore ObjectCalisthenics.NamingConventions.ElementNameMinimalLength
            $keys = array_merge($additional_keys, array_keys($_));

            foreach ($keys as $key) {
                $data[$key] = $this->language->get($key);
            }

            return $data;
        }
    }

    /**
     * Return the language code used in admin or catalog to be used in getLanguageKeys function
     *
     * @return string
     *
     */
    private function getCurrentLanguageCode() {
        $this->load->model('localisation/language');
        $language_info = $this->model_localisation_language->getLanguage($this->config->get('config_language_id'));
        return $language_info['code'];
    }


    public function getCustomerGroupModelRoute() {
        $oc_version = $this->getOcVersion();
        if($oc_version === '2.0') {
            return 'sale/customer_group';
        }
        if($oc_version == '2.1' || $oc_version == '2.2' || $oc_version == '2.3' || $oc_version == '3.0' ) {
            return 'customer/customer_group';
        }
    }

    public function getCustomerGroupModelCall() {
        $oc_version = $this->getOcVersion();
        if($oc_version == '2.0') {
            return 'model_sale_customer_group';
        }
        if($oc_version == '2.1' || $oc_version == '2.2' || $oc_version == '2.3' || $oc_version == '3.0' ) {
            return 'model_customer_customer_group';
        }
    }


    public function getCustomerModelRoute() {
        $oc_version = $this->getOcVersion();
        if($oc_version === '2.0') {
            return 'sale/customer';
        }
        if($oc_version == '2.1' || $oc_version == '2.2' || $oc_version == '2.3' || $oc_version == '3.0' ) {
            return 'customer/customer';
        }
    }

    public function getCustomerModelCall() {
        $oc_version = $this->getOcVersion();
        if($oc_version == '2.0') {
            return 'model_sale_customer';
        }
        if($oc_version == '2.1' || $oc_version == '2.2' || $oc_version == '2.3' || $oc_version == '3.0' ) {
            return 'model_customer_customer';
        }
    }
}
