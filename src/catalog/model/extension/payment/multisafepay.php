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

class ModelExtensionPaymentMultiSafePay extends Model {

    const MAX_PAYMENT_METHOD_LENGTH = 128;

    /**
     * Retrieves MultiSafepay as payment method
     *
     * @param array $address
     * @param double $total
     * @return mixed bool|array $method_data
     *
     */
    public function getMethod($address, $total) {

        if ($total == 0 || !$this->config->get('payment_multisafepay_status')) {
            return false;
        }

        $status = true;

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'multisafepay',
                'title' => $this->getTitle(),
                'terms' => '',
                'sort_order' => $this->config->get('payment_multisafepay_sort_order')
            );
        }

        return $method_data;
    }

    /**
     * Retrieves allowed MultiSafepay payment methods
     *
     * @param array $address
     * @param double $total
     * @return mixed bool|array $method_data
     *
     */
    public function getMethods($address, $total) {

        if ($total == 0 || !$this->config->get('payment_multisafepay_status')) {
            return false;
        }

        $this->load->language('extension/payment/multisafepay');

        $this->load->model('localisation/currency');

        $this->registry->set('multisafepay', new Multisafepay($this->registry));

        $gateways = $this->multisafepay->getOrderedGateways($this->config->get('config_store_id'));

        $methods_data = array();

        foreach ($gateways as $key => $gateway) {
            // if enable
            if (!$this->config->get('payment_multisafepay_'.$gateway['code'].'_status')) {
                continue;
            }

            // if order amount is higher than minimum amount
            if ($this->config->get('payment_multisafepay_'.$gateway['code'].'_min_amount') > 0 && $this->config->get('payment_multisafepay_'.$gateway['code'].'_min_amount') > $total) {
                continue;
            }

            // if order amount is lower than maximun amount
            if ($this->config->get('payment_multisafepay_'.$gateway['code'].'_max_amount') > 0 && $this->config->get('payment_multisafepay_'.$gateway['code'].'_max_amount') < $total) {
                continue;
            }

            // if order currency
            $currencies = $this->config->get('payment_multisafepay_'.$gateway['code'].'_currency');
            $currency_info = $this->model_localisation_currency->getCurrencyByCode($this->session->data['currency']);
            if ($this->config->get('payment_multisafepay_'.$gateway['code'].'_currency') && !in_array($currency_info['currency_id'], $currencies)) {
                continue;
            }

            // if customer is logged and customer group is not
            $allowed_customer_groups_id = $this->config->get('payment_multisafepay_'.$gateway['code'].'_customer_group_id');
            $customer_group_id = $this->customer->getGroupId();
            if ($this->config->get('payment_multisafepay_'.$gateway['code'].'_customer_group_id') && !in_array($customer_group_id, $allowed_customer_groups_id)) {
                continue;
            }

            $query = $this->db->query(
                "SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE 
                geo_zone_id = '" . (int)$this->config->get('payment_multisafepay_'.$gateway['code'].'_geo_zone_id') . "' AND 
                country_id = '" . (int)$address['country_id'] . "' 
                AND (zone_id = '" . (int)$address['zone_id'] . "' 
                OR zone_id = '0')"
            );

            if ($this->config->get('payment_multisafepay_'.$gateway['code'].'_geo_zone_id') && !$query->num_rows) {
                continue;
            }

            $methods_data[] = array(
                'code' => $gateway['route'],
                'title' => $this->getTitle($gateway['description'], $gateway['image']),
                'terms' => '',
                'sort_order' => $this->config->get('payment_multisafepay_'.$gateway['code'].'_sort_order')
            );
        }

        $sort_order = array();
        foreach ($methods_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }
        array_multisort($sort_order, SORT_ASC, $methods_data);

        return $methods_data;
    }

    /**
     * Retrieves MultiSafepay payment methods titles and images,
     * if this one has been setup in the settings
     *
     * @param string $title
     * @param string $image
     * @return string $title
     *
     */
    private function getTitle($title = 'MultiSafepay', $image = 'wallet') {

        $this->registry->set('multisafepay', new Multisafepay($this->registry));
        $shop_url = $this->multisafepay->getShopUrl();
        $locale_code = $this->language->get('code');

        if (!$this->config->get('payment_multisafepay_use_payment_logo') || !$image) {
            return $title;
        }

        if(!file_exists(DIR_IMAGE . 'catalog/multisafepay/' . $image . '.png') && !file_exists(DIR_IMAGE . 'catalog/multisafepay/' . $image . '-' . $locale_code . 'png')) {
            return $title;
        }

        if(!file_exists(DIR_IMAGE . 'catalog/multisafepay/' . $image . '-' . $locale_code . '.png')) {
            $logo = '<img height=32 src="' . $shop_url . 'image/catalog/multisafepay/' . $image . '.png" alt="' . $title . '"/>';
            $title_with_logo = $logo . '  ' . $title;
        }

        if(file_exists(DIR_IMAGE . 'catalog/multisafepay/' . $image . '-' . $locale_code . '.png')) {
            $logo = '<img height=32 src="' . $shop_url . 'image/catalog/multisafepay/' . $image . '-' . $locale_code . '.png" alt="' . $title . '"/>';
            $title_with_logo = $logo . '  ' . $title;
        }

        if (mb_strlen($title_with_logo) > self::MAX_PAYMENT_METHOD_LENGTH) {
            return $title;
        }

        return $title_with_logo;
    }

    /**
     * After generates the payment link, in an order generated in the admin,
     * save the payment link in the order history
     *
     * @param int $order_id
     * @param int $order_status_id
     * @param string $comment
     * @param bool $notify
     *
     */
    public function addPaymentLinkToOrderHistory($order_id, $order_status_id, $comment = '', $notify = false) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
    }

    /**
     * In the scenario that a customer change the payment method in the payment page,
     * using second chance; or redirect payment methods, this function edit the payment method for the given order id
     *
     * @param int $order_id
     * @param array $data
     *
     */
    public function editOrderPaymentMethod($order_id, $data = array()) {
        $payment_method_title = $this->getTitle($data['description'], $data['image']);
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET payment_code = '" . $this->db->escape($data['route']) . "', payment_method = '" . $this->db->escape($payment_method_title) . "' WHERE order_id = '" . (int)$order_id . "'");
    }

}