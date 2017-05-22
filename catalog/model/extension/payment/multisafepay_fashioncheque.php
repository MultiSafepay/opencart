<?php

class ModelExtensionPaymentMultiSafePayFashioncheque extends Model
{

    public function getMethod($address, $total)
    {
        if ($total == 0) {
            return false;
        }
        $this->load->language('extension/payment/multisafepay');
        $storeid = $this->config->get('config_store_id');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('multisafepay_fashioncheque_geo_zone_id_' . $storeid) . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

        /* if ($this->config->get('multisafepay_total') > 0 && $this->config->get('multisafepay_total') > $total) {
          $status = false;
          } else */
        if (!$this->config->get('multisafepay_fashioncheque_geo_zone_id_' . $storeid)) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $totalcents = $total * 100;

        if ($total) {
            if ($this->config->get('multisafepay_fashioncheque_min_amount_' . $storeid) && $totalcents < $this->config->get('multisafepay_fashioncheque_min_amount_' . $storeid)) {
                return false;
            }
            if ($this->config->get('multisafepay_fashioncheque_max_amount_' . $storeid) && $totalcents > $this->config->get('multisafepay_fashioncheque_max_amount_' . $storeid)) {
                return false;
            }
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'multisafepay_fashioncheque',
                'title' => $this->language->get('text_title_fashioncheque'),
                'terms' => '',
                'sort_order' => $this->config->get('multisafepay_fashioncheque_sort_order_' . $storeid)
            );
        }

        return $method_data;
    }

}

?>