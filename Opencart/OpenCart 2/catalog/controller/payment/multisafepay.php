<?php

/**
 * Multisafepay Connect Plugin
 * Date 06-09-2012
 * @version 1.6.1
 * @package 1.5
 * @copyright 2012 MultiSafePay.
 */
class ControllerPaymentMultiSafePay extends Controller {

    public function index() {
        $this->load->model('checkout/order');


        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['button_back'] = $this->language->get('button_back');
        $data['entry_select_gateway'] = $this->language->get('text_select_payment_method');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $data['MSP_CARTID'] = $this->session->data['order_id'];


        $data['action'] = $this->url->link('payment/multisafepay/multisafepayProcess', '', 'SSL');

        $data['back'] = $this->url->link('checkout/payment', '', 'SSL');
        $data['order_id'] = $order_info['order_id'];
        $data['text_paymentmethod'] = $this->language->get('text_paymentmethod');
        $data['text_dirdeb'] = $this->language->get('text_dirdeb');
        $data['gateway'] = '';
        $data['text_banktransfer'] = $this->language->get('text_banktransfer');
        if ($this->config->get('multisafepay_account_type') != 'fastcheckout') {
            $data['msp_gateway'] = ($this->config->get('multisafepay_gateway_selection') == 1) ? true : false;
        } else {
            $data['msp_gateway'] = false;
        }

        $data['order_id'] = $this->session->data['order_id'];

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/multisafepay_default.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/multisafepay_default.tpl', $data);
        } else {
            return $this->load->view('default/template/payment/multisafepay_default.tpl', $data);
        }
    }

    public function multisafepayProcess() {


        $db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        // Language Detection
        $languages = array();

        $query = $db->query("SELECT * FROM " . DB_PREFIX . "language WHERE code='" . $this->session->data['language'] . "'");

        foreach ($query->rows as $result) {
            $languages[$result['code']] = $result;
        }

        $language_string = $languages[$this->session->data['language']]['locale'];

        $loc1 = explode(',', $language_string);

        $locale = $loc1[1];



        if ($this->config->get('multisafepay_account_type') != 'fastcheckout') {
            $multisafepay_redirect_url = $this->config->get('multisafepay_redirect_url');
            if ($multisafepay_redirect_url == 1) {
                $redirect_url = true;
            } else {
                $redirect_url = false;
            }

            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($this->request->post['cartId']);
            $itemsstring = '';

            $html = "<ul>";
            foreach ($this->cart->getProducts() as $product) {
                $html .= '<li>' . $product['quantity'] . ' x ' . $product['name'] . ' </li>';
            }
            $html .= "</ul>";

            //MSP SET DATA FOR TRANSACTION REQUEST
            require_once(dirname(__FILE__) . '/MultiSafepay.combined.php');
            $msp = new MultiSafepay();
            $msp->test = $this->config->get('multisafepay_environment');

            $storeid = $this->config->get('config_store_id');

            if ($storeid === $this->config->get('msp_storeid_1')) {
                $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id_1');
                $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id_1');
                $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code_1');
            } elseif ($storeid === $this->config->get('msp_storeid_2')) {
                $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id_2');
                $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id_2');
                $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code_2');
            } elseif ($storeid === $this->config->get('msp_storeid_3')) {
                $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id_3');
                $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id_3');
                $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code_3');
            } elseif ($storeid === $this->config->get('msp_storeid_4')) {
                $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id_4');
                $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id_4');
                $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code_4');
            } else {
                $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id');
                $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id');
                $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code');
            }

            $msp->merchant['notification_url'] = $this->url->link('payment/multisafepay/callback&type=initial', '', 'SSL');
            $msp->merchant['cancel_url'] = $this->url->link('checkout/checkout', '', 'SSL');
            $msp->merchant['redirect_url'] = $this->url->link('checkout/success', '', 'SSL');
            $msp->merchant['close_window'] = $this->config->get('multisafepay_redirect_url');
            $msp->customer['locale'] = $locale;
            $msp->customer['firstname'] = $order_info['payment_firstname'];
            $msp->customer['lastname'] = $order_info['payment_lastname'];
            $msp->customer['zipcode'] = $order_info['payment_postcode'];
            $msp->customer['city'] = $order_info['payment_city'];
            $msp->customer['email'] = $order_info['email'];
            $msp->customer['phone'] = $order_info['telephone'];
            $msp->customer['country'] = $order_info['payment_iso_code_2'];

            if ($msp->customer['housenumber'] == "") {
                $msp->parseCustomerAddress($order_info['payment_address_2']);
            }

			$msp->customer['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $msp->transaction['id'] = $order_info['order_id'];
            $msp->transaction['currency'] = $order_info['currency_code']; //MSP only supports EUR at the moment  ->  $order_info['currency_code'];

            $msp->transaction['description'] = 'Order #' . $msp->transaction['id'];


            $orderid = $order_info['order_id'];
            $notify = false;
            $newStatus = $this->config->get('multisafepay_order_status_id_initialized');
            $confirm_message = "Order Created at " . date('Y/m/d H:i:s', time());



            //Enable to show the order before the transaction. Side effect, no confirmation email is send.
            //$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$newStatus . "', date_modified = NOW() WHERE order_id = '" . (int)$orderid . "'");
            //$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$orderid . "', order_status_id = '" . (int)$newStatus . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($confirm_message) . "', date_added = NOW()");




            if ($this->customer->isLogged()) {

                $msp->transaction['var1'] = $this->customer->getId() . '|' . $this->customer->getBalance();
                $msp->transaction['var2'] = $this->config->get('config_customer_group_id');
            }

            if (!isset($this->request->post['gateway'])) {
                $msp->transaction['gateway'] = 'IDEAL';
                $gateway = 'IDEAL';
            } else {
                $msp->transaction['gateway'] = $this->request->post['gateway'];
                $gateway = $this->request->post['gateway'];
            }

            if ($gateway == 'MSP_PAYAFTER') {
                $msp->test = $this->config->get('bno_multisafepay_environment');
                $msp->merchant['account_id'] = $this->config->get('bno_multisafepay_merchant_id');
                $msp->merchant['site_id'] = $this->config->get('bno_multisafepay_site_id');
                $msp->merchant['site_code'] = $this->config->get('bno_multisafepay_secure_code');

                /*
                 * 	Start Pay after Delivery
                 */
                $msp->transaction['gateway'] = 'PAYAFTER';
            }
            $msp->gatewayinfo['email'] = $this->customer->getEmail();
            $msp->gatewayinfo['phone'] = ''; //not available
            $msp->gatewayinfo['bankaccount'] = ''; //not available
            $msp->gatewayinfo['referrer'] = $_SERVER['HTTP_REFERER'];
            $msp->gatewayinfo['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $msp->gatewayinfo['birthday'] = ''; //not available



            if ($gateway == 'MSP_PAYAFTER') {

                $products = $this->cart->getProducts();

                // Tax for products

                $taxname = '0';
                $taxtable = new MspAlternateTaxTable('0', 'true');
                $taxrule = new MspAlternateTaxRule(0.00);
                $taxtable->AddAlternateTaxRules($taxrule);
                $msp->cart->AddAlternateTaxTables($taxtable);


                $taxtable = Array();
                foreach ($products AS $product) {
                    $ratiotax = $this->tax->getRates($product['total'], $product['tax_class_id']);
                    foreach ($ratiotax AS $tax_array) {
                        $taxes[] = $tax_array;
                    }
                }

                $unique_taxes = array_unique($taxes);
                foreach ($unique_taxes as $tax) {
                    $taxname = $tax['name'];
                    $taxtable = new MspAlternateTaxTable($tax['name'], 'true');
                    $taxrule = new MspAlternateTaxRule($tax['rate'] / 100);
                    $taxtable->AddAlternateTaxRules($taxrule);
                    $msp->cart->AddAlternateTaxTables($taxtable);
                }

                if (isset($this->session->data['coupon'])) {
                    $coupon_set = true;
                    $this->load->model('checkout/coupon');
                    $coupon_info = $this->model_checkout_coupon->getCoupon($this->session->data['coupon']);
                } else {
                    $coupon_set = false;
                }

                $product_ids = array();
                foreach ($products AS $product) {
                    $product_ids[$product['product_id']] = $product['product_id'];
                }

                if (!in_array($this->config->get('bno_product_fee_id'), $product_ids)) {
                    $product_fee_id = $this->config->get('bno_product_fee_id');
                    if (isset($product_fee_id)) {
                        $option = array();
                        $this->cart->add($product_fee_id, 1, $option);
                    }
                }


                //add products
                foreach ($products AS $product) {
                    // Retrieve which tax table to use.
                    $ratiotax = $this->tax->getRates($product['price'], $product['tax_class_id']);

                    $i = 0;
                    foreach ($ratiotax AS $tax_array) {
                        $taxes[$i] = $tax_array;
                        $i++;
                    }

                    $taxname = $taxes[0]['name'];

                    if ($coupon_set) {
                        if ($coupon_info['type'] == 'F') {
                            $c_item = new MspItem($product['name'], strip_tags($product['model']), $product['quantity'], $product['price'], 'KG', $product['weight']);
                            $c_item->merchant_item_id = $product['product_id'];
                            $c_item->SetTaxTableSelector($taxname);
                            $msp->cart->AddItem($c_item);
                        } else {
                            $price_new = $product['price'] - ($product['price'] / 100 * $coupon_info['discount']);
                            $c_item = new MspItem($product['name'], strip_tags($product['model']), $product['quantity'], $price_new, 'KG', $product['weight']);
                            $c_item->merchant_item_id = $product['product_id'];
                            $c_item->SetTaxTableSelector($taxname);
                            $msp->cart->AddItem($c_item);
                        }
                    } else {
                        $c_item = new MspItem($product['name'], strip_tags($product['model']), $product['quantity'], $product['price'], 'KG', $product['weight']);
                        $c_item->merchant_item_id = $product['product_id'];
                        $c_item->SetTaxTableSelector($taxname);
                        $msp->cart->AddItem($c_item);
                    }
                }


                //Customer credit processing
                if ($this->customer->getBalance() > 0) {
                    $credit = 0 - $this->customer->getBalance();
                    $c_item = new MspItem('Credit', 'Credit', 1, $credit);
                    $c_item->merchant_item_id = '10101010';
                    $c_item->SetTaxTableSelector('0');
                    $msp->cart->AddItem($c_item);
                }

                //add discounts
                if ($coupon_set) {
                    $this->load->model('total/coupon');
                    $total_data = array();
                    $total = $this->cart->getTotal();
                    $start_total = $this->cart->getTotal();
                    $taxes = $this->cart->getTaxes();
                    $this->model_total_coupon->getTotal($total_data, $total, $taxes);
                    if ($coupon_info['type'] == 'F') {
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
                    }
                }

                //add shippingmethod
                $shipping_tax = $this->tax->getRates($this->session->data['shipping_method']['cost'], $this->session->data['shipping_method']['tax_class_id']);

                foreach ($shipping_tax as $key => $value) {
                    $correct_rate = round($value['rate'], 2) / 100;
                    $rule = new MspDefaultTaxRule($correct_rate, 'true'); // Tax rate, shipping taxed
                    $msp->cart->AddDefaultTaxRules($rule);
                    $shipping_select = $value['name'];
                }
                $c_item = new MspItem($this->session->data['shipping_method']['title'] . " " . $order_info['currency_code'], 'Shipping', '1', $this->session->data['shipping_method']['cost'], '0', '0');
                $msp->cart->AddItem($c_item);
                $c_item->SetMerchantItemId('Shipping');
                $c_item->SetTaxTableSelector($shipping_select); //shipping.... $this->session->data['shipping_method']['tax_class_id']
            }
            $msp->transaction['daysactive'] = $this->config->get('multisafepay_days_active');
            $msp->transaction['amount'] = round($order_info['total'] * 100);
            $msp->plugin_name = 'OpenCart';
            $msp->version = '(2.0.0)';

            $msp->transaction['items'] = $html;
            $msp->plugin['shop'] = 'OpenCart';
            $msp->plugin['shop_version'] = VERSION;
            $msp->plugin['plugin_version'] = '2.0.0';
            $msp->plugin['partner'] = '';
            $msp->plugin['shop_root_url'] = '';


            if ($gateway == 'IDEAL' && isset($this->request->post['issuer'])) {
                $msp->extravars = $this->request->post['issuer'];
                $url = $msp->startDirectXMLTransaction();
            } else {
                //$url 								= 	$msp->startCheckout();
                $url = $msp->startTransaction();
            }


            if (!isset($msp->error)) {

                $this->load->model('checkout/order');
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('multisafepay_order_status_id_initialized'), '', true);

                header('Location: ' . $url);
                exit;
            } else {
                $this->language->load('payment/multisafepay');
                $data['back_to_store'] = $this->language->get('back_to_store');
                $data['errorcode'] = $msp->error_code;
                $data['errorstring'] = $msp->error;
                $data['charset'] = $this->language->get('charset');
                $data['language'] = $this->language->get('code');
                $data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
                $data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success', '', 'SSL'));
                $data['text_failure'] = $this->language->get('text_failure');
                $data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkout', '', 'SSL'));
                $data['button_continue'] = $this->language->get('button_continue');
                $data['continue'] = $this->url->link('checkout/checkout', '', 'SSL');

                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/multisafepay_failure.tpl')) {
                    echo $this->load->view($this->config->get('config_template') . '/template/payment/multisafepay_failure.tpl', $data);
                } else {
                    echo $this->load->view('default/template/payment/multisafepay_failure.tpl', $data);
                }
                exit;
            }
        } else {
            include_once('MultiSafepay.combined.php');
            $msp = new MultiSafepay();
            $this->load->model('total/coupon');
            $total_data = array();
            $total = $this->cart->getTotal();
            $start_total = $this->cart->getTotal();
            $taxes = $this->cart->getTaxes();

            $this->model_total_coupon->getTotal($total_data, $total, $taxes);

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

            $msp->test = $this->config->get('multisafepay_environment');
            $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id');
            $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id');
            $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code');
            $msp->merchant['notification_url'] = $this->url->link('payment/multisafepay/fastcheckout&type=initial', '', 'SSL');
            $msp->merchant['redirect_url'] = $this->url->link('checkout/success', '', 'SSL');
            $msp->merchant['cancel_url'] = $this->url->link('checkout/cart', '', 'SSL');
            $msp->use_shipping_notification = true; // This module uses shipping notification
            $msp->transaction['items'] = $this->getCartItemsHTML();

            // Create products array (will be used later)
            $products = $this->cart->getProducts();

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
                $msp->transaction['var2'] = $this->customer->getCustomerGroupId();

                $customerid = $this->customer->getId();
                $customergid = $this->customer->getCustomerGroupId();
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

            if ($this->config->get('fco_tax_percent') != '') {
                $rule = new MspDefaultTaxRule($this->config->get('fco_tax_percent'), 'true'); // Tax rate, shipping taxed
            } else {
                $rule = new MspDefaultTaxRule(0, 'true'); // Tax rate, shipping taxed
            }

            $msp->cart->AddDefaultTaxRules($rule);

            // Tax for products
            $taxtable = Array();
            foreach ($products AS $product) {

                $ratiotax = $this->tax->getRates($product['total'], $product['tax_class_id']);
                foreach ($ratiotax AS $tax_array) {
                    $taxes[] = $tax_array;
                }
            }

            $unique_taxes = $taxes;

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

                if (isset($taxes[0])) {
                    $taxname = $taxes[0]['name'];
                } else {
                    $taxname = 'none';
                }

                $c_item = new MspItem($product['name'], strip_tags($product['model']), $product['quantity'], $product['price'], 'KG', $product['weight']);
                $c_item->merchant_item_id = $product['product_id'];
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

            foreach ($products AS $product) {
                $ratiotax = $this->tax->getRates($product['total'], $product['tax_class_id']);
                $products[$product['key']]['tax'] = 0;
                foreach ($ratiotax AS $tax_array) {
                    $taxes[] = $tax_array;

                    foreach ($taxes as $tax) {
                        if (isset($tax['amount'])) {
                            $products[$product['key']]['tax'] = $tax['amount'];
                        } else {
                            $products[$product['key']]['tax'] = 0;
                        }
                    }
                }
                foreach ($product['option'] as $key => $value) {
                    $products[$product['key']]['option'][$key]['value'] = $value['option_value'];
                }
            }


            foreach ($products AS $product) {
                $products_array[] = $product;
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
                'totals' => $emptyar,
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
                'ip' => $_SERVER['REMOTE_ADDR']);

            // If user logged in fill up extra data
            if ($this->customer->isLogged()) {
                $order_data['customer_id'] = $this->customer->getId();
                $order_data['customer_group_id'] = $this->customer->getCustomerGroupId();
            }

            //Create an order
            $this->load->model('checkout/order');
            // Create an order (pass second argument so we will only update our database with info from MultiSafepay)
            $order_info = $this->model_checkout_order->getOrder($this->request->post['cartId']);
            //$order_id = $this->session->data['last_order_id']+1;
            //$order_id = $this->model_checkout_order->create($order_data);
            $order_id = $this->model_checkout_order->addOrder($order_data);

            //echo $total;exit;
            // Transaction info.
            $msp->transaction['id'] = $order_id;
            $order_data['order_id'] = $order_id;
            $msp->transaction['currency'] = $order_info['currency_code'];
            $msp->transaction['amount'] = round($total * 100, 0); // Has to be in eurocents, no fraction!
            $msp->transaction['description'] = $this->getOrderDescription($order_id);
            $msp->transaction['daysactive'] = $this->config->get('multisafepay_days_active');
            $msp->plugin_name = 'OpenCart' . VERSION;
            $msp->version = '(2.0.0)';


            $msp->plugin['shop'] = 'OpenCart';
            $msp->plugin['shop_version'] = VERSION;
            $msp->plugin['plugin_version'] = '2.0.0';
            $msp->plugin['partner'] = '';
            $msp->plugin['shop_root_url'] = '';

            if (isset($this->session->data['coupon'])) {
                $this->load->model('checkout/coupon');
                $coupon_info = $this->model_checkout_coupon->getCoupon($this->session->data['coupon']);

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
                $this->redirect($url);
            }
        }
    }

    /**
     * Call back function
     */
    public function fastcheckout() {

        if (isset($_GET['type'])) {
            if ($_GET['type'] == 'shipping') {
                $xml = '<?xml version="1.0" encoding="UTF-8"?>';
                $xml .= '<shipping-info>';
                $weight = 0;
                $weight = $_GET['weight'];
                $weight = str_replace(',', '.', $weight);
                $countrycode = strtoupper($_GET['countrycode']);
                $shippers = array();
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 = '" . $this->sanitize($countrycode) . "' AND status = '1'");
                $country = $query->row;
                $country_id = $country['country_id'];

                $shippers = $this->getShippingOptions($country_id);

                $this->load->model('localisation/country');
                //$country_info 									= 	$this->model_localisation_country->getCountry($country_id);


                foreach ($shippers as $key => $value) {
                    foreach ($value['quote'] as $key => $value) {
                        $ship = '<shipping>';
                        $ship .= '<shipping-name>' . $value['title'] . '</shipping-name>';
                        $ship .= '<shipping-cost currency="' . $_GET['currency'] . '">' . $value['cost'] . '</shipping-cost>';
                        $ship .= '</shipping>';
                        $shippers[] = $ship;
                    }
                }

                $shippers = array_unique($shippers);
                $v = 0;
                while (isset($shippers[$v])) {
                    $xml .= $shippers[$v];
                    $v++;
                }
                $xml .= '</shipping-info>';
                //header ("Content-Type:text/xml");  
                print_r($xml);
                exit;
            }
        }
        if (isset($_GET['transactionid'])) {

            $initial = false;
            if (isset($_GET['type'])) {
                if ($_GET['type'] == 'initial')
                    $initial = true;
            }

            require_once(dirname(__FILE__) . '/MultiSafepay.combined.php');
            $msp = new MultiSafepay();
            $msp->test = $this->config->get('multisafepay_environment');
            $order_number = $_GET['transactionid'];

            $this->load->model('checkout/order');

            $order = $this->model_checkout_order->getOrder($order_number);


            $storeid = $order['store_id'];

            if (isset($_GET['site'])) {
                $storeid = $_GET['site'];
            }

            if ($storeid === $this->config->get('msp_storeid_1')) {
                $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id_1');
                $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id_1');
                $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code_1');
            } elseif ($storeid === $this->config->get('msp_storeid_2')) {
                $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id_2');
                $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id_2');
                $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code_2');
            } elseif ($storeid === $this->config->get('msp_storeid_3')) {
                $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id_3');
                $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id_3');
                $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code_3');
            } elseif ($storeid === $this->config->get('msp_storeid_4')) {
                $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id_4');
                $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id_4');
                $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code_4');
            } else {
                $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id');
                $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id');
                $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code');
            }



            /* $msp->merchant['account_id']      	= $this->config->get('multisafepay_merchant_id');
              $msp->merchant['site_id']          	= $this->config->get('multisafepay_site_id');
              $msp->merchant['site_code']        	= $this->config->get('multisafepay_secure_code'); */
            $msp->transaction['id'] = $_GET['transactionid'];
            $details = $msp->details;

            $status = $msp->getStatus();

            if ($msp->details['ewallet']['fastcheckout'] != "YES") {
                $this->callback();
                exit;
            }


            if (isset($msp->error)) {
                echo 'Error: ' . $msp->error;
                exit;
            }
            // Some shortcuts
            $mspcust = $msp->details['customer'];
            $mspshipaddr = $msp->details['customer-delivery'];
            $mspshipping = $msp->details['shipping'];
            $mspcart = $msp->details['shopping-cart'];
            $emptyar = array();


            if (isset($msp->details['transaction']['var1'])) {
                $data = explode('|', $msp->details['transaction']['var1']);

                $customerid = $data[0];
                if (isset($data[1])) {
                    $store_credit = 0 - $data[1];
                } else {
                    $store_credit = 0;
                }
                $customergid = $msp->details['transaction']['var2'];
            } else {
                $customerid = 0;
                $customergid = 0;
                $store_credit = 0;
            }

            // We'll need this big array to place new order
            $order_data = array(
                'invoice_prefix' => '',
                'payment_company_id' => '',
                'payment_tax_id' => '',
                'payment_code' => '',
                'shipping_code' => '',
                'forwarded_ip' => '',
                'user_agent' => '',
                'accept_language' => '',
                'tax' => '',
                'products' => $emptyar,
                'totals' => $emptyar,
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
                'order_id' => $msp->transaction['id']
            );

            // Load object for controlling new orders
            $this->load->model('checkout/order');
            // Create an order (pass second argument so we will only update our database with info from MultiSafepay)
            //$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$this->config->get('multisafepay_order_status_id_initialized'). "', date_modified = NOW() WHERE order_id = '" . $_GET['transactionid'] . "'");

            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($order_data['invoice_prefix']) . "', store_id = '" . (int) $order_data['store_id'] . "', store_name = '" . $this->db->escape($order_data['store_name']) . "', store_url = '" . $this->db->escape($order_data['store_url']) . "', customer_id = '" . (int) $order_data['customer_id'] . "', customer_group_id = '" . (int) $order_data['customer_group_id'] . "', firstname = '" . $this->db->escape($order_data['firstname']) . "', lastname = '" . $this->db->escape($order_data['lastname']) . "', email = '" . $this->db->escape($order_data['email']) . "', telephone = '" . $this->db->escape($order_data['telephone']) . "', fax = '" . $this->db->escape($order_data['fax']) . "', payment_firstname = '" . $this->db->escape($order_data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($order_data['payment_lastname']) . "', payment_company = '" . $this->db->escape($order_data['payment_company']) . "', payment_address_1 = '" . $this->db->escape($order_data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($order_data['payment_address_2']) . "', payment_city = '" . $this->db->escape($order_data['payment_city']) . "', payment_postcode = '" . $this->db->escape($order_data['payment_postcode']) . "', payment_country = '" . $this->db->escape($order_data['payment_country']) . "', payment_country_id = '" . (int) $order_data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($order_data['payment_zone']) . "', payment_zone_id = '" . (int) $order_data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($order_data['payment_address_format']) . "', payment_method = '" . $this->db->escape($order_data['payment_method']) . "', shipping_firstname = '" . $this->db->escape($order_data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($order_data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($order_data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($order_data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($order_data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($order_data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($order_data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($order_data['shipping_country']) . "', shipping_country_id = '" . (int) $order_data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($order_data['shipping_zone']) . "', shipping_zone_id = '" . (int) $order_data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($order_data['shipping_address_format']) . "', shipping_method = '" . $this->db->escape($order_data['shipping_method']) . "', comment = '" . $this->db->escape($order_data['comment']) . "', total = '" . (float) $order_data['total'] . "', affiliate_id = '" . (int) $order_data['affiliate_id'] . "', commission = '" . (float) $order_data['commission'] . "', language_id = '" . (int) $order_data['language_id'] . "', currency_id = '" . (int) $order_data['currency_id'] . "', currency_code = '" . $this->db->escape($order_data['currency_code']) . "', currency_value = '" . (float) $order_data['currency_value'] . "', date_added = NOW(), date_modified = NOW() WHERE order_id = '" . $msp->transaction['id'] . "'");


            //print_r($msp->details);exit;SELECT t1.name, t2.salary FROM employee AS t1, info AS t2 WHERE t1.name = t2.name;

            $data = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id='" . $msp->transaction['id'] . "' AND code='shipping'");


            if (empty($data->row['order_total_id'])) {
                //shipping totals update
                $this->db->query("INSERT INTO `" . DB_PREFIX . "order_total` (order_id, code, title, value, sort_order) VALUES ('" . $msp->transaction['id'] . "', 'shipping', '" . $msp->details['shipping']['name'] . "', '" . $msp->details['shipping']['cost'] . "', '3')");
            } else {
                $order_total_id_tax = $data->row['order_total_id'];
                $this->db->query("UPDATE `" . DB_PREFIX . "order_total` SET value='" . $msp->details['shipping']['cost'] . "' WHERE order_total_id='" . $order_total_id_tax . "'");
            }

            $data = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id='" . $msp->transaction['id'] . "' AND code='total'");
            $order_total_id_tax = $data->row['order_total_id'];
            $this->db->query("UPDATE `" . DB_PREFIX . "order_total` SET value='" . $msp->details['order-total']['total'] . "' WHERE order_total_id='" . $order_total_id_tax . "'");

            //tax totals update
            //$this->db->query("INSERT INTO `" . DB_PREFIX . "order_total` (order_id, code, value) VALUES ('".$msp->transaction['id']."', 'tax', '".$msp->details['total-tax']['total']."')");
            $data = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id='" . $msp->transaction['id'] . "' AND code='tax'");
            $order_total_id_tax = $data->row['order_total_id'];
            $this->db->query("UPDATE `" . DB_PREFIX . "order_total` SET value='" . $msp->details['total-tax']['total'] . "' WHERE order_total_id='" . $order_total_id_tax . "'");


            if (isset($details['transaction']['var1'])) {
                $this->load->model('checkout/coupon');
                $total = $msp->details['transaction']['amount'] / 100;

                $coupon_info = $this->model_checkout_coupon->getCoupon($details['transaction']['var1']);

                if ($coupon_info) {
                    $this->model_checkout_coupon->redeem($coupon_info['coupon_id'], $msp->transaction['id'], 0, $total);
                }
            }

            $sql_result = $this->db->query("SELECT customer_transaction_id FROM  " . DB_PREFIX . "customer_transaction WHERE order_id =  " . $msp->transaction['id'] . " LIMIT 0,1");


            if ($sql_result->num_rows == 0) {
                //Process store credit toto add check so it is only added once to the transactions db.
                $total = $msp->details['transaction']['amount'] / 100;

                $this->load->model('total/credit');
                $order_total = array();
                $order_total['value'] = $store_credit;
                $process_order = $this->model_total_credit->confirm($order_data, $order_total);
            }




            // Change status to pending
            $this->load->language('payment/multisafepay');
            $initialorderstatus = $this->config->get('multisafepay_order_status_id_initialized');
            $comment = $this->language->get('initial');

            // Send a message that will be displayed at the end of fastcheckout process.
            $shopaddress = $this->config->get('config_url');
            $goback = $this->language->get('Ga terug naar de website');


            $notify = false;
            switch ($status) {
                // waiting
                case "initialized":
                    $neworderstatus = $this->config->get('multisafepay_order_status_id_initialized');
                    $comment = $this->language->get('initialized');
                    break;
                // payment complete
                case "completed":
                    $neworderstatus = $this->config->get('multisafepay_order_status_id_completed');
                    $comment = $this->language->get('completed');
                    $notify = true; // Notify user
                    break;
                // waiting (credit cards or direct debit)
                case 'reversed':
                    $neworderstatus = $this->config->get('multisafepay_order_status_id_reversed');
                    break;
                case 'reserved':
                    $neworderstatus = $this->config->get('multisafepay_order_status_id_reserved');
                    break;
                case "uncleared":
                    $neworderstatus = $this->config->get('multisafepay_order_status_id_uncleared');
                    $comment = $this->language->get('uncleared');
                    break;
                // canceled
                case "void":
                    $neworderstatus = $this->config->get('multisafepay_order_status_id_void');
                    $comment = $this->language->get('void');
                    break;
                // declined
                case "declined":
                    $neworderstatus = $this->config->get('multisafepay_order_status_id_declined');
                    $comment = $this->language->get('declined');
                    break;
                // refunded
                case "refunded":
                    $neworderstatus = $this->config->get('multisafepay_order_status_id_refunded');
                    $comment = $this->language->get('refunded');
                    break;
                 case "partial_refunded":
                    $neworderstatus = $this->config->get('multisafepay_order_status_id_partial_refunded');
                    $comment = $this->language->get('refunded');
                    break;
                case "shipped":
                    $neworderstatus = $this->config->get('multisafepay_order_status_id_shipped');
                    $comment = $this->language->get('shipped');
                    break;

                // expired
                case "expired":
                    $neworderstatus = $this->config->get('multisafepay_order_status_id_expired');
                    $comment = $this->language->get('expired');
                    break;
                default:
                    $neworderstatus = 1;
                    $comment = $this->language->get('default');
                    break;
            }

            // If order status doesn't change - return
            $order_data = $this->model_checkout_order->getOrder($msp->transaction['id']);
            if ($order_data['order_status_id'] != $neworderstatus) {
                //$this->model_checkout_order->addOrderHistory($msp->transaction['id'], $initialorderstatus, $comment, true);
                $this->model_checkout_order->addOrderHistory($msp->transaction['id'], $neworderstatus, $comment, true);
                //$this->model_checkout_order->confirm($msp->transaction['id'], $$neworderstatus, $comment, true);
                //$this->model_checkout_order->update($msp->transaction['id'], $neworderstatus, $comment, true);
            }

            if ($initial) {
                print("<a href=\"$shopaddress\">$goback</a>");
            } else {
                echo 'ok';
            }
        }
    }

    function generatePassword($length = 12) {
        return substr(md5(rand() . rand()), 0, $length);
    }

    public function sanitize($data) {
        //remove spaces from the input
        $data = trim($data);
        //convert special characters to html entities
        //most hacking inputs in XSS are HTML in nature, so converting them to special characters so that they are not harmful
        $data = htmlspecialchars($data);
        //sanitize before using any MySQL database queries
        //this will escape quotes in the input.
        //$data = mysql_real_escape_string($data);
        return $data;
    }

// This function checks if we are called to return shipping costs for new address
    private function isNewAddressQuery() {
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
    private function handleShippingMethodsNotification($model) {
        $country = $this->request->get['country'];
        $countryCode = $this->request->get['countrycode'];
        $transactionId = $this->request->get['transactionid'];
        $weight = $this->request->get['weight'];
        $size = $this->request->get['size'];

        header("Content-Type:text/xml");
        print($this->getShippingMethodsFilteredXML($country, $countryCode, $weight, $size, $transactionId));
    }

    // Returns XML with new shipping costs
    private function getShippingMethodsFilteredXML($country, $countryCode, $weight, $size, $transactionId) {
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
    private function getShippingMethodsFiltered($country, $countryCode, $weight, $size, $transactionId) {
        $out = array();

        $shippingopts = $this->getShippingOptionsISO2($countryCode);
        foreach ($shippingopts as $key => $value) {
            $shipping = array();
            $shipping['name'] = $value['title'];
            $shipping['cost'] = $value['quote'][$key]['cost'];
            $shipping['currency'] = $_GET['currency']; // Currently Euro is supported
            $out[] = $shipping;
        }

        return $out;
    }

    /**
     * Call back functiom
     */
    public function callback() {
        $initial_request = (isset($_GET['type']) == 'initial');

        $order_number = $_GET['transactionid'];
        $this->load->model('checkout/order');
        $order = $this->model_checkout_order->getOrder($order_number);
        $storeid = $order['store_id'];

        if (!empty($order_number)) {

            require_once(dirname(__FILE__) . '/MultiSafepay.combined.php');

            $msp = new MultiSafepay();
            if ($order['payment_code'] == "multisafepay_payafter") {
                $msp->test = $this->config->get('multisafepay_payafter_environment');
                $msp->merchant['account_id'] = $this->config->get('multisafepay_payafter_merchant_id');
                $msp->merchant['site_id'] = $this->config->get('multisafepay_payafter_site_id');
                $msp->merchant['site_code'] = $this->config->get('multisafepay_payafter_secure_code');
            } else {
                $msp->test = $this->config->get('multisafepay_environment');

                if ($storeid === $this->config->get('msp_storeid_1')) {
                    $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id_1');
                    $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id_1');
                    $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code_1');
                } elseif ($storeid === $this->config->get('msp_storeid_2')) {
                    $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id_2');
                    $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id_2');
                    $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code_2');
                } elseif ($storeid === $this->config->get('msp_storeid_3')) {
                    $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id_3');
                    $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id_3');
                    $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code_3');
                } elseif ($storeid === $this->config->get('msp_storeid_4')) {
                    $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id_4');
                    $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id_4');
                    $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code_4');
                } else {
                    $msp->merchant['account_id'] = $this->config->get('multisafepay_merchant_id');
                    $msp->merchant['site_id'] = $this->config->get('multisafepay_site_id');
                    $msp->merchant['site_code'] = $this->config->get('multisafepay_secure_code');
                }
            }
            $msp->transaction['id'] = $order_number;
            $status = $msp->getStatus();
            $details = $msp->details;
            $amount = $details['customer']['amount'];
            $orderid = $details['transaction']['id'];
            $success = false;

            if (isset($msp->details['transaction']['var1'])) {
                $data = explode('|', $msp->details['transaction']['var1']);

                $customerid = $data[0];

                if (isset($data[1])) {
                    $store_credit = 0 - $data[1];
                } else {
                    $store_credit = 0;
                }


                $customergid = $msp->details['transaction']['var2'];
            } else {
                $customerid = 0;
                $customergid = 0;
            }


            // Some shortcuts
            $mspcust = $msp->details['customer'];
            if (isset($msp->details['customer-delivery'])) {
                $mspshipaddr = $msp->details['customer-delivery'];
            } else {
                $mspshipaddr['firstname'] = "";
                $mspshipaddr['lastname'] = "";
                $mspshipaddr['address1'] = "";
                $mspshipaddr['housenumber'] = "";
                $mspshipaddr['address2'] = "";
                $mspshipaddr['zipcode'] = "";
                $mspshipaddr['city'] = "";
                $mspshipaddr['countryname'] = "";
                $mspshipaddr['country'] = "";
            }
            if (isset($msp->details['shipping'])) {
                $mspshipping = $msp->details['shipping'];
            } else {
                $mspshipping['name'] = "";
            }
            if (isset($msp->details['shopping-cart'])) {
                $mspcart = $msp->details['shopping-cart'];
            }

            $emptyar = array();

            /**
             * MSP-> WE NOW NOW THE RETURNED STATUS, GET CONFIGURED SHOP STATUS AND PROCESS
             */
            switch ($status) {
                case 'completed':
                    $newStatus = $this->config->get('multisafepay_order_status_id_completed');
                    $success = true;
                    break;
                case 'initialized':
                    $newStatus = $this->config->get('multisafepay_order_status_id_initialized');
                    $success = true;
                    break;
                case 'uncleared':
                    $newStatus = $this->config->get('multisafepay_order_status_id_uncleared');
                    break;
                case 'reserved':
                    $newStatus = $this->config->get('multisafepay_order_status_id_reserved');
                    break;
                case 'void':
                    $newStatus = $this->config->get('multisafepay_order_status_id_void');
                    break;
                case 'cancelled':
                    $newStatus = $this->config->get('multisafepay_order_status_id_void');
                    break;
                case 'declined':
                    $newStatus = $this->config->get('multisafepay_order_status_id_declined');
                    break;
                case 'reversed':
                    $newStatus = $this->config->get('multisafepay_order_status_id_reversed');
                    break;
                case 'refunded':
                    $newStatus = $this->config->get('multisafepay_order_status_id_refunded');
                    break;
                case 'partial_refunded':
                    $newStatus = $this->config->get('multisafepay_order_status_id_partial_refunded');
                    break;
                case 'expired':
                    $newStatus = $this->config->get('multisafepay_order_status_id_expired');
                    break;
                case 'shipped':
                    $newStatus = $this->config->get('multisafepay_order_status_id_shipped');
                    break;
                default:
                    $newStatus = $this->config->get('multisafepay_order_status_id_initialized');
                    $displaylink = '<p><a href="' . $this->url->link('payment/multisafepay/_multisafepayfailure', '', 'SSL') . '">Terug naar ' . $this->config->get('config_name') . '</a></p>';
                    $displaymessage = "Payment failed";
                    break;
            }

            /**
             * MSP -> THE TRANSACTION GO OKAY?
             */
            if ($success) {
                $return_link = $this->url->link('checkout/success', '', 'SSL');
                $confirm_message = "Confirmed order at " . date('Y/m/d H:i:s', time());
            } else {
                $return_link = $this->url->link('payment/multisafepay/_multisafepayfailure', '', 'SSL');
                $confirm_message = "Order " . $status . " at " . date('Y/m/d H:i:s', time());
            }

            $message = "Order " . $status . " at " . date('Y/m/d H:i:s ', time());

            /*
             * 	MSP returned data is processed so update the order
             */
            $this->load->model('checkout/order');
            $order = $this->model_checkout_order->getOrder($orderid);

            if ($status != 'shipped') {
                if ($order['order_status_id'] != $newStatus && $order['order_status_id'] != '3') {

                    if ($order['order_status_id'] != $this->config->get('multisafepay_order_status_id_completed') && $status == 'completed') {
                        // $this->model_checkout_order->update($orderid, $newStatus, $message, false);
                        $this->model_checkout_order->addOrderHistory($orderid, $newStatus, $message, true);
                    }
                }
            }

            $displaylink = '<p><a href="' . $return_link . '">Terug naar ' . $this->config->get('config_name') . '</a></p>';
            $displaymessage = "OK";

            if ($initial_request) {
                echo $displaylink;
            } else {
                header("Content-type: text/plain");
                echo $displaymessage;
            }
            exit;
        }
    }

    /**
     * MSP SETUP FAILER TEMPLATE
     */
    public function _multisafepayfailure() {
        $this->language->load('payment/multisafepay');
        $data['text_failure'] = $this->language->get('text_failure');
        $data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/payment', '', 'SSL'));
        $data['button_continue'] = $this->language->get('button_continue');
        $data['continue'] = $this->url->link('checkout/payment', '', 'SSL');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/multisafepay_failure.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/multisafepay_failure.tpl';
        } else {
            $this->template = 'default/template/payment/multisafepay_failure.tpl';
        }
        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    /**
     * MSP GET SERVER FOR TRANSACTION REQUEST
     */
    public function _oc_multisafepay_getenvironment() {
        $multisafepay_test = 'https://testapi.multisafepay.com/ewx/';
        $multisafepay_live = 'https://api.multisafepay.com/ewx/';

        if ($this->config->get('multisafepay_environment') == 0) {
            return true;
        } else {
            return false;
        }
    }

    // Try to guess shipping costs, see also controller/total/shipping.php
    private function getShippingOptions($country_id) {
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
        $this->load->model('extension/extension');
        //$this->load->model('setting/extension');

        $amount = $_REQUEST['amount'] / 100;
        $results = $this->model_extension_extension->getExtensions('shipping');



        foreach ($results as $result) {
            // If module is enabled
            if ($this->config->get($result['code'] . '_status')) {

                $this->load->model('shipping/' . $result['code']);

                if ($result['code'] != 'weight' && $result['code'] != 'free'  && $result['code'] != 'item') {
                    $quote = $this->{'model_shipping_' . $result['code']}->getQuote($address_data);

                    if ($amount <= $this->config->get('multisafepay_fco_free_ship')) {
                        if ($quote) {
                            $quote_data[$result['code']] = array(
                                'title' => $quote['title'],
                                'quote' => $quote['quote'],
                                'sort_order' => $quote['sort_order'],
                                'error' => $quote['error']
                            );
                        }
                    } else {
                        $this->language->load('shipping/free');

                        $quote_datas['free'] = array(
                            'code' => 'free.free',
                            'title' => $this->language->get('text_description'),
                            'cost' => 0.00,
                            'tax_class_id' => 0,
                            'text' => $this->currency->format(0.00)
                        );

                        $quote_data['weight'] = array(
                            'code' => 'weight',
                            'title' => $this->language->get('text_title'),
                            'quote' => $quote_datas,
                            'sort_order' => $this->config->get('weight_sort_order'),
                            'error' => false
                        );
                        
                        
                    }
                }elseif ($result['code'] == 'item') { 
                	$this->load->language('shipping/item');
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('item_geo_zone_id') . "' AND country_id = '" . (int)$address_data['country_id'] . "' AND (zone_id = '" . (int)$address_data['zone_id'] . "' OR zone_id = '0')");
			
					if (!$this->config->get('item_geo_zone_id')) {
						$status = true;
					} elseif ($query->num_rows) {
						$status = true;
					} else {
						$status = false;
					}
			
					$method_data = array();
			
					if ($status) {
						$items = $_GET['items_count'];
			

						 $quote_datas = array();
						 if ($status) {

	                        $quote_datas['item'] = array(
							'code'         => 'item.item',
							'title'        => $this->language->get('text_description'),
							'cost'         => $this->config->get('item_cost') * $items,
							'tax_class_id' => $this->config->get('item_tax_class_id'),
							'text'         => $this->currency->format($this->tax->calculate($this->config->get('item_cost') * $items, $this->config->get('item_tax_class_id'), $this->config->get('config_tax')))
						);	
	                        $quote_data['item'] = array(
	                            'code' => 'item',
	                            'title' => $this->language->get('text_title'),
	                            'quote' => $quote_datas,
	                            'sort_order' => $this->config->get('item_sort_order'),
	                            'error' => false
	                        );
						}
					}
                }elseif ($result['code'] == 'free') {
                    $this->language->load('shipping/free');

                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name");

                    foreach ($query->rows as $result) {
                        if ($this->config->get('weight_' . $result['geo_zone_id'] . '_status')) {
                            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $result['geo_zone_id'] . "' AND country_id = '" . (int) $address_data['country_id'] . "' AND (zone_id = '" . (int) $address_data['zone_id'] . "' OR zone_id = '0')");

                            if ($query->num_rows) {
                                $status = true;
                            } else {
                                $status = false;
                            }
                        } else {
                            $status = false;
                        }
                    }


                    if ($amount >= $this->config->get('multisafepay_fco_free_ship') && $this->config->get('multisafepay_fco_free_ship') != '') {
                        $status = true;
                    }

                    $quote_datas = array();
                    if ($status) {

                        $quote_datas['free'] = array(
                            'code' => 'free.free',
                            'title' => $this->language->get('text_description'),
                            'cost' => 0.00,
                            'tax_class_id' => 0,
                            'text' => $this->currency->format(0.00)
                        );

                        $quote_data['weight'] = array(
                            'code' => 'weight',
                            'title' => $this->language->get('text_title'),
                            'quote' => $quote_datas,
                            'sort_order' => $this->config->get('weight_sort_order'),
                            'error' => false
                        );
                    }
                } else {
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name");

                    foreach ($query->rows as $result) {

                        if ($this->config->get('weight_' . $result['geo_zone_id'] . '_status')) {
                            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $result['geo_zone_id'] . "' AND country_id = '" . (int) $address_data['country_id'] . "' AND (zone_id = '" . (int) $address_data['zone_id'] . "' OR zone_id = '0')");

                            if ($query->num_rows) {
                                $status = true;
                            } else {
                                $status = false;
                            }
                        } else {
                            $status = false;
                        }

                        if ($status) {
                            $cost = '';
                            $weight = $_GET['weight']; //$this->cart->getWeight();

                            $rates = explode(',', $this->config->get('weight_' . $result['geo_zone_id'] . '_rate'));

                            foreach ($rates as $rate) {
                                $data = explode(':', $rate);

                                if ($data[0] >= $weight) {
                                    if (isset($data[1])) {
                                        $cost = $data[1];
                                    }

                                    break;
                                }
                            }
                            
                           
                            $quote_datas = array();
                            if ((string) $cost != '') {
                                $this->language->load('shipping/weight');
                                $quote_datas['weight_' . $result['geo_zone_id']] = array(
                                    'code' => 'weight.weight_' . $result['geo_zone_id'],
                                    'title' => $result['name'] . '  (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class_id')) . ')',
                                    'cost' => $cost,
                                    'tax_class_id' => $this->config->get('weight_tax_class_id'),
                                    'text' => $this->currency->format($this->tax->calculate($cost, $this->config->get('weight_tax_class_id'), $this->config->get('config_tax')))
                                );
                                $quote_data['weight'] = array(
                                    'code' => 'weight',
                                    'title' => $this->language->get('text_title'),
                                    'quote' => $quote_datas,
                                    'sort_order' => $this->config->get('weight_sort_order'),
                                    'error' => false
                                );
                            }
                        }
                    }
                }
            }
        }


        return $quote_data;
    }

    // Try to guess shipping costs based on ISO2 country code
    private function getShippingOptionsISO2($country_iso2) {
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

    private function getOrderDescription($order_id) {
        $date = date("D M j G:i:s T Y");
        $shopname = $this->config->get('config_name');
        $desc = 'Order ID#' . $order_id . ' from ' . $shopname . ' placed on ' . $date;

        return $desc;
    }

    private function getCartItemsHTML() {
        $products = $this->cart->getProducts();
        $cart_items = '<br/><ul>';
        foreach ($products as $product) {
            $cart_items .= '<li>' . $product['quantity'] . ' x ' . $product['name'] . '</li>';
        }
        $cart_items .= '</ul>';

        return $cart_items;
    }

    private function langISO2langId($langiso) {
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $lang) {
            if (stristr($lang['locale'], $langiso)) { // If found in locale, not 100% reliable
                return $lang['language_id'];
            }
        }

        return 1; // In emergency return 1 (should be english)
    }

    public function feed() {

        $output = '<?xml version="1.0" encoding="UTF-8"?>';
        $output .= '<feed xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $this->load->model('catalog/product');

        $products = $this->model_catalog_product->getProducts();
        foreach ($products as $product) {
            $output .= '  <product>';
            $output .= '    <product_id>' . $product['product_id'] . '</product_id>';
            $output .= '    <options>';
            $options = $this->model_catalog_product->getProductOptions($product['product_id']);
            foreach ($options as $option) {
                $output .= '      <option>';
                $output .= '        <name>' . $option['name'] . '</name>';
                $output .= '        <values>';
                if(!empty($option['option_value']))
                {
                    foreach ($option['option_value'] as $value) {
                      $output .= '          <value>' . $value['name'] . '</value>';
                    }
                }
                $output .= '        </values>';
                $output .= '      </option>';
            }
            $output .= '    </options>';
            $output .= '  </product>';
        }

        $output .= '</feed>';

        $this->response->addHeader('Content-Type: application/xml');
        $this->response->setOutput($output);
    }

}

?>