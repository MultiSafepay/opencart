<?php

class CatalogControllerExtensionPaymentMultiSafePayTest extends MultisafepayTestSuiteForOpenCart {

    public function testFormPaymentMethodMultiSafepay() {
        $this->buildSessionPaymentData('multisafepay', 'MultiSafepay');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodAfterpay() {
        $this->buildSessionPaymentData('multisafepay/afterPay', 'Afterpay');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/direct/", $multisafepay_response->getOutput());
        $this->assertRegExp("/AFTERPAY/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodAmex() {
        $this->buildSessionPaymentData('multisafepay/amex', 'American Express');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/AMEX/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodAliPay() {
        $this->buildSessionPaymentData('multisafepay/aliPay', 'Ali Pay');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/direct/", $multisafepay_response->getOutput());
        $this->assertRegExp("/ALIPAY/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodApplePay() {
        $this->buildSessionPaymentData('multisafepay/applePay', 'Apple Pay');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/APPLEPAY/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodBancontact() {
        $this->buildSessionPaymentData('multisafepay/bancontact', 'Bancontact');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/MISTERCASH/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodBankTransfer() {
        $this->buildSessionPaymentData('multisafepay/bankTransfer', 'Bank transfer');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/direct/", $multisafepay_response->getOutput());
        $this->assertRegExp("/BANKTRANS/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodBelfius() {
        $this->buildSessionPaymentData('multisafepay/belfius', 'Belfius');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/BELFIUS/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodBoekenbon() {
        $this->buildSessionPaymentData('multisafepay/boekenbon', 'Boekenbon');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/BOEKENBON/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodCbc() {
        $this->buildSessionPaymentData('multisafepay/cbc', 'CBC');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/direct/", $multisafepay_response->getOutput());
        $this->assertRegExp("/CBC/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodCreditCard() {
        $this->buildSessionPaymentData('multisafepay/creditCard', 'Card payment');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/CREDITCARD/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodDbrtp() {
        $this->buildSessionPaymentData('multisafepay/dbrtp', 'Request to Pay');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/DBRTP/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodDirectBank() {
        $this->buildSessionPaymentData('multisafepay/directBank', 'Direct Bank');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/DIRECTBANK/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodDotPay() {
        $this->buildSessionPaymentData('multisafepay/dotpay', 'DotPay');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/gateway_info/", $multisafepay_response->getOutput());
        $this->assertRegExp("/Meta/", $multisafepay_response->getOutput());
        $this->assertRegExp("/DOTPAY/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodEps() {
        $this->buildSessionPaymentData('multisafepay/eps', 'EPS');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/EPS/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodEinvoicing() {
        $this->buildSessionPaymentData('multisafepay/eInvoice', 'E-Invoicing');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/direct/", $multisafepay_response->getOutput());
        $this->assertRegExp("/gateway_info/", $multisafepay_response->getOutput());
        $this->assertRegExp("/Meta/", $multisafepay_response->getOutput());
        $this->assertRegExp("/EINVOICE/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodGezondheidsbon() {
        $this->buildSessionPaymentData('multisafepay/gezondheidsbon', 'Gezondheidsbon');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/GEZONDHEID/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodGiroPay() {
        $this->buildSessionPaymentData('multisafepay/giroPay', 'GiroPay');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/GIROPAY/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodIn3() {
        $this->buildSessionPaymentData('multisafepay/in3', 'in3');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/direct/", $multisafepay_response->getOutput());
        $this->assertRegExp("/IN3/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodIdealQr() {
        $this->buildSessionPaymentData('multisafepay/idealQr', 'iDEAL QR');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/gateway_info/", $multisafepay_response->getOutput());
        $this->assertRegExp("/QrCode/", $multisafepay_response->getOutput());
        $this->assertRegExp("/IDEALQR/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodKbc() {
        $this->buildSessionPaymentData('multisafepay/kbc', 'KBC');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/direct/", $multisafepay_response->getOutput());
        $this->assertRegExp("/KBC/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodKlarna() {
        $this->buildSessionPaymentData('multisafepay/klarna', 'Klarna');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/KLARNA/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodMaestro() {
        $this->buildSessionPaymentData('multisafepay/maestro', 'Maestro');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/MAESTRO/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodMastercard() {
        $this->buildSessionPaymentData('multisafepay/mastercard', 'Mastercard');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/MASTERCARD/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodParfumCadeaukaart() {
        $this->buildSessionPaymentData('multisafepay/parfumCadeaukaart', 'ParfumCadeaukaart');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/PARFUMCADE/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodPayAfterDelivery() {
        $this->buildSessionPaymentData('multisafepay/payAfterDelivery', 'Pay After Delivery');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/direct/", $multisafepay_response->getOutput());
        $this->assertRegExp("/gateway_info/", $multisafepay_response->getOutput());
        $this->assertRegExp("/Meta/", $multisafepay_response->getOutput());
        $this->assertRegExp("/birthday/", $multisafepay_response->getOutput());
        $this->assertRegExp("/bankaccount/", $multisafepay_response->getOutput());
        $this->assertRegExp("/PAYAFTER/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodPaypal() {
        $this->buildSessionPaymentData('multisafepay/paypal', 'Paypal');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/direct/", $multisafepay_response->getOutput());
        $this->assertRegExp("/PAYPAL/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodPaysafeCard() {
        $this->buildSessionPaymentData('multisafepay/paysafecard', 'PaySafe Card');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/PSAFECARD/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodSepaDirDeb() {
        $this->buildSessionPaymentData('multisafepay/dirDeb', 'SEPA Direct Debit');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/direct/", $multisafepay_response->getOutput());
        $this->assertRegExp("/gateway_info/", $multisafepay_response->getOutput());
        $this->assertRegExp("/Account/", $multisafepay_response->getOutput());
        $this->assertRegExp("/account_holder_name/", $multisafepay_response->getOutput());
        $this->assertRegExp("/account_holder_iban/", $multisafepay_response->getOutput());
        $this->assertRegExp("/emandate/", $multisafepay_response->getOutput());
        $this->assertRegExp("/DIRDEB/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodTrustly() {
        $this->buildSessionPaymentData('multisafepay/trustly', 'Trustly');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/TRUSTLY/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodVisa() {
        $this->buildSessionPaymentData('multisafepay/visa', 'Visa');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/VISA/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodVvvGiftCard() {
        $this->buildSessionPaymentData('multisafepay/vvvGiftCard', 'vvvGiftCard');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/VVVGIFTCRD/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodWebshopGiftCard() {
        $this->buildSessionPaymentData('multisafepay/webshopGiftCard', 'Web Shop GiftCard');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/WEBSHOPGIFTCARD/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodBabycad() {
        $this->buildSessionPaymentData('multisafepay/babycad', 'Baby Cadeaubon');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/BABYCAD/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodBeautywellness() {
        $this->buildSessionPaymentData('multisafepay/beautywellness', 'Beauty & Wellness');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/BEAUTYWELL/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodFashionCheque() {
        $this->buildSessionPaymentData('multisafepay/fashionCheque', 'Fashioncheque');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/FASHIONCHQ/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodFashionGiftCard() {
        $this->buildSessionPaymentData('multisafepay/fashionGiftCard', 'Fashiongiftcard');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/FASHIONGFT/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodFietsenbon() {
        $this->buildSessionPaymentData('multisafepay/fietsenbon', 'Fietsenbon');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/FIETSENBON/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodGivaCard() {
        $this->buildSessionPaymentData('multisafepay/givaCard', 'GivaCard');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/GIVACARD/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodGoodCard() {
        $this->buildSessionPaymentData('multisafepay/goodCard', 'Good Card');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/GOODCARD/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodNationaleTuinbon() {
        $this->buildSessionPaymentData('multisafepay/nationaleTuinbon', 'Nationale Tuinbon');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/NATNLETUIN/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodPodium() {
        $this->buildSessionPaymentData('multisafepay/podium', 'Podium');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/PODIUM/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodSportFit() {
        $this->buildSessionPaymentData('multisafepay/sportFit', 'Sport & Fit');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/SPORTENFIT/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodWellnessGiftCard() {
        $this->buildSessionPaymentData('multisafepay/wellnessGiftCard', 'Wellness gift card ');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/WELLNESSGIFTCARD/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodWijnCadeau() {
        $this->buildSessionPaymentData('multisafepay/wijnCadeau', 'Wijncadeau');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/WIJNCADEAU/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodWinkelCheque() {
        $this->buildSessionPaymentData('multisafepay/winkelCheque', 'Winkelcheque');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/WINKELCHEQUE/", $multisafepay_response->getOutput());
    }

    public function testFormPaymentMethodYourGift() {
        $this->buildSessionPaymentData('multisafepay/yourGift', 'YourGift');
        $this->cart->add(28, 1);
        $multisafepay_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $multisafepay_response->getOutput());
        $this->assertRegExp("/redirect/", $multisafepay_response->getOutput());
        $this->assertRegExp("/YOURGIFT/", $multisafepay_response->getOutput());
    }

    public function tearDown() {
        $products_cart = $this->cart->getProducts();
        if(!empty($products_cart)) {
            $this->cart->clear();
        }
    }

    public function testGetMethod() {
        $customer_information = $this->getCustomerAccountInformation();
        $this->load->model($this->multisafepay_version_control->getExtensionRoute());
        $model_call = $this->multisafepay_version_control->getStandartModelCall();
        $response = $this->{$model_call}->getMethod($customer_information['address'][0], 500);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('code', $response);
        $this->assertArrayHasKey('title', $response);
        $this->assertArrayHasKey('terms', $response);
        $this->assertArrayHasKey('sort_order', $response);
    }

    public function testGetMethods() {
        $customer_information = $this->getCustomerAccountInformation();
        $this->load->model($this->multisafepay_version_control->getExtensionRoute());
        $model_call = $this->multisafepay_version_control->getStandartModelCall();
        $response = $this->{$model_call}->getMethods($customer_information['address'][0], 500);
        $this->assertIsArray($response);
        foreach ($response as $key => $value) {
            $this->assertIsArray($value);
            $this->assertArrayHasKey('code', $value);
            $this->assertArrayHasKey('title', $value);
            $this->assertArrayHasKey('terms', $value);
            $this->assertArrayHasKey('sort_order', $value);
        }
    }
}
