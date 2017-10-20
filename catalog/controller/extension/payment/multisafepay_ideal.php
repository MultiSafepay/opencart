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
class ControllerExtensionPaymentMultiSafePayIdeal extends Controller
{

    public function index()
    {
        $storeid = $this->config->get('config_store_id');
        $this->load->language('extension/payment/multisafepay');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['button_back'] = $this->language->get('button_back');
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        //$this->load->library('encryption');
        $data['action'] = $this->url->link('extension/payment/multisafepay/multisafepayProcess', '', 'SSL');
        $data['back'] = $this->url->link('checkout/checkout', '', 'SSL');
        $data['gateway'] = 'IDEAL';
        $data['MSP_CARTID'] = $this->session->data['order_id'];



        $data['order_id'] = $this->session->data['order_id'];



        //GET IDEAL ISSUERS
        require_once(dirname(__FILE__) . '/MultiSafepay.combined.php');
        $msp = new MultiSafepay();
        $msp->test = $this->config->get('payment_multisafepay_environment_' . $storeid);
        $msp->merchant['account_id'] = $this->config->get('payment_multisafepay_merchant_id_' . $storeid);
        $msp->merchant['site_id'] = $this->config->get('payment_multisafepay_site_id_' . $storeid);
        $msp->merchant['site_code'] = $this->config->get('payment_multisafepay_secure_code_' . $storeid);
        $iDealIssuers = $msp->getIdealIssuers();


        $idealselect = '<div id="issuerselect">'; // . $this->language->get('text_select_bank');
        $idealselect .= '<select name="issuer">';
        $idealselect .= '<option value="">' . $this->language->get('text_select_bank') . '</option>';
        if ($this->config->get('payment_multisafepay_environment_' . $storeid)) {
            foreach ($iDealIssuers['issuers'] as $issuer) {
                $idealselect .= '<option value="' . $issuer['code']['VALUE'] . '">' . $issuer['description']['VALUE'] . '</option>';
            }
        } else {
            foreach ($iDealIssuers['issuers']['issuer'] as $issuer) {
                $idealselect .= '<option value="' . $issuer['code']['VALUE'] . '">' . $issuer['description']['VALUE'] . '</option>';
            }
        }
        $idealselect .= '</select></div>';
        //END ideal issuers request data
        $data['ISSUER_SELECT'] = $idealselect;



        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/multisafepay_ideal')) {
            return $this->load->view($this->config->get('config_template') . '/template/extension/payment/multisafepay_ideal', $data);
        } elseif (file_exists(DIR_TEMPLATE . 'default/template/extension/payment/multisafepay_ideal') && VERSION < '2.2.0.0') {
            return $this->load->view('default/template/extension/payment/multisafepay_ideal', $data);
        } else {
            return $this->load->view('extension/payment/multisafepay_ideal', $data);
        }
    }

}

?>