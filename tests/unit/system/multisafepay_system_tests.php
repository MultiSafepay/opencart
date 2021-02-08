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

class MultiSafePaySystemTest extends MultisafepayTestSuiteForOpenCart {

    public function setUp() {
        parent::setUp();
        $this->generateCustomerInformation();
        $this->generateGeoZoneAndTaxes();
        $this->unSetTaxRateOfShippingMethod();
        $this->cart->add(28, 1);
    }

    public function testGetOrderRequestObjectDataType() {
        $order_request = array(
            'order_id' => (string)time(),
            'test_mode'=> true,
            'type'     => 'redirect',
            'gateway'  => ''
        );

        $mock = $this->getMockMultiSafepayGetOrderRequestObject($order_request['order_id'], '0', '0');
        $response = $mock->getOrderRequestObject($order_request);
        $response = $response->getData();

        // Start Check Data
        $this->assertIsArray($response);

        // Payment Options
        $this->assertArrayHasKey('payment_options', $response);
        $this->assertArrayHasKey('notification_url', $response['payment_options']);
        $this->assertArrayHasKey('redirect_url', $response['payment_options']);
        $this->assertArrayHasKey('cancel_url', $response['payment_options']);
        $this->assertArrayHasKey('close_window', $response['payment_options']);


        // Second Chance
        $this->assertArrayHasKey('second_chance', $response);
        $this->assertArrayHasKey('send_email', $response['second_chance']);

        // Customer
        $this->assertArrayHasKey('customer', $response);
        $this->assertArrayHasKey('firstname', $response['customer']);
        $this->assertArrayHasKey('lastname', $response['customer']);
        $this->assertArrayHasKey('address1', $response['customer']);
        $this->assertArrayHasKey('address2', $response['customer']);
	    $this->assertArrayHasKey('company_name', $response['customer']);
        $this->assertArrayHasKey('house_number', $response['customer']);
        $this->assertArrayHasKey('zip_code', $response['customer']);
        $this->assertArrayHasKey('city', $response['customer']);
        $this->assertArrayHasKey('state', $response['customer']);
        $this->assertArrayHasKey('country', $response['customer']);
        $this->assertArrayHasKey('phone', $response['customer']);
        $this->assertArrayHasKey('email', $response['customer']);
        $this->assertArrayHasKey('ip_address', $response['customer']);
        $this->assertArrayHasKey('locale', $response['customer']);
        $this->assertArrayHasKey('referrer', $response['customer']);
        $this->assertArrayHasKey('forwarded_ip', $response['customer']);
        $this->assertArrayHasKey('user_agent', $response['customer']);
        $this->assertNotEmpty($response['customer']['firstname']);
        $this->assertNotEmpty($response['customer']['lastname']);
        $this->assertNotEmpty($response['customer']['address1']);
        $this->assertNotEmpty($response['customer']['zip_code']);
        $this->assertNotEmpty($response['customer']['city']);
        $this->assertNotEmpty($response['customer']['state']);
        $this->assertNotEmpty($response['customer']['country']);
        $this->assertNotEmpty($response['customer']['phone']);
        $this->assertNotEmpty($response['customer']['email']);
        $this->assertNotEmpty($response['customer']['ip_address']);
        $this->assertNotEmpty($response['customer']['locale']);
        $this->assertNotEmpty($response['customer']['forwarded_ip']);
        $this->assertNotEmpty($response['customer']['user_agent']);

        // Delivery
        $this->assertArrayHasKey('delivery', $response);
        $this->assertArrayHasKey('firstname', $response['delivery']);
        $this->assertArrayHasKey('lastname', $response['delivery']);
        $this->assertArrayHasKey('address1', $response['delivery']);
        $this->assertArrayHasKey('address2', $response['delivery']);
	    $this->assertArrayHasKey('company_name', $response['delivery']);
        $this->assertArrayHasKey('house_number', $response['delivery']);
        $this->assertArrayHasKey('zip_code', $response['delivery']);
        $this->assertArrayHasKey('city', $response['delivery']);
        $this->assertArrayHasKey('state', $response['delivery']);
        $this->assertArrayHasKey('country', $response['delivery']);
        $this->assertArrayHasKey('phone', $response['delivery']);
        $this->assertArrayHasKey('email', $response['delivery']);
        $this->assertArrayHasKey('ip_address', $response['delivery']);
        $this->assertArrayHasKey('locale', $response['delivery']);
        $this->assertArrayHasKey('referrer', $response['delivery']);
        $this->assertArrayHasKey('forwarded_ip', $response['delivery']);
        $this->assertArrayHasKey('user_agent', $response['delivery']);
        $this->assertNotEmpty($response['delivery']['firstname']);
        $this->assertNotEmpty($response['delivery']['lastname']);
        $this->assertNotEmpty($response['delivery']['address1']);
        $this->assertNotEmpty($response['delivery']['zip_code']);
        $this->assertNotEmpty($response['delivery']['city']);
        $this->assertNotEmpty($response['delivery']['state']);
        $this->assertNotEmpty($response['delivery']['country']);
        $this->assertNotEmpty($response['delivery']['phone']);
        $this->assertNotEmpty($response['delivery']['email']);
        $this->assertNotEmpty($response['delivery']['ip_address']);
        $this->assertNotEmpty($response['delivery']['locale']);
        $this->assertNotEmpty($response['delivery']['forwarded_ip']);
        $this->assertNotEmpty($response['delivery']['user_agent']);

        // Shopping Cart
        $this->assertArrayHasKey('shopping_cart', $response);
        $this->assertArrayHasKey('items', $response['shopping_cart']);
        $this->assertIsArray($response['shopping_cart']['items']);

        foreach ($response['shopping_cart']['items'] as $item) {
            $this->assertArrayHasKey('name', $item);
            $this->assertArrayHasKey('description', $item);
            $this->assertArrayHasKey('unit_price', $item);
            $this->assertArrayHasKey('currency', $item);
            $this->assertArrayHasKey('quantity', $item);
            $this->assertArrayHasKey('merchant_item_id', $item);
            $this->assertArrayHasKey('tax_table_selector', $item);
            $this->assertIsArray($item['weight']);
            if(!empty($item['weight'])) {
                $this->assertArrayHasKey('unit', $item['weight']);
                $this->assertArrayHasKey('value', $item['weight']);
            }
            $this->assertNotEmpty($item['name']);
            $this->assertIsString($item['name']);
            $this->assertNotEmpty($item['unit_price']);
            $this->assertIsString($item['unit_price']);
            $this->assertNotEmpty($item['currency']);
            $this->assertIsString($item['currency']);
            $this->assertNotEmpty($item['quantity']);
            $this->assertIsInt($item['quantity']);
            $this->assertNotEmpty($item['merchant_item_id']);
        }

        // Checkout Options
        $this->assertArrayHasKey('checkout_options', $response);
        $this->assertArrayHasKey('tax_tables', $response['checkout_options']);
        $this->assertArrayHasKey('alternate', $response['checkout_options']['tax_tables']);
        if(!empty($response['checkout_options']['tax_tables']['alternate'])) {
            foreach ($response['checkout_options']['tax_tables']['alternate'] as $tax_table) {
                $this->assertArrayHasKey('name', $tax_table);
                $this->assertArrayHasKey('rules', $tax_table);
            }
        }

        $this->assertArrayHasKey('days_active', $response);
        $this->assertIsInt($response['days_active']);

        // Plugin Version
        $this->assertArrayHasKey('plugin', $response);
        $this->assertArrayHasKey('sdk_version', $response['plugin']);
        $this->assertArrayHasKey('plugin_version', $response['plugin']);
        $this->assertArrayHasKey('shop', $response['plugin']);
        $this->assertArrayHasKey('shop_version', $response['plugin']);
        $this->assertArrayHasKey('partner', $response['plugin']);
        $this->assertArrayHasKey('shop_root_url', $response['plugin']);

        $this->assertEquals($this->multisafepay->getPluginVersion(), $response['plugin']['plugin_version']);

    }

