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

class Helper {

    public function __construct($registry) {
        $this->registry = $registry;
        $this->oc_version = $this->multisafepay_version_control->getOcVersion();
        $this->route = $this->multisafepay_version_control->getExtensionRoute();
        $this->shipping_key_prefix = $this->multisafepay_version_control->getShippingKeyPrefix();
    }

    /**
     * Magic method that returns any object used in OpenCart from registry object
     * when has not been found inside this class
     *
     * @param string $name
     * @return object
     */
    public function __get($name) {
        return $this->registry->get($name);
    }

    /**
     * Return a random order id from database.
     *
     * @return int
     */
    public function getRandomOrderId() {
        $sql = "SELECT order_id FROM `" . DB_PREFIX . "order` ORDER BY RAND() LIMIT 1";
        $query = $this->db->query($sql);
        if($query->rows) {
            return $query->row['order_id'];
        }
    }

    /**
     * Add Coupon to database
     *
     * @param array $data
     * @return int $coupon_id
     *
     */
    public function addCoupon($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "coupon SET name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', discount = '" . (float)$data['discount'] . "', type = '" . $this->db->escape($data['type']) . "', total = '" . (float)$data['total'] . "', logged = '" . (int)$data['logged'] . "', shipping = '" . (int)$data['shipping'] . "', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "', uses_total = '" . (int)$data['uses_total'] . "', uses_customer = '" . (int)$data['uses_customer'] . "', status = '" . (int)$data['status'] . "', date_added = NOW()");
        $coupon_id = $this->db->getLastId();
        return $coupon_id;
    }

    /**
     * Remove Coupon from database
     *
     * @param int $coupon_id
     *
     */
    public function deleteCoupon($coupon_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "coupon WHERE coupon_id = '" . (int)$coupon_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = '" . (int)$coupon_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "coupon_category WHERE coupon_id = '" . (int)$coupon_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "coupon_history WHERE coupon_id = '" . (int)$coupon_id . "'");
    }

    /**
     * Return the tax class id for the shipping flat rate method used in some tests method.
     *
     * @return int $tax_class_id
     *
     */
    public function getTaxClassFromFlatShippingMethod() {
        $key = $this->shipping_key_prefix . 'flat_tax_class_id';
        $query = $this->db->query("SELECT `value` FROM " . DB_PREFIX . "setting WHERE `key`='" . $key . "'");
        if($query->row) {
            return $query->row['value'];
        }
    }

    /**
     * Edit the tax class id for the shipping flat rate method
     *
     * @param int $tax_class_id
     *
     */
    public function editTaxClassFromFlatShippingMethod($tax_class_id) {
        $key = $this->shipping_key_prefix . 'flat_tax_class_id';
        $this->db->query("UPDATE " . DB_PREFIX . "setting SET `value`='" . (int)$tax_class_id . "' WHERE `key`='" . $key . "'");
    }

    /**
     * Get tax class id for the given product id
     *
     * @param int $product_id
     * @return int $tax_class_id
     *
     */
    public function getTaxClassIdByProduct($product_id) {
        $query = $this->db->query("SELECT tax_class_id FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
        if($query->row) {
            return $query->row['tax_class_id'];
        }
    }

    /**
     * Edit tax class id for the given product id
     *
     * @param int $product_id
     * @param int $tax_class_id
     *
     */
    public function editProductTaxClassId($product_id, $tax_class_id) {
        $this->db->query("UPDATE " . DB_PREFIX . "product SET tax_class_id = '" . (int)$tax_class_id . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        $this->cache->delete('product');
    }

    /**
     * Add a tax rate
     *
     * @param array $data
     * @return int $tax_rate_id
     *
     */
    public function addTaxRate($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "tax_rate SET name = '" . $this->db->escape($data['name']) . "', rate = '" . (float)$data['rate'] . "', `type` = '" . $this->db->escape($data['type']) . "', geo_zone_id = '" . (int)$data['geo_zone_id'] . "', date_added = NOW(), date_modified = NOW()");
        $tax_rate_id = $this->db->getLastId();
        if (isset($data['tax_rate_customer_group'])) {
            foreach ($data['tax_rate_customer_group'] as $customer_group_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "tax_rate_to_customer_group SET tax_rate_id = '" . (int)$tax_rate_id . "', customer_group_id = '" . (int)$customer_group_id . "'");
            }
        }
        return $tax_rate_id;
    }

