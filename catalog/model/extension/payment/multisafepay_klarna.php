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
class ModelExtensionPaymentMultiSafePayKlarna extends Model
{

    public function getMethod($address, $total)
    {
        if ($total == 0) {
            return false;
        }

        $storeid = $this->config->get('config_store_id');


        if ($this->session->data['currency'] != 'EUR') {
            return false;
        }

        /* Get ip adress filtering for Klarna */
        $filter_active = $this->config->get('payment_multisafepay_klarna_ip_validation_enabler_' . $storeid);
        $ipaddress = array();

        if ($filter_active) {
            $data = $this->config->get('payment_multisafepay_klarna_ip_validation_address_' . $storeid);
            $ipaddress = explode(';', $data);
        }
        if (!in_array($_SERVER["REMOTE_ADDR"], $ipaddress) && $filter_active) {
            return false;
        }


        $totalcents = $total * 100;

        if ($total) {
            if ($this->config->get('payment_multisafepay_klarna_min_amount_' . $storeid) && $totalcents < $this->config->get('payment_multisafepay_klarna_min_amount_' . $storeid)) {
                return false;
            }
            if ($this->config->get('payment_multisafepay_klarna_max_amount_' . $storeid) && $totalcents > $this->config->get('payment_multisafepay_klarna_max_amount_' . $storeid)) {
                return false;
            }
        }



        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('payment_multisafepay_klarna_geo_zone_id_' . $storeid) . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

        /* if ($this->config->get('payment_multisafepay_total') > 0 && $this->config->get('payment_multisafepay_total') > $total) {
          $status = false;
          } else */
        if (!$this->config->get('payment_multisafepay_klarna_geo_zone_id_' . $storeid)) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'multisafepay_klarna',
                'title' => $this->language->get('text_title_klarna'),
                'terms' => '',
                'sort_order' => $this->config->get('payment_multisafepay_klarna_sort_order_' . $storeid)
            );
        }

        return $method_data;
    }

}

?>