    public function testGetOrderRequestObjectDataValues() {
        $order_request = array(
            'order_id' => '1185',
            'test_mode'=> true,
            'type'     => 'redirect',
            'gateway'  => ''
        );
        $mock = $this->getMockMultiSafepayGetOrderRequestObject($order_request['order_id'], '0', '0');
        $response = $mock->getOrderRequestObject($order_request);
        $response = $response->getData();

        $this->assertEquals('redirect', $response['type']);
        $this->assertEquals('EUR', $response['currency']);
        $this->assertEquals('246086', $response['amount']);
        $this->assertEquals('246086', $response['amount']);

        // Payment Options
        $this->assertContains("index.php?route=" . $this->multisafepay_version_control->getExtensionRoute() . "/callback", $response['payment_options']['notification_url']);
        $this->assertContains("index.php?route=checkout/success", $response['payment_options']['redirect_url']);
        $this->assertContains("index.php?route=checkout/checkout", $response['payment_options']['cancel_url']);
        $this->assertEquals('1', $response['payment_options']['close_window']);

        // Second Chance
        $this->assertEquals('1', $response['second_chance']['send_email']);

        // Customer
        $this->assertEquals('John', $response['customer']['firstname']);
        $this->assertEquals('Doe', $response['customer']['lastname']);
        $this->assertEquals('Kraanspoor', $response['customer']['address1']);
        $this->assertEquals('', $response['customer']['address2']);
	    $this->assertEquals('MultiSafepay', $response['customer']['company_name']);
        $this->assertEquals('39C', $response['customer']['house_number']);
        $this->assertEquals('1033SC', $response['customer']['zip_code']);
        $this->assertEquals('Noord-Holland', $response['customer']['city']);
        $this->assertEquals('Noord-Holland', $response['customer']['state']);
        $this->assertEquals('NL', $response['customer']['country']);
        $this->assertEquals('0031345678933', $response['customer']['phone']);
        $this->assertSame($response['customer']['email'], filter_var($response['customer']['email'], FILTER_VALIDATE_EMAIL));
        $this->assertEquals('127.0.0.1', $response['customer']['ip_address']);
        $this->assertContains( $response['customer']['locale'], array('en_GB', 'en_US', 'nl_NL'));
        $this->assertEquals('', $response['customer']['referrer']);
        $this->assertEquals('127.0.0.1', $response['customer']['forwarded_ip']);
        $this->assertEquals('Mozilla/5.0 (iPad; CPU OS 7_0_1 like Mac OS X; sl-SI) AppleWebKit/534.8.1 (KHTML, like Gecko) Version/3.0.5 Mobile/8B118 Safari/6534.8.1', $response['customer']['user_agent']);

        // Delivery
        $this->assertEquals('John', $response['delivery']['firstname']);
        $this->assertEquals('Doe', $response['delivery']['lastname']);
        $this->assertEquals('Kraanspoor', $response['delivery']['address1']);
        $this->assertEquals('', $response['delivery']['address2']);
	    $this->assertEquals('MultiSafepay', $response['delivery']['company_name']);
        $this->assertEquals('39C', $response['delivery']['house_number']);
        $this->assertEquals('1033SC', $response['delivery']['zip_code']);
        $this->assertEquals('Noord-Holland', $response['delivery']['city']);
        $this->assertEquals('Noord-Holland', $response['delivery']['state']);
        $this->assertEquals('NL', $response['delivery']['country']);
        $this->assertEquals('0031345678933', $response['delivery']['phone']);
        $this->assertSame($response['delivery']['email'], filter_var($response['delivery']['email'], FILTER_VALIDATE_EMAIL));
        $this->assertEquals('127.0.0.1', $response['delivery']['ip_address']);
        $this->assertContains( $response['delivery']['locale'], array('en_GB', 'en_US', 'nl_NL'));
        $this->assertEquals('', $response['delivery']['referrer']);
        $this->assertEquals('127.0.0.1', $response['delivery']['forwarded_ip']);
        $this->assertEquals('Mozilla/5.0 (iPad; CPU OS 7_0_1 like Mac OS X; sl-SI) AppleWebKit/534.8.1 (KHTML, like Gecko) Version/3.0.5 Mobile/8B118 Safari/6534.8.1', $response['delivery']['user_agent']);

        // Shopping Cart Item 0
        $this->assertEquals('1 x Canon EOS 5D', $response['shopping_cart']['items'][0]['name']);
        $this->assertEquals('78.4600020000', $response['shopping_cart']['items'][0]['unit_price']);
        $this->assertEquals('EUR', $response['shopping_cart']['items'][0]['currency']);
        $this->assertEquals('1', $response['shopping_cart']['items'][0]['quantity']);
        $this->assertEquals('30', $response['shopping_cart']['items'][0]['merchant_item_id']);
        $this->assertEquals('21', $response['shopping_cart']['items'][0]['tax_table_selector']);
        $this->assertEquals('KG', $response['shopping_cart']['items'][0]['weight']['unit']);
        $this->assertEquals('0', $response['shopping_cart']['items'][0]['weight']['value']);

        // Shopping Cart Item 1
        $this->assertEquals('1 x Samsung Galaxy Tab 10.1', $response['shopping_cart']['items'][1]['name']);
        $this->assertEquals('156.9121579998', $response['shopping_cart']['items'][1]['unit_price']);
        $this->assertEquals('EUR', $response['shopping_cart']['items'][1]['currency']);
        $this->assertEquals('1', $response['shopping_cart']['items'][1]['quantity']);
        $this->assertEquals('49', $response['shopping_cart']['items'][1]['merchant_item_id']);
        $this->assertEquals('21', $response['shopping_cart']['items'][1]['tax_table_selector']);
        $this->assertEquals('KG', $response['shopping_cart']['items'][1]['weight']['unit']);
        $this->assertEquals('0', $response['shopping_cart']['items'][1]['weight']['value']);

        // Shopping Cart Item 2
        $this->assertEquals('1 x HTC Touch HD', $response['shopping_cart']['items'][2]['name']);
        $this->assertEquals('78.4600020000', $response['shopping_cart']['items'][2]['unit_price']);
        $this->assertEquals('EUR', $response['shopping_cart']['items'][2]['currency']);
        $this->assertEquals('1', $response['shopping_cart']['items'][2]['quantity']);
        $this->assertEquals('28', $response['shopping_cart']['items'][2]['merchant_item_id']);
        $this->assertEquals('21', $response['shopping_cart']['items'][2]['tax_table_selector']);
        $this->assertEquals('G', $response['shopping_cart']['items'][2]['weight']['unit']);
        $this->assertEquals('146.4', $response['shopping_cart']['items'][2]['weight']['value']);

        // Shopping Cart Item 3
        $this->assertEquals('1 x Palm Treo Pro', $response['shopping_cart']['items'][3]['name']);
        $this->assertEquals('219.6801595998', $response['shopping_cart']['items'][3]['unit_price']);
        $this->assertEquals('EUR', $response['shopping_cart']['items'][3]['currency']);
        $this->assertEquals('1', $response['shopping_cart']['items'][3]['quantity']);
        $this->assertEquals('29', $response['shopping_cart']['items'][3]['merchant_item_id']);
        $this->assertEquals('21', $response['shopping_cart']['items'][3]['tax_table_selector']);
        $this->assertEquals('G', $response['shopping_cart']['items'][3]['weight']['unit']);
        $this->assertEquals('133', $response['shopping_cart']['items'][3]['weight']['value']);

        // Shopping Cart Item 4
        $this->assertEquals('1 x iMac', $response['shopping_cart']['items'][4]['name']);
        $this->assertEquals('1271.0520324000', $response['shopping_cart']['items'][4]['unit_price']);
        $this->assertEquals('EUR', $response['shopping_cart']['items'][4]['currency']);
        $this->assertEquals('1', $response['shopping_cart']['items'][4]['quantity']);
        $this->assertEquals('41', $response['shopping_cart']['items'][4]['merchant_item_id']);
        $this->assertEquals('21', $response['shopping_cart']['items'][4]['tax_table_selector']);
        $this->assertEquals('KG', $response['shopping_cart']['items'][4]['weight']['unit']);
        $this->assertEquals('5', $response['shopping_cart']['items'][4]['weight']['value']);

        // Shopping Cart Item 5
        $this->assertEquals('2 x Apple Cinema 30"', $response['shopping_cart']['items'][5]['name']);
        $this->assertEquals('112.9824028800', $response['shopping_cart']['items'][5]['unit_price']);
        $this->assertEquals('EUR', $response['shopping_cart']['items'][5]['currency']);
        $this->assertEquals('2', $response['shopping_cart']['items'][5]['quantity']);
        $this->assertEquals('42', $response['shopping_cart']['items'][5]['merchant_item_id']);
        $this->assertEquals('21', $response['shopping_cart']['items'][5]['tax_table_selector']);
        $this->assertEquals('KG', $response['shopping_cart']['items'][5]['weight']['unit']);
        $this->assertEquals('12.5', $response['shopping_cart']['items'][5]['weight']['value']);

        // Shopping Cart Item 6
        $this->assertEquals('Flat Shipping Rate', $response['shopping_cart']['items'][6]['name']);
        $this->assertEquals('3.9230001000', $response['shopping_cart']['items'][6]['unit_price']);
        $this->assertEquals('EUR', $response['shopping_cart']['items'][6]['currency']);
        $this->assertEquals('1', $response['shopping_cart']['items'][6]['quantity']);
        $this->assertEquals('msp-shipping', $response['shopping_cart']['items'][6]['merchant_item_id']);
        $this->assertEquals('0', $response['shopping_cart']['items'][6]['tax_table_selector']);

        // Taxes
        $this->assertEquals('21', $response['checkout_options']['tax_tables']['alternate'][0]['name']);
        $this->assertEquals('0.21', $response['checkout_options']['tax_tables']['alternate'][0]['rules'][0]['rate']);



        // Plugin
        $this->assertEquals($this->multisafepay->getPluginVersion(), $response['plugin']['plugin_version']);
        $this->assertEquals('OpenCart', $response['plugin']['shop']);
        $this->assertEquals($this->multisafepay->getShopUrl(), $response['plugin']['shop_root_url']);
    }


