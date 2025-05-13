<?php

class ModelExtensionPaymentMultiSafePay extends Model {

    public function __construct($registry) {
        parent::__construct($registry);
        $this->registry->set('multisafepay_version_control', new Multisafepayversioncontrol($registry));
        $this->oc_version = $this->multisafepay_version_control->getOcVersion();
    }

    /**
     * This function add a new order history element
     *
     * @param int $order_id
     * @param int $order_status_id
     * @param string $comment
     * @param bool $notify
     */
    public function addOrderHistory($order_id, $order_status_id, $comment = '', $notify = false) {
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
    }

    /**
     * Return all currencies registered in the store to be used in the
     * autocomplete function AJAX call in admin filtered by name
     *
     * @param array $data
     * @return array $currency_data
     *
     */
    public function getCurrencies($data = array()) {
        $currency_data = array();

        $sql = "SELECT * FROM " . DB_PREFIX . "currency";

        if (!empty($data['filter_name'])) {
            $sql .= " AND (title LIKE '%" . $this->db->escape($data['filter_name']) . "%' OR code LIKE '%" . $this->db->escape($data['filter_name']) . "%')";
        }

        $sql .= " ORDER BY title ASC";

        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $currency_data[$result['code']] = array(
                'currency_id'   => $result['currency_id'],
                'title'         => $result['title'],
                'code'          => $result['code'],
                'symbol_left'   => $result['symbol_left'],
                'symbol_right'  => $result['symbol_right'],
                'decimal_place' => $result['decimal_place'],
                'value'         => $result['value'],
                'status'        => $result['status'],
                'date_modified' => $result['date_modified']
            );
        }

