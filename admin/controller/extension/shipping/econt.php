<?php
class ControllerExtensionShippingEcont extends Controller {
	private $error = array();
	private $delivery_type = 'from_office';

	public function index() {
		$this->document->addScript('view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
		$this->document->addStyle('view/javascript/jquery/magnific/magnific-popup.css');

		$this->language->load('extension/shipping/econt');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/shipping/econt');

		$this->load->model('setting/setting');
		$dataConfig = $this->model_setting_setting->getSetting('shipping_econt');
		if (!isset($dataConfig['shipping_econt_show_econt_modal']) || !$dataConfig['shipping_econt_show_econt_modal']) {
			$dataConfig['shipping_econt_show_econt_modal'] = true;
			$this->model_setting_setting->editSetting('shipping_econt', $dataConfig);
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			unset($dataConfig['shipping_econt_pay_after_accept'], $dataConfig['shipping_econt_pay_after_test'], $dataConfig['shipping_econt_pay_choose']);

			$this->model_setting_setting->editSetting('shipping_econt', $this->request->post + $dataConfig);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
		}

		if (isset($this->request->post['shipping_econt_client_id'])) {
			unset($this->request->post['shipping_econt_client_id']);
		}

		$data['alert_error_aps_cd'] = $this->language->get('error_aps_cd');

		$data['user_token'] = (isset($this->session->data['user_token']) ? $this->session->data['user_token'] : '');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['name_person'])) {
			$data['error_name_person'] = $this->error['name_person'];
		} else {
			$data['error_name_person'] = '';
		}

		if (isset($this->error['client'])) {
			$data['error_client'] = $this->error['client'];
		} else {
			$data['error_client'] = '';
		}

		if (isset($this->error['addresses'])) {
			$data['error_addresses'] = $this->error['addresses'];
		} else {
			$data['error_addresses'] = '';
		}

		if (isset($this->error['get_data'])) {
			$data['error_get_data'] = $this->error['get_data'];
		} else {
			$data['error_get_data'] = '';
		}

		if (isset($this->error['office'])) {
			$data['error_office'] = $this->error['office'];
		} else {
			$data['error_office'] = '';
		}

		if (isset($this->error['sms'])) {
			$data['error_sms'] = $this->error['sms'];
		} else {
			$data['error_sms'] = '';
		}

		if (isset($this->error['aps_cd'])) {
			$data['error_aps_cd'] = $this->error['aps_cd'];
		} else {
			$data['error_aps_cd'] = '';
		}

		if (isset($this->error['econt_cd'])) {
			$data['error_econt_cd'] = $this->error['econt_cd'];
		} else {
			$data['error_econt_cd'] = '';
		}

		if (isset($this->error['shipping_payment'])) {
			$data['error_shipping_payment'] = $this->error['shipping_payment'];
		} else {
			$data['error_shipping_payment'] = '';
		}