    /**
     * Delete a tax rate
     *
     * @param int $tax_rate_id
     *
     */
    public function deleteTaxRate($tax_rate_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "tax_rate WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "tax_rate_to_customer_group WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
    }

    /**
     * Add a tax class
     *
     * @param array $data
     * @return int $tax_class_id
     *
     */
    public function addTaxClass($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "tax_class SET title = '" . $this->db->escape($data['title']) . "', description = '" . $this->db->escape($data['description']) . "', date_added = NOW()");
        $tax_class_id = $this->db->getLastId();
        if (isset($data['tax_rule'])) {
            foreach ($data['tax_rule'] as $tax_rule) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "tax_rule SET tax_class_id = '" . (int)$tax_class_id . "', tax_rate_id = '" . (int)$tax_rule['tax_rate_id'] . "', based = '" . $this->db->escape($tax_rule['based']) . "', priority = '" . (int)$tax_rule['priority'] . "'");
            }
        }
        $this->cache->delete('tax_class');
        return $tax_class_id;
    }

    /**
     * Delete a tax class
     *
     * @param int $tax_class_id
     *
     */
    public function deleteTaxClass($tax_class_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "tax_class WHERE tax_class_id = '" . (int)$tax_class_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "tax_rule WHERE tax_class_id = '" . (int)$tax_class_id . "'");
    }

    /**
     * Add a geo zone
     *
     * @param array $data
     * @return int $geo_zone_id
     *
     */
    public function addGeoZone($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "geo_zone SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', date_added = NOW()");
        $geo_zone_id = $this->db->getLastId();
        if (isset($data['zone_to_geo_zone'])) {
            foreach ($data['zone_to_geo_zone'] as $value) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "' AND country_id = '" . (int)$value['country_id'] . "' AND zone_id = '" . (int)$value['zone_id'] . "'");

                $this->db->query("INSERT INTO " . DB_PREFIX . "zone_to_geo_zone SET country_id = '" . (int)$value['country_id'] . "', zone_id = '" . (int)$value['zone_id'] . "', geo_zone_id = '" . (int)$geo_zone_id . "', date_added = NOW()");
            }
        }
        $this->cache->delete('geo_zone');
        return $geo_zone_id;
    }

    /**
     * Delete a geo zone
     *
     * @param int $geo_zone_id
     *
     */
    public function deleteGeoZone($geo_zone_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
        $this->cache->delete('geo_zone');
    }

    /**
     * Delete a customer
     *
     * @param int $customer_id
     *
     */
    public function deleteCustomer($customer_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_activity WHERE customer_id = '" . (int)$customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");
        if($this->oc_version === '3.0') {
            $this->db->query("DELETE FROM " . DB_PREFIX . "customer_affiliate WHERE customer_id = '" . (int)$customer_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "customer_approval WHERE customer_id = '" . (int)$customer_id . "'");
        }
    }

    /**
     * Add reward points to customer account
     *
     * @param int $customer_id
     * @param string $description
     * @param float $points
     * @param int $order_id
     *
     */
    public function addReward($customer_id, $description = '', $points = '', $order_id = 0) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', points = '" . (int)$points . "', description = '" . $this->db->escape($description) . "', date_added = NOW()");
    }

    /**
     * Add Voucher
     *
     * @param int $order_id
     * @param array $data
     * @return int $voucher_id
     *
     */
    public function addVoucher($order_id, $data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "voucher SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($data['code']) . "', from_name = '" . $this->db->escape($data['from_name']) . "', from_email = '" . $this->db->escape($data['from_email']) . "', to_name = '" . $this->db->escape($data['to_name']) . "', to_email = '" . $this->db->escape($data['to_email']) . "', voucher_theme_id = '" . (int)$data['voucher_theme_id'] . "', message = '" . $this->db->escape($data['message']) . "', amount = '" . (float)$data['amount'] . "', status = '1', date_added = NOW()");

        return $this->db->getLastId();
    }

    /**
     * Add Order History
     *
     * @param int $order_id
     * @param int $order_status_id
     * @param string $comment
     * @param bool $notify
     * @param bool $override
     *
     */
    public function addOrderHistory($order_id, $order_status_id, $comment = '', $notify = false, $override = false) {
        $order_info = $this->getOrder($order_id);

        if ($order_info) {
            // Fraud Detection
            $this->load->model('account/customer');

            $customer_info = $this->model_account_customer->getCustomer($order_info['customer_id']);

            if ($customer_info && $customer_info['safe']) {
                $safe = true;
            } else {
                $safe = false;
            }

            // Only do the fraud check if the customer is not on the safe list and the order status is changing into the complete or process order status
            if (!$safe && !$override && in_array($order_status_id, array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')))) {
                // Anti-Fraud
                $this->load->model('setting/extension');

                $extensions = $this->model_setting_extension->getExtensions('fraud');

                foreach ($extensions as $extension) {
                    if ($this->config->get('fraud_' . $extension['code'] . '_status')) {
                        $this->load->model('extension/fraud/' . $extension['code']);

                        if (property_exists($this->{'model_extension_fraud_' . $extension['code']}, 'check')) {
                            $fraud_status_id = $this->{'model_extension_fraud_' . $extension['code']}->check($order_info);

                            if ($fraud_status_id) {
                                $order_status_id = $fraud_status_id;
                            }
                        }
                    }
                }
            }

            // If current order status is not processing or complete but new status is processing or complete then commence completing the order
            if (!in_array($order_info['order_status_id'], array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status'))) && in_array($order_status_id, array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')))) {
                // Redeem coupon, vouchers and reward points
                $order_totals = $this->getOrderTotals($order_id);

                foreach ($order_totals as $order_total) {
                    $this->load->model('extension/total/' . $order_total['code']);

                    if (property_exists($this->{'model_extension_total_' . $order_total['code']}, 'confirm')) {
                        // Confirm coupon, vouchers and reward points
                        $fraud_status_id = $this->{'model_extension_total_' . $order_total['code']}->confirm($order_info, $order_total);

                        // If the balance on the coupon, vouchers and reward points is not enough to cover the transaction or has already been used then the fraud order status is returned.
                        if ($fraud_status_id) {
                            $order_status_id = $fraud_status_id;
                        }
                    }
                }

                // Stock subtraction
                $order_products = $this->getOrderProducts($order_id);

                foreach ($order_products as $order_product) {
                    $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");

                    $order_options = $this->getOrderOptions($order_id, $order_product['order_product_id']);

                    foreach ($order_options as $order_option) {
                        $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND subtract = '1'");
                    }
                }

                // Add commission if sale is linked to affiliate referral.
                if ($order_info['affiliate_id'] && $this->config->get('config_affiliate_auto')) {
                    $this->load->model('account/customer');

                    if (!$this->model_account_customer->getTotalTransactionsByOrderId($order_id)) {
                        $this->model_account_customer->addTransaction($order_info['affiliate_id'], $this->language->get('text_order_id') . ' #' . $order_id, $order_info['commission'], $order_id);
                    }
                }
            }

            // Update the DB with the new statuses
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

            $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");

            // If old order status is the processing or complete status but new status is not then commence restock, and remove coupon, voucher and reward history
            if (in_array($order_info['order_status_id'], array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status'))) && !in_array($order_status_id, array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')))) {
                // Restock
                $order_products = $this->getOrderProducts($order_id);

                foreach($order_products as $order_product) {
                    $this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");

                    $order_options = $this->getOrderOptions($order_id, $order_product['order_product_id']);

                    foreach ($order_options as $order_option) {
                        $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND subtract = '1'");
                    }
                }

                // Remove coupon, vouchers and reward points history
                $order_totals = $this->getOrderTotals($order_id);

                foreach ($order_totals as $order_total) {
                    $this->load->model('extension/total/' . $order_total['code']);

                    if (property_exists($this->{'model_extension_total_' . $order_total['code']}, 'unconfirm')) {
                        $this->{'model_extension_total_' . $order_total['code']}->unconfirm($order_id);
                    }
                }

                // Remove commission if sale is linked to affiliate referral.
                if ($order_info['affiliate_id']) {
                    $this->load->model('account/customer');

                    $this->model_account_customer->deleteTransactionByOrderId($order_id);
                }
            }

        }
    }

    /**
     * Delete an order
     *
     * @param int $order_id
     *
     */
    public function deleteOrder($order_id) {
        // Void the order first
        $this->addOrderHistory($order_id, 0);

        $this->db->query("DELETE FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "order_option` WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "order_history` WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE `or`, ort FROM `" . DB_PREFIX . "order_recurring` `or`, `" . DB_PREFIX . "order_recurring_transaction` `ort` WHERE order_id = '" . (int)$order_id . "' AND ort.order_recurring_id = `or`.order_recurring_id");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "customer_transaction` WHERE order_id = '" . (int)$order_id . "'");

        // Gift Voucher
        $this->load->model('extension/total/voucher');
        $this->model_extension_total_voucher->disableVoucher($order_id);
    }

    /**
     * Returns a Sdk object
     *
     * @return Sdk object
     * @throws InvalidApiKeyException
     *
     */
    public function getSdkObject($enviroment, $api_key) {

        $this->language->load($this->route);

        require_once(DIR_SYSTEM . 'library/multisafepay/vendor/autoload.php');

        try {
            $sdk = new \MultiSafepay\Sdk($api_key, $enviroment);
        }
        catch (\MultiSafepay\Exception\InvalidApiKeyException $invalidApiKeyException ) {
            if ($this->config->get('payment_multisafepay_debug_mode')) {
                $this->log->write($invalidApiKeyException->getMessage());
            }
            $this->session->data['error'] = $this->language->get('text_error');
            $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
        }

        return $sdk;

    }

    /**
     * Check if there are orders in database to make tests over them
     *
     */
    public function hasOrders() {
        $sql = "SELECT order_id FROM `" . DB_PREFIX . "order` ORDER BY RAND() LIMIT 1";
        $query = $this->db->query($sql);
        if(!$query->rows) {
            return false;
        }
        if($query->rows) {
            return true;
        }
    }

    /**
     * Generates a token, used in OC 2.0
     *
     * @param int $length
     *
     */
    public function token($length = 32) {
        // Create token to login with
        $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= $string[mt_rand(0, strlen($string) - 1)];
        }

        return $token;
    }

    /**
     * Get Order
     *
     * @param int $order_id
     * @return array
     *
     */
    public function getOrder($order_id) {
        if($this->oc_version === '2.0') {
            $order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");
            if ($order_query->num_rows) {
                $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");
                if ($country_query->num_rows) {
                    $payment_iso_code_2 = $country_query->row['iso_code_2'];
                    $payment_iso_code_3 = $country_query->row['iso_code_3'];
                } else {
                    $payment_iso_code_2 = '';
                    $payment_iso_code_3 = '';
                }
                $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");
                if ($zone_query->num_rows) {
                    $payment_zone_code = $zone_query->row['code'];
                } else {
                    $payment_zone_code = '';
                }
                $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");
                if ($country_query->num_rows) {
                    $shipping_iso_code_2 = $country_query->row['iso_code_2'];
                    $shipping_iso_code_3 = $country_query->row['iso_code_3'];
                } else {
                    $shipping_iso_code_2 = '';
                    $shipping_iso_code_3 = '';
                }
                $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");
                if ($zone_query->num_rows) {
                    $shipping_zone_code = $zone_query->row['code'];
                } else {
                    $shipping_zone_code = '';
                }
                $this->load->model('localisation/language');
                $language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);
                if ($language_info) {
                    $language_code = $language_info['code'];
                } else {
                    $language_code = $this->config->get('config_language');
                }
                return array(
                    'order_id'                => $order_query->row['order_id'],
                    'invoice_no'              => $order_query->row['invoice_no'],
                    'invoice_prefix'          => $order_query->row['invoice_prefix'],
                    'store_id'                => $order_query->row['store_id'],
                    'store_name'              => $order_query->row['store_name'],
                    'store_url'               => $order_query->row['store_url'],
                    'customer_id'             => $order_query->row['customer_id'],
                    'firstname'               => $order_query->row['firstname'],
                    'lastname'                => $order_query->row['lastname'],
                    'email'                   => $order_query->row['email'],
                    'telephone'               => $order_query->row['telephone'],
                    'custom_field'            => unserialize($order_query->row['custom_field']),
                    'payment_firstname'       => $order_query->row['payment_firstname'],
                    'payment_lastname'        => $order_query->row['payment_lastname'],
                    'payment_company'         => $order_query->row['payment_company'],
                    'payment_address_1'       => $order_query->row['payment_address_1'],
                    'payment_address_2'       => $order_query->row['payment_address_2'],
                    'payment_postcode'        => $order_query->row['payment_postcode'],
                    'payment_city'            => $order_query->row['payment_city'],
                    'payment_zone_id'         => $order_query->row['payment_zone_id'],
                    'payment_zone'            => $order_query->row['payment_zone'],
                    'payment_zone_code'       => $payment_zone_code,
                    'payment_country_id'      => $order_query->row['payment_country_id'],
                    'payment_country'         => $order_query->row['payment_country'],
                    'payment_iso_code_2'      => $payment_iso_code_2,
                    'payment_iso_code_3'      => $payment_iso_code_3,
                    'payment_address_format'  => $order_query->row['payment_address_format'],
                    'payment_custom_field'    => unserialize($order_query->row['payment_custom_field']),
                    'payment_method'          => $order_query->row['payment_method'],
                    'payment_code'            => $order_query->row['payment_code'],
                    'shipping_firstname'      => $order_query->row['shipping_firstname'],
                    'shipping_lastname'       => $order_query->row['shipping_lastname'],
                    'shipping_company'        => $order_query->row['shipping_company'],
                    'shipping_address_1'      => $order_query->row['shipping_address_1'],
                    'shipping_address_2'      => $order_query->row['shipping_address_2'],
                    'shipping_postcode'       => $order_query->row['shipping_postcode'],
                    'shipping_city'           => $order_query->row['shipping_city'],
                    'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
                    'shipping_zone'           => $order_query->row['shipping_zone'],
                    'shipping_zone_code'      => $shipping_zone_code,
                    'shipping_country_id'     => $order_query->row['shipping_country_id'],
                    'shipping_country'        => $order_query->row['shipping_country'],
                    'shipping_iso_code_2'     => $shipping_iso_code_2,
                    'shipping_iso_code_3'     => $shipping_iso_code_3,
                    'shipping_address_format' => $order_query->row['shipping_address_format'],
                    'shipping_custom_field'   => unserialize($order_query->row['shipping_custom_field']),
                    'shipping_method'         => $order_query->row['shipping_method'],
                    'shipping_code'           => $order_query->row['shipping_code'],
                    'comment'                 => $order_query->row['comment'],
                    'total'                   => $order_query->row['total'],
                    'order_status_id'         => $order_query->row['order_status_id'],
                    'order_status'            => $order_query->row['order_status'],
                    'affiliate_id'            => $order_query->row['affiliate_id'],
                    'commission'              => $order_query->row['commission'],
                    'language_id'             => $order_query->row['language_id'],
                    'language_code'           => $language_code,
                    'currency_id'             => $order_query->row['currency_id'],
                    'currency_code'           => $order_query->row['currency_code'],
                    'currency_value'          => $order_query->row['currency_value'],
                    'ip'                      => $order_query->row['ip'],
                    'forwarded_ip'            => $order_query->row['forwarded_ip'],
                    'user_agent'              => $order_query->row['user_agent'],
                    'accept_language'         => $order_query->row['accept_language'],
                    'date_added'              => $order_query->row['date_added'],
                    'date_modified'           => $order_query->row['date_modified']
                );
            } else {
                return false;
            }
        }
        if($this->oc_version != '2.0') {
            $order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");
            if ($order_query->num_rows) {
                $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");
                if ($country_query->num_rows) {
                    $payment_iso_code_2 = $country_query->row['iso_code_2'];
                    $payment_iso_code_3 = $country_query->row['iso_code_3'];
                } else {
                    $payment_iso_code_2 = '';
                    $payment_iso_code_3 = '';
                }
                $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");
                if ($zone_query->num_rows) {
                    $payment_zone_code = $zone_query->row['code'];
                } else {
                    $payment_zone_code = '';
                }
                $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");
                if ($country_query->num_rows) {
                    $shipping_iso_code_2 = $country_query->row['iso_code_2'];
                    $shipping_iso_code_3 = $country_query->row['iso_code_3'];
                } else {
                    $shipping_iso_code_2 = '';
                    $shipping_iso_code_3 = '';
                }
                $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");
                if ($zone_query->num_rows) {
                    $shipping_zone_code = $zone_query->row['code'];
                } else {
                    $shipping_zone_code = '';
                }
                $this->load->model('localisation/language');
                $language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);
                if ($language_info) {
                    $language_code = $language_info['code'];
                } else {
                    $language_code = $this->config->get('config_language');
                }
                return array(
                    'order_id'                => $order_query->row['order_id'],
                    'invoice_no'              => $order_query->row['invoice_no'],
                    'invoice_prefix'          => $order_query->row['invoice_prefix'],
                    'store_id'                => $order_query->row['store_id'],
                    'store_name'              => $order_query->row['store_name'],
                    'store_url'               => $order_query->row['store_url'],
                    'customer_id'             => $order_query->row['customer_id'],
                    'firstname'               => $order_query->row['firstname'],
                    'lastname'                => $order_query->row['lastname'],
                    'email'                   => $order_query->row['email'],
                    'telephone'               => $order_query->row['telephone'],
                    'custom_field'            => json_decode($order_query->row['custom_field'], true),
                    'payment_firstname'       => $order_query->row['payment_firstname'],
                    'payment_lastname'        => $order_query->row['payment_lastname'],
                    'payment_company'         => $order_query->row['payment_company'],
                    'payment_address_1'       => $order_query->row['payment_address_1'],
                    'payment_address_2'       => $order_query->row['payment_address_2'],
                    'payment_postcode'        => $order_query->row['payment_postcode'],
                    'payment_city'            => $order_query->row['payment_city'],
                    'payment_zone_id'         => $order_query->row['payment_zone_id'],
                    'payment_zone'            => $order_query->row['payment_zone'],
                    'payment_zone_code'       => $payment_zone_code,
                    'payment_country_id'      => $order_query->row['payment_country_id'],
                    'payment_country'         => $order_query->row['payment_country'],
                    'payment_iso_code_2'      => $payment_iso_code_2,
                    'payment_iso_code_3'      => $payment_iso_code_3,
                    'payment_address_format'  => $order_query->row['payment_address_format'],
                    'payment_custom_field'    => json_decode($order_query->row['payment_custom_field'], true),
                    'payment_method'          => $order_query->row['payment_method'],
                    'payment_code'            => $order_query->row['payment_code'],
                    'shipping_firstname'      => $order_query->row['shipping_firstname'],
                    'shipping_lastname'       => $order_query->row['shipping_lastname'],
                    'shipping_company'        => $order_query->row['shipping_company'],
                    'shipping_address_1'      => $order_query->row['shipping_address_1'],
                    'shipping_address_2'      => $order_query->row['shipping_address_2'],
                    'shipping_postcode'       => $order_query->row['shipping_postcode'],
                    'shipping_city'           => $order_query->row['shipping_city'],
                    'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
                    'shipping_zone'           => $order_query->row['shipping_zone'],
                    'shipping_zone_code'      => $shipping_zone_code,
                    'shipping_country_id'     => $order_query->row['shipping_country_id'],
                    'shipping_country'        => $order_query->row['shipping_country'],
                    'shipping_iso_code_2'     => $shipping_iso_code_2,
                    'shipping_iso_code_3'     => $shipping_iso_code_3,
                    'shipping_address_format' => $order_query->row['shipping_address_format'],
                    'shipping_custom_field'   => json_decode($order_query->row['shipping_custom_field'], true),
                    'shipping_method'         => $order_query->row['shipping_method'],
                    'shipping_code'           => $order_query->row['shipping_code'],
                    'comment'                 => $order_query->row['comment'],
                    'total'                   => $order_query->row['total'],
                    'order_status_id'         => $order_query->row['order_status_id'],
                    'order_status'            => $order_query->row['order_status'],
                    'affiliate_id'            => $order_query->row['affiliate_id'],
                    'commission'              => $order_query->row['commission'],
                    'language_id'             => $order_query->row['language_id'],
                    'language_code'           => $language_code,
                    'currency_id'             => $order_query->row['currency_id'],
                    'currency_code'           => $order_query->row['currency_code'],
                    'currency_value'          => $order_query->row['currency_value'],
                    'ip'                      => $order_query->row['ip'],
                    'forwarded_ip'            => $order_query->row['forwarded_ip'],
                    'user_agent'              => $order_query->row['user_agent'],
                    'accept_language'         => $order_query->row['accept_language'],
                    'date_added'              => $order_query->row['date_added'],
                    'date_modified'           => $order_query->row['date_modified']
                );
            } else {
                return false;
            }
        }
    }

    /**
     * Add Order
     *
     * @param array $data
     * @return int $order_id
     *
     */
    public function addOrder($data) {
        if($this->oc_version === '2.0') {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : '') . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_custom_field = '" . $this->db->escape(isset($data['payment_custom_field']) ? serialize($data['payment_custom_field']) : '') . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_custom_field = '" . $this->db->escape(isset($data['shipping_custom_field']) ? serialize($data['shipping_custom_field']) : '') . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float)$data['total'] . "', affiliate_id = '" . (int)$data['affiliate_id'] . "', commission = '" . (float)$data['commission'] . "', marketing_id = '" . (int)$data['marketing_id'] . "', tracking = '" . $this->db->escape($data['tracking']) . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = NOW(), date_modified = NOW()");
            $order_id = $this->db->getLastId();
            if (isset($data['products'])) {
                foreach ($data['products'] as $product) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "'");

                    $order_product_id = $this->db->getLastId();

                    foreach ($product['option'] as $option) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
                    }
                }
            }
            $this->load->model('checkout/voucher');
            if (isset($data['vouchers'])) {
                foreach ($data['vouchers'] as $voucher) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($voucher['description']) . "', code = '" . $this->db->escape($voucher['code']) . "', from_name = '" . $this->db->escape($voucher['from_name']) . "', from_email = '" . $this->db->escape($voucher['from_email']) . "', to_name = '" . $this->db->escape($voucher['to_name']) . "', to_email = '" . $this->db->escape($voucher['to_email']) . "', voucher_theme_id = '" . (int)$voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($voucher['message']) . "', amount = '" . (float)$voucher['amount'] . "'");

                    $order_voucher_id = $this->db->getLastId();

                    $voucher_id = $this->model_checkout_voucher->addVoucher($order_id, $voucher);

                    $this->db->query("UPDATE " . DB_PREFIX . "order_voucher SET voucher_id = '" . (int)$voucher_id . "' WHERE order_voucher_id = '" . (int)$order_voucher_id . "'");
                }
            }
            if (isset($data['totals'])) {
                foreach ($data['totals'] as $total) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
                }
            }
            return $order_id;
        }
        if($this->oc_version != '2.0') {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_custom_field = '" . $this->db->escape(isset($data['payment_custom_field']) ? json_encode($data['payment_custom_field']) : '') . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_custom_field = '" . $this->db->escape(isset($data['shipping_custom_field']) ? json_encode($data['shipping_custom_field']) : '') . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float)$data['total'] . "', affiliate_id = '" . (int)$data['affiliate_id'] . "', commission = '" . (float)$data['commission'] . "', marketing_id = '" . (int)$data['marketing_id'] . "', tracking = '" . $this->db->escape($data['tracking']) . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = NOW(), date_modified = NOW()");
            $order_id = $this->db->getLastId();
            if (isset($data['products'])) {
                foreach ($data['products'] as $product) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "'");

                    $order_product_id = $this->db->getLastId();

                    foreach ($product['option'] as $option) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
                    }
                }
            }
            if (isset($data['vouchers'])) {
                foreach ($data['vouchers'] as $voucher) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($voucher['description']) . "', code = '" . $this->db->escape($voucher['code']) . "', from_name = '" . $this->db->escape($voucher['from_name']) . "', from_email = '" . $this->db->escape($voucher['from_email']) . "', to_name = '" . $this->db->escape($voucher['to_name']) . "', to_email = '" . $this->db->escape($voucher['to_email']) . "', voucher_theme_id = '" . (int)$voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($voucher['message']) . "', amount = '" . (float)$voucher['amount'] . "'");

                    $order_voucher_id = $this->db->getLastId();

                    $voucher_id = $this->addVoucher($order_id, $voucher);

                    $this->db->query("UPDATE " . DB_PREFIX . "order_voucher SET voucher_id = '" . (int)$voucher_id . "' WHERE order_voucher_id = '" . (int)$order_voucher_id . "'");
                }
            }
            if (isset($data['totals'])) {
                foreach ($data['totals'] as $total) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
                }
            }
            return $order_id;
        }
    }

    /**
     * Add a customer
     *
     * @param array $data
     * @return int $customer_id
     *
     */
    public function addCustomer($data) {
        if($this->oc_version === '2.0') {
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer SET customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : '') . "', newsletter = '" . (int)$data['newsletter'] . "', salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', status = '" . (int)$data['status'] . "', approved = '" . (int)$data['approved'] . "', safe = '" . (int)$data['safe'] . "', date_added = NOW()");
            $customer_id = $this->db->getLastId();
            if (isset($data['address'])) {
                foreach ($data['address'] as $address) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($address['firstname']) . "', lastname = '" . $this->db->escape($address['lastname']) . "', company = '" . $this->db->escape($address['company']) . "', address_1 = '" . $this->db->escape($address['address_1']) . "', address_2 = '" . $this->db->escape($address['address_2']) . "', city = '" . $this->db->escape($address['city']) . "', postcode = '" . $this->db->escape($address['postcode']) . "', country_id = '" . (int)$address['country_id'] . "', zone_id = '" . (int)$address['zone_id'] . "', custom_field = '" . $this->db->escape(isset($address['custom_field']) ? serialize($address['custom_field']) : '') . "'");
                    if (isset($address['default'])) {
                        $address_id = $this->db->getLastId();
                        $this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
                    }
                }
            }
            return $customer_id;
        }

        if($this->oc_version === '2.1' || $this->oc_version === '2.2' || $this->oc_version === '2.3') {
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer SET customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "', newsletter = '" . (int)$data['newsletter'] . "', salt = '" . $this->db->escape($salt = $this->token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', status = '" . (int)$data['status'] . "', approved = '" . (int)$data['approved'] . "', safe = '" . (int)$data['safe'] . "', date_added = NOW()");
            $customer_id = $this->db->getLastId();
            if (isset($data['address'])) {
                foreach ($data['address'] as $address) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($address['firstname']) . "', lastname = '" . $this->db->escape($address['lastname']) . "', company = '" . $this->db->escape($address['company']) . "', address_1 = '" . $this->db->escape($address['address_1']) . "', address_2 = '" . $this->db->escape($address['address_2']) . "', city = '" . $this->db->escape($address['city']) . "', postcode = '" . $this->db->escape($address['postcode']) . "', country_id = '" . (int)$address['country_id'] . "', zone_id = '" . (int)$address['zone_id'] . "', custom_field = '" . $this->db->escape(isset($address['custom_field']) ? json_encode($address['custom_field']) : '') . "'");

                    if (isset($address['default'])) {
                        $address_id = $this->db->getLastId();

                        $this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
                    }
                }
            }
            return $customer_id;
        }

        if($this->oc_version === '3.0') {
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer SET customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : json_encode(array())) . "', newsletter = '" . (int)$data['newsletter'] . "', salt = '" . $this->db->escape($salt = $this->token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', status = '" . (int)$data['status'] . "', safe = '" . (int)$data['safe'] . "', date_added = NOW()");
            $customer_id = $this->db->getLastId();
            if (isset($data['address'])) {
                foreach ($data['address'] as $address) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($address['firstname']) . "', lastname = '" . $this->db->escape($address['lastname']) . "', company = '" . $this->db->escape($address['company']) . "', address_1 = '" . $this->db->escape($address['address_1']) . "', address_2 = '" . $this->db->escape($address['address_2']) . "', city = '" . $this->db->escape($address['city']) . "', postcode = '" . $this->db->escape($address['postcode']) . "', country_id = '" . (int)$address['country_id'] . "', zone_id = '" . (int)$address['zone_id'] . "', custom_field = '" . $this->db->escape(isset($address['custom_field']) ? json_encode($address['custom_field']) : json_encode(array())) . "'");
                    if (isset($address['default'])) {
                        $address_id = $this->db->getLastId();
                        $this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
                    }
                }
            }
            if ($data['affiliate']) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "customer_affiliate SET customer_id = '" . (int)$customer_id . "', company = '" . $this->db->escape($data['company']) . "', website = '" . $this->db->escape($data['website']) . "', tracking = '" . $this->db->escape($data['tracking']) . "', commission = '" . (float)$data['commission'] . "', tax = '" . $this->db->escape($data['tax']) . "', payment = '" . $this->db->escape($data['payment']) . "', cheque = '" . $this->db->escape($data['cheque']) . "', paypal = '" . $this->db->escape($data['paypal']) . "', bank_name = '" . $this->db->escape($data['bank_name']) . "', bank_branch_number = '" . $this->db->escape($data['bank_branch_number']) . "', bank_swift_code = '" . $this->db->escape($data['bank_swift_code']) . "', bank_account_name = '" . $this->db->escape($data['bank_account_name']) . "', bank_account_number = '" . $this->db->escape($data['bank_account_number']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : json_encode(array())) . "', status = '" . (int)$data['affiliate'] . "', date_added = NOW()");
            }
            return $customer_id;
        }
    }

}