    public function testGetMoneyObjectOrderAmountAsFloatFromMoneyObject() {
        $response = $this->multisafepay->getMoneyObjectOrderAmount('127.2100', 'EUR', '0.78460002');
        $this->assertEquals('EUR', $response->getCurrency());
        $this->assertEquals('9980.89685442', $response->getAmount());
        $this->assertEquals(998089.6854419999, $response->getAmountInCents());
        $this->assertIsFloat($response->getAmount());
        $this->assertIsFloat($response->getAmountInCents());
    }

    public function testGetOrderRequestRoundingIssueObjectDataValues() {
        $order_request = array(
            'order_id' => '1185',
            'test_mode'=> true,
            'type'     => 'redirect',
            'gateway'  => ''
        );
        $mock = $this->getMockMultiSafepayGetOrderRequestObject($order_request['order_id'], '1', '0');
        $response = $mock->getOrderRequestObject($order_request);
        $response = $response->getData();

        $this->assertEquals('redirect', $response['type']);
        $this->assertEquals('EUR', $response['currency']);
        $this->assertEquals('9981', $response['amount']);
        $this->assertIsNotFloat($response['amount']);
        $this->assertIsInt($response['amount']);

        // Payment Options
        $this->assertContains("index.php?route=" . $this->multisafepay_version_control->getExtensionRoute() . "/callback", $response['payment_options']['notification_url']);
        $this->assertContains("index.php?route=checkout/success", $response['payment_options']['redirect_url']);
        $this->assertContains("index.php?route=checkout/checkout", $response['payment_options']['cancel_url']);
        $this->assertEquals('1', $response['payment_options']['close_window']);

        // Second Chance
        $this->assertEquals('1', $response['second_chance']['send_email']);

        // Customer
        $this->assertEquals('John', $response['customer']['firstname']);
        $this->assertEquals('Doe', $response['customer']['lastname']);
        $this->assertEquals('Kraanspoor', $response['customer']['address1']);
        $this->assertEquals('', $response['customer']['address2']);
	    $this->assertEquals('MultiSafepay', $response['customer']['company_name']);
        $this->assertEquals('39C', $response['customer']['house_number']);
        $this->assertEquals('1033SC', $response['customer']['zip_code']);
        $this->assertEquals('Noord-Holland', $response['customer']['city']);
        $this->assertEquals('Noord-Holland', $response['customer']['state']);
        $this->assertEquals('NL', $response['customer']['country']);
        $this->assertEquals('0031345678933', $response['customer']['phone']);
        $this->assertSame($response['customer']['email'], filter_var($response['customer']['email'], FILTER_VALIDATE_EMAIL));
        $this->assertEquals('127.0.0.1', $response['customer']['ip_address']);
        $this->assertContains( $response['customer']['locale'], array('en_GB', 'en_US', 'nl_NL'));
        $this->assertEquals('', $response['customer']['referrer']);
        $this->assertEquals('127.0.0.1', $response['customer']['forwarded_ip']);
        $this->assertEquals('Mozilla/5.0 (iPad; CPU OS 7_0_1 like Mac OS X; sl-SI) AppleWebKit/534.8.1 (KHTML, like Gecko) Version/3.0.5 Mobile/8B118 Safari/6534.8.1', $response['customer']['user_agent']);

        // Delivery
        $this->assertEquals('John', $response['delivery']['firstname']);
        $this->assertEquals('Doe', $response['delivery']['lastname']);
        $this->assertEquals('Kraanspoor', $response['delivery']['address1']);
        $this->assertEquals('', $response['delivery']['address2']);
	    $this->assertEquals('MultiSafepay', $response['delivery']['company_name']);
        $this->assertEquals('39C', $response['delivery']['house_number']);
        $this->assertEquals('1033SC', $response['delivery']['zip_code']);
        $this->assertEquals('Noord-Holland', $response['delivery']['city']);
        $this->assertEquals('Noord-Holland', $response['delivery']['state']);
        $this->assertEquals('NL', $response['delivery']['country']);
        $this->assertEquals('0031345678933', $response['delivery']['phone']);
        $this->assertSame($response['delivery']['email'], filter_var($response['delivery']['email'], FILTER_VALIDATE_EMAIL));
        $this->assertEquals('127.0.0.1', $response['delivery']['ip_address']);
        $this->assertContains( $response['delivery']['locale'], array('en_GB', 'en_US', 'nl_NL'));
        $this->assertEquals('', $response['delivery']['referrer']);
        $this->assertEquals('127.0.0.1', $response['delivery']['forwarded_ip']);
        $this->assertEquals('Mozilla/5.0 (iPad; CPU OS 7_0_1 like Mac OS X; sl-SI) AppleWebKit/534.8.1 (KHTML, like Gecko) Version/3.0.5 Mobile/8B118 Safari/6534.8.1', $response['delivery']['user_agent']);

        // Shopping Cart Item 0
        $this->assertEquals('1 x iPhone', $response['shopping_cart']['items'][0]['name']);
        $this->assertEquals('79.2446020200', $response['shopping_cart']['items'][0]['unit_price']);
        $this->assertEquals('EUR', $response['shopping_cart']['items'][0]['currency']);
        $this->assertEquals('1', $response['shopping_cart']['items'][0]['quantity']);
        $this->assertEquals('40', $response['shopping_cart']['items'][0]['merchant_item_id']);
        $this->assertEquals('21', $response['shopping_cart']['items'][0]['tax_table_selector']);
        $this->assertEquals('KG', $response['shopping_cart']['items'][0]['weight']['unit']);
        $this->assertEquals('10', $response['shopping_cart']['items'][0]['weight']['value']);

        // Shopping Cart Item 1
        $this->assertEquals('Flat Shipping Rate', $response['shopping_cart']['items'][1]['name']);
        $this->assertEquals('3.9230001000', $response['shopping_cart']['items'][1]['unit_price']);
        $this->assertEquals('EUR', $response['shopping_cart']['items'][1]['currency']);
        $this->assertEquals('1', $response['shopping_cart']['items'][1]['quantity']);
        $this->assertEquals('msp-shipping', $response['shopping_cart']['items'][1]['merchant_item_id']);
        $this->assertEquals('0', $response['shopping_cart']['items'][1]['tax_table_selector']);

        // Taxes
        $this->assertEquals('21', $response['checkout_options']['tax_tables']['alternate'][0]['name']);
        $this->assertEquals('0.21', $response['checkout_options']['tax_tables']['alternate'][0]['rules'][0]['rate']);

        $this->assertEquals('0', $response['checkout_options']['tax_tables']['alternate'][1]['name']);
        $this->assertEquals('0', $response['checkout_options']['tax_tables']['alternate'][1]['rules'][0]['rate']);


        // Plugin
        $this->assertEquals($this->multisafepay->getPluginVersion(), $response['plugin']['plugin_version']);
        $this->assertEquals('OpenCart', $response['plugin']['shop']);
        $this->assertEquals($this->multisafepay->getShopUrl(), $response['plugin']['shop_root_url']);
    }

