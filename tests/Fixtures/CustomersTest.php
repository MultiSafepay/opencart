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

class CustomersTest {

    public function __construct($customer_key) {
        $this->key = $customer_key;
        $this->first_name = 'John';
        $this->last_name = 'Doe';
        $this->email = 'integration@multisafepay.com';

        $this->telephone = ['0031345678933', '0034691246168'];
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
            'customer_id' => '1',
            'customer_group_id' => '1',
            'firstname' => $this->first_name,
            'lastname' => $this->last_name,
            'email' => $this->email,
            'telephone' => $this->telephone[$this->key],
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
            'payment_zone_id' => $this->post_code[$this->key],
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

}