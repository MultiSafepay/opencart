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

class ControllerExtensionPaymentMultiSafePayTest extends OpenCartMultiSafepayTest {

    public function setUp() {
        parent::setUp();
        $products_cart = $this->cart->getProducts();
        if(empty($products_cart)) {
            $this->cart->add(28, 1);
        }
    }

    public function testFormPaymentMethodMultiSafepay() {
        $this->buildSessionPaymentData('multisafepay', 'MultiSafepay');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodAfterpay() {
        $this->buildSessionPaymentData('multisafepay/afterPay', 'Afterpay');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/AFTERPAY/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodAmex() {
        $this->buildSessionPaymentData('multisafepay/amex', 'American Express');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/AMEX/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodAliPay() {
        $this->buildSessionPaymentData('multisafepay/aliPay', 'Ali Pay');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/ALIPAY/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodApplePay() {
        $this->buildSessionPaymentData('multisafepay/applePay', 'Apple Pay');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/APPLEPAY/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodBancontact() {
        $this->buildSessionPaymentData('multisafepay/bancontact', 'Bancontact');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/MISTERCASH/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodBankTransfer() {
        $this->buildSessionPaymentData('multisafepay/bankTransfer', 'Bank transfer');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/BANKTRANS/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodBelfius() {
        $this->buildSessionPaymentData('multisafepay/belfius', 'Belfius');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/BELFIUS/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodBoekenbon() {
        $this->buildSessionPaymentData('multisafepay/boekenbon', 'Boekenbon');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/BOEKENBON/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodCbc() {
        $this->buildSessionPaymentData('multisafepay/cbc', 'CBC');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/CBC/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodCreditCard() {
        $this->buildSessionPaymentData('multisafepay/creditCard', 'Credit Card');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/CREDITCARD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodDbrtp() {
        $this->buildSessionPaymentData('multisafepay/dbrtp', 'Request to Pay');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/DBRTP/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodDirectBank() {
        $this->buildSessionPaymentData('multisafepay/directBank', 'Direct Bank');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/DIRECTBANK/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodDotPay() {
        $this->buildSessionPaymentData('multisafepay/dotpay', 'DotPay');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/gateway_info/", $msp_response->getOutput());
        $this->assertRegExp("/Meta/", $msp_response->getOutput());
        $this->assertRegExp("/DOTPAY/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodEps() {
        $this->buildSessionPaymentData('multisafepay/eps', 'EPS');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/EPS/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodEinvoicing() {
        $this->buildSessionPaymentData('multisafepay/eInvoice', 'E-Invoicing');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/gateway_info/", $msp_response->getOutput());
        $this->assertRegExp("/Meta/", $msp_response->getOutput());
        $this->assertRegExp("/EINVOICE/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodGezondheidsbon() {
        $this->buildSessionPaymentData('multisafepay/gezondheidsbon', 'Gezondheidsbon');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/GEZONDHEID/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodGiroPay() {
        $this->buildSessionPaymentData('multisafepay/giroPay', 'GiroPay');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/GIROPAY/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodIn3() {
        $this->buildSessionPaymentData('multisafepay/in3', 'in3');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/IN3/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodIdealQr() {
        $this->buildSessionPaymentData('multisafepay/idealQr', 'iDEAL QR');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/gateway_info/", $msp_response->getOutput());
        $this->assertRegExp("/QrCode/", $msp_response->getOutput());
        $this->assertRegExp("/IDEALQR/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodIng() {
        $this->buildSessionPaymentData('multisafepay/ing', 'iDEAL');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/INGHOME/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodKbc() {
        $this->buildSessionPaymentData('multisafepay/kbc', 'KBC');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/KBC/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodKlarna() {
        $this->buildSessionPaymentData('multisafepay/klarna', 'Klarna');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/KLARNA/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodMaestro() {
        $this->buildSessionPaymentData('multisafepay/maestro', 'Maestro');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/MAESTRO/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodMastercard() {
        $this->buildSessionPaymentData('multisafepay/mastercard', 'Mastercard');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/MASTERCARD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodParfumCadeaukaart() {
        $this->buildSessionPaymentData('multisafepay/parfumCadeaukaart', 'ParfumCadeaukaart');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/PARFUMCADE/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodPayAfterDelivery() {
        $this->buildSessionPaymentData('multisafepay/payAfterDelivery', 'Pay After Delivery');
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
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/direct/", $msp_response->getOutput());
        $this->assertRegExp("/PAYPAL/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodPaysafeCard() {
        $this->buildSessionPaymentData('multisafepay/paysafecard', 'PaySafe Card');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/PSAFECARD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodBetaalplan() {
        $this->buildSessionPaymentData('multisafepay/betaalplan', 'Santander Betaalplan');
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
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/TRUSTLY/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodVisa() {
        $this->buildSessionPaymentData('multisafepay/visa', 'Visa');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/VISA/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodVvvGiftCard() {
        $this->buildSessionPaymentData('multisafepay/vvvGiftCard', 'vvvGiftCard');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/VVVGIFTCRD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodWebshopGiftCard() {
        $this->buildSessionPaymentData('multisafepay/webshopGiftCard', 'Web Shop GiftCard');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/WEBSHOPGIFTCARD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodBabycad() {
        $this->buildSessionPaymentData('multisafepay/babycad', 'Baby Cadeaubon');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/BABYCAD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodBeautywellness() {
        $this->buildSessionPaymentData('multisafepay/beautywellness', 'Beauty & Wellness');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/BEAUTYWELL/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodFashionCheque() {
        $this->buildSessionPaymentData('multisafepay/fashionCheque', 'Fashioncheque');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/FASHIONCHQ/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodFashionGiftCard() {
        $this->buildSessionPaymentData('multisafepay/fashionGiftCard', 'Fashiongiftcard');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/FASHIONGFT/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodFietsenbon() {
        $this->buildSessionPaymentData('multisafepay/fietsenbon', 'Fietsenbon');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/FIETSENBON/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodGivaCard() {
        $this->buildSessionPaymentData('multisafepay/givaCard', 'GivaCard');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/GIVACARD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodGoodCard() {
        $this->buildSessionPaymentData('multisafepay/goodCard', 'Good Card');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/GOODCARD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodNationaleTuinbon() {
        $this->buildSessionPaymentData('multisafepay/nationaleTuinbon', 'Nationale Tuinbon');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/NATNLETUIN/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodPodium() {
        $this->buildSessionPaymentData('multisafepay/podium', 'Podium');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/PODIUM/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodSportFit() {
        $this->buildSessionPaymentData('multisafepay/sportFit', 'Sport & Fit');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/SPORTENFIT/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodWellnessGiftCard() {
        $this->buildSessionPaymentData('multisafepay/wellnessGiftCard', 'Wellness gift card ');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/WELLNESSGIFTCARD/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodWijnCadeau() {
        $this->buildSessionPaymentData('multisafepay/wijnCadeau', 'Wijncadeau');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/WIJNCADEAU/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodWinkelCheque() {
        $this->buildSessionPaymentData('multisafepay/winkelCheque', 'Winkelcheque');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/WINKELCHEQUE/", $msp_response->getOutput());
    }

    public function testFormPaymentMethodYourGift() {
        $this->buildSessionPaymentData('multisafepay/yourGift', 'YourGift');
        $msp_response = $this->dispatchAction('checkout/confirm');
        $this->assertRegExp("/order_id/", $msp_response->getOutput());
        $this->assertRegExp("/redirect/", $msp_response->getOutput());
        $this->assertRegExp("/YOURGIFT/", $msp_response->getOutput());
    }
}