<?php

class TaxClasses {

    public function getTaxClasses($tax_rate_id) {
        $tax_classes = array(
            'title' => 'VAT 21',
            'description' => 'VAT 21 Netherlands',
            'tax_rule' => array(
                array(
                    'tax_rate_id' => $tax_rate_id,
                    'based' => 'payment',
                    'priority' => '1',
                )
            ),
        );
        return $tax_classes;
    }

}