		if (isset($this->error['request_courier_type'])) {
			$data['error_request_courier_type'] = $this->error['request_courier_type'];
		} else {
			$data['error_request_courier_type'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_shipping'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/shipping/econt', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/shipping/econt', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', 'SSL');

		$data['office_locator'] = 'https://www.bgmaps.com/templates/econt?office_type=to_office_courier&shop_url=' . HTTPS_SERVER;
		$data['office_locator_domain'] = 'https://www.bgmaps.com';

		if (isset($this->request->post['shipping_econt_test'])) {
			$data['shipping_econt_test'] = $this->request->post['shipping_econt_test'];
		} else {
			$data['shipping_econt_test'] = $this->config->get('shipping_econt_test');
		}

		if (isset($this->request->post['shipping_econt_username'])) {
			$data['shipping_econt_username'] = $this->request->post['shipping_econt_username'];
		} else {
			$data['shipping_econt_username'] = $this->config->get('shipping_econt_username');
		}

		if (isset($this->request->post['shipping_econt_password'])) {
			$data['shipping_econt_password'] = $this->request->post['shipping_econt_password'];
		} else {
			$data['shipping_econt_password'] = $this->config->get('shipping_econt_password');
		}

		if (isset($this->request->post['shipping_econt_address_id'])) {
			$data['shipping_econt_address_id'] = $this->request->post['shipping_econt_address_id'];
		} else {
			$data['shipping_econt_address_id'] = $this->config->get('shipping_econt_address_id');
		}

		if (isset($this->request->post['shipping_econt_post_pack_enabled'])) {
			$data['shipping_econt_post_pack_enabled'] = $this->request->post['shipping_econt_post_pack_enabled'];
		} else {
			$data['shipping_econt_post_pack_enabled'] = $this->config->get('shipping_econt_post_pack_enabled');
		}

		if (isset($this->request->post['shipping_econt_name'])) {
			$data['shipping_econt_name'] = $this->request->post['shipping_econt_name'];
		} else {
			$data['shipping_econt_name'] = $this->config->get('shipping_econt_name');
		}

		if (isset($this->request->post['shipping_econt_name_person'])) {
			$data['shipping_econt_name_person'] = $this->request->post['shipping_econt_name_person'];
		} else {
			$data['shipping_econt_name_person'] = $this->config->get('shipping_econt_name_person');
		}

		if (isset($this->request->post['shipping_econt_domain'])) {
			$data['shipping_econt_domain'] = $this->request->post['shipping_econt_domain'];
		} else {
			$data['shipping_econt_domain'] = $this->config->get('shipping_econt_domain');
		}

		if (isset($this->request->post['shipping_econt_phone'])) {
			$data['shipping_econt_phone'] = $this->request->post['shipping_econt_phone'];
		} else {
			$data['shipping_econt_phone'] = $this->config->get('shipping_econt_phone');
		}

		if (isset($this->request->post['shipping_econt_addresses'])) {
			$data['shipping_econt_addresses'] = $this->request->post['shipping_econt_addresses'];
		} elseif ($this->config->get('shipping_econt_addresses')) {
			if (!is_array($this->config->get('shipping_econt_addresses'))) {
				$data['shipping_econt_addresses'] = unserialize($this->config->get('shipping_econt_addresses'));
			} else {
				$data['shipping_econt_addresses'] = $this->config->get('shipping_econt_addresses');
			}
		} else {
			$data['shipping_econt_addresses'] = array();
		}

		if (isset($this->request->post['shipping_econt_office_id'])) {
			$data['shipping_econt_office_id'] = $this->request->post['shipping_econt_office_id'];
		} else {
			$data['shipping_econt_office_id'] = $this->config->get('shipping_econt_office_id');
		}

		if (isset($this->request->post['shipping_econt_office_aps_id'])) {
			$data['shipping_econt_office_aps_id'] = $this->request->post['shipping_econt_office_aps_id'];
		} else {
			$data['shipping_econt_office_aps_id'] = $this->config->get('shipping_econt_office_aps_id');
		}

		if (isset($this->request->post['shipping_econt_cd'])) {
			$data['shipping_econt_cd'] = $this->request->post['shipping_econt_cd'];
		} else {
			$data['shipping_econt_cd'] = $this->config->get('shipping_econt_cd');
		}

		if (isset($this->request->post['shipping_econt_shipping_from'])) {
			$data['shipping_econt_shipping_from'] = $this->request->post['shipping_econt_shipping_from'];
		} else {
			$data['shipping_econt_shipping_from'] = $this->config->get('shipping_econt_shipping_from');
		}

		$data['clients'] = $this->config->get('shipping_econt_clients');
		if (isset($this->request->post['shipping_econt_client_id'])) {
			$data['shipping_econt_client_id'] = $this->request->post['shipping_econt_client_id'];
		} else {
			$data['shipping_econt_client_id'] = $this->config->get('shipping_econt_client_id');
		}

		if (isset($this->request->post['shipping_econt_oc'])) {
			$data['shipping_econt_oc'] = $this->request->post['shipping_econt_oc'];
		} else {
			$data['shipping_econt_oc'] = $this->config->get('shipping_econt_oc');
		}

		if (isset($this->request->post['shipping_econt_total_for_oc'])) {
			$data['shipping_econt_total_for_oc'] = $this->request->post['shipping_econt_total_for_oc'];
		} else {
			$data['shipping_econt_total_for_oc'] = $this->config->get('shipping_econt_total_for_oc');
		}

		if (isset($this->request->post['shipping_econt_dc'])) {
			$data['shipping_econt_dc'] = $this->request->post['shipping_econt_dc'];
		} else {
			$data['shipping_econt_dc'] = $this->config->get('shipping_econt_dc');
		}

		if (isset($this->request->post['shipping_econt_invoice_before_cd'])) {
			$data['shipping_econt_invoice_before_cd'] = $this->request->post['shipping_econt_invoice_before_cd'];
		} else {
			$data['shipping_econt_invoice_before_cd'] = $this->config->get('shipping_econt_invoice_before_cd');
		}

		if (isset($this->request->post['shipping_econt_pay_after_accept'])) {
			$data['shipping_econt_pay_after_accept'] = $this->request->post['shipping_econt_pay_after_accept'];
		} else {
			$data['shipping_econt_pay_after_accept'] = $this->config->get('shipping_econt_pay_after_accept');
		}

		if (isset($this->request->post['shipping_econt_pay_after_test'])) {
			$data['shipping_econt_pay_after_test'] = $this->request->post['shipping_econt_pay_after_test'];
		} else {
			$data['shipping_econt_pay_after_test'] = $this->config->get('shipping_econt_pay_after_test');
		}

		if (isset($this->request->post['shipping_econt_pay_choose'])) {
			$data['shipping_econt_pay_choose'] = $this->request->post['shipping_econt_pay_choose'];
		} else {
			$data['shipping_econt_pay_choose'] = $this->config->get('shipping_econt_pay_choose');
		}

		if (isset($this->request->post['shipping_econt_shipping_payment_enabled'])) {
			$data['shipping_econt_shipping_payment_enabled'] = $this->request->post['shipping_econt_shipping_payment_enabled'];
		} else {
			$data['shipping_econt_shipping_payment_enabled'] = $this->config->get('shipping_econt_shipping_payment_enabled');
		}

		if (isset($this->request->post['shipping_econt_side'])) {
			$data['shipping_econt_side'] = $this->request->post['shipping_econt_side'];
		} else {
			$data['shipping_econt_side'] = $this->config->get('shipping_econt_side');
		}

		if (isset($this->request->post['shipping_econt_payment_method'])) {
			$data['shipping_econt_payment_method'] = $this->request->post['shipping_econt_payment_method'];
		} else {
			$data['shipping_econt_payment_method'] = $this->config->get('shipping_econt_payment_method');
		}

		if (isset($this->request->post['shipping_econt_saturday'])) {
			$data['shipping_econt_saturday'] = $this->request->post['shipping_econt_saturday'];
		} else {
			$data['shipping_econt_saturday'] = $this->config->get('shipping_econt_saturday');
		}

		if (isset($this->request->post['shipping_econt_restday'])) {
			$data['shipping_econt_restday'] = $this->request->post['shipping_econt_restday'];
		} else {
			$data['shipping_econt_restday'] = $this->config->get('shipping_econt_restday');
		}

		if (isset($this->request->post['shipping_econt_format_print'])) {
			$data['shipping_econt_format_print'] = $this->request->post['shipping_econt_format_print'];
		} else {
			$data['shipping_econt_format_print'] = $this->config->get('shipping_econt_format_print');
		}

		if (isset($this->request->post['shipping_econt_package'])) {
			$data['shipping_econt_package'] = $this->request->post['shipping_econt_package'];
		} else {
			$data['shipping_econt_package'] = $this->config->get('shipping_econt_package');
		}

		$data['shipping_econt_key_word'] = $this->config->get('shipping_econt_key_word');

		if (isset($this->request->post['shipping_econt_shipping_payments'])) {
			$data['shipping_econt_shipping_payments'] = $this->request->post['shipping_econt_shipping_payments'];
		} elseif ($this->config->get('shipping_econt_shipping_payments')) {
			if (!is_array($this->config->get('shipping_econt_shipping_payments'))) {
				$data['shipping_econt_shipping_payments'] = unserialize($this->config->get('shipping_econt_shipping_payments'));
			} else {
				$data['shipping_econt_shipping_payments'] = $this->config->get('shipping_econt_shipping_payments');
			}
		} else {
			$data['shipping_econt_shipping_payments'] = array();
		}

		if (isset($this->request->post['shipping_econt_priority_time'])) {
			$data['shipping_econt_priority_time'] = $this->request->post['shipping_econt_priority_time'];
		} else {
			$data['shipping_econt_priority_time'] = $this->config->get('shipping_econt_priority_time');
		}

		if (!$this->config->get('shipping_econt_test')) {
			$url = 'http://ee.econt.com/load_direct.php?target=EeLoadingInstructions';
		} else {
			$url = 'http://demo.econt.com/ee/load_direct.php?target=EeLoadingInstructions';
		}

		$data['instructionsFormUrl'] = $url . '&login_username=' . $this->config->get('shipping_econt_username') . '&login_password=' . md5($this->config->get('shipping_econt_password')) . '&target_type=client&id_target=' . $this->config->get('shipping_econt_client_id');

		$data['instructions_types'] = array(
			array('code' => 'take', 'title' => $this->language->get('text_instructions_take')),
			array('code' => 'give', 'title' => $this->language->get('text_instructions_give')),
			array('code' => 'return', 'title' => $this->language->get('text_instructions_return')),
		);

		if (isset($this->request->post['shipping_econt_instruction'])) {
			$data['shipping_econt_instruction'] = $this->request->post['shipping_econt_instruction'];
		} else {
			$data['shipping_econt_instruction'] = $this->config->get('shipping_econt_instruction');
		}

		foreach ($data['instructions_types'] as $instructionType) {
			if (isset($this->request->post['shipping_econt_instructions_select'][$instructionType['code']])) {
				$data['shipping_econt_instructions_select'][$instructionType['code']] = $this->request->post['shipping_econt_instructions_select'][$instructionType['code']];
			} else {
				$instructionsConfig = $this->config->get('shipping_econt_instructions_select');
				$data['shipping_econt_instructions_select'][$instructionType['code']] = $instructionsConfig[$instructionType['code']];
			}
		}

		if ($this->config->get('shipping_econt_instruction_list')) {
			if (!is_array($this->config->get('shipping_econt_instruction_list'))) {
				$data['shipping_econt_instructions_id'] = unserialize($this->config->get('shipping_econt_instruction_list'));
			} else {
				$data['shipping_econt_instructions_id'] = $this->config->get('shipping_econt_instruction_list');
			}
			if (isset($data['shipping_econt_instructions_id'][$this->config->get('shipping_econt_client_id')])) {
				$data['shipping_econt_instructions_id'] = $data['shipping_econt_instructions_id'][$this->config->get('shipping_econt_client_id')];
			} else {
				$data['shipping_econt_instructions_id'] = array();
			}
		} else {
			$data['shipping_econt_instructions_id'] = array();
		}

		if (isset($this->request->post['shipping_econt_currency'])) {
			$data['shipping_econt_currency'] = $this->request->post['shipping_econt_currency'];
		} else {
			$data['shipping_econt_currency'] = $this->config->get('shipping_econt_currency');
		}

		if (isset($this->request->post['shipping_econt_weight_class_id'])) {
			$data['shipping_econt_weight_class_id'] = $this->request->post['shipping_econt_weight_class_id'];
		} else {
			$data['shipping_econt_weight_class_id'] = $this->config->get('shipping_econt_weight_class_id');
		}

		if (isset($this->request->post['shipping_econt_order_status_id'])) {
			$data['shipping_econt_order_status_id'] = $this->request->post['shipping_econt_order_status_id'];
		} else {
			$data['shipping_econt_order_status_id'] = $this->config->get('shipping_econt_order_status_id');
		}

		if (isset($this->request->post['shipping_econt_geo_zone_id'])) {
			$data['shipping_econt_geo_zone_id'] = $this->request->post['shipping_econt_geo_zone_id'];
		} else {
			$data['shipping_econt_geo_zone_id'] = $this->config->get('shipping_econt_geo_zone_id');
		}

		if (isset($this->request->post['shipping_econt_status'])) {
			$data['shipping_econt_status'] = $this->request->post['shipping_econt_status'];
		} else {
			$data['shipping_econt_status'] = $this->config->get('shipping_econt_status');
		}

		if (isset($this->request->post['shipping_econt_sort_order'])) {
			$data['shipping_econt_sort_order'] = $this->request->post['shipping_econt_sort_order'];
		} else {
			$data['shipping_econt_sort_order'] = $this->config->get('shipping_econt_sort_order');
		}

		$data['payment_methods'] = array(
			array('code' => 'CASH', 'title' => $this->language->get('text_cash')),
			array('code' => 'CREDIT', 'title' => $this->language->get('text_credit')),
			array('code' => 'BONUS', 'title' => $this->language->get('text_bonus')),
			array('code' => 'VOUCHER', 'title' => $this->language->get('text_voucher'))
		);

		$data['partial_delivery_instructions'] = array(
			array('code' => 'ACCEPT', 'title' => $this->language->get('text_partial_delivery_accept')),
			array('code' => 'TEST', 'title' => $this->language->get('text_partial_delivery_test'))
		);

		$data['inventory_types'] = array(
			array('code' => 'DIGITAL', 'title' => $this->language->get('text_digital')),
			array('code' => 'LOADING', 'title' => $this->language->get('text_loading'))
		);

		$data['shipping_econt_cd_options'] = array(
			array('code' => '',  'title' => $this->language->get('text_choose_cd')),
			array('code' => '0', 'title' => $this->language->get('text_no')),
			array('code' => '1', 'title' => $this->language->get('text_yes_cash')),
		);
		if ($this->config->get('shipping_econt_agreement_list')) {
			$agreementList = $this->config->get('shipping_econt_agreement_list');
			if (isset($agreementList[$this->config->get('shipping_econt_client_id')])) {
				foreach ($agreementList[$this->config->get('shipping_econt_client_id')] as $agreementNum) {
					$data['shipping_econt_cd_options'][] = array(
						'code'  => 'cd' . $agreementNum,
						'title' => $this->language->get('text_yes_agreement') . ' ' . $agreementNum
					);
				}
			}
		}

		if (isset($this->request->post['shipping_econt_request_courier'])) {
			$data['shipping_econt_request_courier'] = $this->request->post['shipping_econt_request_courier'];
		} else {
			$data['shipping_econt_request_courier'] = $this->config->get('shipping_econt_request_courier');
		}

		if (isset($this->request->post['shipping_econt_request_courier_type'])) {
			$data['shipping_econt_request_courier_type'] = $this->request->post['shipping_econt_request_courier_type'];
		} else {
			$data['shipping_econt_request_courier_type'] = $this->config->get('shipping_econt_request_courier_type');
		}

		if (isset($this->request->post['shipping_econt_request_courier_time_from'])) {
			$data['shipping_econt_request_courier_time_from'] = $this->request->post['shipping_econt_request_courier_time_from'];
		} else {
			$data['shipping_econt_request_courier_time_from'] = $this->config->get('shipping_econt_request_courier_time_from');
		}

		if (isset($this->request->post['shipping_econt_request_courier_time_to'])) {
			$data['shipping_econt_request_courier_time_to'] = $this->request->post['shipping_econt_request_courier_time_to'];
		} else {
			$data['shipping_econt_request_courier_time_to'] = $this->config->get('shipping_econt_request_courier_time_to');
		}

		$hours = array();
		for ($h = 9; $h <= 17; $h++) {
			for ($m = 0; $m <= 45; $m+=15) {
				if (($h == 9 && $m < 30)|| ($h == 17 && $m > 30)) {
					continue;
				}
				$hours[$h.':'.$m] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':' . str_pad($m, 2, '0', STR_PAD_LEFT);
			}
		}
		$hours2 = $hours;
	
		$data['hours_between1'] = $hours;
		$data['hours_between2'] = $hours2;

		$this->load->model('localisation/currency');

		$data['currencies'] = $this->model_localisation_currency->getCurrencies();

		$this->load->model('localisation/weight_class');

		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['cities'] = $this->model_extension_shipping_econt->getCitiesWithOffices($this->delivery_type);

		$office = $this->model_extension_shipping_econt->getOffice($data['shipping_econt_office_id']);

		if ($office) {
			$data['shipping_econt_office_city_id'] = $office['city_id'];
			$data['shipping_econt_office_code'] = $office['office_code'];
		} else {
			$data['shipping_econt_office_city_id'] = 0;
			$data['shipping_econt_office_code'] = '';
		}

		$data['offices'] = $this->model_extension_shipping_econt->getOfficesByCityId($data['shipping_econt_office_city_id'], $this->delivery_type);

		$office_aps = $this->model_extension_shipping_econt->getOffice($data['shipping_econt_office_aps_id']);

		if ($office_aps) {
			$data['shipping_econt_office_city_aps_id'] = $office_aps['city_id'];
			$data['shipping_econt_office_aps_code'] = $office_aps['office_code'];
		} else {
			$data['shipping_econt_office_city_aps_id'] = 0;
			$data['shipping_econt_office_aps_code'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['shipping_econt_logged'] = $this->config->get('shipping_econt_logged');

		$addresses = array($this->language->get('text_please_choose_address'));
		if ($this->config->get('shipping_econt_address_list')) {
			foreach ($this->config->get('shipping_econt_address_list') as $address) {
				$label = $address['city_post_code'] . ', ' . $address['city'];
				if ($address['quarter']) {
					$label .= ', ' . $address['quarter'];
				}
				if ($address['street']) {
					$label .= ', ' . $address['street'];
				}
				if ($address['street_num']) {
					$label .= ' ' . $address['street_num'];
				}
				if ($address['other']) {
					$label .= ', ' . $address['other'];
				}
				$addresses[] = $label;
			}
		}
		$addresses = array_combine(range(-1, count($addresses) - 2), array_values($addresses));
		$data['shipping_econt_address_list'] = $addresses;

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['cron_path'] = DIR_APPLICATION . 'controller/extension/econt_cron.php';
		$data['show_econt_modal'] = !$this->config->get('shipping_econt_show_econt_modal');

		$this->response->setOutput($this->load->view('extension/shipping/econt', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/econt')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->request->post['shipping_econt_shipping_from'] == 'OFFICE') {
			if (!$this->request->post['shipping_econt_office_id']) {
				$this->error['office'] = $this->language->get('error_office');
			}
		}

		if (!$this->request->post['shipping_econt_client_id']) {
			$this->error['client'] = $this->language->get('error_choose_client');
		}

		if ($this->request->post['shipping_econt_address_id'] == -1) {
			$this->error['addresses'] = $this->language->get('error_address');
		}

		if ($this->request->post['shipping_econt_shipping_from'] == 'APS' && $this->request->post['shipping_econt_cd'] && (!$this->request->post['shipping_econt_cd_agreement'] || $this->request->post['shipping_econt_cd_agreement'] && empty($this->request->post['shipping_econt_cd_agreement_num']))) {
			$this->error['aps_cd'] = $this->language->get('error_aps_cd');
		}

		$priorities = array();
		if (isset($this->request->post['shipping_econt_shipping_payments']) && is_array($this->request->post['shipping_econt_shipping_payments'])) {
			foreach ($this->request->post['shipping_econt_shipping_payments'] as $shippingPayment) {
				if (!ctype_digit($shippingPayment['criteria_value'])) {
					$this->error['shipping_payment'] = $this->language->get('error_shipping_payment');
				}

				if (!is_numeric($shippingPayment['amount_door'])) {
					$this->error['shipping_payment'] = $this->language->get('error_shipping_payment');
				}

				if (!is_numeric($shippingPayment['amount_office'])) {
					$this->error['shipping_payment'] = $this->language->get('error_shipping_payment');
				}

				if (!is_numeric($shippingPayment['amount_aps'])) {
					$this->error['shipping_payment'] = $this->language->get('error_shipping_payment');
				}

				if (!(ctype_digit($shippingPayment['priority']) && $shippingPayment['priority'] >= 1 && $shippingPayment['priority'] <= 100)) {
					$this->error['shipping_payment'] = $this->language->get('error_shipping_payment');
				}

				if (in_array($shippingPayment['priority'], $priorities)) {
					$this->error['shipping_payment'] = $this->language->get('error_shipping_payment_priority');
				}

				$priorities[] = $shippingPayment['priority'];

				if (isset($this->error['shipping_payment'])) {
					break;
				}
			}
		}

		if ($this->request->post['shipping_econt_cd'] === '') {
			$this->error['econt_cd'] = $this->language->get('error_choose_cd');
		}

		if ($this->request->post['shipping_econt_request_courier']) {
			if (!$this->request->post['shipping_econt_request_courier_type']) {
				$this->error['request_courier_type'] = $this->language->get('error_request_courier_type');
			}

			if ($this->request->post['shipping_econt_request_courier_type'] == 'BETWEEN') {
				$timeFrom = explode(':', $this->request->post['shipping_econt_request_courier_time_from']);
				$timeTo = explode(':', $this->request->post['shipping_econt_request_courier_time_to']);
				$timeFrom = $timeFrom[0] * 60 + $timeFrom[1];
				$timeTo = $timeTo[0] * 60 + $timeTo[1];

				if ($timeTo - $timeFrom < 15) {
					$this->error['request_courier_type'] = $this->language->get('error_request_courier_time_to');
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function login() {
		$this->load->model('setting/setting');
		$this->load->model('extension/shipping/econt');
		$this->load->language('extension/shipping/econt');

		$results_data = array();

		$dataService = array(
			'type'     => 'profile',
			'test'     => $this->request->post['test'],
			'username' => $this->request->post['username'],
			'password' => $this->request->post['password']
		);

		if (!empty($this->request->post['username']) && !empty($this->request->post['password'])) {
			$results = $this->serviceTool($dataService);
			if (isset($results->error)) {
				$results_data['message'] = (string)$results->error->message;
			} elseif (isset($results->client_info->id)) {
				$data = $this->model_setting_setting->getSetting('shipping_econt');
				$data['shipping_econt_logged'] = true;
				$data['shipping_econt_test'] = $this->request->post['test'];
				$data['shipping_econt_username'] = $this->request->post['username'];
				$data['shipping_econt_password'] = $this->request->post['password'];
				$data['shipping_econt_domain'] = $_SERVER['HTTP_HOST'];
				$data['shipping_econt_client_id'] = -1;
				$data['shipping_econt_address_id'] = -1;

				if (isset($data['shipping_econt_cd']) && $data['shipping_econt_cd'] != 0 && $data['shipping_econt_cd'] != 1) {
					$data['shipping_econt_cd'] = '';
				}

				$this->model_setting_setting->editSetting('shipping_econt', $data);

				$this->model_extension_shipping_econt->updateData($dataService);

				$results_data['success'] = true;
			} else {
				$results_data['message'] = $this->language->get('error_general');
			}
		} else {
			$results_data['message'] = $this->language->get('error_username_password');
		}

		$this->response->setOutput(json_encode($results_data));
	}

	public function logout()
	{
		$this->load->model('setting/setting');
		$data = $this->model_setting_setting->getSetting('shipping_econt');
		$data['shipping_econt_logged'] = false;
		$data['shipping_econt_status'] = 0;
		$this->model_setting_setting->editSetting('shipping_econt', $data);
	}

	public function getClientInfo()
	{
		$clientId = $this->request->post['client_id'];
		if ($clientId) {
			$this->load->model('setting/setting');
			$data = $this->model_setting_setting->getSetting('shipping_econt');

			$results_data = array();
			$keywords     = $data['shipping_econt_keyword_list'];
			$agreements   = $data['shipping_econt_agreement_list'];
			$instructions = $data['shipping_econt_instruction_list'];

			if (!isset($keywords[$clientId])) {
				$keywords[$clientId] = '';
			}

			if (!isset($agreements[$clientId])) {
				$agreements[$clientId] = array();
			}

			if (!isset($instructions[$clientId])) {
				$instructions[$clientId] = array();
			}

			$results_data['keyword']      = $keywords[$clientId];
			$results_data['agreements']   = $agreements[$clientId];
			$results_data['instructions'] = $instructions[$clientId];

			$this->response->setOutput(json_encode($results_data));
		}
	}

	private function checkCronCourierRequest($timeFrom)
    {
        $timeFromEx = explode(':', $timeFrom);
        $timeFromInt = $timeFromEx[0] * 60 + $timeFromEx[1];
        $currTime = date('H') * 60 + date('i');

        $diff = $timeFromInt - $currTime;
        return ($diff <= 30 && $diff >= 0);
    }

	public function refreshDataCron()
	{
		$this->load->model('setting/setting');

		$dataSettings = $this->model_setting_setting->getSetting('shipping_econt');

		if (isset($dataSettings['shipping_econt_cron_last_run']) && $dataSettings['shipping_econt_cron_last_run'] >= time()) {
			return;
		}

		if (date('H') == 3) {
			$this->load->model('extension/shipping/econt');

			$test = $dataSettings['shipping_econt_test'];
			$username = $dataSettings['shipping_econt_username'];
			$password = $dataSettings['shipping_econt_password'];

			$data = array(
				'test'     => $test,
				'username' => $username,
				'password' => $password
			);

			for ($i = 0; $i <= 6; $i++) {
				$this->model_extension_shipping_econt->{'updateStep' . $i}($data);
			}
			$this->model_extension_shipping_econt->updateData($data);

			$dataSettings['shipping_econt_cron_last_run'] = time() + 4000;
		}

		if ($dataSettings['shipping_econt_request_courier'] && date('w') >= 1 && date('w') <= 5) {
			if ($this->checkCronCourierRequest($dataSettings['shipping_econt_request_courier_time_from'])) {
				$this->load->model('extension/sale/econt');

				$orderIds = array();
				$orders = $this->model_extension_sale_econt->getOrdersForCourier();
				foreach ($orders as $order) {
					$orderIds[] = $order['order_id'];
				}
				$this->model_extension_sale_econt->courierRequest($orderIds, $dataSettings['shipping_econt_request_courier_type'], $dataSettings['shipping_econt_request_courier_time_from'], $dataSettings['shipping_econt_request_courier_time_from'], $dataSettings['shipping_econt_request_courier_time_to'], true);
				$dataSettings['shipping_econt_cron_last_run'] = time() + 4000;
			}
		}

		$this->model_setting_setting->editSetting('shipping_econt', $dataSettings);
	}

	public function refreshData() {
		@ini_set('memory_limit', '512M');
		@ini_set('max_execution_time', 3600);

		$this->language->load('extension/shipping/econt');

		$this->load->model('setting/setting');
		$this->load->model('extension/shipping/econt');

		$data = $this->model_setting_setting->getSetting('shipping_econt');
		$test = $data['shipping_econt_test'];
		$username = $data['shipping_econt_username'];
		$password = $data['shipping_econt_password'];

		if (isset($this->request->post['step'])) {
			$step = $this->request->post['step'];
		} else {
			$step = 0;
		}

		if (isset($this->request->post['zone'])) {
			$zoneUpdate = $this->request->post['zone'];
		} else {
			$zoneUpdate = 1;
		}

		$data = array(
			'test'     => $test,
			'username' => $username,
			'password' => $password
		);

		// $this->model_extension_shipping_econt->updateData($data);
		// $this->session->data['success'] = $this->language->get('text_success_update');
		// $this->response->setOutput(json_encode(array('success' => true)));
		// return;

		$results_data = array();
		$zones = $this->model_extension_shipping_econt->getZones($zoneUpdate, 2);
		if (!$zones || $zones[0]['zone_id'] == $zoneUpdate) {
			$result = $this->model_extension_shipping_econt->{'updateStep' . $step}($data, $zoneUpdate);
			if ($result) {
				if (!isset($zones[1]) || $step < 2) {
					if ($step == 6) {
						$this->model_extension_shipping_econt->updateData($data);
						$this->session->data['success'] = $this->language->get('text_success_update');
						$results_data['success'] = true;
					} else {
						$results_data['step'] = $step + 1;
						$results_data['zone'] = 1;
					}
				} else {
					$results_data['step'] = $step;
					$results_data['zone'] = $zones[1]['zone_id'];
				}
			} else {
				$results_data['error'] = true;
				$results_data['message'] = $this->language->get('error_connect');
			}
		} else {
			$results_data['step'] = $step;
			$results_data['zone'] = $zones[0]['zone_id'];
		}

		$this->response->setOutput(json_encode($results_data));
	}

	public function getProfile() {
		$this->language->load('shipping/econt');

		$this->load->model('setting/setting');
		$this->load->model('extension/shipping/econt');

		$data = $this->model_setting_setting->getSetting('shipping_econt');
		$test = $data['shipping_econt_test'];
		$username = $data['shipping_econt_username'];
		$password = $data['shipping_econt_password'];

		$data = array(
			'test'     => $test,
			'username' => $username,
			'password' => $password,
			'type'     => 'profile'
		);

		$profile_data = array();

		$results = $this->serviceTool($data);

		if ($results) {
			if (isset($results->error)) {
				$profile_data['error'] = (string)$results->error->message;
			} else {
				if (isset($results->client_info)) {
					$profile_data['client_info'] = $results->client_info;

					if (!empty($results->client_info->id)) {
						if (!$test) {
							$instructions_form_url = 'http://ee.econt.com/load_direct.php?target=EeLoadingInstructions';
						} else {
							$instructions_form_url = 'http://demo.econt.com/ee/load_direct.php?target=EeLoadingInstructions';
						}

						$profile_data['instructions_form_url'] = $instructions_form_url . '&login_username=' . $username . '&login_password=' . md5($password) . '&target_type=client&id_target=' . (string)$results->client_info->id;
					}
				}

				if (isset($results->addresses)) {
					foreach ($results->addresses->e as $address) {

						if (isset($address->city) && isset($address->city_post_code)) {
							$city = $this->model_extension_shipping_econt->getCityByNameAndPostcode($address->city, $address->city_post_code);

							if ($city) {
								$address->city_id = $city['city_id'];
							}
						}

						$profile_data['addresses'][] = $address;
					}
				}
			}
		} else {
			$profile_data['error'] = $this->language->get('error_connect');
		}

		$this->response->setOutput(json_encode($profile_data));
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

		if (isset($this->request->get['filter_name'])) {
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

		if (isset($this->request->get['filter_name'])) {
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
			$aps = null;
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

	public function getClients() {
		$this->language->load('extension/shipping/econt');

		if (isset($this->request->post['test'])) {
			$test = $this->request->post['test'];
		} else {
			$test = 0;
		}

		if (isset($this->request->post['username'])) {
			$username = $this->request->post['username'];
		} else {
			$username = '';
		}

		if (isset($this->request->post['password'])) {
			$password = $this->request->post['password'];
		} else {
			$password = '';
		}

		$data = array(
			'test'     => $test,
			'username' => $username,
			'password' => $password,
			'type'     => 'access_clients'
		);

		$clients_data = array();

		$results = $this->serviceTool($data);

		if ($results) {
			if (isset($results->error)) {
				$clients_data['error'] = (string)$results->error->message;
			} else {
				if (isset($results->clients)) {
					foreach ($results->clients->client as $client) {
						$clients_data['key_words'][] = (string)$client->key_word;

						if (isset($client->cd_agreements)) {
							foreach ($client->cd_agreements->cd_agreement as $cd_agreement) {
								$clients_data['cd_agreement_nums'][] = (string)$cd_agreement->num;
							}
						}

						if (isset($client->instructions)) {
							foreach ($client->instructions->e as $instruction) {
								$clients_data['instructions'][(string)$instruction->type][] = (string)$instruction->template;
							}
						}
					}
				}
			}
		} else {
			$clients_data['error'] = $this->language->get('error_connect');
		}

		$this->response->setOutput(json_encode($clients_data));
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
						<client_software>ExtensaOpenCart3x</client_software>
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

	public function install() {
		$this->load->model('setting/setting');

		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/sale/econt');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/sale/econt');

		$shipping_data = array(
			'shipping_estimator'  => 0,
			'shipping_status'     => 1,
			'shipping_sort_order' => $this->config->get('shipping_sort_order')
		);

		$this->model_setting_setting->editSetting('shipping', $shipping_data);

		$cod_data = array(
			'cod_status' => 0
		);

		$this->model_setting_setting->editSetting('cod', $cod_data);

		$econt_data = $this->model_setting_setting->getSetting('shipping_econt');
		$econt_data['shipping_econt_oc'] = 1;
		$econt_data['shipping_econt_total_for_oc'] = 0;
		$this->model_setting_setting->editSetting('shipping_econt', $econt_data);

		$this->load->model('extension/shipping/econt');

		$this->model_extension_shipping_econt->createTables();
		$this->model_extension_shipping_econt->importData();

		@mail('support@extensadev.com', 'Econt Express Shipping Module installed (OpenCart)', HTTP_CATALOG . ' - ' . $this->config->get('config_name') . "\r\n" . 'version - ' . VERSION . "\r\n" . 'IP - ' . $this->request->server['REMOTE_ADDR'], 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n" . 'From: ' . $this->config->get('config_owner') . ' <' . $this->config->get('config_email') . '>' . "\r\n");
	}

	public function uninstall() {
		$this->load->model('extension/shipping/econt');

		$this->model_extension_shipping_econt->deleteTables();
	}
}
?>
