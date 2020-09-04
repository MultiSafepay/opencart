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

class ModelExtensionPaymentMultiSafePayTest extends OpenCartMultiSafepayTest {

    public function testGetMethod() {
        $address = $this->getCustomerPaymentInformation();
        $this->load->model('extension/payment/multisafepay');
        $response = $this->model_extension_payment_multisafepay->getMethod(500, $address);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('code', $response);
        $this->assertArrayHasKey('title', $response);
        $this->assertArrayHasKey('terms', $response);
        $this->assertArrayHasKey('sort_order', $response);
    }


    public function testGetMethods() {
        $address = $this->getCustomerPaymentInformation();
        $this->load->model('extension/payment/multisafepay');
        $response = $this->model_extension_payment_multisafepay->getMethods(500, $address);
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