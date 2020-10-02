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

class CatalogControllerExtensionPaymentMultiSafePayTest extends MultisafepayTestSuiteForOpenCart {

    public function testFormPaymentMethodMultiSafepay() {
        $this->buildSessionPaymentData('multisafepay', 'MultiSafepay');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodAfterpay() {
        $this->buildSessionPaymentData('multisafepay/afterPay', 'Afterpay');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/AFTERPAY/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodAmex() {
        $this->buildSessionPaymentData('multisafepay/amex', 'American Express');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/AMEX/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodAliPay() {
        $this->buildSessionPaymentData('multisafepay/aliPay', 'Ali Pay');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/ALIPAY/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodApplePay() {
        $this->buildSessionPaymentData('multisafepay/applePay', 'Apple Pay');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/APPLEPAY/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodBancontact() {
        $this->buildSessionPaymentData('multisafepay/bancontact', 'Bancontact');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/MISTERCASH/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodBankTransfer() {
        $this->buildSessionPaymentData('multisafepay/bankTransfer', 'Bank transfer');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/BANKTRANS/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodBelfius() {
        $this->buildSessionPaymentData('multisafepay/belfius', 'Belfius');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/BELFIUS/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodBoekenbon() {
        $this->buildSessionPaymentData('multisafepay/boekenbon', 'Boekenbon');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/BOEKENBON/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodCbc() {
        $this->buildSessionPaymentData('multisafepay/cbc', 'CBC');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/CBC/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodCreditCard() {
        $this->buildSessionPaymentData('multisafepay/creditCard', 'Credit Card');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/CREDITCARD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodDbrtp() {
        $this->buildSessionPaymentData('multisafepay/dbrtp', 'Request to Pay');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/DBRTP/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodDirectBank() {
        $this->buildSessionPaymentData('multisafepay/directBank', 'Direct Bank');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/DIRECTBANK/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodDotPay() {
        $this->buildSessionPaymentData('multisafepay/dotpay', 'DotPay');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/gateway_info/", $msp_response->getOutput());
        $this->assertRegExp("/Meta/", $msp_response->getOutput());
        $this->assertRegExp("/DOTPAY/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodEps() {
        $this->buildSessionPaymentData('multisafepay/eps', 'EPS');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/EPS/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodEinvoicing() {
        $this->buildSessionPaymentData('multisafepay/eInvoice', 'E-Invoicing');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/gateway_info/", $msp_response->getOutput());
        $this->assertRegExp("/Meta/", $msp_response->getOutput());
        $this->assertRegExp("/EINVOICE/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodGezondheidsbon() {
        $this->buildSessionPaymentData('multisafepay/gezondheidsbon', 'Gezondheidsbon');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/GEZONDHEID/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodGiroPay() {
        $this->buildSessionPaymentData('multisafepay/giroPay', 'GiroPay');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/GIROPAY/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodIn3() {
        $this->buildSessionPaymentData('multisafepay/in3', 'in3');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/IN3/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodIdealQr() {
        $this->buildSessionPaymentData('multisafepay/idealQr', 'iDEAL QR');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/gateway_info/", $msp_response->getOutput());
        $this->assertRegExp("/QrCode/", $msp_response->getOutput());
        $this->assertRegExp("/IDEALQR/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodIng() {
        $this->buildSessionPaymentData('multisafepay/ing', 'iDEAL');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/INGHOME/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodKbc() {
        $this->buildSessionPaymentData('multisafepay/kbc', 'KBC');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/KBC/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodKlarna() {
        $this->buildSessionPaymentData('multisafepay/klarna', 'Klarna');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/KLARNA/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodMaestro() {
        $this->buildSessionPaymentData('multisafepay/maestro', 'Maestro');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/MAESTRO/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodMastercard() {
        $this->buildSessionPaymentData('multisafepay/mastercard', 'Mastercard');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/MASTERCARD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodParfumCadeaukaart() {
        $this->buildSessionPaymentData('multisafepay/parfumCadeaukaart', 'ParfumCadeaukaart');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/PARFUMCADE/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodPayAfterDelivery() {
        $this->buildSessionPaymentData('multisafepay/payAfterDelivery', 'Pay After Delivery');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/gateway_info/", $msp_response->getOutput());
        $this->assertRegExp("/Meta/", $msp_response->getOutput());
        $this->assertRegExp("/birthday/", $msp_response->getOutput());
        $this->assertRegExp("/bankaccount/", $msp_response->getOutput());
        $this->assertRegExp("/PAYAFTER/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodPaypal() {
        $this->buildSessionPaymentData('multisafepay/paypal', 'Paypal');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/PAYPAL/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodPaysafeCard() {
        $this->buildSessionPaymentData('multisafepay/paysafecard', 'PaySafe Card');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/PSAFECARD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodBetaalplan() {
        $this->buildSessionPaymentData('multisafepay/betaalplan', 'Santander Betaalplan');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/gateway_info/", $msp_response->getOutput());
        $this->assertRegExp("/Meta/", $msp_response->getOutput());
        $this->assertRegExp("/birthday/", $msp_response->getOutput());
        $this->assertRegExp("/gender/", $msp_response->getOutput());
        $this->assertRegExp("/bankaccount/", $msp_response->getOutput());
        $this->assertRegExp("/SANTANDER/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodSepaDirDeb() {
        $this->buildSessionPaymentData('multisafepay/dirDeb', 'SEPA Direct Debit');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/gateway_info/", $msp_response->getOutput());
        $this->assertRegExp("/Account/", $msp_response->getOutput());
        $this->assertRegExp("/account_holder_name/", $msp_response->getOutput());
        $this->assertRegExp("/account_holder_iban/", $msp_response->getOutput());
        $this->assertRegExp("/emandate/", $msp_response->getOutput());
        $this->assertRegExp("/DIRDEB/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodTrustly() {
        $this->buildSessionPaymentData('multisafepay/trustly', 'Trustly');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/TRUSTLY/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodVisa() {
        $this->buildSessionPaymentData('multisafepay/visa', 'Visa');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/VISA/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodVvvGiftCard() {
        $this->buildSessionPaymentData('multisafepay/vvvGiftCard', 'vvvGiftCard');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/VVVGIFTCRD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodWebshopGiftCard() {
        $this->buildSessionPaymentData('multisafepay/webshopGiftCard', 'Web Shop GiftCard');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/WEBSHOPGIFTCARD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodBabycad() {
        $this->buildSessionPaymentData('multisafepay/babycad', 'Baby Cadeaubon');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/BABYCAD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodBeautywellness() {
        $this->buildSessionPaymentData('multisafepay/beautywellness', 'Beauty & Wellness');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/BEAUTYWELL/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodFashionCheque() {
        $this->buildSessionPaymentData('multisafepay/fashionCheque', 'Fashioncheque');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/FASHIONCHQ/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodFashionGiftCard() {
        $this->buildSessionPaymentData('multisafepay/fashionGiftCard', 'Fashiongiftcard');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/FASHIONGFT/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodFietsenbon() {
        $this->buildSessionPaymentData('multisafepay/fietsenbon', 'Fietsenbon');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/FIETSENBON/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodGivaCard() {
        $this->buildSessionPaymentData('multisafepay/givaCard', 'GivaCard');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/GIVACARD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodGoodCard() {
        $this->buildSessionPaymentData('multisafepay/goodCard', 'Good Card');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/GOODCARD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodNationaleTuinbon() {
        $this->buildSessionPaymentData('multisafepay/nationaleTuinbon', 'Nationale Tuinbon');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/NATNLETUIN/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodPodium() {
        $this->buildSessionPaymentData('multisafepay/podium', 'Podium');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/PODIUM/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodSportFit() {
        $this->buildSessionPaymentData('multisafepay/sportFit', 'Sport & Fit');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/SPORTENFIT/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodWellnessGiftCard() {
        $this->buildSessionPaymentData('multisafepay/wellnessGiftCard', 'Wellness gift card ');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/WELLNESSGIFTCARD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodWijnCadeau() {
        $this->buildSessionPaymentData('multisafepay/wijnCadeau', 'Wijncadeau');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/WIJNCADEAU/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodWinkelCheque() {
        $this->buildSessionPaymentData('multisafepay/winkelCheque', 'Winkelcheque');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/WINKELCHEQUE/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodYourGift() {
        $this->buildSessionPaymentData('multisafepay/yourGift', 'YourGift');
        $this->cart->add(28, 1);
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/YOURGIFT/", $msp_response->getOutput());
    }

    public function tearDown() {
        $products_cart = $this->cart->getProducts();
        if(!empty($products_cart)) {
            $this->cart->clear();
        }
    }

    public function testGetMethod() {
        $address = $this->getCustomerPaymentInformation();
        $this->load->model($this->multisafepay_version_control->getExtensionRoute());
        $model_call = $this->multisafepay_version_control->getStandartModelCall();
        $response = $this->{$model_call}->getMethod(500, $address);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('code', $response);
        $this->assertArrayHasKey('title', $response);
        $this->assertArrayHasKey('terms', $response);
        $this->assertArrayHasKey('sort_order', $response);
    }

    public function testGetMethods() {
        $address = $this->getCustomerPaymentInformation();
        $this->load->model($this->multisafepay_version_control->getExtensionRoute());
        $model_call = $this->multisafepay_version_control->getStandartModelCall();
        $response = $this->{$model_call}->getMethods(500, $address);
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