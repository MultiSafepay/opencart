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

class OrdersTest {

    public function __construct($order_id, $order_key) {
        $this->faker = Faker\Factory::create('nl_NL');
        $this->order_id = $order_id;
        $this->key = $order_key;
        $this->store_name = $this->faker->sentence(6);
        $this->store_url = 'https://www.multisafepay.com/';
        $this->date_time = $this->faker->date('Y-m-d') . ' ' . $this->faker->time('H:i:s');
        $this->user_agent = 'Mozilla/5.0 (iPad; CPU OS 7_0_1 like Mac OS X; sl-SI) AppleWebKit/534.8.1 (KHTML, like Gecko) Version/3.0.5 Mobile/8B118 Safari/6534.8.1';
        $this->ip_address = '127.0.0.1';
    }

    public function getOrderId() {
        return $this->order_id;
    }

    public function getOrderInformation() {
        $order[0] = array(
            'total'             => '3136.4558',
            'currency_id'       => '3',
            'currency_code'     => 'EUR',
            'currency_value'    => '0.78460002'
        );

        $order[1] = array(
            'total'             => '127.2100',
            'currency_id'       => '3',
            'currency_code'     => 'EUR',
            'currency_value'    => '0.78460002'
        );

        $order[2] = array(
            'total'             => '106.0000',
            'currency_id'       => '3',
            'currency_code'     => 'EUR',
            'currency_value'    => '0.78460002'
        );

        $order[3] = array(
            'total'             => '95.9000',
            'currency_id'       => '3',
            'currency_code'     => 'EUR',
            'currency_value'    => '0.78460002'
        );

        $order[4] = array(
            'total'             => '1639.6667',
            'currency_id'       => '3',
            'currency_code'     => 'EUR',
            'currency_value'    => '0.78460002'
        );

        $order = array(
            'order_id' => $this->order_id,
            'invoice_no' => '0',
            'invoice_prefix' => 'INV-2020-00',
            'store_id' => '0',
            'store_name' => $this->store_name,
            'store_url' => $this->store_url,
            'payment_address_format' => '',
            'shipping_address_format' => '',
            'payment_method' => 'MultiSafepay',
            'payment_code' => 'multisafepay',
            'shipping_method' => 'Flat Shipping Rate',
            'shipping_code' => 'flat.flat',
            'comment' => '',
            'total' => $order[$this->key]['total'],
            'order_status_id' => '0',
            'order_status' => '',
            'affiliate_id' => '0',
            'commission' => '0.0000',
            'marketing_id' => '',
            'tracking' => '',
            'language_id' => '1',
            'language_code' => 'en-gb',
            'currency_id' => $order[$this->key]['currency_id'],
            'currency_code' => $order[$this->key]['currency_code'],
            'currency_value' => $order[$this->key]['currency_value'],
            'ip' => $this->ip_address,
            'forwarded_ip' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'accept_language' => 'en-GB,en;q=0.9',
            'date_added' => $this->date_time,
            'date_modified' => $this->date_time,
            'option' => array(),
            'download' => array(),
            'products' => $this->getProducts(),
            'totals' => $this->getTotals()
        );
        return $order;
    }

