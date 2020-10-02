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