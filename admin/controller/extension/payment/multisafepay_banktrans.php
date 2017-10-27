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
 
ini_set('display_errors', '1');

class ControllerExtensionPaymentMultiSafePaybanktrans extends Controller
{

    private $error = array();

    public function index()
    {
        $this->load->language('extension/payment/multisafepay');
        $this->load->language('extension/payment/multisafepay_banktrans');
        $this->load->model('setting/setting');
   		$this->load->model("setting/store");
		$this->load->model("localisation/geo_zone");

        $this->document->setTitle($this->language->get('heading_title'));
        
		$stores = $this->getStores();
       
		foreach($stores as $store)
		{
			if (($this->request->server['REQUEST_METHOD'] == "POST") && ($this->validate($store['id'])))
			{
				$post = $this->request->post['stores'][$store['id']];
//				echo '<pre>'; print_r ($post, false); die();
				
				$this->model_setting_setting->editSetting('payment_multisafepay_banktrans', $post, $store['id']);
			}
		}

        $this->session->data['success'] = $this->language->get('text_success');
        
        
        $data['stores'] = $stores;

        $data['text_edit']              = $this->language->get('text_edit');
        $data['text_enabled']           = $this->language->get('text_enabled');
        $data['text_disabled']          = $this->language->get('text_disabled');
        $data['text_all_zones']         = $this->language->get('text_all_zones');
        $data['text_set_order_status']  = $this->language->get('text_set_order_status');
        $data['heading_title']          = $this->language->get('heading_title');
        $data['entry_status']           = $this->language->get('entry_status');
        $data['entry_sort_order']       = $this->language->get('entry_sort_order');

        $data['button_save']            = $this->language->get('button_save');
        $data['button_cancel']          = $this->language->get('button_cancel');
        $data['tab_general']            = $this->language->get('tab_general');

        $data['breadcrumbs']    = array();
        $data['breadcrumbs'][]  = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->setup_link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => false
        );

        $data['text_min_amount'] = $this->language->get('text_min_amount');
        $data['text_max_amount'] = $this->language->get('text_max_amount');



        // Geo Zone
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$settings = array(
			"payment_multisafepay_banktrans_status"       => 1,
			"payment_multisafepay_banktrans_max_amount"   => null,
			"payment_multisafepay_banktrans_min_amount"   => null,
			"payment_multisafepay_banktrans_sort_order"   => 1,
			"payment_multisafepay_banktrans_geo_zone_id"  => 1
		);

		foreach($stores as $store)
		{
			foreach ($settings as $setting_name => $default_value)
			{
				if (isset($this->request->post['stores'][$store['id']][$setting_name])){
					$data['stores'][$store['id']][$setting_name] = $this->request->post['stores'][$store['id']][$setting_name];
				} else {
					$data['stores'][$store['id']][$setting_name] = $this->config->get($setting_name);
				}
			}
        }
		
		
		$data['breadcrumbs'] = array();


		$data['breadcrumbs'][] = array(
			"href"      => $this->url->link('marketplace/extension', "type=payment&" . 'user_token='.$this->session->data['user_token'], "SSL"),
			"text"      => $this->language->get("text_payment"),
			"separator" => ' :: ',
		);

		$data['breadcrumbs'][] = array(
			"href"      => $this->url->link("extension/payment/multisafepay_banktrans", 'user_token='.$this->session->data['user_token'], "SSL"),
			"text"      => $this->language->get("heading_title"),
			"separator" => " :: ",
		);

		$data['action'] = $this->url->link('extension/payment/multisafepay_banktrans', "user_token=".$this->session->data['user_token'], "SSL");
		$data['cancel'] = $this->url->link('marketplace/extension', "type=payment&" . 'user_token='.$this->session->data['user_token'], "SSL");

        $this->template = 'extension/payment/multisafepay_banktrans';
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this->template, $data));

    }

    private function setup_link($route)
    {
        return $link = $this->url->link($route, 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL');
    }



	private function validate ($store = 0)
	{
		if (!$this->user->hasPermission("modify", "extension/payment/multisafepay_banktrans"))
		{
			$this->error['warning'] = $this->language->get("error_permission");
		}

		return (count($this->error) == 0);
	}
    
    

	protected function getStores()
	{
		$sql = $this->db->query(sprintf("SELECT store_id as id, name FROM %sstore", DB_PREFIX));
		$rows = $sql->rows;
		$default = array(
			array(
				'id' => 0,
				'name' => $this->config->get('config_name')
			)
		);
		$allStores = array_merge($default, $rows);

		return $allStores;
	}

}

?>