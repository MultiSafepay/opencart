<?php

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