    public function getProducts() {
        $products[0] = array(
            array(
                'order_product_id' => 1759,
                'order_id' => $this->order_id,
                'product_id' => 30,
                'name' => 'Canon EOS 5D',
                'model' => 'Product 30',
                'quantity' => 1,
                'price' => 100.0000,
                'total' => 100.0000,
                'tax' => 21.0000,
                'reward' => 0,
                'option' => array(),
            ),
            array(
                'order_product_id' => 1760,
                'order_id' => $this->order_id,
                'product_id' => 49,
                'name' => 'Samsung Galaxy Tab 10.1',
                'model' => 'Product 49',
                'quantity' => 1,
                'price' => 199.9900,
                'total' => 199.9900,
                'tax' => 41.9979,
                'reward' => 0,
                'option' => array(),
            ),
            array(
                'order_product_id' => 1761,
                'order_id' => $this->order_id,
                'product_id' => 28,
                'name' => 'HTC Touch HD',
                'model' => 'Product 28',
                'quantity' => 1,
                'price' => 100.0000,
                'total' => 100.0000,
                'tax' => 21.0000,
                'reward' => 0,
                'option' => array(),
            ),
            array(
                'order_product_id' => 1762,
                'order_id' => $this->order_id,
                'product_id' => 29,
                'name' => 'Palm Treo Pro',
                'model' => 'Product 29',
                'quantity' => 1,
                'price' => 279.9900,
                'total' => 279.9900,
                'tax' => 58.7979,
                'reward' => 0,
                'option' => array(),
            ),
            array(
                'order_product_id' => 1763,
                'order_id' => $this->order_id,
                'product_id' => 41,
                'name' => 'iMac',
                'model' => 'Product 41',
                'quantity' => 1,
                'price' => 1620.0000,
                'total' => 1620.0000,
                'tax' => 340.2000,
                'reward' => 0,
                'option' => array(),
            ),
            array(
                'order_product_id' => 1764,
                'order_id' => $this->order_id,
                'product_id' => 42,
                'name' => 'Apple Cinema 30"',
                'model' => 'Product 42',
                'quantity' => 2,
                'price' => 144.0000,
                'total' => 288.0000,
                'tax' => 30.2400,
                'reward' => 0,
                'option' => array(),
            ),

        );
        $products[1] = array(
            array(
                'order_product_id' => 545,
                'order_id' => $this->order_id,
                'product_id' => 40,
                'name' => 'iPhone',
                'model' => 'Product 40',
                'quantity' => 1,
                'price' => 101.0000,
                'total' => 101.0000,
                'tax' => 31.3100,
                'reward' => 100,
                'option' => array(),
            )
        );
        $products[2] = array(
            array(
                'order_product_id' => 7288,
                'order_id' => $this->order_id,
                'product_id' => 40,
                'name' => 'iPhone',
                'model' => 'Product 40',
                'quantity' => 1,
                'price' => 101.0000,
                'total' => 101.0000,
                'tax' => 0.0000,
                'reward' => 100,
                'option' => array(),
            )
        );
        $products[3] = array(
            array(
                'order_product_id' => 7288,
                'order_id' => $this->order_id,
                'product_id' => 40,
                'name' => 'iPhone',
                'model' => 'Product 40',
                'quantity' => 1,
                'price' => 101.0000,
                'total' => 101.0000,
                'tax' => 0.0000,
                'reward' => 100,
                'option' => array(),
            )
        );
        $products[4] = array(
            array(
                'order_product_id' => 7288,
                'order_id' => $this->order_id,
                'product_id' => 40,
                'name' => 'iPhone',
                'model' => 'Product 40',
                'quantity' => 2,
                'price' => 101.0000,
                'total' => 202.0000,
                'tax' => 0.0000,
                'reward' => 200,
                'option' => array(),
            ),
            array(
                'order_product_id' => 7288,
                'order_id' => $this->order_id,
                'product_id' => 43,
                'name' => 'MacBook',
                'model' => 'Product 16',
                'quantity' => 1,
                'price' => 1500.0000,
                'total' => 1500.0000,
                'tax' => 0.0000,
                'reward' => 0,
                'option' => array(),
            )
        );
        return $products[$this->key];
    }


