<?php
class ControllerExtensionShippingEcont extends Controller {
	private $error = array();
	private $delivery_type = 'to_office';

	public function index() {
		$this->load->language('extension/shipping/econt');
		$this->load->language('checkout/checkout');

		$this->load->model('extension/shipping/econt');

		$results = array();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if ($this->customer->isLogged()) {
				$this->model_extension_shipping_econt->addCustomer($this->request->post);
			}

			if (isset($this->session->data['econt']) && $this->session->data['econt']) {
				unset($this->session->data['econt']['priority_time_cb']);
				$this->session->data['econt'] = array_merge($this->session->data['econt'], $this->request->post);
			} else {
				$this->session->data['econt'] = $this->request->post;
			}

			$this->session->data['econt']['shipping_econt_validate'] = TRUE;

			$results['submit'] = true;
			$this->response->setOutput(json_encode($results));
		}

		if ((!$this->cart->hasProducts() && (!isset($this->session->data['vouchers']) || !$this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$results['redirect'] = $this->url->link('checkout/cart');

			$this->response->setOutput(json_encode($results));
		}

		if (!$this->customer->isLogged() && !isset($this->session->data['guest'])) {
			$results['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');

			$this->response->setOutput(json_encode($results));
		}

		if ($this->cart->hasShipping()) {
			$data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 5);
			$data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 6);
		} else {
			$data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 3);
			$data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 4);
		}

		if (isset($this->error['address'])) {
			$data['error_address'] = $this->error['address'];
		} else {
			$data['error_address'] = '';
		}

		if (isset($this->error['office'])) {
			$data['error_office'] = $this->error['office'];
		} else {
			$data['error_office'] = '';
		}

		if (isset($this->error['office_aps'])) {
			$data['error_office_aps'] = $this->error['office_aps'];
		} else {
			$data['error_office_aps'] = '';
		}

		if (isset($this->error['cd_payment_aps'])) {
			$data['error_cd_payment_aps'] = $this->error['cd_payment_aps'];
		} else {
			$data['error_cd_payment_aps'] = '';
		}

		if (isset($this->error['priority_time'])) {
			$data['error_priority_time'] = $this->error['priority_time'];
		} else {
			$data['error_priority_time'] = '';
		}

		$data['action'] = $this->url->link('extension/shipping/econt', '', 'SSL');

		$data['office_locator'] = 'https://www.bgmaps.com/templates/econt?office_type=to_office_courier&shop_url=' . HTTPS_SERVER;
		$data['office_locator_domain'] = 'https://www.bgmaps.com';

		if ($this->customer->isLogged()) {
			$shipping_address = $this->model_extension_shipping_econt->getCustomer($this->customer->getId());

			if (!$shipping_address) {
				$this->load->model('account/address');

				$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address']['address_id']);
			}
		} elseif (isset($this->session->data['econt'])) {
			$shipping_address = $this->session->data['econt'];
		}

		if (isset($this->request->post['shipping_to'])) {
			$data['shipping_to'] = $this->request->post['shipping_to'];
		} elseif (isset($shipping_address['shipping_to'])) {
			$data['shipping_to'] = $shipping_address['shipping_to'];
		} else {
			$data['shipping_to'] = 'OFFICE';
		}

		if (isset($this->request->post['postcode'])) {
			$data['postcode'] = $this->request->post['postcode'];
		} elseif (isset($shipping_address['postcode'])) {
			$data['postcode'] = $shipping_address['postcode'];
		} else {
			$data['postcode'] = $this->session->data['shipping_address']['postcode'];
		}

		if (isset($this->request->post['city_id'])) {
			$data['city_id'] = $this->request->post['city_id'];
		} elseif (isset($shipping_address['city_id'])) {
			$data['city_id'] = $shipping_address['city_id'];
		} else {
			$data['city_id'] = $this->model_extension_shipping_econt->getCityIdByPostCode($data['postcode']);
		}

		if (isset($this->request->post['city'])) {
			$data['city'] = $this->request->post['city'];
		} elseif ($data['city_id']) {
			$city = $this->model_extension_shipping_econt->getCityByCityId($data['city_id']);
			$data['city'] = $city['name'];
		} else {
			$data['city'] = $this->session->data['shipping_address']['city'];
		}

		if (isset($this->request->post['quarter'])) {
			$data['quarter'] = $this->request->post['quarter'];
		} elseif (isset($shipping_address['quarter'])) {
			$data['quarter'] = $shipping_address['quarter'];
		} else {
			$data['quarter'] = '';
		}

		if (isset($this->request->post['street'])) {
			$data['street'] = $this->request->post['street'];
		} elseif (isset($shipping_address['street'])) {
			$data['street'] = $shipping_address['street'];
		} else {
			$data['street'] = '';
		}

		if (isset($this->request->post['street_num'])) {
			$data['street_num'] = $this->request->post['street_num'];
		} elseif (isset($shipping_address['street_num'])) {
			$data['street_num'] = $shipping_address['street_num'];
		} else {
			$data['street_num'] = '';
		}

		if (isset($this->request->post['other'])) {
			$data['other'] = $this->request->post['other'];
		} elseif (isset($shipping_address['other'])) {
			$data['other'] = $shipping_address['other'];
		} else {
			$data['other'] = '';
		}

		if (isset($this->request->post['office_city_id'])) {
			$data['office_city_id'] = $this->request->post['office_city_id'];
		} elseif (isset($this->session->data['econt']['office_city_id'])) {
			$data['office_city_id'] = $this->session->data['econt']['office_city_id'];
		} else {
			$data['office_city_id'] = 0;
		}

		if (isset($this->request->post['office_id'])) {
			$data['office_id'] = $this->request->post['office_id'];
		} elseif (isset($shipping_address['office_id'])) {
			$data['office_id'] = $shipping_address['office_id'];
		} else {
			$data['office_id'] = 0;
		}

		if (isset($this->request->post['office_aps_id'])) {
			$data['office_aps_id'] = $this->request->post['office_aps_id'];
		} elseif (isset($shipping_address['office_aps_id'])) {
			$data['office_aps_id'] = $shipping_address['office_aps_id'];
		} else {
			$data['office_aps_id'] = 0;
		}

		if (isset($this->request->post['office_code'])) {
			$data['office_code'] = $this->request->post['office_code'];
		} elseif (isset($this->session->data['econt']['office_code'])) {
			$data['office_code'] = $this->session->data['econt']['office_code'];
		} else {
			$data['office_code'] = '';
		}


		if (isset($this->request->post['office_aps_code'])) {
			$data['office_aps_code'] = $this->request->post['office_aps_code'];
		} elseif (isset($this->session->data['econt']['office_aps_code'])) {
			$data['office_aps_code'] = $this->session->data['econt']['office_aps_code'];
		} else {
			$data['office_aps_code'] = '';
		}

		if (isset($this->request->post['priority_time_cb'])) {
			$data['priority_time_cb'] = $this->request->post['priority_time_cb'];
		} elseif (isset($this->session->data['econt']['priority_time_cb'])) {
			$data['priority_time_cb'] = $this->session->data['econt']['priority_time_cb'];
		} else {
			$data['priority_time_cb'] = false;
		}

		if (isset($this->request->post['priority_time_type_id'])) {
			$data['priority_time_type_id'] = $this->request->post['priority_time_type_id'];
		} elseif (isset($this->session->data['econt']['priority_time_type_id'])) {
			$data['priority_time_type_id'] = $this->session->data['econt']['priority_time_type_id'];
		} else {
			$data['priority_time_type_id'] = 'BEFORE';
		}

		if (isset($this->request->post['priority_time_hour_id'])) {
			$data['priority_time_hour_id'] = $this->request->post['priority_time_hour_id'];
		} elseif (isset($this->session->data['econt']['priority_time_hour_id'])) {
			$data['priority_time_hour_id'] = $this->session->data['econt']['priority_time_hour_id'];
		} else {
			$data['priority_time_hour_id'] = '';
		}

		if (isset($this->request->post['express_city_courier_cb'])) {
			$data['express_city_courier_cb'] = $this->request->post['express_city_courier_cb'];
		} elseif (isset($this->session->data['econt']['express_city_courier_cb'])) {
			$data['express_city_courier_cb'] = $this->session->data['econt']['express_city_courier_cb'];
		} else {
			$data['express_city_courier_cb'] = false;
		}

		if (isset($this->request->post['express_city_courier_e'])) {
			$data['express_city_courier_e'] = $this->request->post['express_city_courier_e'];
		} elseif (isset($this->session->data['econt']['express_city_courier_e'])) {
			$data['express_city_courier_e'] = $this->session->data['econt']['express_city_courier_e'];
		} else {
			$data['express_city_courier_e'] = 'e1';
		}

		if ($this->config->get('shipping_econt_saturday')) {
			$data['saturday_status'] = true;
		} else {
			$data['saturday_status'] = false;
		}

		if ($this->config->get('shipping_econt_restday') && date('w') == 5) {
			$data['restday_status'] = true;
		} else {
			$data['restday_status'] = false;
		}

		if (isset($this->request->post['restday'])) {
			$data['saturday'] = $this->request->post['restday'];
		} elseif (isset($this->session->data['econt']['restday'])) {
			$data['restday'] = $this->session->data['econt']['restday'];
		} else {
			$data['restday'] = '0';
		}

		$data['to_door'] = true;

		$data['to_office'] = true;

		$data['to_aps'] = true;

		if ($this->config->get('payment_econt_cod_status') && $this->config->get('shipping_econt_cd')) {
			$data['cd'] = true;
		} else {
			$data['cd'] = false;
		}

		if (!$data['cd']) {
			$data['cd_payment'] = FALSE;
		} elseif (isset($this->request->post['cd_payment'])) {
			$data['cd_payment'] = $this->request->post['cd_payment'];
		} elseif (isset($this->session->data['econt']['cd_payment'])) {
			$data['cd_payment'] = $this->session->data['econt']['cd_payment'];
		} else {
			$data['cd_payment'] = TRUE;
		}

		$total = $this->currency->format($this->cart->getTotal(), $this->config->get('shipping_econt_currency'), '', false);

		$hours = array();
		for ($h = 9; $h <= 17; $h++) {
			for ($m = 0; $m <= 45; $m+=15) {
				$hours[] = $h . ':' . str_pad($m, 2, '0', STR_PAD_RIGHT);
			}
		}

		array_shift($hours);
		array_shift($hours);
		array_pop($hours);

		$data['priority_time_types'] = array(
			array('id' => 'BEFORE', 'name' => $this->language->get('text_before'), 'hours' => $hours),
			array('id' => 'IN', 'name' => $this->language->get('text_in'), 'hours' => $hours),
			array('id' => 'AFTER', 'name' => $this->language->get('text_after'), 'hours' => $hours)
		);

		$addresses = $this->config->get('shipping_econt_address_list');
		$address = current($addresses);

		if ((count($addresses) == 1) && ($address['city_post_code'] == $data['postcode'])) {
			$data['express_city_courier'] = true;
		} else {
			$data['express_city_courier'] = false;
		}

		$data['sender_post_code'] = $address['city_post_code'];

		$data['countries'] = $this->model_extension_shipping_econt->getCountries();
		$data['econt_country_id'] = $this->model_extension_shipping_econt->getCountry();
		$data['cities'] = $this->model_extension_shipping_econt->getCitiesWithOffices($this->delivery_type);

		if ((!$data['office_city_id'] || !$data['office_code']) && $data['office_id']) {
			$office = $this->model_extension_shipping_econt->getOffice($data['office_id']);

			if ($office) {
				$data['office_city_id'] = $office['city_id'];
				$data['office_code'] = $office['office_code'];
			}
		}

		if (!$data['office_city_id'] && $data['city'] && $data['city_id']) {
			$data['office_city_id'] = $data['city_id'];
		}

		$data['offices'] = array();

		if ($data['office_city_id']) {
			$data['offices'] = $this->model_extension_shipping_econt->getOfficesByCityId($data['office_city_id'], $this->delivery_type);
		}

		$data['dc_cp'] = ($this->config->get('shipping_econt_dc') == '2');
		$data['dc'] = ($this->config->get('shipping_econt_dc') == '1');

		$data['invoice_before_cd'] = $this->config->get('shipping_econt_invoice_before_cd');

		$data['pay_after_accept'] = (bool) $this->config->get('shipping_econt_pay_after_accept');
		
		if (!$data['pay_after_accept']) {
			$data['pay_after_test'] = false;
		} else {
			$data['pay_after_test'] = (bool) $this->config->get('shipping_econt_pay_after_test');
		}

		if (!$data['pay_after_accept']) {
			$data['pay_choose'] = false;
		} else {
			$data['pay_choose'] = (bool) $this->config->get('shipping_econt_pay_choose');
		}

		if ($this->config->get('shipping_econt_partial_delivery') && ($this->cart->countProducts() > 1)) {
			$data['partial_delivery'] = true;
		} else {
			$data['partial_delivery'] = false;
		}

		if (!empty($this->session->data['shipping_methods']['econt']['quote']['shipping_econt_office']['text'])) {
			$data['office_calculated'] = true;
		} else {
			$data['office_calculated'] = false;
		}

		if (!empty($this->session->data['shipping_methods']['econt']['quote']['shipping_econt_aps']['text'])) {
			$data['office_aps_calculated'] = true;
		} else {
			$data['office_aps_calculated'] = false;
		}

		if (isset($this->session->data['econt']['office_price'])) {
			$data['office_price'] = $this->session->data['econt']['office_price'];
		}
		if (isset($this->session->data['econt']['aps_price'])) {
			$data['aps_price'] = $this->session->data['econt']['aps_price'];
		}

		$results['html'] = $this->load->view('extension/shipping/econt', $data);
		$this->response->setOutput(json_encode($results));
	}

	protected function validate() {
		if (empty($this->request->post['next_step'])) {
			return true;
		}

		if ($this->request->post['shipping_to'] == 'DOOR' && $this->request->post['postcode'] && $this->request->post['city'] && ($this->request->post['quarter'] && $this->request->post['other'] || $this->request->post['street'] && $this->request->post['street_num'])) {
			if (!$this->model_extension_shipping_econt->validateAddress($this->request->post)) {
				$this->error['address'] = $this->language->get('error_address');
			}
		} elseif ($this->request->post['shipping_to'] == 'DOOR') {
			$this->error['address'] = $this->language->get('error_address');
		}

		if ($this->request->post['shipping_to'] == 'OFFICE') {
			if (!$this->request->post['office_id']) {
				$this->error['office'] = $this->language->get('error_office');
			}
		}

		if ($this->request->post['shipping_to'] == 'APS') {
			if (!$this->request->post['office_aps_id']) {
				$this->error['office_aps'] = $this->language->get('error_office');
			}

			if (!$this->request->post['cd_payment']) {
				$this->error['cd_payment_aps'] = $this->language->get('error_cd_payment_aps');
			}
		}

		if (isset($this->request->post['priority_time_cb'])) {
			if (!$this->request->post['priority_time_hour_id'] || $this->request->post['priority_time_hour_id'] < 9 || $this->request->post['priority_time_hour_id'] > 18) {
				$this->error['priority_time'] = $this->language->get('error_priority_time');
			}
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function getCitiesByName() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/shipping/econt');

			$filter_name = $this->request->get['filter_name'];

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 10;
			}

			$json = $this->model_extension_shipping_econt->getCitiesByName($filter_name, $limit);
		}

		$this->response->setOutput(json_encode($json));
	}

	public function getQuartersByName() {
		$json = array();

		if (isset($this->request->get['filter_name']) && isset($this->request->get['city_id']) && $this->request->get['filter_name'] && $this->request->get['city_id']) {
			$this->load->model('extension/shipping/econt');

			$filter_name = $this->request->get['filter_name'];

			if (isset($this->request->get['city_id'])) {
				$city_id = $this->request->get['city_id'];
			} else {
				$city_id = 0;
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 10;
			}

			$json = $this->model_extension_shipping_econt->getQuartersByName($filter_name, $city_id, $limit);
		}

		$this->response->setOutput(json_encode($json));
	}

	public function getStreetsByName() {
		$json = array();

		if (isset($this->request->get['filter_name']) && isset($this->request->get['city_id']) && $this->request->get['filter_name'] && $this->request->get['city_id']) {
			$this->load->model('extension/shipping/econt');

			$filter_name = $this->request->get['filter_name'];

			if (isset($this->request->get['city_id'])) {
				$city_id = $this->request->get['city_id'];
			} else {
				$city_id = 0;
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 10;
			}

			$json = $this->model_extension_shipping_econt->getStreetsByName($filter_name, $city_id, $limit);
		}

		$this->response->setOutput(json_encode($json));
	}

	public function getCitiesByCountryId() {
		if ($this->request->post['country_id']) {
			$this->session->data['econt'] = array();
			$this->session->data['econt']['econt_country_id'] = $this->request->post['country_id'];

			$this->load->model('extension/shipping/econt');
			$this->response->setOutput(json_encode($this->model_extension_shipping_econt->getCitiesWithOffices()));
		}
	}

	public function getOfficesByCityId() {
		$this->load->model('extension/shipping/econt');

		if (isset($this->request->post['city_id'])) {
			$city_id = $this->request->post['city_id'];
		} else {
			$city_id = 0;
		}

		if (isset($this->request->post['aps'])) {
			$aps = $this->request->post['aps'];
		} else {
			$aps = 0;
		}

		$results = $this->model_extension_shipping_econt->getOfficesByCityId($city_id, $this->delivery_type, $aps);

		$this->response->setOutput(json_encode($results));
	}

	public function getOffice() {
		$this->load->model('extension/shipping/econt');

		if (isset($this->request->post['office_id'])) {
			$office_id = $this->request->post['office_id'];
		} else {
			$office_id = 0;
		}

		$results = $this->model_extension_shipping_econt->getOffice($office_id);

		$this->response->setOutput(json_encode($results));
	}

	public function getOfficeByOfficeCode() {
		$this->load->model('extension/shipping/econt');

		$json = array();

		if (isset($this->request->post['office_code']) && $this->request->post['office_code']) {
			$office = $this->model_extension_shipping_econt->getOfficeByOfficeCode(trim($this->request->post['office_code']));
			if (!empty($office)) {
				$json['office_id'] = $office['office_id'];
				$json['city_id'] = $office['city_id'];

				$json['offices'] = $this->model_extension_shipping_econt->getOfficesByCityId($office['city_id'], $this->delivery_type);
			} else {
				$json['error'] = $this->language->get('error_office_not_found');
			}
		} else {
			$json['error'] = $this->language->get('error_office_not_found');
		}

		$this->response->setOutput(json_encode($json));
	}

	protected function serviceTool($data) {
		if (!$data['test']) {
			$url = 'http://www.econt.com/e-econt/xml_service_tool.php';
		} else {
			$url = 'http://demo.econt.com/e-econt/xml_service_tool.php';
		}

		$request = '<?xml version="1.0" ?>
					<request>
						<client>
							<username>' . $data['username'] . '</username>
							<password>' . $data['password'] . '</password>
						</client>
						<client_software>ExtensaOpenCart2x</client_software>
						<request_type>' . $data['type'] . '</request_type>
						<mediator>extensa</mediator>';

		if (isset($data['xml'])) {
			$request .= $data['xml'];
		}

		$request .= '</request>';

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('xml' => $request));

		$response = curl_exec($ch);

		curl_close($ch);

		libxml_use_internal_errors(true);
		return simplexml_load_string($response);
	}
}
?>