    public function testGetGatewayInfoMetaData() {
        $order_request = array(
            'order_id'      => '1185',
            'test_mode'     => true,
            'type'          => 'redirect',
            'gateway_info'  => 'Meta',
            'gender'        => 'male',
            'birthday'      => '1985-01-22',
            'bankaccount'   => 'NL87ABNA0000000001',
            'gateway'       => ''
        );
        $mock = $this->getMockMultiSafepayGetOrderRequestObject($order_request['order_id'], '1', '0');
        $response = $mock->getOrderRequestObject($order_request);
        $response = $response->getData();

        $this->assertIsArray($response['gateway_info']);
        $this->assertArrayHasKey('phone', $response['gateway_info']);
        $this->assertArrayHasKey('email', $response['gateway_info']);
        $this->assertArrayHasKey('gender', $response['gateway_info']);
        $this->assertArrayHasKey('birthday', $response['gateway_info']);
        $this->assertArrayHasKey('bankaccount', $response['gateway_info']);

    }

    public function testGetGatewayInfoMetaValues() {
        $order_request = array(
            'order_id'      => '1185',
            'test_mode'     => true,
            'type'          => 'redirect',
            'gateway_info'  => 'Meta',
            'gender'        => 'male',
            'birthday'      => '1985-01-22',
            'bankaccount'   => 'NL87ABNA0000000001',
            'gateway'       => ''
        );
        $mock = $this->getMockMultiSafepayGetOrderRequestObject($order_request['order_id'], '1', '0');
        $response = $mock->getOrderRequestObject($order_request);
        $response = $response->getData();
        $this->assertEquals('0031345678933', $response['gateway_info']['phone']);
//        $this->assertEquals('integration@multisafepay.com', $response['gateway_info']['email']);
        $this->assertEquals('male', $response['gateway_info']['gender']);
        $this->assertEquals('1985-01-22', $response['gateway_info']['birthday']);
        $this->assertEquals('NL87ABNA0000000001', $response['gateway_info']['bankaccount']);
    }


    public function testGetGatewayInfoAccountData() {
        $order_request = array(
            'order_id'              => '1185',
            'test_mode'             => true,
            'type'                  => 'redirect',
            'gateway_info'          => 'Account',
            'account_holder_iban'   => 'NL87ABNA0000000001',
            'account_holder_name'   => 'MultiSafepay',
            'emandate'              => '1185'
        );
        $mock = $this->getMockMultiSafepayGetOrderRequestObject($order_request['order_id'], '1', '0');
        $response = $mock->getOrderRequestObject($order_request);
        $response = $response->getData();
        $this->assertIsArray($response['gateway_info']);
        $this->assertArrayHasKey('account_holder_iban', $response['gateway_info']);
        $this->assertArrayHasKey('account_holder_name', $response['gateway_info']);
        $this->assertArrayHasKey('emandate', $response['gateway_info']);
    }