        return $currency_data;

    }

    /**
     * Function that remove all old extensions in database and update the code name in settings table.
     * Then return true or false if old files still exists
     *
     */
    public function removeOldExtensionsAndFiles() {

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = 'payment' and `code` LIKE '%multisafepay%' AND `code` != 'multisafepay'");

        if ($query->num_rows && $this->oc_version === '3.0') {
            foreach ($query->rows as $result) {
                $this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = '" . $this->db->escape('payment_multisafepay') . "' WHERE `code` = '" . $this->db->escape($result['code']) . "'");
            }
            foreach ($query->rows as $result) {
                $this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `type` = '" . $this->db->escape($result['type']) . "' AND `code` = '" . $this->db->escape($result['code']) . "'");
            }
        }

        $files = $this->getOldFilesThatCurrentlyExist();

        if (!empty($files)) {
            return true;
        }

        return false;

    }

    /**
     * Function that returns all the old extension files that currently exist in the server
     *
     */
    public function getOldFilesThatCurrentlyExist() {
        $files = array();
        $list_of_files = $this->getAllOldFiles();
        foreach ($list_of_files as $file) {
            if(file_exists($file)) {
                $files[] = $file;
            }
        }
        $files = array_unique($files);
        return $files;
    }

    /**
     * Function that remove all old extension files that currently exist in the server
     *
     */
    public function removeOldFiles() {
        $files = $this->getOldFilesThatCurrentlyExist();
        foreach ($files as $file) {
            if(!unlink($file)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Function that check if a new version exists comparing the current version with
     * the latest release tag in github
     *
     */
    public function checkForNewVersions() {
        $this->registry->set('multisafepay', new Multisafepay($this->registry));
        $current_version = $this->multisafepay->getPluginVersion();
        $options = array(
            'http'=> array(
                'method'=>"GET",
                'header'=>"Accept-language: en\r\n" .
                    "Cookie: foo=bar\r\n" .
                    "User-Agent: PHP\r\n"
            )
        );
        $context = stream_context_create($options);
        $content = file_get_contents('https://api.github.com/repos/multisafepay/opencart/releases/latest', false, $context);
        if($content) {
            $information = json_decode($content);
            $latest_version_release = $information->tag_name;
            if($latest_version_release > $current_version) {
                return true;
            }
        }
        return false;
    }

    /**
     * Function that returns a list of old extension files
     *
     */
    private function getAllOldFiles() {
        $root_application = str_replace('admin/', '', DIR_APPLICATION);
        $result = array();
        $folders = $this->getOldExtensionsFolders();
        $files = $this->getOldExtensionsFiles();
        foreach ($folders as $folder) {
            if(strpos($folder, 'view') !== false) {
                $ext = '.twig';
            }
            if(strpos($folder, 'admin/view/template') === false) {
                $ext = '.php';
            }
            foreach ($files as $file) {
                $result[] = $root_application . $folder . $file . $ext;
            }
        }
        $other_files = $this->getOldFilesOutOfTheList();
        foreach ($other_files as $file) {
            $result[] = $root_application . $file;
        }
        return $result;
    }

    /**
     * Function that returns a list of old extension folders
     *
     */
    private function getOldExtensionsFolders() {
        return array(
            'admin/controller/extension/payment/',
            'admin/view/template/extension/payment/',
            'admin/language/en-gb/extension/payment/',
            'admin/language/nl-nl/extension/payment/',
            'catalog/controller/extension/payment/',
            'catalog/model/extension/payment/',
        );
    }

    /**
     * Function that returns a list of old extension files that are not inside the folders
     * named in getOldExtensionsFolders
     *
     */
    private function getOldFilesOutOfTheList() {
        return array(
            'admin/view/image/payment/multisafepay.svg',
            'admin/view/template/extension/total/multisafepay.twig',
            'admin/controller/extension/total/multisafepay.php',
            'admin/language/en-gb/extension/payment/multisafepay_degrotespeelgoedwinkel.php',
            'admin/language/en-gb/extension/payment/multisafepay_ebon.php',
            'admin/language/en-gb/extension/payment/multisafepay_parfumnl.php',
            'admin/language/en-gb/extension/payment/multisafepay_wallet.php',
            'admin/language/en-gb/extension/total/multisafepay.php',
            'admin/language/nl-nl/extension/total/multisafepay.php',
            'admin/language/nl-nl/extension/payment/multisafepay_wallet.php',
            'catalog/controller/extension/payment/multisafepay_fastcheckout.php',
            'catalog/controller/extension/payment/MultiSafepay.combined.php',
            'catalog/controller/extension/payment/multisafepay_wallet.php',
            'catalog/language/en-gb/extension/total/multisafepaypayafterfee.php',
            'catalog/language/nl-nl/extension/total/multisafepay_fee.php',
            'catalog/view/theme/default/template/extension/payment/multisafepay_default.twig',
            'catalog/view/theme/default/template/extension/payment/multisafepay_failure.twig',
            'catalog/view/theme/default/template/extension/payment/multisafepay_fastcheckout.twig',
            'catalog/view/theme/default/template/extension/payment/multisafepay_ideal.twig',
            'image/multisafepay/afterpay.svg',
            'image/multisafepay/amex.svg',
            'image/multisafepay/applepay.svg',
            'image/multisafepay/babygiftcard.svg',
            'image/multisafepay/banktrans.svg',
            'image/multisafepay/belfius.svg',
            'image/multisafepay/boekenbon.svg',
            'image/multisafepay/dirdeb.svg',
            'image/multisafepay/directbank.svg',
            'image/multisafepay/dotpay.svg',
            'image/multisafepay/einvoice.svg',
            'image/multisafepay/erotiekbon.svg',
            'image/multisafepay/fashioncheque.svg',
            'image/multisafepay/fastcheckout.svg',
            'image/multisafepay/gezondheidsbon.svg',
            'image/multisafepay/giropay.svg',
            'image/multisafepay/ideal.svg',
            'image/multisafepay/ing.svg',
            'image/multisafepay/kbc.svg',
            'image/multisafepay/klarna.svg',
            'image/multisafepay/lief.svg',
            'image/multisafepay/maestro.svg',
            'image/multisafepay/mastercard.svg',
            'image/multisafepay/mistercash.svg',
            'image/multisafepay/multisafepay.svg',
            'image/multisafepay/parfumcadeaukaart.svg',
            'image/multisafepay/payafter.svg',
            'image/multisafepay/paypal.svg',
            'image/multisafepay/paysafecard.svg',
            'image/multisafepay/trustly.svg',
            'image/multisafepay/visa.svg',
            'image/multisafepay/vvv.svg',
            'image/multisafepay/wallet.svg',
            'image/multisafepay/webshopgiftcard.svg',
            'vqmod/xml/multisafepay_fastcheckout.xml'
        );
    }

    /**
     * Function that returns a list of files names that could be inside the folders named in
     * getOldExtensionsFolders and be part of the old extension
     *
     */
    private function getOldExtensionsFiles() {
        return array(
            'multisafepay_afterpay',
            'multisafepay_amex',
            'multisafepay_applepay',
            'multisafepay_babygiftcard',
            'multisafepay_banktrans',
            'multisafepay_belfius',
            'multisafepay_boekenbon',
            'multisafepay_dirdeb',
            'multisafepay_directbank',
            'multisafepay_dotpay',
            'multisafepay_einvoice',
            'multisafepay_erotiekbon',
            'multisafepay_fashioncheque',
            'multisafepay_gezondheidsbon',
            'multisafepay_giropay',
            'multisafepay_ideal',
            'multisafepay_ing',
            'multisafepay_kbc',
            'multisafepay_klarna',
            'multisafepay_lief',
            'multisafepay_maestro',
            'multisafepay_mastercard',
            'multisafepay_mistercash',
            'multisafepay_parfumcadeaukaart',
            'multisafepay_payafter',
            'multisafepay_paypal',
            'multisafepay_paysafecard',
            'multisafepay_trustly',
            'multisafepay_visa',
            'multisafepay_vvv',
            'multisafepay_webshopgiftcard',
        );
    }

    /**
     * Function that gets event by code from database
     *
     * @param string $code
     * @return array
     *
     */
    public function getEventByCode($code) {
        $query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "event` WHERE `code` = '" . $this->db->escape($code) . "' LIMIT 1");
        return $query->row;
    }

    /**
     * Function that gets event by code from database
     *
     * @param string $code
     * @param string $trigger
     * @param string $action
     * @param int $status
     * @param int $sort_order
     * @return int
     *
     */
    public function addEvent($code, $trigger, $action, $status = 1, $sort_order = 0) {
        if($this->oc_version === '3.0') {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = '" . $this->db->escape($code) . "', `trigger` = '" . $this->db->escape($trigger) . "', `action` = '" . $this->db->escape($action) . "', `sort_order` = '" . (int)$sort_order . "', `status` = '" . (int)$status . "'");
        }
        if($this->oc_version === '2.3') {
            $this->db->query("INSERT INTO `".DB_PREFIX."event` SET `code` = '".$this->db->escape($code)."', `trigger` = '".$this->db->escape($trigger)."', `action` = '".$this->db->escape($action)."', `date_added` = NOW(), `status` = '".(int)$status."'");
        }
        if($this->oc_version === '2.0' || $this->oc_version === '2.1' || $this->oc_version === '2.2') {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = '" . $this->db->escape($code) . "', `trigger` = '" . $this->db->escape($trigger) . "', `action` = '" . $this->db->escape($action) . "'");
        }
        return $this->db->getLastId();
    }

    /**
     * Function that remove an event by code from database
     *
     * @param string $code
     *
     */
    public function deleteEventByCode($code) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "event` WHERE `code` = '" . $this->db->escape($code) . "'");
    }

    /**
     * Return the id of the next invoice number
     *
     * @param int $order_id
     *
     */
    public function getNextInvoiceId($order_id) {
        $this->load->model('sale/order');
        $order_info = $this->model_sale_order->getOrder($order_id);

        if ($order_info && !$order_info['invoice_no']) {
            $query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "order` WHERE invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "'");
            if ($query->row['invoice_no']) {
                $invoice_no = $query->row['invoice_no'] + 1;
            }
            if (!$query->row['invoice_no']) {
                $invoice_no = 1;
            }
            return $invoice_no;
        }
    }

    /**
     * Return the id of the next invoice number
     *
     * @param int $order_id
     *
     */
    public function getSettingValue($key, $store_id = 0) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `key` = '" . $this->db->escape($key) . "'");
        if ($query->num_rows) {
        	if($query->row['serialized']) {
        		return json_decode($query->row['value'], true);
	        }
            return $query->row['value'];
        }
        return null;
    }

    /**
     * Remove coupons, vouchers, reward points and affiliate commission in full refunds
     *
     * @param int $order_id
     *
     */
    public function removeCouponsVouchersRewardsPointsAffiliateCommission($order_id) {

        $this->db->query("DELETE FROM `" . DB_PREFIX . "coupon_history` WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "voucher_history` WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");

    }

}

class ModelPaymentMultiSafePay extends ModelExtensionPaymentMultiSafePay { }
