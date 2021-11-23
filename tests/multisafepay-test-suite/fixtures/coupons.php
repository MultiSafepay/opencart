<?php

class Coupons {

    public function getCoupon() {
        $coupon = array(
            'name' => '-10% Discount',
            'code' => '101010',
            'type' => 'P',
            'discount' => '10.0000',
            'total' => '0.0000',
            'logged' => 0,
            'shipping' => 0,
            'product' => '',
            'category' => '',
            'date_start' => '2014-01-01',
            'date_end' => '2021-01-01',
            'uses_total' => '100',
            'uses_customer' => '100',
            'status' => 1
        );
        return $coupon;
    }

    public function getCouponInfo() {
        $coupon = array(
            'coupon_id' => '113',
            'code' => '101010',
            'name' => '-10% Discount',
            'type' => 'P',
            'discount' => '10.0000',
            'shipping' => 0,
            'total' => 0.0000,
            'product' => array(),
            'date_added' => '2020-09-24 11:24:24',
            'date_start' => '2014-01-01',
            'date_end' => '2021-01-01',
            'uses_total' => '100',
            'uses_customer' => '100',
            'status' => 1,
            'is_order_lower_than_taxes' => '',
        );
        return $coupon;
    }
}