    public function testGetGatewayInfoAccountValues() {
        $order_request = array(
            'order_id'              => '1185',
            'test_mode'             => true,
            'type'                  => 'redirect',
            'gateway_info'          => 'Account',
            'account_holder_iban'   => 'NL87ABNA0000000001',
            'account_holder_name'   => 'MultiSafepay',
            'emandate'              => '1185'
        );
        $mock = $this->getMockMultiSafepayGetOrderRequestObject($order_request['order_id'], '1', '0');
        $response = $mock->getOrderRequestObject($order_request);
        $response = $response->getData();
        $this->assertEquals('NL87ABNA0000000001', $response['gateway_info']['account_holder_iban']);
        $this->assertEquals('MultiSafepay', $response['gateway_info']['account_holder_name']);
        $this->assertEquals('1185', $response['gateway_info']['emandate']);
    }

    public function testGetGatewayInfoQrCodeData() {
        $order_request = array(
            'order_id'              => '1185',
            'test_mode'             => true,
            'type'                  => 'redirect',
            'gateway_info'          => 'QrCode',
        );
        $mock = $this->getMockMultiSafepayGetOrderRequestObject($order_request['order_id'], '1', '0');
        $response = $mock->getOrderRequestObject($order_request);
        $response = $response->getData();
        $this->assertIsArray($response['gateway_info']);
        $this->assertArrayHasKey('qr_size', $response['gateway_info']);
        $this->assertArrayHasKey('allow_multiple', $response['gateway_info']);
        $this->assertArrayHasKey('allow_change_amount', $response['gateway_info']);
    }

    public function testGetGatewayInfoQrCodeValues() {
        $order_request = array(
            'order_id'              => '1185',
            'test_mode'             => true,
            'type'                  => 'redirect',
            'gateway_info'          => 'QrCode',
        );
        $mock = $this->getMockMultiSafepayGetOrderRequestObject($order_request['order_id'], '1', '0');
        $response = $mock->getOrderRequestObject($order_request);
        $response = $response->getData();
        $this->assertEquals('250', $response['gateway_info']['qr_size']);
        $this->assertFalse($response['gateway_info']['allow_multiple']);
        $this->assertFalse($response['gateway_info']['allow_change_amount']);
    }

    public function testSimpleTransaction() {
        $order_request = array(
            'order_id' => '6559',
            'test_mode'=> true,
            'type'     => 'redirect',
            'gateway'  => ''
        );
        $mock = $this->getMockMultiSafepayGetOrderRequestObject($order_request['order_id'], '2', '1');
        $response = $mock->getOrderRequestObject($order_request);
        $response = $response->getData();

        $this->assertEquals('redirect', $response['type']);
        $this->assertEquals('EUR', $response['currency']);
        $this->assertEquals('8317', $response['amount']);
        $this->assertIsNotFloat($response['amount']);
        $this->assertIsInt($response['amount']);

        // Payment Options
        $this->assertContains("index.php?route=" . $this->multisafepay_version_control->getExtensionRoute() . "/callback", $response['payment_options']['notification_url']);
        $this->assertContains("index.php?route=checkout/success", $response['payment_options']['redirect_url']);
        $this->assertContains("index.php?route=checkout/checkout", $response['payment_options']['cancel_url']);
        $this->assertEquals('1', $response['payment_options']['close_window']);

        // Second Chance
        $this->assertEquals('1', $response['second_chance']['send_email']);

        // Customer
        $this->assertEquals('John', $response['customer']['firstname']);
        $this->assertEquals('Doe', $response['customer']['lastname']);
        $this->assertEquals('Urb. El Saladillo, Edf. Altair Oficina', $response['customer']['address1']);
        $this->assertEquals('', $response['customer']['address2']);
	    $this->assertEquals('MultiSafepay', $response['customer']['company_name']);
        $this->assertEquals('207', $response['customer']['house_number']);
        $this->assertEquals('29688', $response['customer']['zip_code']);
        $this->assertEquals('Malaga', $response['customer']['city']);
        $this->assertEquals('Malaga', $response['customer']['state']);
        $this->assertEquals('ES', $response['customer']['country']);
        $this->assertEquals('0034691246168', $response['customer']['phone']);
        $this->assertSame($response['customer']['email'], filter_var($response['customer']['email'], FILTER_VALIDATE_EMAIL));
        $this->assertEquals('127.0.0.1', $response['customer']['ip_address']);
        $this->assertContains( $response['customer']['locale'], array('en_GB', 'en_US', 'nl_NL'));
        $this->assertEquals('', $response['customer']['referrer']);
        $this->assertEquals('127.0.0.1', $response['customer']['forwarded_ip']);
        $this->assertEquals('Mozilla/5.0 (iPad; CPU OS 7_0_1 like Mac OS X; sl-SI) AppleWebKit/534.8.1 (KHTML, like Gecko) Version/3.0.5 Mobile/8B118 Safari/6534.8.1', $response['customer']['user_agent']);

        // Delivery
        $this->assertEquals('John', $response['delivery']['firstname']);
        $this->assertEquals('Doe', $response['delivery']['lastname']);
        $this->assertEquals('Urb. El Saladillo, Edf. Altair Oficina', $response['delivery']['address1']);
        $this->assertEquals('', $response['delivery']['address2']);
	    $this->assertEquals('MultiSafepay', $response['delivery']['company_name']);
        $this->assertEquals('207', $response['delivery']['house_number']);
        $this->assertEquals('29688', $response['delivery']['zip_code']);
        $this->assertEquals('Malaga', $response['delivery']['city']);
        $this->assertEquals('Malaga', $response['delivery']['state']);
        $this->assertEquals('ES', $response['delivery']['country']);
        $this->assertEquals('0034691246168', $response['delivery']['phone']);
        $this->assertSame($response['delivery']['email'], filter_var($response['delivery']['email'], FILTER_VALIDATE_EMAIL));
        $this->assertEquals('127.0.0.1', $response['delivery']['ip_address']);
        $this->assertContains( $response['delivery']['locale'], array('en_GB', 'en_US', 'nl_NL'));
        $this->assertEquals('', $response['delivery']['referrer']);
        $this->assertEquals('127.0.0.1', $response['delivery']['forwarded_ip']);
        $this->assertEquals('Mozilla/5.0 (iPad; CPU OS 7_0_1 like Mac OS X; sl-SI) AppleWebKit/534.8.1 (KHTML, like Gecko) Version/3.0.5 Mobile/8B118 Safari/6534.8.1', $response['delivery']['user_agent']);

        // Shopping Cart Item 0
        $this->assertEquals('1 x iPhone', $response['shopping_cart']['items'][0]['name']);
        $this->assertEquals('79.2446020200', $response['shopping_cart']['items'][0]['unit_price']);
        $this->assertEquals('EUR', $response['shopping_cart']['items'][0]['currency']);
        $this->assertEquals('1', $response['shopping_cart']['items'][0]['quantity']);
        $this->assertEquals('40', $response['shopping_cart']['items'][0]['merchant_item_id']);
        $this->assertEquals('0', $response['shopping_cart']['items'][0]['tax_table_selector']);
        $this->assertEquals('KG', $response['shopping_cart']['items'][0]['weight']['unit']);
        $this->assertEquals('10', $response['shopping_cart']['items'][0]['weight']['value']);

        // Shopping Cart Item 1
        $this->assertEquals('Flat Shipping Rate', $response['shopping_cart']['items'][1]['name']);
        $this->assertEquals('3.9230001000', $response['shopping_cart']['items'][1]['unit_price']);
        $this->assertEquals('EUR', $response['shopping_cart']['items'][1]['currency']);
        $this->assertEquals('1', $response['shopping_cart']['items'][1]['quantity']);
        $this->assertEquals('msp-shipping', $response['shopping_cart']['items'][1]['merchant_item_id']);
        $this->assertEquals('0', $response['shopping_cart']['items'][1]['tax_table_selector']);

        // Taxes
        $this->assertEquals('0', $response['checkout_options']['tax_tables']['alternate'][0]['name']);
        $this->assertEquals('0', $response['checkout_options']['tax_tables']['alternate'][0]['rules'][0]['rate']);


        // Plugin
        $this->assertEquals($this->multisafepay->getPluginVersion(), $response['plugin']['plugin_version']);
        $this->assertEquals('OpenCart', $response['plugin']['shop']);
        $this->assertEquals($this->multisafepay->getShopUrl(), $response['plugin']['shop_root_url']);

    }