    public function getTotals() {
        $totals[0] = array(
            array(
                'order_total_id' => 2087,
                'order_id' => $this->order_id,
                'code' => 'sub_total',
                'title' => 'Sub-Total',
                'value' => 2587.9800,
                'sort_order' => 0,
            ),
            array(
                'order_total_id' => 2088,
                'order_id' => $this->order_id,
                'code' => 'shipping',
                'title' => 'Flat Shipping Rate',
                'value' => 5.0000,
                'sort_order' => 3,
            ),
            array(
                'order_total_id' => 2092,
                'order_id' => $this->order_id,
                'code' => 'tax',
                'title' => 'VAT (21%)',
                'value' => 543.4758,
                'sort_order' => 9,
            ),
            array(
                'order_total_id' => 2094,
                'order_id' => $this->order_id,
                'code' => 'total',
                'title' => 'Total',
                'value' => 3136.4558,
                'sort_order' => 13,
            )
        );
        $totals[1] = array(
            array(
                'order_total_id' => 2087,
                'order_id' => $this->order_id,
                'code' => 'sub_total',
                'title' => 'Sub-Total',
                'value' => 101.0000,
                'sort_order' => 0,
            ),
            array(
                'order_total_id' => 2088,
                'order_id' => $this->order_id,
                'code' => 'shipping',
                'title' => 'Flat Shipping Rate',
                'value' => 5.0000,
                'sort_order' => 3,
            ),
            array(
                'order_total_id' => 2093,
                'order_id' => $this->order_id,
                'code' => 'tax',
                'title' => 'VAT (21%)',
                'value' => 21.2100,
                'sort_order' => 9,
            ),
            array(
                'order_total_id' => 2094,
                'order_id' => $this->order_id,
                'code' => 'total',
                'title' => 'Total',
                'value' => 127.2100,
                'sort_order' => 13,
            ),
        );
        $totals[2] = array(
            array(
                'order_total_id' => 32411,
                'order_id' => $this->order_id,
                'code' => 'sub_total',
                'title' => 'Sub-Total',
                'value' => 101.0000,
                'sort_order' => 0,
            ),
            array(
                'order_total_id' => 32412,
                'order_id' => $this->order_id,
                'code' => 'shipping',
                'title' => 'Flat Shipping Rate',
                'value' => 5.0000,
                'sort_order' => 3,
            ),
            array(
                'order_total_id' => 32413,
                'order_id' => $this->order_id,
                'code' => 'total',
                'title' => 'Total',
                'value' => 106.0000,
                'sort_order' => 13,
            ),
        );
        $totals[3] = array(
            array(
                'order_total_id' => 32420,
                'order_id' => $this->order_id,
                'code' => 'sub_total',
                'title' => 'Sub-Total',
                'value' => 101.0000,
                'sort_order' => 0,
            ),
            array(
                'order_total_id' => 32421,
                'order_id' => $this->order_id,
                'code' => 'shipping',
                'title' => 'Flat Shipping Rate',
                'value' => 5.0000,
                'sort_order' => 3,
            ),
            array(
                'order_total_id' => 32422,
                'order_id' => $this->order_id,
                'code' => 'coupon',
                'title' => 'Coupon (101010)',
                'value' => -10.1000,
                'sort_order' => 4,
            ),
            array(
                'order_total_id' => 32423,
                'order_id' => $this->order_id,
                'code' => 'total',
                'title' => 'Total',
                'value' => 95.9000,
                'sort_order' => 13,
            ),
        );
        $totals[4] = array(
            array(
                'order_total_id' => 32669,
                'order_id' => $this->order_id,
                'code' => 'sub_total',
                'title' => 'Sub-Total',
                'value' => 1702.0000,
                'sort_order' => 0,
            ),
            array(
                'order_total_id' => 32670,
                'order_id' => $this->order_id,
                'code' => 'reward',
                'title' => 'Reward Points (100)',
                'value' => -67.3333,
                'sort_order' => 2,
            ),
            array(
                'order_total_id' => 32671,
                'order_id' => $this->order_id,
                'code' => 'shipping',
                'title' => 'Flat Shipping Rate',
                'value' => 5.0000,
                'sort_order' => 3,
            ),
            array(
                'order_total_id' => 32672,
                'order_id' => $this->order_id,
                'code' => 'total',
                'title' => 'Total',
                'value' => 1639.6667,
                'sort_order' => 13,
            ),
        );
        return $totals[$this->key];
    }



}