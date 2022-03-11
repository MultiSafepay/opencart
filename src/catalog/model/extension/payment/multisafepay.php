<?php

class ModelExtensionPaymentMultiSafePay extends Model {

    public function __construct($registry) {
        parent::__construct($registry);
        $this->registry->set('multisafepay_version_control', new Multisafepayversioncontrol($registry));
        $this->oc_version = $this->multisafepay_version_control->getOcVersion();
        $this->route = $this->multisafepay_version_control->getExtensionRoute();
        $this->key_prefix = $this->multisafepay_version_control->getKeyPrefix();
    }

    /**
     * Retrieves MultiSafepay as payment method
     *
     * @param array $address
     * @param double $total
     * @return mixed bool|array $method_data
     *
     */
    public function getMethod($address, $total) {

        if ($total <= 0 || !$this->config->get($this->key_prefix . 'multisafepay_status')) {
            return false;
        }

        $status = true;

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'multisafepay',
                'title' => $this->getTitle(),
                'terms' => '',
                'sort_order' => $this->config->get($this->key_prefix . 'multisafepay_sort_order')
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

        if ($total == 0 || !$this->config->get($this->key_prefix . 'multisafepay_status')) {
            return false;
        }

        $this->load->language($this->route);

        $this->load->model('localisation/currency');

        $this->registry->set('multisafepay', new Multisafepay($this->registry));

        $gateways = $this->multisafepay->getOrderedGateways($this->config->get('config_store_id'));

        $methods_data = array();

        foreach ($gateways as $key => $gateway) {
            // if enable
            if (!$this->config->get($this->key_prefix . 'multisafepay_'.$gateway['code'].'_status')) {
                continue;
            }

            // if order amount is higher than minimum amount
            if ($this->config->get($this->key_prefix . 'multisafepay_'.$gateway['code'].'_min_amount') > 0 && $this->config->get($this->key_prefix . 'multisafepay_'.$gateway['code'].'_min_amount') > $total) {
                continue;
            }

            // if order amount is lower than maximun amount
            if ($this->config->get($this->key_prefix . 'multisafepay_'.$gateway['code'].'_max_amount') > 0 && $this->config->get($this->key_prefix . 'multisafepay_'.$gateway['code'].'_max_amount') < $total) {
                continue;
            }

            // if order currency
            $currencies = $this->config->get($this->key_prefix . 'multisafepay_'.$gateway['code'].'_currency');
            $currency_info = $this->model_localisation_currency->getCurrencyByCode($this->session->data['currency']);
            if ($this->config->get($this->key_prefix . 'multisafepay_'.$gateway['code'].'_currency') && !in_array($currency_info['currency_id'], $currencies)) {
                continue;
            }

            // if customer belongs to customer group set for this payment method
            $allowed_customer_groups_id = $this->config->get($this->key_prefix . 'multisafepay_'.$gateway['code'].'_customer_group_id');
            $customer_group_id = ($this->customer->getGroupId()) ? $this->customer->getGroupId() : $this->config->get('config_customer_group_id');

            if ($this->config->get($this->key_prefix . 'multisafepay_'.$gateway['code'].'_customer_group_id') && !in_array($customer_group_id, $allowed_customer_groups_id)) {
                continue;
            }

            $query = $this->db->query(
                "SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE
                geo_zone_id = '" . (int)$this->config->get($this->key_prefix . 'multisafepay_'.$gateway['code'].'_geo_zone_id') . "' AND
                country_id = '" . (int)$address['country_id'] . "'
                AND (zone_id = '" . (int)$address['zone_id'] . "'
                OR zone_id = '0')"
            );

            if ($this->config->get($this->key_prefix . 'multisafepay_'.$gateway['code'].'_geo_zone_id') && !$query->num_rows) {
                continue;
            }

	        if ($gateway['type'] === 'generic') {
	        	$description = $this->config->get($this->key_prefix . 'multisafepay_'.$gateway['code'].'_name');
	        	$image = $this->config->get($this->key_prefix . 'multisafepay_'.$gateway['code'].'_image');
		        $title = $this->getTitle($description, $image, true);
	        }

	        if ($gateway['type'] !== 'generic') {
	        	$title = $this->getTitle($gateway['description'], $gateway['image']);
	        }

            $methods_data[] = array(
                'code' => $gateway['route'],
                'title' => $title,
                'terms' => '',
                'sort_order' => $this->config->get($this->key_prefix . 'multisafepay_'.$gateway['code'].'_sort_order')
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
     * @param string  $title
     * @param string  $image
     * @param boolean $is_generic
     * @return string $title
     *
     */
    private function getTitle($title = 'MultiSafepay', $image = 'wallet', $is_generic = false) {

        $this->registry->set('multisafepay', new Multisafepay($this->registry));
        $shop_url = $this->multisafepay->getShopUrl();
        $locale_code = $this->language->get('code');

        if (!$this->config->get($this->key_prefix . 'multisafepay_use_payment_logo') || !$image) {
            return $title;
        }

        if(!$is_generic && !file_exists(DIR_IMAGE . 'catalog/multisafepay/' . $image . '.png') && !file_exists(DIR_IMAGE . 'catalog/multisafepay/' . $image . '-' . $locale_code . 'png')) {
            return $title;
        }

        if(!$is_generic && !file_exists(DIR_IMAGE . 'catalog/multisafepay/' . $image . '-' . $locale_code . '.png')) {
            $logo = '<img height=20 src="' . $shop_url . 'image/catalog/multisafepay/' . $image . '.png" alt="' . $title . '"/>';
            $title_with_logo = $logo . '  ' . $title;
        }

        if(!$is_generic && file_exists(DIR_IMAGE . 'catalog/multisafepay/' . $image . '-' . $locale_code . '.png')) {
            $logo = '<img height=20 src="' . $shop_url . 'image/catalog/multisafepay/' . $image . '-' . $locale_code . '.png" alt="' . $title . '"/>';
            $title_with_logo = $logo . '  ' . $title;
        }

        if($is_generic) {
	        $logo = '<img height=20 src="' . $shop_url . 'image/' . $image . '" alt="' . $title . '"/>';
	        $title_with_logo = $logo . '  ' . $title;
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
        $payment_method = $this->getTitle($data['description'], $data['image']);
        $payment_method_title = trim(strip_tags($payment_method));
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET payment_code = '" . $this->db->escape($data['route']) . "', payment_method = '" . $this->db->escape($payment_method_title) . "' WHERE order_id = '" . (int)$order_id . "'");
    }

    /**
     * Return the products for the given order_id
     * This function has been duplicated from OC 3.0 since is not presente in previous versions
     *
     *
     * @param int $order_id
     * @return array
     *
     */
    public function getOrderProducts($order_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
        return $query->rows;
    }

    /**
     * Return the products options selected in a giver order_id for the given order_product_id
     * This function has been duplicated from OC 3.0 since is not presente in previous versions
     *
     *
     * @param int $order_id
     * @param int $order_product_id
     * @return array
     *
     */
    public function getOrderOptions($order_id, $order_product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
        return $query->rows;
    }

    /**
     * Return all order totals lines for a giver order_id
     * This function has been duplicated from OC 3.0 since is not presente in previous versions
     *
     *
     * @param int $order_id
     * @return array
     *
     */
    public function getOrderTotals($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");
        return $query->rows;
    }

	/**
	 * Return all gift vouchers lines for a giver order_id
	 *
	 * @param int $order_id
	 * @return array
	 *
	 */
	public function getOrderVouchers($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
		return $query->rows;
	}

	/**
	 * Return the id of the next invoice number
	 *
	 * @param int $order_id
	 *
	 */
	public function getSettingValue($key, $store_id = 0) {
		$query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `key` = '" . $this->db->escape($key) . "'");
		if ($query->num_rows) {
			return $query->row['value'];
		}
		return null;
	}

    /**
     * Return all order status
     * This function doesn`t exist in Catalog OpenCart 2 and that`s why this is a duplicated
     *
     *
     * @return array
     *
     */
    public function getOrderStatuses() {
        $query = $this->db->query("SELECT order_status_id, name FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
        $order_status_data = $query->rows;
        return $order_status_data;
    }

    /**
     * Explore the database and return the Order Totals Keys
     */
    public function getDetectedOrderTotalsKeys() {
        $query = $this->db->query("SELECT DISTINCT code FROM " . DB_PREFIX . "extension WHERE type = 'total'");
        if ($query->num_rows) {
            $codes = array();
            foreach ($query->rows as $result) {
                $codes[] = $result['code'];
            }
            return $codes;
        }
        return array();
    }

    // phpcs:ignore
    public function getCoupon($code) {
        $status = true;

        $coupon_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon` WHERE code = '" . $this->db->escape($code) . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1'");

        if ($coupon_query->num_rows) {
            if ($coupon_query->row['total'] > $this->cart->getSubTotal()) {
                $status = false;
            }

            $coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch WHERE ch.coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");

            if ($coupon_query->row['uses_total'] > 0 && ($coupon_history_query->row['total'] >= $coupon_query->row['uses_total'])) {
                $status = false;
            }

            if ($coupon_query->row['logged'] && !$this->customer->getId()) {
                $status = false;
            }

            if ($this->customer->getId()) {
                $coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch WHERE ch.coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "' AND ch.customer_id = '" . (int)$this->customer->getId() . "'");

                if ($coupon_query->row['uses_customer'] > 0 && ($coupon_history_query->row['total'] >= $coupon_query->row['uses_customer'])) {
                    $status = false;
                }
            }

            // Products
            $coupon_product_data = array();

            $coupon_product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon_product` WHERE coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");

            foreach ($coupon_product_query->rows as $product) {
                $coupon_product_data[] = $product['product_id'];
            }

            // Categories
            $coupon_category_data = array();

            $coupon_category_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon_category` cc LEFT JOIN `" . DB_PREFIX . "category_path` cp ON (cc.category_id = cp.path_id) WHERE cc.coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");

            foreach ($coupon_category_query->rows as $category) {
                $coupon_category_data[] = $category['category_id'];
            }

            $product_data = array();

            if ($coupon_product_data || $coupon_category_data) {
                foreach ($this->cart->getProducts() as $product) {
                    if (in_array($product['product_id'], $coupon_product_data)) {
                        $product_data[] = $product['product_id'];

                        continue;
                    }

                    foreach ($coupon_category_data as $category_id) {
                        $coupon_category_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = '" . (int)$product['product_id'] . "' AND category_id = '" . (int)$category_id . "'");

                        if ($coupon_category_query->row['total']) {
                            $product_data[] = $product['product_id'];

                            continue;
                        }
                    }
                }

                if (!$product_data) {
                    $status = false;
                }
            }
        }

        if (!$coupon_query->num_rows) {
            $status = false;
        }

        if ($status) {
            return array(
                'coupon_id'     => $coupon_query->row['coupon_id'],
                'code'          => $coupon_query->row['code'],
                'name'          => $coupon_query->row['name'],
                'type'          => $coupon_query->row['type'],
                'discount'      => $coupon_query->row['discount'],
                'shipping'      => $coupon_query->row['shipping'],
                'total'         => $coupon_query->row['total'],
                'product'       => $product_data,
                'date_start'    => $coupon_query->row['date_start'],
                'date_end'      => $coupon_query->row['date_end'],
                'uses_total'    => $coupon_query->row['uses_total'],
                'uses_customer' => $coupon_query->row['uses_customer'],
                'status'        => $coupon_query->row['status'],
                'date_added'    => $coupon_query->row['date_added']
            );
        }
    }

}

class ModelPaymentMultiSafePay extends ModelExtensionPaymentMultiSafePay { }