    public function testSimpleTransactionWithCoupons() {
        $this->generateCoupons();
        $order_request = array(
            'order_id' => '6560',
            'test_mode'=> true,
            'type'     => 'redirect',
            'gateway'  => ''
        );
        $this->session->data['coupon'] = '101010';
        $mock = $this->getMockMultiSafepayGetOrderRequestObject($order_request['order_id'], '3', '1');
        $response = $mock->getOrderRequestObject($order_request);
        $response = $response->getData();

        $this->assertEquals('redirect', $response['type']);
        $this->assertEquals('EUR', $response['currency']);
        $this->assertEquals('7524', $response['amount']);
        $this->assertIsNotFloat($response['amount']);
        $this->assertIsInt($response['amount']);

        // Payment Options
        $this->assertContains("index.php?route=" . $this->multisafepay_version_control->getExtensionRoute() . "/callback", $response['payment_options']['notification_url']);
        $this->assertContains("index.php?route=checkout/success", $response['payment_options']['redirect_url']);
        $this->assertContains("index.php?route=checkout/checkout", $response['payment_options']['cancel_url']);
        $this->assertEquals('1', $response['payment_options']['close_window']);

        // Second Chance
        $this->assertEquals('1', $response['second_chance']['send_email']);

        // Customer
        $this->assertEquals('John', $response['customer']['firstname']);
        $this->assertEquals('Doe', $response['customer']['lastname']);
        $this->assertEquals('Urb. El Saladillo, Edf. Altair Oficina', $response['customer']['address1']);
        $this->assertEquals('', $response['customer']['address2']);
	    $this->assertEquals('MultiSafepay', $response['customer']['company_name']);
        $this->assertEquals('207', $response['customer']['house_number']);
        $this->assertEquals('29688', $response['customer']['zip_code']);
        $this->assertEquals('Malaga', $response['customer']['city']);
        $this->assertEquals('Malaga', $response['customer']['state']);
        $this->assertEquals('ES', $response['customer']['country']);
        $this->assertEquals('0034691246168', $response['customer']['phone']);
        $this->assertSame($response['customer']['email'], filter_var($response['customer']['email'], FILTER_VALIDATE_EMAIL));
        $this->assertEquals('127.0.0.1', $response['customer']['ip_address']);
        $this->assertContains( $response['customer']['locale'], array('en_GB', 'en_US', 'nl_NL'));
        $this->assertEquals('', $response['customer']['referrer']);
        $this->assertEquals('127.0.0.1', $response['customer']['forwarded_ip']);
        $this->assertEquals('Mozilla/5.0 (iPad; CPU OS 7_0_1 like Mac OS X; sl-SI) AppleWebKit/534.8.1 (KHTML, like Gecko) Version/3.0.5 Mobile/8B118 Safari/6534.8.1', $response['customer']['user_agent']);

        // Delivery
        $this->assertEquals('John', $response['delivery']['firstname']);
        $this->assertEquals('Doe', $response['delivery']['lastname']);
        $this->assertEquals('Urb. El Saladillo, Edf. Altair Oficina', $response['delivery']['address1']);
        $this->assertEquals('', $response['delivery']['address2']);
	    $this->assertEquals('MultiSafepay', $response['delivery']['company_name']);
        $this->assertEquals('207', $response['delivery']['house_number']);
        $this->assertEquals('29688', $response['delivery']['zip_code']);
        $this->assertEquals('Malaga', $response['delivery']['city']);
        $this->assertEquals('Malaga', $response['delivery']['state']);
        $this->assertEquals('ES', $response['delivery']['country']);
        $this->assertEquals('0034691246168', $response['delivery']['phone']);
        $this->assertSame($response['delivery']['email'], filter_var($response['delivery']['email'], FILTER_VALIDATE_EMAIL));
        $this->assertEquals('127.0.0.1', $response['delivery']['ip_address']);
        $this->assertContains( $response['delivery']['locale'], array('en_GB', 'en_US', 'nl_NL'));
        $this->assertEquals('', $response['delivery']['referrer']);
        $this->assertEquals('127.0.0.1', $response['delivery']['forwarded_ip']);
        $this->assertEquals('Mozilla/5.0 (iPad; CPU OS 7_0_1 like Mac OS X; sl-SI) AppleWebKit/534.8.1 (KHTML, like Gecko) Version/3.0.5 Mobile/8B118 Safari/6534.8.1', $response['delivery']['user_agent']);

        // Shopping Cart Item 0
        $this->assertEquals('1 x iPhone - Coupon applied: -10% Discount', $response['shopping_cart']['items'][0]['name']);
        $this->assertEquals('71.3201418180', $response['shopping_cart']['items'][0]['unit_price']);
        $this->assertEquals('EUR', $response['shopping_cart']['items'][0]['currency']);
        $this->assertEquals('1', $response['shopping_cart']['items'][0]['quantity']);
        $this->assertEquals('40', $response['shopping_cart']['items'][0]['merchant_item_id']);
        $this->assertEquals('0', $response['shopping_cart']['items'][0]['tax_table_selector']);
        $this->assertEquals('KG', $response['shopping_cart']['items'][0]['weight']['unit']);
        $this->assertEquals('10', $response['shopping_cart']['items'][0]['weight']['value']);

        // Shopping Cart Item 1
        $this->assertEquals('Flat Shipping Rate', $response['shopping_cart']['items'][1]['name']);
        $this->assertEquals('3.9230001000', $response['shopping_cart']['items'][1]['unit_price']);
        $this->assertEquals('EUR', $response['shopping_cart']['items'][1]['currency']);
        $this->assertEquals('1', $response['shopping_cart']['items'][1]['quantity']);
        $this->assertEquals('msp-shipping', $response['shopping_cart']['items'][1]['merchant_item_id']);
        $this->assertEquals('0', $response['shopping_cart']['items'][1]['tax_table_selector']);

        // Taxes
        $this->assertEquals('0', $response['checkout_options']['tax_tables']['alternate'][0]['name']);
        $this->assertEquals('0', $response['checkout_options']['tax_tables']['alternate'][0]['rules'][0]['rate']);


        // Plugin
        $this->assertEquals($this->multisafepay->getPluginVersion(), $response['plugin']['plugin_version']);
        $this->assertEquals('OpenCart', $response['plugin']['shop']);
        $this->assertEquals($this->multisafepay->getShopUrl(), $response['plugin']['shop_root_url']);

    }


