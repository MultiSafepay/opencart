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

    /**
     * This function add a new order history
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

        if ($query->num_rows) {
            foreach ($query->rows as $result) {
                $this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = '" . $this->db->escape('payment_multisafepay') . "' WHERE `code` = 'payment_" . $this->db->escape($result['code']) . "'");
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
            'image/multisafepay/alipay.svg',
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
            'image/multisafepay/santander.svg',
            'image/multisafepay/trustly.svg',
            'image/multisafepay/visa.svg',
            'image/multisafepay/vvv.svg',
            'image/multisafepay/wallet.svg',
            'image/multisafepay/webshopgiftcard.svg'
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
            'multisafepay_alipay',
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
            'multisafepay_santander',
            'multisafepay_trustly',
            'multisafepay_visa',
            'multisafepay_vvv',
            'multisafepay_webshopgiftcard',
        );
    }

}