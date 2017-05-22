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
class ModelExtensionTotalMultisafepayPayafterFee extends Model
{

    public function getTotal($total)
    {
        $this->load->language('extension/payment/multisafepay');


        $fee = $this->config->get('multisafepaypayafterfee');
        $iso = 'NLD';


        if (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == 'multisafepay_payafter') {
            $total['totals'][] = array(
                'code' => 'multisafepaypayafterfee',
                'title' => $this->language->get('entry_paymentfee'),
                'value' => $fee[$iso]['fee'],
                'sort_order' => $fee[$iso]['sort_order']
            );




            $tax_rates = $this->tax->getRates($fee[$iso]['fee'], $fee[$iso]['tax_class_id']);

            /* foreach ($tax_rates as $tax_rate) {
              if (!isset($taxes[$tax_rate['tax_rate_id']])) {
              $taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
              } else {
              $taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
              }
              } */
            foreach ($tax_rates as $tax_rate) {
                if (!isset($total['taxes'][$tax_rate['tax_rate_id']])) {
                    $total['taxes'][$tax_rate['tax_rate_id']] = $tax_rate['amount'];
                } else {
                    $total['taxes'][$tax_rate['tax_rate_id']] += $tax_rate['amount'];
                }
            }

            $total['total'] += $fee[$iso]['fee'];
        }
    }

}

?>