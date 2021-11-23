<?php

class TaxRates {

    public function getTaxRates($geo_zone_id) {
        $tax_rates = array(
            'name' => 'VAT 21',
            'type' => 'P',
            'rate' => '21',
            'tax_rate_customer_group' => array(
                array(
                    '1'
                )
            ),
            'geo_zone_id' => $geo_zone_id,
        );
        return $tax_rates;
    }

}