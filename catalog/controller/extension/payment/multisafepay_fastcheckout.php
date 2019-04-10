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
 * @author      TechSupport <techsupport@multisafepay.com>
 * @copyright   Copyright (c) 2017 MultiSafepay, Inc. (http://www.multisafepay.com)
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class ControllerExtensionPaymentmultisafepayfastcheckout extends Controller
{

    public function index()
    {
        if ($this->cart->hasProducts()) {
            // There are some items in cart - process!
            $this->process();
        } else {
            // No items in cart - redirect to main page
            $this->response->redirect($this->url->link('checkout/cart'));
        }
    }

    public function process()
    {
        $storeid = $this->config->get('config_store_id');

        require_once(DIR_APPLICATION . 'controller/extension/payment/MultiSafepay.combined.php');

        $msp = new MultiSafepay();
        $this->load->model('extension/total/coupon');
        $total_data = array();
        $total = $this->cart->getTotal();
        $start_total = $this->cart->getTotal();
        $taxes = $this->cart->getTaxes();

        $total_data_arr = array(
            'totals' => &$total_data,
            'total' => &$total,
            'taxes' => &$taxes
        );

        $this->model_extension_total_coupon->getTotal($total_data_arr);

        if ($start_total != $total) {
            $discount_total = 0;
            $start_total = $start_total;
            $total = $total;
            $discount_total = $discount_total - ($start_total - $total);

            $c_item = new MspItem('Coupon', 'Coupon', 1, $discount_total);
            $c_item->merchant_item_id = '10101010';
            $c_item->SetTaxTableSelector('0');
            $msp->cart->AddItem($c_item);
        }


        $this->load->model('extension/total/voucher');
        $total_data = array();
        $total = $this->cart->getTotal();
        $start_total = $this->cart->getTotal();
        $taxes = $this->cart->getTaxes();

        $total_data_arr = array(
            'totals' => &$total_data,
            'total' => &$total,
            'taxes' => &$taxes
        );

        $this->model_extension_total_voucher->getTotal($total_data_arr);

        if ($start_total != $total) {
            $discount_total = 0;
            $start_total = $start_total;
            $total = $total;
            $discount_total = $discount_total - ($start_total - $total);

            $c_item = new MspItem('Voucher', 'Voucher', 1, $discount_total);
            $c_item->merchant_item_id = '10101011';
            $c_item->SetTaxTableSelector('0');
            $msp->cart->AddItem($c_item);
        }


        $db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        // Language Detection
        $languages = array();

        $query = $db->query("SELECT * FROM " . DB_PREFIX . "language WHERE code='" . $this->session->data['language'] . "'");

        foreach ($query->rows as $result) {
            $languages[$result['code']] = $result;
        }

        $language_string = $languages[$this->session->data['language']]['locale'];

        $loc1 = explode(',', $language_string);

        $locale = $loc1[0];

        $msp->test = $this->config->get('payment_multisafepay_environment');
        $msp->merchant['account_id'] = $this->config->get('payment_multisafepay_merchant_id');
        $msp->merchant['site_id'] = $this->config->get('payment_multisafepay_site_id');
        $msp->merchant['site_code'] = $this->config->get('payment_multisafepay_secure_code');
        $msp->merchant['notification_url'] = $this->url->link('extension/payment/multisafepay/fastcheckout&type=initial');
        $msp->merchant['redirect_url'] = $this->url->link('checkout/success', '', 'SSL');
        $msp->merchant['cancel_url'] = $this->url->link('checkout/cart');
        $msp->use_shipping_notification = true; // This module uses shipping notification
        $msp->transaction['items'] = $this->getCartItemsHTML();

        // Create products array (will be used later)
        $products = $this->cart->getProducts();
        if ($this->config->get('payment_multisafepay_b2b') != 'false') {
            $msp->customer['company'] = $this->config->get('payment_multisafepay_b2b');
        }


        $customerid = 0;
        $customergid = 0;

        // Customer info (is available)
        if ($this->customer->isLogged()) {
            $msp->customer['firstname'] = $this->customer->getFirstName();
            $msp->customer['lastname'] = $this->customer->getLastName();
            $msp->customer['phone'] = $this->customer->getTelephone();
            $msp->customer['email'] = $this->customer->getEmail();

            $this->load->model('account/address');
            $addr = $this->model_account_address->getAddress($this->customer->getAddressId());

            $msp->customer['locale'] = $locale; // iso code
            $msp->customer['address1'] = $addr['address_1'];
            $msp->customer['address2'] = $addr['address_2'];
            $msp->customer['zipcode'] = $addr['postcode'];
            $msp->customer['city'] = $addr['city'];
            $msp->customer['country'] = $msp->customer['locale']; // iso code
            $msp->customer['referrer'] = $_SERVER['HTTP_REFERER'];
            $msp->customer['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $msp->customer['ipaddress'] = $_SERVER['REMOTE_ADDR'];

            //Added 2-1-12. Process store credit if positive balance. This is added as a negative product amount like we did with the coupon.
            if ($this->customer->getBalance() > 0) {
                $credit = 0 - $this->customer->getBalance();
                $c_item = new MspItem('Credit', 'Credit', 1, $credit);
                $c_item->merchant_item_id = '10101010';
                $c_item->SetTaxTableSelector('0');
                $msp->cart->AddItem($c_item);
            }
            $msp->transaction['var1'] = $this->customer->getId() . '|' . $this->customer->getBalance();
            $msp->transaction['var2'] = $this->config->get('config_customer_group_id');

            $customerid = $this->customer->getId();
            $customergid = $this->config->get('config_customer_group_id');
        }

        // Taxes
        // Default tax for shipping
        // Workaround: get tax from the first product as default
        // TODO -> Add configurable defaul tax class
        $keys = array_keys($products);

        $defrates = $this->tax->getRates($products[$keys[0]]['price'], $products[$keys[0]]['tax_class_id']);
        $rate = 0;
        foreach ($defrates AS $defrate) {
            $rate = $defrate['rate'];
        }

        $correct_rate = round($rate, 2) / 100;


        // Tax for products
        $taxtable = Array();
        foreach ($products AS $product) {
            $ratiotax = $this->tax->getRates($product['total'], $product['tax_class_id']);
            foreach ($ratiotax AS $tax_array) {
                $taxes[] = $tax_array;
            }
        }

        $unique_taxes = $taxes; //array_unique($taxes);
        foreach ($unique_taxes as $tax) {
            $taxname = $tax['name'];
            //TODO -> ADD CONFIGURABLE OPTION TO SET SHIPPING TAX INC OR EXC;
            $taxtable = new MspAlternateTaxTable($tax['name'], 'true');
            $taxrule = new MspAlternateTaxRule($tax['rate'] / 100);
            $taxtable->AddAlternateTaxRules($taxrule);
            $msp->cart->AddAlternateTaxTables($taxtable);
        }

        $taxname = 'none';
        //TODO -> ADD CONFIGURABLE OPTION TO SET SHIPPING TAX INC OR EXC;
        $taxtable = new MspAlternateTaxTable($taxname, 'true');
        $taxrule = new MspAlternateTaxRule(0);
        $taxtable->AddAlternateTaxRules($taxrule);
        $msp->cart->AddAlternateTaxTables($taxtable);


        // Cart content
        foreach ($products AS $product) {
            // Retrieve which tax table to use.
            $ratiotax = $this->tax->getRates($product['price'], $product['tax_class_id']);
            $i = 0;
            foreach ($ratiotax AS $tax_array) {
                $taxes[$i] = $tax_array;
                $i++;
            }


            //print_r($taxes);exit;
            if (isset($taxes[0])) {
                $taxname = $taxes[0]['name'];
            } else {
                $taxname = 'none';
            }


            $product['weight'] = $product['weight'] / $product['quantity'];
            $product_name = $this->_getProductName($product);

            $c_item = new MspItem($product_name, strip_tags($product['model']), $product['quantity'], $product['price'], 'KG', $product['weight']);
            $c_item->merchant_item_id = $this->_getUniqueProductID($product);;
            $c_item->SetTaxTableSelector($taxname);
            $msp->cart->AddItem($c_item);
        }

        // Agreement acceptance.
        /* if($this->config->get('multisafepayoc_tos_url')) {
          $field = new MspCustomField('acceptagreements', 'checkbox', '');
          $link = trim( $this->config->get('multisafepayoc_tos_url') );
          $description = array(
          'nl' => 'Ik ga akkoord met de <a href="'.$link.'" target="_blank">algemene voorwaarden</a>',
          'en' => 'I accept the <a href="'.$link.'" target="_blank">terms and conditions</a>',
          );
          $field->descriptionRight = array('value' => $description);
          $error = array(
          'nl' => 'U dient akkoord te gaan met de algemene voorwaarden',
          'en' => 'Please accept the terms and conditions',
          );
          $validation = new MspCustomFieldValidation('regex', '^[1]$', $error);
          $field->AddValidation($validation);
          $msp->fields->AddField($field);
          } */

        // Precreate order
        $mspcust = $msp->details['customer'];
        $mspshipaddr = $msp->details['customer-delivery'];
        $mspshipping = $msp->details['shipping'];
        $emptyar = array();

        $products_array = array();


        foreach ($products AS $productkey => $product) {
            $ratiotax = $this->tax->getRates($product['total'], $product['tax_class_id']);
            $products[$productkey]['tax'] = 0;
            foreach ($ratiotax AS $tax_array) {
                $taxes[] = $tax_array;



                foreach ($taxes as $tax) {
                    if (isset($tax['amount'])) {
                        $products[$productkey]['tax'] = $tax['amount'];
                    } else {
                        $products[$productkey]['tax'] = 0;
                    }
                }
            }
            foreach ($product['option'] as $key => $value) {
                $products[$productkey]['option'][$key]['value'] = $value['option_value'];
            }
        }




        foreach ($products AS $product) {
            $products_array[] = $product;
        }

        // Totals
        $this->load->model('setting/extension');

        $total_data = array();
        $totalamount = 0;
        $taxes = $this->cart->getTaxes();

        $total_data_arr = array(
            'totals' => &$total_data,
            'total' => &$totalamount,
            'taxes' => &$taxes
        );


        // Display prices
        if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
            $sort_order = array();

            $results = $this->model_setting_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('extension/total/' . $result['code']);

                    $this->{'model_extension_total_' . $result['code']}->getTotal($total_data_arr);
                }
            }

            $sort_order = array();

            foreach ($total_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $total_data);
        }

        $data['totals'] = array();

        foreach ($total_data as $totalamount) {
            $data['totals'][] = $totalamount;
        }

        // We'll need this big array to place new order
        $order_data = array(
            'products' => $products_array,
            'invoice_prefix' => '',
            'payment_company_id' => '',
            'payment_tax_id' => '',
            'payment_code' => '',
            'shipping_code' => '',
            'forwarded_ip' => '',
            'user_agent' => '',
            'accept_language' => '',
            'tax' => '',
            'vouchers' => array(),
            //'products'                		=> 	$products,
            'totals' => $data['totals'],
            'download' => $emptyar,
            'option' => $emptyar,
            'store_id' => $this->config->get('config_store_id'),
            'store_name' => $this->config->get('config_name'),
            'store_url' => $this->config->get('config_url'),
            'customer_id' => $customerid, // Order as guest
            'customer_group_id' => $customergid,
            'firstname' => $mspcust['firstname'],
            'lastname' => $mspcust['lastname'],
            'telephone' => $mspcust['phone1'],
            'fax' => $mspcust['phone2'],
            'email' => $mspcust['email'],
            'shipping_firstname' => $mspshipaddr['firstname'],
            'shipping_lastname' => $mspshipaddr['lastname'],
            'shipping_company' => '',
            'shipping_address_1' => $mspshipaddr['address1'] . " " . $mspshipaddr['housenumber'],
            'shipping_address_2' => $mspshipaddr['address2'],
            'shipping_postcode' => $mspshipaddr['zipcode'],
            'shipping_city' => $mspshipaddr['city'],
            'shipping_zone_id' => '',
            'shipping_zone' => '',
            'shipping_zone_code' => '',
            'shipping_country_id' => '',
            'shipping_country' => $mspshipaddr['countryname'],
            'shipping_iso_code_2' => $mspshipaddr['country'],
            'shipping_iso_code_3' => '',
            'shipping_address_format' => '',
            'shipping_method' => $mspshipping['name'],
            'payment_firstname' => $mspcust['firstname'],
            'payment_lastname' => $mspcust['lastname'],
            'payment_company' => '',
            'payment_address_1' => $mspcust['address1'] . " " . $mspcust['housenumber'],
            'payment_address_2' => $mspcust['address2'],
            'payment_postcode' => $mspcust['zipcode'],
            'payment_city' => $mspcust['city'],
            'payment_zone_id' => '',
            'payment_zone' => '',
            'payment_zone_code' => '',
            'payment_country_id' => '',
            'payment_country' => $mspcust['countryname'],
            'payment_iso_code_2' => $mspcust['country'],
            'payment_iso_code_3' => '',
            'payment_address_format' => '',
            'payment_method' => 'MultiSafepay Fastcheckout',
            'comment' => '',
            'total' => $total,
            'language_id' => $this->langISO2langId($mspcust['country']),
            'language_code' => '',
            'language_filename' => '',
            'language_directory' => '',
            'currency_id' => '',
            'currency_code' => $msp->details['transaction']['currency'],
            'currency_value' => 1,
            'reward' => '',
            'affiliate_id' => '',
            'commission' => '',
            'ip' => $_SERVER['REMOTE_ADDR'],
            'marketing_id' => '',
            'tracking' => '',);

        // If user logged in fill up extra data
        if ($this->customer->isLogged()) {
            $order_data['customer_id'] = $this->customer->getId();
            $order_data['customer_group_id'] = $this->config->get('config_customer_group_id');
        }

        //Create an order
        $this->load->model('checkout/order');
        // Create an order (pass second argument so we will only update our database with info from MultiSafepay)
        //$order_id = $this->session->data['last_order_id']+1;


        $order_id = $this->model_checkout_order->addOrder($order_data);


        //$order_id = $this->model_checkout_order->addOrder($order_data);
        //echo $total;exit;
        // Transaction info.
        $msp->transaction['id'] = $order_id;
        $order_data['order_id'] = $order_id;
        $msp->transaction['currency'] = 'EUR';
        $msp->transaction['amount'] = round($total * 100, 0); // Has to be in eurocents, no fraction!
        $msp->transaction['description'] = $this->getOrderDescription($order_id);
        $msp->plugin_name = 'OpenCart' . VERSION;
        $msp->version = '2.2.1';

        $msp->plugin['shop'] = 'OpenCart';
        $msp->plugin['shop_version'] = VERSION;
        $msp->plugin['plugin_version'] = '2.2.1';
        $msp->plugin['partner'] = '';
        $msp->plugin['shop_root_url'] = '';

        if (isset($this->session->data['coupon'])) {
            $this->load->model('extension/total/coupon');
            $coupon_info = $this->model_extension_total_coupon->getCoupon($this->session->data['coupon']);

            if ($coupon_info) {
                $msp->transaction['var3'] = $coupon_info['coupon_id'];
            }

            unset($this->session->data['coupon']);
            $this->session->data['coupon'] = '';
        }

        $this->cart->clear();
        $this->session->data['last_order_id'] = $order_id;
        $url = $msp->startCheckout();

        if ($msp->error) {
            echo 'Error: ' . $msp->error;
        } else {
            $this->response->redirect($url);
        }
    }

    // Try to guess shipping costs, see also controller/total/shipping.php
    private function getShippingOptions($country_id)
    {
        $address_data = array(
            'firstname' => '',
            'lastname' => '',
            'company' => '',
            'address_1' => '',
            'address_2' => '',
            'postcode' => '',
            'city' => '',
            'zone_id' => '',
            'zone' => '',
            'zone_code' => '',
            'country_id' => $country_id,
            'country' => '',
            'iso_code_2' => '',
            'iso_code_3' => '',
            'address_format' => ''
        );

        $quote_data = array();

        // Load interface for getting extension information
        $this->load->model('setting/extension');

        $results = $this->model_setting_extension->getExtensions('shipping');

        foreach ($results as $result) {
            // If module is enabled
            if ($this->config->get($result['code'] . '_status')) {
                $this->load->model('extension/shipping/' . $result['code']);

                $quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($address_data);

                if ($quote) {
                    $quote_data[$result['code']] = array(
                        'title' => $quote['title'],
                        'quote' => $quote['quote'],
                        'sort_order' => $quote['sort_order'],
                        'error' => $quote['error']
                    );
                }
            }
        }
        return $quote_data;
    }

    // Try to guess shipping costs based on ISO2 country code
    private function getShippingOptionsISO2($country_iso2)
    {
        // First convert iso2 code to country_id
        $this->load->model('localisation/country');

        $countries = $country_info = $this->model_localisation_country->getCountries();

        $country_id = 0;
        foreach ($countries as $country) {
            if ($country['iso_code_2'] == $country_iso2) {
                $country_id = $country['country_id'];
                break;
            }
        }

        // Return empty array if country with given iso code was not found
        if ($country_id == 0)
            return array();

        return $this->getShippingOptions($country_id);
    }


    private function _getProductName($product){

        $options = '';
        foreach ($product['option'] as $option){
            $options .= $option['name'] . ': ' . $option['value'] . ', ';
        }

        if ($options){
            $options = ' (' . substr($options,0, -1) . ')';
        }

        $product_name = $product['name'] . $options;
        return $product_name;
    }

    private function _getUniqueProductID ($product){

        $merchant_item_id = $product['product_id'];
        foreach ($product['option'] as $option){
            $merchant_item_id .= '-' . $option['product_option_id'];
        }

        return $merchant_item_id;
    }


    private function getOrderDescription($order_id)
    {
        $date = date("D M j G:i:s T Y");
        $shopname = $this->config->get('config_name');
        $desc = 'Order ID#' . $order_id . ' from ' . $shopname . ' placed on ' . $date;

        return $desc;
    }

    private function getCartItemsHTML()
    {
        $products = $this->cart->getProducts();
        $cart_items = '<br/><ul>';
        foreach ($products as $product) {
            $cart_items .= '<li>' . $product['quantity'] . ' x ' . $product['name'] . '</li>';
        }
        $cart_items .= '</ul>';

        return $cart_items;
    }

    private function langISO2langId($langiso)
    {
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $lang) {
            if (stristr($lang['locale'], $langiso)) { // If found in locale, not 100% reliable
                return $lang['language_id'];
            }
        }

        return 1; // In emergency return 1 (should be english)
    }

    // MSP will call us here
    public function callback()
    {
        chdir('system/multisafepayoc');

        require_once(DIR_APPLICATION . 'controller/extension/payment/MultiSafepay.combined.php');

        if (isset($this->session->data['coupon'])) {
            unset($this->session->data['coupon']);
        }

        $transactionid = $this->request->get['transactionid'];
        $initial = ($this->request->get['type'] == "initial");

        if ($this->isNewAddressQuery()) {
            $this->handleShippingMethodsNotification();
            return;
        }

        // Construct Multisafepay object
        $msp = new MultiSafepay();

        // Basic info
        $msp->test = $this->config->get('multisafepayoc_test');
        $msp->merchant['account_id'] = $this->config->get('multisafepayoc_account_id');
        $msp->merchant['site_id'] = $this->config->get('multisafepayoc_site_id');
        $msp->merchant['site_code'] = $this->config->get('multisafepayoc_site_security_code');
        $msp->transaction['id'] = (int) $transactionid;

        // Get status from MSP
        $status = $msp->getStatus();

        if (isset($msp->details['transaction']['var1'])) {
            $data = explode('|', $msp->details['transaction']['var1']);

            $customerid = $data[0];
            $store_credit = 0 - $data[1];
            $customergid = $msp->details['transaction']['var2'];
        } else {
            $customerid = 0;
            $customergid = 0;
        }

        if ($initial) {
            // We got an error.
            if ($msp->error) {
                echo "Ini err";
                return;
            }

            // Some shortcuts
            $mspcust = $msp->details['customer'];
            $mspshipaddr = $msp->details['customer-delivery'];
            $mspshipping = $msp->details['shipping'];
            $mspcart = $msp->details['shopping-cart'];
            $emptyar = array();

            // We'll need this big array to place new order
            $order_data = array(
                'products' => $emptyar,
                'totals' => $emptyar,
                'download' => $emptyar,
                'option' => $emptyar,
                'store_id' => $this->config->get('config_store_id'),
                'store_name' => $this->config->get('config_name'),
                'store_url' => $this->config->get('config_url'),
                'customer_id' => $customerid,
                'customer_group_id' => $customergid,
                'firstname' => $mspcust['firstname'],
                'lastname' => $mspcust['lastname'],
                'telephone' => $mspcust['phone1'],
                'fax' => $mspcust['phone2'],
                'email' => $mspcust['email'],
                'shipping_firstname' => $mspshipaddr['firstname'],
                'shipping_lastname' => $mspshipaddr['lastname'],
                'shipping_company' => '',
                'shipping_address_1' => $mspshipaddr['address1'] . " " . $mspshipaddr['housenumber'],
                'shipping_address_2' => $mspshipaddr['address2'],
                'shipping_postcode' => $mspshipaddr['zipcode'],
                'shipping_city' => $mspshipaddr['city'],
                'shipping_zone_id' => '',
                'shipping_zone' => '',
                'shipping_zone_code' => '',
                'shipping_country_id' => '',
                'shipping_country' => $mspshipaddr['countryname'],
                'shipping_iso_code_2' => $mspshipaddr['country'],
                'shipping_iso_code_3' => '',
                'shipping_address_format' => '',
                'shipping_method' => $mspshipping['name'],
                'payment_firstname' => $mspcust['firstname'],
                'payment_lastname' => $mspcust['lastname'],
                'payment_company' => 'MultiSafepay',
                'payment_address_1' => $mspcust['address1'] . " " . $mspcust['housenumber'],
                'payment_address_2' => $mspcust['address2'],
                'payment_postcode' => $mspcust['zipcode'],
                'payment_city' => $mspcust['city'],
                'payment_zone_id' => '',
                'payment_zone' => '',
                'payment_zone_code' => '',
                'payment_country_id' => '',
                'payment_country' => $mspcust['countryname'],
                'payment_iso_code_2' => $mspcust['country'],
                'payment_iso_code_3' => '',
                'payment_address_format' => '',
                'payment_method' => 'MultiSafepay Fastcheckout',
                'comment' => '',
                'total' => $msp->details['transaction']['amount'] / 100,
                'language_id' => $this->langISO2langId($mspcust['country']),
                'language_code' => '',
                'language_filename' => '',
                'language_directory' => '',
                'currency_id' => '',
                'currency_code' => $msp->details['transaction']['currency'],
                'currency_value' => 1,
                'reward' => '',
                'affiliate_id' => '',
                'commission' => '',
                'ip' => '0.0.0.0');

            // Load object for controlling new orders
            $this->load->model('checkout/order');
            // Create an order (pass second argument so we will only update our database with info from MultiSafepay)
            $this->model_checkout_order->create($order_data, $transactionid);


            // Change status to pending
            $this->load->language('extension/payment/multisafepayoc');
            $initialorderstatus = 1;
            $comment = $this->language->get('initial');
            $this->model_checkout_order->confirm($transactionid, $initialorderstatus, $comment);

            // Send a message that will be displayed at the end of fastcheckout process.
            $shopaddress = $this->config->get('config_url');
            $thankyou = $this->language->get('thankyou');
            $goback = $this->language->get('gobacktoshop');
            print("<strong>$thankyou. <a href=\"$shopaddress\">$goback</a>.</strong>");
        } else {
            // ****************************** order status update
            $this->load->model('checkout/order');
            $this->load->language('extension/payment/multisafepayoc');

            // What's going on with the transaction?
            if ($msp->error && !$initial) {
                echo "Err" . $msp->error_code . ": " . $msp->error;
                exit(-1);
            }

            // Get order (if it exists)
            $notify = false;
            switch ($status) {
                // waiting
                case "initialized":
                    $neworderstatus = 2; // Processing
                    $comment = $this->language->get('initialized');
                    break;
                // payment complete
                case "completed":
                    $neworderstatus = 15; // Processed
                    $comment = $this->language->get('completed');
                    $notify = true; // Notify user
                    break;
                // waiting (credit cards or direct debit)
                case "uncleared":
                    $neworderstatus = 1; // Pending
                    $comment = $this->language->get('uncleared');
                    break;
                // canceled
                case "void":
                    $neworderstatus = 16; // Voided
                    $comment = $this->language->get('void');
                    break;
                // declined
                case "declined":
                    $neworderstatus = 8; // Denied
                    $comment = $this->language->get('declined');
                    break;
                // refunded
                case "refunded":
                    $neworderstatus = 11; // Refunded
                    $comment = $this->language->get('refunded');
                    break;
                // expired
                case "expired":
                    $neworderstatus = 14; // Expired
                    $comment = $this->language->get('expired');
                    break;
                default:
                    $neworderstatus = 0;
                    $comment = $this->language->get('default');
                    break;
            }

            // If order status doesn't change - return
            $order_data = $this->model_checkout_order->getOrder($transactionid);
            if ($order_data['order_status_id'] == $neworderstatus) {
                echo "ok";
                return;
            }

            // Change order status accordingly
            $this->model_checkout_order->update($transactionid, $neworderstatus, $comment, $notify);
            $this->log->write("Received notification from MultiSafepay: order_id: " . $transactionid . ", status: " . $status . ", new order status id: " . $neworderstatus . " comment: " . $comment . "\n");

            // Send updated information about order (only invoiceid supported)
            $msp->transaction['invoice_id'] = $order_data['invoice_no']; // only invoice ID known
            $this->log->write("Sending invoice id: " . $msp->transaction['invoice_id']);
            $msp->updateTransaction();
            $this->log->write("Order update sent");

            echo "ok";
        }
    }

    // This function checks if we are called to return shipping costs for new address
    private function isNewAddressQuery()
    {
        // Check for mandatory parameters
        $country = $this->request->get['country'];
        $countryCode = $this->request->get['countrycode'];
        $transactionId = $this->request->get['transactionid'];

        if (empty($country) || empty($countryCode) || empty($transactionId))
            return false;
        else
            return true;
    }

    // Handles new shipping costs request
    private function handleShippingMethodsNotification($model)
    {
        $country = $this->request->get['country'];
        $countryCode = $this->request->get['countrycode'];
        $transactionId = $this->request->get['transactionid'];
        $weight = $this->request->get['weight'];
        $size = $this->request->get['size'];

        header("Content-Type:text/xml");
        print($this->getShippingMethodsFilteredXML($country, $countryCode, $weight, $size, $transactionId));
    }

    // Returns XML with new shipping costs
    private function getShippingMethodsFilteredXML($country, $countryCode, $weight, $size, $transactionId)
    {
        $methods = $this->getShippingMethodsFiltered($country, $countryCode, $weight, $size, $transactionId);

        $outxml .= '<shipping-info>';
        foreach ($methods as $method) {
            $outxml .= '<shipping>';
            $outxml .= '<shipping-name>';
            $outxml .= $method['name'];
            $outxml .= '</shipping-name>';
            $outxml .= '<shipping-cost currency="' . $method['currency'] . '">';
            $outxml .= $method['cost'];
            $outxml .= '</shipping-cost>';
            $outxml .= '</shipping>';
        }
        $outxml .= '</shipping-info>';

        return $outxml;
    }

    // Get shipping methods for given parameters
    // Result as an array:
    // 'name' => 'test-name'
    // 'cost' => '123'
    // 'currency' => 'EUR' (currently only this supported)
    private function getShippingMethodsFiltered($country, $countryCode, $weight, $size, $transactionId)
    {
        $out = array();

        $shippingopts = $this->getShippingOptionsISO2($countryCode);
        foreach ($shippingopts as $key => $value) {
            $shipping = array();
            $shipping['name'] = $value['title'];
            $shipping['cost'] = $value['quote'][$key]['cost'];
            $shipping['currency'] = 'EUR'; // Currently Euro is supported
            $out[] = $shipping;
        }

        return $out;
    }

}

?>
