<?php

class ModelPaymentMultiSafePayPayafter extends Model {

    public function getMethod($address, $total) {
        if($total == 0){
	        return false;
        }
        if ($this->session->data['currency'] != 'EUR') {
            return false;
        }
        $storeid = $this->config->get('config_store_id');

        /* Get ip adress filtering for PAD */
        $filter_active = $this->config->get('multisafepay_payafter_ip_validation_enabler_'.$storeid);
        $ipaddress = array();

        if ($filter_active) {
            $data = $this->config->get('multisafepay_payafter_ip_validation_address_'.$storeid);
            $ipaddress = explode(';', $data);
        }
        if (!in_array($_SERVER["REMOTE_ADDR"], $ipaddress) && $filter_active) {
            return false;
        }


        $totalcents = $total * 100;

        if ($total) {
            if ($this->config->get('multisafepay_payafter_min_amount_'.$storeid) && $totalcents < $this->config->get('multisafepay_payafter_min_amount_'.$storeid)) {
                return false;
            }
            if ($this->config->get('multisafepay_payafter_max_amount_'.$storeid) && $totalcents > $this->config->get('multisafepay_payafter_max_amount_'.$storeid)) {
                return false;
            }
        }



        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('multisafepay_payafter_geo_zone_id_'.$storeid) . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

        /* if ($this->config->get('multisafepay_total') > 0 && $this->config->get('multisafepay_total') > $total) {
          $status = false;
          } else */
        if (!$this->config->get('multisafepay_payafter_geo_zone_id_'.$storeid)) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'multisafepay_payafter',
                'title' => $this->language->get('text_title_payafter'),
                'terms' => '',
                'sort_order' => $this->config->get('multisafepay_payafter_sort_order_'.$storeid)
            );
        }

        return $method_data;
    }

}

?>