    public function testSimpleTransactionWithRewardPoints() {
        $order_request = array(
            'order_id' => '6610',
            'test_mode'=> true,
            'type'     => 'redirect',
            'gateway'  => ''
        );

        $this->session->data['reward'] = '100';
        $mock = $this->getMockMultiSafepayGetOrderRequestObject($order_request['order_id'], '4', '1');
        $response = $mock->getOrderRequestObject($order_request);
        $response = $response->getData();

        $this->assertEquals('redirect', $response['type']);
        $this->assertEquals('EUR', $response['currency']);
        $this->assertEquals('128648', $response['amount']);
        $this->assertIsNotFloat($response['amount']);
        $this->assertIsInt($response['amount']);

        // Payment Options
        $this->assertContains("index.php?route=" . $this->multisafepay_version_control->getExtensionRoute() . "/callback", $response['payment_options']['notification_url']);
        $this->assertContains("index.php?route=checkout/success", $response['payment_options']['redirect_url']);
        $this->assertContains("index.php?route=checkout/checkout", $response['payment_options']['cancel_url']);
        $this->assertEquals('1', $response['payment_options']['close_window']);

        // Second Chance
        $this->assertEquals('1', $response['second_chance']['send_email']);

        // Customer
        $this->assertEquals('John', $response['customer']['firstname']);
        $this->assertEquals('Doe', $response['customer']['lastname']);
        $this->assertEquals('Urb. El Saladillo, Edf. Altair Oficina', $response['customer']['address1']);
        $this->assertEquals('', $response['customer']['address2']);
	    $this->assertEquals('MultiSafepay', $response['customer']['company_name']);
        $this->assertEquals('207', $response['customer']['house_number']);
        $this->assertEquals('29688', $response['customer']['zip_code']);
        $this->assertEquals('Malaga', $response['customer']['city']);
        $this->assertEquals('Malaga', $response['customer']['state']);
        $this->assertEquals('ES', $response['customer']['country']);
        $this->assertEquals('0034691246168', $response['customer']['phone']);
        $this->assertSame($response['customer']['email'], filter_var($response['customer']['email'], FILTER_VALIDATE_EMAIL));
        $this->assertEquals('127.0.0.1', $response['customer']['ip_address']);
        $this->assertContains( $response['customer']['locale'], array('en_GB', 'en_US', 'nl_NL'));
        $this->assertEquals('', $response['customer']['referrer']);
        $this->assertEquals('127.0.0.1', $response['customer']['forwarded_ip']);
        $this->assertEquals('Mozilla/5.0 (iPad; CPU OS 7_0_1 like Mac OS X; sl-SI) AppleWebKit/534.8.1 (KHTML, like Gecko) Version/3.0.5 Mobile/8B118 Safari/6534.8.1', $response['customer']['user_agent']);

        // Delivery
        $this->assertEquals('John', $response['delivery']['firstname']);
        $this->assertEquals('Doe', $response['delivery']['lastname']);
        $this->assertEquals('Urb. El Saladillo, Edf. Altair Oficina', $response['delivery']['address1']);
        $this->assertEquals('', $response['delivery']['address2']);
	    $this->assertEquals('MultiSafepay', $response['delivery']['company_name']);
        $this->assertEquals('207', $response['delivery']['house_number']);
        $this->assertEquals('29688', $response['delivery']['zip_code']);
        $this->assertEquals('Malaga', $response['delivery']['city']);
        $this->assertEquals('Malaga', $response['delivery']['state']);
        $this->assertEquals('ES', $response['delivery']['country']);
        $this->assertEquals('0034691246168', $response['delivery']['phone']);
        $this->assertSame($response['delivery']['email'], filter_var($response['delivery']['email'], FILTER_VALIDATE_EMAIL));
        $this->assertEquals('127.0.0.1', $response['delivery']['ip_address']);
        $this->assertContains( $response['delivery']['locale'], array('en_GB', 'en_US', 'nl_NL'));
        $this->assertEquals('', $response['delivery']['referrer']);
        $this->assertEquals('127.0.0.1', $response['delivery']['forwarded_ip']);
        $this->assertEquals('Mozilla/5.0 (iPad; CPU OS 7_0_1 like Mac OS X; sl-SI) AppleWebKit/534.8.1 (KHTML, like Gecko) Version/3.0.5 Mobile/8B118 Safari/6534.8.1', $response['delivery']['user_agent']);

        // Shopping Cart Item 0
        $this->assertEquals('2 x iPhone - Discount applied: 52.83€ using reward points (100)', $response['shopping_cart']['items'][0]['name']);
        $this->assertEquals('52.8297346800', $response['shopping_cart']['items'][0]['unit_price']);
        $this->assertEquals('EUR', $response['shopping_cart']['items'][0]['currency']);
        $this->assertEquals('2', $response['shopping_cart']['items'][0]['quantity']);
        $this->assertEquals('40', $response['shopping_cart']['items'][0]['merchant_item_id']);
        $this->assertEquals('0', $response['shopping_cart']['items'][0]['tax_table_selector']);
        $this->assertEquals('KG', $response['shopping_cart']['items'][0]['weight']['unit']);
        $this->assertEquals('10', $response['shopping_cart']['items'][0]['weight']['value']);

        // Shopping Cart Item 1
        $this->assertEquals('1 x MacBook', $response['shopping_cart']['items'][1]['name']);
        $this->assertEquals('1176.9000300000', $response['shopping_cart']['items'][1]['unit_price']);
        $this->assertEquals('EUR', $response['shopping_cart']['items'][1]['currency']);
        $this->assertEquals('1', $response['shopping_cart']['items'][1]['quantity']);
        $this->assertEquals('43', $response['shopping_cart']['items'][1]['merchant_item_id']);
        $this->assertEquals('0', $response['shopping_cart']['items'][1]['tax_table_selector']);
        $this->assertEquals('KG', $response['shopping_cart']['items'][1]['weight']['unit']);
        $this->assertEquals('0', $response['shopping_cart']['items'][1]['weight']['value']);

        // Shopping Cart Item 2
        $this->assertEquals('Flat Shipping Rate', $response['shopping_cart']['items'][2]['name']);
        $this->assertEquals('3.9230001000', $response['shopping_cart']['items'][2]['unit_price']);
        $this->assertEquals('EUR', $response['shopping_cart']['items'][2]['currency']);
        $this->assertEquals('1', $response['shopping_cart']['items'][2]['quantity']);
        $this->assertEquals('msp-shipping', $response['shopping_cart']['items'][2]['merchant_item_id']);
        $this->assertEquals('0', $response['shopping_cart']['items'][2]['tax_table_selector']);

        // Taxes
        $this->assertEquals('0', $response['checkout_options']['tax_tables']['alternate'][0]['name']);
        $this->assertEquals('0', $response['checkout_options']['tax_tables']['alternate'][0]['rules'][0]['rate']);


        // Plugin
        $this->assertEquals($this->multisafepay->getPluginVersion(), $response['plugin']['plugin_version']);
        $this->assertEquals('OpenCart', $response['plugin']['shop']);
        $this->assertEquals($this->multisafepay->getShopUrl(), $response['plugin']['shop_root_url']);

    }

