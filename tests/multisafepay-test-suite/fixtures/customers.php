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

class Customers {

    public function __construct($customer_key, $customer_id) {
        $location = ['nl_NL','es_ES'];
        $this->faker = Faker\Factory::create($location[$customer_key]);
        $this->key = $customer_key;
        $this->customer_id = $customer_id;
        $this->first_name = 'John';
        $this->last_name = 'Doe';
        $this->email = $this->faker->email;
        $this->telephone = ['0031345678933', '0034691246168'];
        $this->fax = '0031345678933';
        $this->address = ['Kraanspoor', 'Urb. El Saladillo, Edf. Altair'];
        $this->address_2 = ['39C', 'Oficina 207'];
        $this->company = 'MultiSafepay';
        $this->country_id = ['150', '195'];
        $this->country = ['Netherlands', 'Spain'];
        $this->country_iso_code_2 = ['NL', 'ES'];
        $this->country_iso_code_3 = ['NLD', 'ESP'];
        $this->zone_id = ['2336', '3002'];
        $this->city = ['Noord-Holland', 'Malaga'];
        $this->zone_code = ['NH', 'MA'];
        $this->post_code = ['1033SC', '29688'];
    }

    public function getCustomerAccount() {
        $customer = array(
            'customer_id' => $this->customer_id,
            'customer_group_id' => '1',
            'firstname' => $this->first_name,
            'lastname' => $this->last_name,
            'email' => $this->email,
            'telephone' => $this->telephone[$this->key],
            'fax'      => $this->fax,
            'custom_field' => array(),
        );
        return $customer;
    }

    public function getCustomerPayment() {
        $customer_payment = array(
            'payment_firstname' => $this->first_name,
            'payment_lastname' => $this->last_name,
            'payment_company' => $this->company,
            'payment_address_1' => $this->address[$this->key],
            'payment_address_2' => $this->address_2[$this->key],
            'payment_postcode' => $this->post_code[$this->key],
            'payment_city' => $this->city[$this->key],
            'payment_zone_id' => $this->zone_id[$this->key],
            'payment_zone' => $this->city[$this->key],
            'payment_zone_code' => $this->zone_code[$this->key],
            'payment_country_id' => $this->country_id[$this->key],
            'payment_country' => $this->country[$this->key],
            'payment_iso_code_2' => $this->country_iso_code_2[$this->key],
            'payment_iso_code_3' => $this->country_iso_code_3[$this->key],
            'payment_custom_field' => array(),
        );
        return $customer_payment;
    }

    public function getCustomerShipment() {
        $customer_shipping = array(
            'shipping_firstname' => $this->first_name,
            'shipping_lastname' => $this->last_name,
            'shipping_company' => $this->company,
            'shipping_address_1' => $this->address[$this->key],
            'shipping_address_2' => $this->address_2[$this->key],
            'shipping_postcode' => $this->post_code[$this->key],
            'shipping_city' => $this->city[$this->key],
            'shipping_zone_id' => $this->zone_id[$this->key],
            'shipping_zone' => $this->city[$this->key],
            'shipping_zone_code' => $this->zone_code[$this->key],
            'shipping_country_id' => $this->country_id[$this->key],
            'shipping_country' => $this->country[$this->key],
            'shipping_iso_code_2' => $this->country_iso_code_2[$this->key],
            'shipping_iso_code_3' => $this->country_iso_code_3[$this->key],
            'shipping_custom_field' => array(),
        );
        return $customer_shipping;
    }

    public function getCustomer() {
        $customer = array();
        $customer = array_merge($customer, $this->getCustomerAccount());
        $customer = array_merge($customer, $this->getCustomerPayment());
        $customer = array_merge($customer, $this->getCustomerShipment());
        return $customer;
    }

    public function getCustomerAccountData() {
        $customer = $this->getCustomerAccount();
        $address = $this->getCustomerPayment();
        $customer_account_data = array(
            'customer_group_id' => $customer['customer_group_id'],
            'firstname' => $customer['firstname'],
            'lastname' => $customer['lastname'],
            'email' => $customer['email'],
            'telephone' => $customer['telephone'],
            'fax'      => $this->fax,
            'custom_field' => array(),
            'newsletter' => 1,
            'password' => 'BG49cgqz1Hu',
            'confirm' => 'BG49cgqz1Hu',
            'status' => 1,
            'safe' => 1,
            'address' => array(
                array(
                'firstname' => $address['payment_firstname'],
                'lastname' => $address['payment_lastname'],
                'company' => $address['payment_company'],
                'address_1' => $address['payment_address_1'],
                'address_2' => $address['payment_address_2'],
                'city' => $address['payment_city'],
                'postcode' => $address['payment_postcode'],
                'country_id' => $address['payment_country_id'],
                'zone_id' => $address['payment_zone_id'],
                'custom_field' => array(),
                'default' => 1,
                )
            ),
            'company' => '',
            'website' => '',
            'tracking' => '',
            'commission' =>  '5',
            'tax' => '',
            'payment' => 'cheque',
            'cheque' => '',
            'paypal' => '',
            'bank_name' => '',
            'bank_branch_number' => '',
            'bank_swift_code' => '',
            'bank_account_name' => '',
            'bank_account_number' => '',
            'affiliate' => 0,
            'fax' => '',
            'approved' => 1,
        );
        return $customer_account_data;
    }

}