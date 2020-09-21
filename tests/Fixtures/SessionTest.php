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

class SessionTest {

    public function __construct($customer_id) {
        $this->customer_id = $customer_id;
        $this->faker = Faker\Factory::create('es_ES');
        $this->first_name = $this->faker->firstName;
        $this->last_name = $this->faker->lastName;
        $this->email = $this->faker->email;
        $this->telephone = $this->faker->phoneNumber;
        $this->address = $this->faker->streetAddress;
        $this->company = $this->faker->company;
        $this->country_id = '195';
        $this->country = 'Spain';
        $this->country_iso_code_2 = 'ES';
        $this->country_iso_code_3 = 'ESP';
        $this->country = 'Spain';
        $this->zone_id = '3001';
        $this->city = 'Madrid';
        $this->zone_code = 'MD';
        $this->post_code = '28001';
        $this->order_id = (string)time();
        $this->user_token = bin2hex(openssl_random_pseudo_bytes(16));
    }

    public function getSessionInformation() {
        $session = array(
            'language' => 'en-gb',
            'currency' => 'EUR',
            'customer_id' => $this->customer_id,
            'shipping_address' => array(
                'address_id' => 1,
                'firstname' => $this->first_name,
                'lastname' => $this->last_name,
                'company' => $this->company,
                'address_1' => $this->address,
                'address_2' =>  '',
                'postcode' => $this->post_code,
                'city' => $this->city,
                'zone_id' => $this->zone_id,
                'zone' => $this->city,
                'zone_code' => $this->zone_code,
                'country_id' => $this->country_id,
                'country' => $this->country,
                'iso_code_2' => $this->country_iso_code_2,
                'iso_code_3' => $this->country_iso_code_3,
                'address_format' => '',
                'custom_field' => array()
            ),
            'payment_address' => array(
                'address_id' => 1,
                'firstname' => $this->first_name,
                'lastname' => $this->last_name,
                'company' => $this->company,
                'address_1' => $this->address,
                'address_2' =>  '',
                'postcode' => $this->post_code,
                'city' => $this->city,
                'zone_id' => $this->zone_id,
                'zone' => $this->city,
                'zone_code' => $this->zone_code,
                'country_id' => $this->country_id,
                'country' => $this->country,
                'iso_code_2' => $this->country_iso_code_2,
                'iso_code_3' => $this->country_iso_code_3,
                'address_format' => '',
                'custom_field' => array()
            ),
            'user_id' => 1,
            'user_token' => $this->user_token,
            'comment' => '',
            'order_id' => $this->order_id,
            'coupon' => '101010',
            'shipping_methods' => array(
                'flat' => array(
                    'title' => 'Flat Rate',
                    'quote' => array(
                        'flat' => array(
                            'code' => 'flat.flat',
                            'title' => 'Flat Shipping Rate',
                            'cost' => 5.00,
                            'tax_class_id' => 12,
                            'text' => '5.00€',
                        )
                    ),
                    'sort_order' => 1,
                    'error' => ''
                ),
                'item' => array(
                    'title' => 'Per Item',
                    'quote' => array(
                        'item' => array(
                            'code' => 'item.item',
                            'title' => 'Per Item Shipping Rate',
                            'cost' => 3,
                            'tax_class_id' => 11,
                            'text' => '3.00€',
                        ),
                    ),
                    'sort_order' => 2,
                    'error' => ''
                ),
            ),
            'shipping_method' => array(
                'code' => 'flat.flat',
                'title' => 'Flat Shipping Rate',
                'cost' => 5.00,
                'tax_class_id' => 12,
                'text' => '5.00€'
            )
        );
        return $session;
    }
}