    public function testGetOrderInfoHasKeys() {
        $order_id = $this->getRandomOrderId();
        $response = $this->multisafepay->getOrderInfo($order_id);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('order_id', $response);
        $this->assertArrayHasKey('store_name', $response);
        $this->assertArrayHasKey('store_url', $response);
        $this->assertArrayHasKey('currency_code', $response);
        $this->assertArrayHasKey('currency_value', $response);
        $this->assertArrayHasKey('email', $response);
        $this->assertArrayHasKey('payment_firstname', $response);
        $this->assertArrayHasKey('payment_lastname', $response);
        $this->assertArrayHasKey('payment_address_1', $response);
        $this->assertArrayHasKey('payment_address_2', $response);
        $this->assertArrayHasKey('payment_postcode', $response);
        $this->assertArrayHasKey('payment_city', $response);
        $this->assertArrayHasKey('payment_zone', $response);
        $this->assertArrayHasKey('payment_iso_code_2', $response);
    }

    public function testGetCustomerObjectPaymentHasKeys() {
        $order_id = $this->getRandomOrderId();
        $response = $this->multisafepay->getCustomerObject($order_id, 'payment')->getData();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('firstname', $response);
        $this->assertArrayHasKey('lastname', $response);
        $this->assertArrayHasKey('address1', $response);
        $this->assertArrayHasKey('zip_code', $response);
        $this->assertArrayHasKey('city', $response);
        $this->assertArrayHasKey('state', $response);
        $this->assertArrayHasKey('country', $response);
        $this->assertArrayHasKey('ip_address', $response);
        $this->assertArrayHasKey('locale', $response);
        $this->assertArrayHasKey('user_agent', $response);
    }

    public function testGetCustomerObjectShippingHasKeys() {
        $order_id = $this->getRandomOrderId();
        $response = $this->multisafepay->getCustomerObject($order_id, 'shipping')->getData();
        $this->assertArrayHasKey('firstname', $response);
        $this->assertArrayHasKey('lastname', $response);
        $this->assertArrayHasKey('address1', $response);
        $this->assertArrayHasKey('zip_code', $response);
        $this->assertArrayHasKey('city', $response);
        $this->assertArrayHasKey('state', $response);
        $this->assertArrayHasKey('country', $response);
        $this->assertArrayHasKey('ip_address', $response);
        $this->assertArrayHasKey('locale', $response);
        $this->assertArrayHasKey('user_agent', $response);
    }

    public function testGetSecondChanceObjectAsTrue() {
        $response = $this->multisafepay->getSecondChanceObject(true)->getData();
        $this->assertArrayHasKey('send_email', $response);
        $this->assertTrue($response['send_email']);
    }

    public function testGetSecondChanceObjectAsFalse() {
        $response = $this->multisafepay->getSecondChanceObject(false)->getData();
        $this->assertArrayHasKey('send_email', $response);
        $this->assertFalse($response['send_email']);
    }

    public function testGetSecondChanceObjectWithDefinedSetting() {
        $payment_multisafepay_second_chance = ($this->config->get('payment_multisafepay_second_chance')) ? false : true;
        $this->assertIsBool($payment_multisafepay_second_chance);
        $response = $this->multisafepay->getSecondChanceObject($payment_multisafepay_second_chance)->getData();
        $this->assertArrayHasKey('send_email', $response);
    }

    public function testGetAnalyticsAccountIdObjectWhenIsEmpty() {
        $payment_multisafepay_google_analytics_account_id = '';
        $this->assertEmpty($payment_multisafepay_google_analytics_account_id);
        $this->assertIsString($payment_multisafepay_google_analytics_account_id);
        $response = $this->multisafepay->getAnalyticsAccountIdObject($payment_multisafepay_google_analytics_account_id);
        $this->assertFalse($response);
    }

    public function testGetAnalyticsAccountIdObjectWhenIsNotEmpty() {
        $payment_multisafepay_google_analytics_account_id = 'UA-XXXXXXXX';
        $this->assertIsString($payment_multisafepay_google_analytics_account_id);
        $response = $this->multisafepay->getAnalyticsAccountIdObject($payment_multisafepay_google_analytics_account_id)->getData();
        $this->assertArrayHasKey('account', $response);
        $this->assertEquals($payment_multisafepay_google_analytics_account_id, $response['account']);
    }

    public function testGetPluginDetailsObjectHasKeys() {
        $response = $this->multisafepay->getPluginDetailsObject()->getData();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('sdk_version', $response);
        $this->assertArrayHasKey('plugin_version', $response);
        $this->assertArrayHasKey('shop', $response);
        $this->assertArrayHasKey('shop_version', $response);
        $this->assertArrayHasKey('shop_root_url', $response);
    }

    public function testGetOrderDescriptionObject() {
        $order_id = $this->getRandomOrderId();
        $response = $this->multisafepay->getOrderDescriptionObject($order_id)->getData();
        $this->assertIsString($response);
    }

    public function testGetPaymentOptionsObject() {
        $response = $this->multisafepay->getPaymentOptionsObject()->getData();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('notification_url', $response);
        $this->assertArrayHasKey('redirect_url', $response);
        $this->assertArrayHasKey('cancel_url', $response);
        $this->assertArrayHasKey('close_window', $response);
    }

    public function testGetShoppingCartItems() {
        $order_id = $this->getRandomOrderId();
        $response = $this->multisafepay->getShoppingCartItems($order_id)->getData();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('items', $response);
        foreach ($response['items'] as $key => $item) {
            $this->assertArrayHasKey('name', $item);
            $this->assertArrayHasKey('currency', $item);
            $this->assertArrayHasKey('quantity', $item);
            $this->assertArrayHasKey('merchant_item_id', $item);
        }
    }

}