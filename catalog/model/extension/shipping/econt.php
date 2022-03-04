<?php
class ModelExtensionShippingEcont extends Model {
	const BULGARIA_ECONT_CODE = 1033;

	private $delivery_type = 'to_office';

	function getQuote($address) {
		$this->load->language('extension/shipping/econt');

		if (isset($address['validate'])) {
			$status = true;
		} else {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_econt_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

			if (!$this->config->get('shipping_econt_geo_zone_id')) {
				$status = true;
			} elseif ($query->num_rows) {
				$status = true;
			} else {
				$status = false;
			}
		}

		$method_data = array();

		if ($status) {
			$quote_data = array();

			$to_office = true;
			$to_door = true;
			$to_aps = true;

			if ($to_office || $to_aps) {
				$quote_data['shipping_econt_office'] = array(
					'code'         => 'econt.shipping_econt_office',
					'title'        => $this->language->get('text_description') . ' - ' . $this->language->get('text_to_office_aps'),
					'cost'         => 0.00,
					'tax_class_id' => 0,
					'text'         => '<span id="office_price">' . $this->currency->format(0.00, $this->config->get('config_currency')) . '</span>'
				);
			}

			if ($to_door) {
				$quote_data['shipping_econt_door'] = array(
					'code'         => 'econt.shipping_econt_door',
					'title'        => $this->language->get('text_description') . ' - ' . $this->language->get('text_to_door'),
					'cost'         => 0.00,
					'tax_class_id' => 0,
					'text'         => $this->currency->format(0.00, $this->config->get('config_currency'))
				);
			}

			$method_data = array(
				'code'       => 'econt',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_econt_sort_order'),
				'error'      => false
			);

			$receiver_address = array();

			if (!empty($this->session->data['econt']['city_id']) || !empty($this->session->data['econt']['office_city_id'])) {
				$receiver_address['post_code'] = $this->session->data['econt']['postcode'];
				$receiver_address['city'] = $this->session->data['econt']['city'];
				$receiver_address['city_id'] = $this->session->data['econt']['city_id'];
				$receiver_address['office_city_id'] = $this->session->data['econt']['office_city_id'];
				$receiver_address['office_id'] = $this->session->data['econt']['office_id'];
				$receiver_address['quarter'] = $this->session->data['econt']['quarter'];
				$receiver_address['street'] = $this->session->data['econt']['street'];
				$receiver_address['street_num'] = $this->session->data['econt']['street_num'];
				$receiver_address['other'] = $this->session->data['econt']['other'];
			} else {
				if ($this->customer->isLogged()) {
					$shipping_address = $this->getCustomer($this->customer->getId());

					if ($shipping_address) {
						$receiver_address['post_code'] = $shipping_address['postcode'];
						$receiver_address['city'] = $shipping_address['city'];
						$receiver_address['city_id'] = $shipping_address['city_id'];
						$receiver_address['office_id'] = $shipping_address['office_id'];
						$receiver_address['quarter'] = $shipping_address['quarter'];
						$receiver_address['street'] = $shipping_address['street'];
						$receiver_address['street_num'] = $shipping_address['street_num'];
						$receiver_address['other'] = $shipping_address['other'];
					} else {
						$this->load->model('account/address');

						$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address']['address_id']);

						$receiver_address['post_code'] = $shipping_address['postcode'];
						$receiver_address['city'] = $shipping_address['city'];
					}
				} else {
					$receiver_address['post_code'] = $this->session->data['shipping_address']['postcode'];
					$receiver_address['city'] = $this->session->data['shipping_address']['city'];
				}
			}

			if (empty($receiver_address['city_id'])) {
				if (!empty($receiver_address['post_code'])) {
					$cityId = $this->model_extension_shipping_econt->getCityIdByPostCode($receiver_address['post_code']);
					$city = $this->getCityByCityId($cityId);
					$receiver_address['post_code'] = $city['post_code'];
					$receiver_address['city'] = $city['name'];
					$receiver_address['city_id'] = $city['city_id'];
				} else {
					$cities = $this->getCitiesByName($receiver_address['city']);

					if ($cities) {
						if (count($cities) > 1) {
							foreach ($cities as $city) {
								if (trim($city['post_code']) == trim($receiver_address['post_code'])) {
									$receiver_address['post_code'] = $city['post_code'];
									$receiver_address['city_id'] = $city['city_id'];
									break;
								}
							}
						} else {
							$receiver_address['post_code'] = $cities[0]['post_code'];
							$receiver_address['city_id'] = $cities[0]['city_id'];
						}
					}
				}
			}

			if (!empty($receiver_address['city_id'])) {
				$data = array();

				//$total = round($this->currency->format($this->cart->getTotal(), $this->config->get('shipping_econt_currency'), '', false), 2);

				$this->load->model('setting/extension');

				$total_data = array(
					'totals' => &$totals,
					'taxes'  => &$taxes,
					'total'  => &$total
				);

				$total = 0;
				$taxes = $this->cart->getTaxes();
				$sort_order = array();

				$results = $this->model_setting_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get('total_' . $result['code'] . '_status')) {
						$this->load->model('extension/total/' . $result['code']);
						if ($result['code'] != 'shipping') {
							$this->{'model_extension_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
						}
					}
				}

				$total = round($this->currency->format($total, $this->config->get('shipping_econt_currency'), '', false), 2);

				$data['system']['validate'] = 1;
				$data['system']['response_type'] = 'XML';
				$data['system']['only_calculate'] = 1;

				$data['client']['username'] = $this->config->get('shipping_econt_username');
				$data['client']['password'] = $this->config->get('shipping_econt_password');

				$row = array();

				$clients = $this->config->get('shipping_econt_clients');
				$clientId = $this->config->get('shipping_econt_client_id');

				$row['sender']['name'] = $clients[$clientId]['name'];
				$row['sender']['name_person'] = $clients[$clientId]['name'];

				if ($this->config->get('shipping_econt_shipping_from') == 'OFFICE') {
					$sender_office = $this->getOffice($this->config->get('shipping_econt_office_id'));
					if ($sender_office) {
						$city = $this->getCityByCityId($sender_office['city_id']);
						if ($city) {
							$row['sender']['city'] = $city['name'];
							$row['sender']['post_code'] = $city['post_code'];
							$row['sender']['office_code'] = $sender_office['office_code'];
						}
					}
				} else {
					$sender_address = $this->config->get('shipping_econt_address_list');
					$sender_address = $sender_address[$this->config->get('shipping_econt_address_id')];

					$row['sender']['city'] = $sender_address['city'];
					$row['sender']['post_code'] = $sender_address['city_post_code'];
					$row['sender']['quarter'] = $sender_address['quarter'];
					$row['sender']['street'] = $sender_address['street'];
					$row['sender']['street_num'] = $sender_address['street_num'];
					$row['sender']['street_other'] = $sender_address['other'];
				}

				$row['sender']['phone_num'] = $this->config->get('shipping_econt_phone');
				$row['sender']['street_bl'] = '';
				$row['sender']['street_vh'] = '';
				$row['sender']['street_et'] = '';
				$row['sender']['street_ap'] = '';

				if ($this->customer->isLogged()) {
					$receiver_name_person = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
					$receiver_email = $this->customer->getEmail();
					$receiver_phone_num = $this->customer->getTelephone();

				} elseif (isset($this->session->data['guest'])) {
					$receiver_name_person = $this->session->data['guest']['firstname'] . ' ' . $this->session->data['guest']['lastname'];
					$receiver_email = $this->session->data['guest']['email'];
					$receiver_phone_num = $this->session->data['guest']['telephone'];
				} else {
					$receiver_name_person = $this->session->data['customer']['firstname'] . ' ' . $this->session->data['customer']['lastname'];
					$receiver_email = $this->session->data['customer']['email'];
					$receiver_phone_num = $this->session->data['customer']['telephone'];
				}

				if (!empty($this->session->data['shipping_address']['company'])) {
					$company = $this->session->data['shipping_address']['company'];
				} else {
					$company = $receiver_name_person;
				}

				$row['receiver']['name'] = $company;
				$row['receiver']['name_person'] = $receiver_name_person;
				$row['receiver']['receiver_email'] = $receiver_email;
				$row['receiver']['street_bl'] = '';
				$row['receiver']['street_vh'] = '';
				$row['receiver']['street_et'] = '';
				$row['receiver']['street_ap'] = '';
				$row['receiver']['phone_num'] = $receiver_phone_num;

				if ($this->config->get('shipping_econt_sms')) {
					$sms_no = $this->config->get('shipping_econt_sms_no');
				} else {
					$sms_no = '';
				}

				$row['receiver']['sms_no'] = $sms_no;
				$row['shipment']['envelope_num'] = '';

				$weight = 0;
				$width = $height = $length = 0;
				$description = array();

				foreach ($this->cart->getProducts() as $product) {
					$description[] = $product['name'];

					if ($product['shipping']) {
						$product_weight = (float)$product['weight'];
						if (!empty($product_weight)) {
							$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('shipping_econt_weight_class_id'));
						} else {
							$data['error']['weight'][$product['product_id']] = $product;
						}
						
						$w = $this->length->convert($product['width'], $product['length_class_id'], 1) * $product['quantity'];
						$h = $this->length->convert($product['height'], $product['length_class_id'], 1) * $product['quantity'];
						$l = $this->length->convert($product['length'], $product['length_class_id'], 1) * $product['quantity'];

						if (!$w || !$h || !$l) {
							$width = $height = $length = -1;
						}

						if ($width > -1) {
							$width += $w;
							$height += $h;
							$length += $l;
						}
					}
				}

				if (!empty($weight)) {
					$data['error']['no_weight'] = false;
				} else {
					$data['error']['no_weight'] = true;

					$weight = 1;
				}

				if ($width === -1) {
					$width = $height = $length = 0;
				}

				$less60cm = false;
				if ($width && $height && $length && $width < 60 && $height < 60 && $length < 60) {
					$less60cm = true;
				}

				$row['shipment']['description'] = implode(', ', $description);
				$row['shipment']['pack_count'] = 1;
				$row['shipment']['weight'] = $weight;
				$row['shipment']['shipment_pack_dimensions_l'] = $length;
				$row['shipment']['shipment_pack_dimensions_w'] = $width;
				$row['shipment']['shipment_pack_dimensions_h'] = $height;
				$row['shipment']['size_under_60cm'] = 0;

				$row['payment']['side'] = $this->config->get('shipping_econt_side');
				$row['payment']['method'] = $this->config->get('shipping_econt_payment_method');

				$receiver_share_sum_door = '';
				$receiver_share_sum_office = '';
				$receiver_share_sum_aps = '';

				if ($this->config->get('shipping_econt_shipping_payment_enabled') && $row['payment']['side'] == 'RECEIVER') {
					$shipping_payments = $this->config->get('shipping_econt_shipping_payments');
					if ($shipping_payments && is_array($shipping_payments)) {
						usort($shipping_payments, array('ModelExtensionShippingEcont', 'sortByPriority'));
						foreach ($shipping_payments as $shippingPayment) {
							$shippingCondition = false;
							if ($shippingPayment['condition'] == 'weight') {
								if ($shippingPayment['criteria'] == 'lt') {
									$shippingCondition = ($weight < $shippingPayment['criteria_value']);
								} else {
									$shippingCondition = ($weight > $shippingPayment['criteria_value']);
								}
							} elseif ($shippingPayment['condition'] == 'total_amount') {
								if ($shippingPayment['criteria'] == 'lt') {
									$shippingCondition = ($total < $shippingPayment['criteria_value']);
								} else {
									$shippingCondition = ($total > $shippingPayment['criteria_value']);
								}
							} elseif ($shippingPayment['condition'] == 'cart') {
								if ($shippingPayment['criteria'] == 'lt') {
									$shippingCondition = ($this->cart->countProducts() < $shippingPayment['criteria_value']);
								} else {
									$shippingCondition = ($this->cart->countProducts() > $shippingPayment['criteria_value']);
								}
							}
		
							if ($shippingCondition) {
								$receiver_share_sum_door = $shippingPayment['amount_door'];
								$receiver_share_sum_office = $shippingPayment['amount_office'];
								$receiver_share_sum_aps = $shippingPayment['amount_aps'];
								break;
							}
						}
					}
				} elseif ($row['payment']['side'] == 'SENDER') {
					$receiver_share_sum_door = $receiver_share_sum_office = $receiver_share_sum_aps = 0;
				}

				if ($row['payment']['method'] == 'CREDIT') {
					$key_word = $this->config->get('shipping_econt_key_word');
				} else {
					$key_word = '';
				}

				$row['payment']['key_word'] = $key_word;

				$row['services']['e'] = '';
				$row['services']['pack1'] = '';
				$row['services']['pack2'] = '';
				$row['services']['pack3'] = '';
				$row['services']['pack4'] = '';
				$row['services']['pack5'] = '';
				$row['services']['pack6'] = '';
				$row['services']['pack7'] = '';
				$row['services']['pack8'] = '';
				$row['services']['ref'] = '';

				$methods = array('door', 'office');
				foreach ($methods as $method) {
					$rowNow = $row;

					$isMachine = false;

					if ($method == 'door') {
						$rowNow['receiver']['city'] = $receiver_address['city'];
						$rowNow['receiver']['post_code'] = $receiver_address['post_code'];
						$rowNow['receiver']['quarter'] = (isset($receiver_address['quarter']) ? $receiver_address['quarter'] : '');
						$rowNow['receiver']['street'] = (isset($receiver_address['street']) ? $receiver_address['street'] : '');
						$rowNow['receiver']['street_num'] = (isset($receiver_address['street_num']) ? $receiver_address['street_num'] : '');
						$rowNow['receiver']['street_other'] = (isset($receiver_address['other']) ? $receiver_address['other'] : '');
					} else {
						if (isset($receiver_address['office_id']) && $receiver_address['office_id']) {
							$receiver_office = $this->getOffice($receiver_address['office_id']);
							if ($receiver_office['is_machine']) {
								$isMachine = true;
							}
							$rowNow['receiver']['office_code'] = $receiver_office['office_code'];
						}
						if (isset($receiver_address['office_city_id'])) {
							$city = $this->getCityByCityId($receiver_address['office_city_id']);
							$rowNow['receiver']['city'] = $city['name'];
							$rowNow['receiver']['post_code'] = $city['post_code'];
						} else {
							$rowNow['receiver']['city'] = $receiver_address['city'];
							$rowNow['receiver']['post_code'] = $receiver_address['post_code'];
						}
					}

					if ($method == 'door' || !$isMachine) {
						$rowNow['services']['dc'] = ($this->config->get('shipping_econt_dc') == 1) ? 'ON' : '';
						$rowNow['services']['dc_cp'] = ($this->config->get('shipping_econt_dc') == 2) ? 'ON' : '';

						$rowNow['shipment']['pay_after_accept'] = (int)$this->config->get('shipping_econt_pay_after_accept');
						$rowNow['shipment']['pay_after_test'] = (int)$this->config->get('shipping_econt_pay_after_test');
						$rowNow['shipment']['pay_choose'] = (int)$this->config->get('shipping_econt_pay_choose');
						if ($rowNow['shipment']['pay_choose']) {
							$rowNow['packing_list']['partial_delivery'] = 1;
							$rowNow['packing_list']['type'] = 'digital';
							foreach ($this->cart->getProducts() as $product) {
								$rowNow['packing_list']['row'][] = array(
									'inventory_num' => $product['product_id'],
									'description'   => $product['name'],
									'weight'        => $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('shipping_econt_weight_class_id')),
									'price'         => $product['price'],
								);
							}
						}

						if ($this->config->get('shipping_econt_shipping_from') != 'APS' && $this->config->get('shipping_econt_oc') && ($total >= $this->config->get('shipping_econt_total_for_oc'))) {
							$rowNow['services']['oc'] = $total;
							$rowNow['services']['oc_currency'] = $this->config->get('shipping_econt_currency');
						}
					}

					$cd_active = true;
					if (isset($this->session->data['econt']['cd_payment'])) {
						$cd_active = $this->session->data['econt']['cd_payment'];
					} else {
						$cd_active = $this->config->get('shipping_econt_cd');
					}

					if ($cd_active) {
						if (mb_strpos($this->config->get('shipping_econt_cd'), 'cd') === 0) {
							$cd_agreement_num = mb_substr($this->config->get('shipping_econt_cd'), 2);
						} else {
							$cd_agreement_num = '';
						}

						$rowNow['services']['cd'] = array('type' => 'GET', 'value' => $total);
						$rowNow['services']['cd_currency'] = $this->config->get('shipping_econt_currency');
						$rowNow['services']['cd_agreement_num'] = $cd_agreement_num;
					}

					if ($method == 'door') {
						if (isset($this->session->data['econt']['priority_time_cb']) && isset($this->session->data['econt']['priority_time_type_id']) && isset($this->session->data['econt']['priority_time_hour_id'])) {
							$rowNow['services']['p'] = array('type' => $this->session->data['econt']['priority_time_type_id'], 'value' => $this->session->data['econt']['priority_time_hour_id']);
						}

						if (isset($this->session->data['econt']['restday']) && $this->session->data['econt']['restday'] && $this->config->get('shipping_econt_restday') && date('w') == 5) {
							$rowNow['shipment']['send_date'] = date('Y-m-d');
							$rowNow['shipment']['delivery_day'] = 'half_day';
						}
					}

					$tariff_sub_code = $this->config->get('shipping_econt_shipping_from') . '_' . strtoupper($method);
					if ($tariff_sub_code == 'OFFICE_OFFICE') {
						$tariff_code = 2;
					} elseif ($tariff_sub_code == 'DOOR_OFFICE' || $tariff_sub_code == 'OFFICE_DOOR') {
						$tariff_code = 3;
					} elseif ($tariff_sub_code == 'DOOR_DOOR') {
						$tariff_code = 4;
					}

					$rowNow['shipment']['tariff_code'] = $tariff_code;
					$rowNow['shipment']['tariff_sub_code'] = $tariff_sub_code;

					if ($weight >= 50) {
						$rowNow['shipment']['shipment_type'] = 'CARGO';
						$rowNow['shipment']['cargo_code'] = 81;
					} elseif ($weight <= 20 && $tariff_sub_code == 'OFFICE_OFFICE' && $less60cm && $this->config->get('shipping_econt_post_pack_enabled')) {
						$rowNow['shipment']['shipment_type'] = 'POST_PACK';
						$rowNow['shipment']['size_under_60cm'] = 1;
					} else {
						$rowNow['shipment']['shipment_type'] = 'PACK';
					}

					if ($isMachine) {
						if ($weight < 5) {
							$rowNow['shipment']['aps_box_size'] = 'Small';
						} else if ($weight < 10) {
							$rowNow['shipment']['aps_box_size'] = 'Medium';
						} else {
							$rowNow['shipment']['aps_box_size'] = 'Large';
						}
					}

					if ($this->config->get('shipping_econt_side') == 'RECEIVER') {
						$rowNow['payment']['method'] = 'CASH';
						$rowNow['payment']['key_word'] = '';
					}

					$calculate = true;
					if ($method == 'door') {
						if ($receiver_share_sum_door !== '') {
							$data['error']['fixed'] = false;
							$rowNow['payment']['receiver_share_sum'] = $this->currency->convert($receiver_share_sum_door, $this->config->get('shipping_econt_currency'), $this->config->get('config_currency'));

							$priceText = $this->currency->format($this->currency->convert($rowNow['payment']['receiver_share_sum'], $this->config->get('shipping_econt_currency'), $this->config->get('config_currency')), $this->config->get('config_currency'));
							$method_data['quote']['shipping_econt_door']['cost'] = $this->currency->convert($rowNow['payment']['receiver_share_sum'], $this->config->get('shipping_econt_currency'), $this->config->get('config_currency'));
							$method_data['quote']['shipping_econt_door']['text'] = $priceText;

							$rowNow['payment']['side'] = 'SENDER';
							$calculate = false;
						}
					} else {
						$toType = 'office';
						if ($isMachine) {
							$toType = 'aps';
						}
						if (${'receiver_share_sum_'.$toType} !== '') {
							$calculate = false;
							$data['error']['fixed'] = false;
							$rowNow['payment']['receiver_share_sum'] = $this->currency->convert(${'receiver_share_sum_'.$toType}, $this->config->get('shipping_econt_currency'), $this->config->get('config_currency'));

							$priceText = $this->currency->format($this->currency->convert(${'receiver_share_sum_'.$toType}, $this->config->get('shipping_econt_currency'), $this->config->get('config_currency')), $this->config->get('config_currency'));
							$method_data['quote']['shipping_econt_office']['cost'] = $this->currency->convert($rowNow['payment']['receiver_share_sum'], $this->config->get('shipping_econt_currency'), $this->config->get('config_currency'));
							$method_data['quote']['shipping_econt_office']['text'] = $priceText;

							$rowNow['payment']['side'] = 'SENDER';
						}
					}

					if (isset($data['error']['weight'])) {
						$calculate = false;
						$session['error']['fixed'] = true;
						$method_data['quote']['shipping_econt_'.$method]['cost'] = 0.00;
						$method_data['quote']['shipping_econt_'.$method]['text'] = $this->language->get('text_processing');
					}

					$dataTmp = $data;
					unset($dataTmp['loadings']['to_door'], $dataTmp['loadings']['to_office']);

					$data['loadings']['to_' . $method]['row'] = $rowNow;
					$dataTmp['loadings']['to_' . $method]['row'] = $rowNow;
					if ($calculate) {
						$results = $this->parcelImport($dataTmp);
						if ($results && !empty($results->result->e)) {
							$e = $results->result->e;
							if (!empty($e->error) && isset($this->session->data['econt']['shipping_to']) && strtolower($this->session->data['econt']['shipping_to']) == $method) {
								$method_data['shipping_econt_error'] = (string)$e->error;
								$method_data['quote']['shipping_econt_'.$method]['text'] = '';
							} elseif (isset($e->loading_price->total)) {
								if (isset($data['error']['weight'])) {
									$session['error']['fixed'] = true;
									$method_data['quote'][$method_code]['cost'] = 0.00;
									$method_data['quote'][$method_code]['text'] = $this->language->get('text_processing');
								} else {
									$priceText = $this->currency->format($this->currency->convert((float)$e->loading_price->total, $this->config->get('shipping_econt_currency'), $this->config->get('config_currency')), $this->config->get('config_currency'));
									$method_data['quote']['shipping_econt_'.$method]['cost'] = $this->currency->convert((float)$e->loading_price->total, $this->config->get('shipping_econt_currency'), $this->config->get('config_currency'));
									$method_data['quote']['shipping_econt_'.$method]['text'] = $priceText;
								}
							}
						} else {
							$method_data['shipping_econt_error'] = $this->language->get('error_connect');
							if (isset($method_data['quote']['shipping_econt_office'])) {
								$method_data['quote']['shipping_econt_office']['text'] = '';
							}
							if (isset($method_data['quote']['shipping_econt_door'])) {
								$method_data['quote']['shipping_econt_door']['text'] = '';
							}
						}
					}
				}
			} else {
				$method_data['shipping_econt_error'] = $this->language->get('error_calculate');
				if (isset($method_data['quote']['shipping_econt_office'])) {
					$method_data['quote']['shipping_econt_office']['text'] = '';
				}
				if (isset($method_data['quote']['shipping_econt_door'])) {
					$method_data['quote']['shipping_econt_door']['text'] = '';
				}
			}

			if (!isset($method_data['shipping_econt_error'])) {
				$this->session->data['econt']['order_id'] = $this->addOrder($data);
			}
		}

		return $method_data;
	}

	private function sortByPriority($a, $b) {
        return $a['priority'] - $b['priority'];
    }

	protected function prepareXML($data) {
		$xml = '';

		foreach ($data as $key => $value) {
			if ($key && $key == 'error') {
				continue;
			}

			if ($key && ($key == 'p' || $key == 'cd')) {
				$xml .= '<' . $key . ' type="' . htmlspecialchars($value['type']) . '">' . htmlspecialchars($value['value']) . '</' . $key . '>' . "\r\n";
			} else {
				if (!is_numeric($key) && $key != 'to_door' && $key != 'to_office' && $key != 'to_aps') {
					$xml .= '<' . $key . '>';
				}

				if (is_array($value)) {
					$xml .= "\r\n" . $this->prepareXML($value);
				} else {
					$xml .= htmlspecialchars($value);
				}

				if (!is_numeric($key) && $key != 'to_door' && $key != 'to_office' && $key != 'to_aps') {
					$xml .= '</' . $key . '>' . "\r\n";
				}
			}
		}

		return $xml;
	}

	private function parcelImport($data) {
		if (!$this->config->get('shipping_econt_test')) {
			$url = 'http://www.econt.com/e-econt/xml_parcel_import2.php';
		} else {
			$url = 'http://demo.econt.com/e-econt/xml_parcel_import2.php';
		}

		foreach ($data['loadings'] as $key => $row) {
			$data['loadings'][$key]['row']['mediator'] = 'extensa';
			$data['loadings'][$key]['row']['client_software'] = 'ExtensaOpenCart3x';
		}

		$request = '<?xml version="1.0" ?>';
		$request .= '<parcels>';
		$request .= $this->prepareXML($data);
		$request .= '</parcels>';

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('xml' => $request));

		$response = curl_exec($ch);

		curl_close($ch);

		libxml_use_internal_errors(TRUE);
		return simplexml_load_string($response);
	}

	public function addCustomer($data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "econt_customer WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "econt_customer SET customer_id = '" . (int)$this->customer->getId() . "', shipping_to = '" . $this->db->escape($data['shipping_to']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city = '" . $this->db->escape($data['city']) . "', quarter = '" . $this->db->escape($data['quarter']) . "', street = '" . $this->db->escape($data['street']) . "', street_num = '" . $this->db->escape($data['street_num']) . "', other = '" . $this->db->escape($data['other']) . "', city_id = '" . (int)$data['city_id'] . "', office_id = '" . (int)$data['office_id'] . "'");
	}

	public function getCustomer() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "econt_customer WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->row;
	}

	public function getOrder($econt_order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "econt_order WHERE econt_order_id = '" . (int)$econt_order_id . "'");

		return $query->row;
	}

	public function addOrder($data, $order_id = 0) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "econt_order SET order_id = '" . (int)$order_id . "', data = '" . $this->db->escape(serialize($data)) . "', requested_courier = '0'");

		$econt_order_id = $this->db->getLastId();

		return $econt_order_id;
	}

	public function editOrder($econt_order_id, $order_id, $data = array()) {
		$sql = "UPDATE " . DB_PREFIX . "econt_order SET order_id = '" . (int)$order_id . "'";

		if ($data) {
			$sql .= ", data = '" . $this->db->escape(serialize($data)) . "'";
		}

		$sql .= " WHERE econt_order_id = '" . (int)$econt_order_id . "'";

		$this->db->query($sql);
	}

	public function getCitiesByName($name, $limit = 10) {
		if (strtolower($this->config->get('config_language')) == 'bg') {
			$suffix = '';
		} else {
			$suffix = '_en';
		}

		$sql = "SELECT *, c.name" . $suffix . " AS name FROM " . DB_PREFIX . "econt_city c WHERE";

		if ($name) {
			$sql .= " (LCASE(c.name) LIKE '%" . $this->db->escape(utf8_strtolower($name)) . "%' OR LCASE(c.name_en) LIKE '%" . $this->db->escape(utf8_strtolower($name)) . "%')";
		}

		$sql .= (($name) ? " AND" : '') . " c.country_id = '" . $this->getCountry() . "'";

		$sql .= " ORDER BY c.name" . $suffix;

		$sql .= " LIMIT " . (int)$limit;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getQuartersByName($name, $city_id, $limit = 10) {
		if (strtolower($this->config->get('config_language')) == 'bg') {
			$suffix = '';
		} else {
			$suffix = '_en';
		}

		$sql = "SELECT *, q.name" . $suffix . " AS name FROM " . DB_PREFIX . "econt_quarter q WHERE 1";

		if ($name) {
			$sql .= " AND (LCASE(q.name) LIKE '%" . $this->db->escape(utf8_strtolower($name)) . "%' OR LCASE(q.name_en) LIKE '%" . $this->db->escape(utf8_strtolower($name)) . "%')";
		}

		if ($city_id) {
			$sql .= " AND q.city_id = '" . (int)$city_id . "'";
		}

		$sql .= " ORDER BY q.name" . $suffix;

		$sql .= " LIMIT " . (int)$limit;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getStreetsByName($name, $city_id, $limit = 10) {
		if (strtolower($this->config->get('config_language')) == 'bg') {
			$suffix = '';
		} else {
			$suffix = '_en';
		}

		$sql = "SELECT *, s.name" . $suffix . " AS name FROM " . DB_PREFIX . "econt_street s WHERE 1";

		if ($name) {
			$sql .= " AND (LCASE(s.name) LIKE '%" . $this->db->escape(utf8_strtolower($name)) . "%' OR LCASE(s.name_en) LIKE '%" . $this->db->escape(utf8_strtolower($name)) . "%')";
		}

		if ($city_id) {
			$sql .= " AND s.city_id = '" . (int)$city_id . "'";
		}

		$sql .= " ORDER BY s.name" . $suffix;

		$sql .= " LIMIT " . (int)$limit;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getCountries() {
		if (strtolower($this->config->get('config_language')) == 'bg') {
			$suffix = '';
		} else {
			$suffix = '_en';
		}

		$sql = "SELECT `country_id`, `name`, `name_en`, `name" . $suffix . "` AS `nameC` FROM `" . DB_PREFIX . "econt_country`";
		if (!$this->config->get('shipping_econt_international')) {
			$sql .= " WHERE `country_id` = " . self::BULGARIA_ECONT_CODE;
		}
		$sql .= " ORDER BY `name" . $suffix . "` ASC";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getCountry() {
		if (isset($this->session->data['econt']['econt_country_id']) && is_numeric($this->session->data['econt']['econt_country_id'])) {
			return (int)$this->session->data['econt']['econt_country_id'];
		}

		$shippingCountry = $this->session->data['shipping_address']['country'];
		foreach ($this->getCountries() as $country) {
			if ($shippingCountry == $country['name'] || $shippingCountry == $country['name_en']) {
				return $country['country_id'];
			}
		}
	}

	public function getCitiesWithOffices($delivery_type = '') {
		if (strtolower($this->config->get('config_language')) == 'bg') {
			$suffix = '';
		} else {
			$suffix = '_en';
		}

		$sql = "SELECT c.city_id, c.name" . $suffix . " AS name FROM " . DB_PREFIX . "econt_city c INNER JOIN " . DB_PREFIX . "econt_office o ON (c.city_id = o.city_id) ";

		if ($delivery_type) {
			$sql .= " INNER JOIN " . DB_PREFIX . "econt_city_office eco ON o.office_code = eco.office_code AND o.city_id = eco.city_id AND eco.delivery_type = '" . $delivery_type . "' ";
		}

		$sql .= " WHERE c.country_id = '" . $this->getCountry() . "'";

		$sql .= " GROUP BY c.city_id ORDER BY c.name" . $suffix;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getOfficesByCityId($city_id, $delivery_type = '') {
		if (strtolower($this->config->get('config_language')) == 'bg') {
			$suffix = '';
		} else {
			$suffix = '_en';
		}

		$sql = "SELECT *, o.name" . $suffix . " AS name, o.address" . $suffix . " AS address FROM " . DB_PREFIX . "econt_office o ";

		if ($delivery_type) {
			$sql .= " INNER JOIN " . DB_PREFIX . "econt_city_office eco ON o.office_code = eco.office_code AND o.city_id = eco.city_id AND eco.delivery_type = '" . $delivery_type . "' ";
		}

		$sql .= " WHERE o.city_id = '" . (int)$city_id . "' GROUP BY o.office_id ORDER BY o.name" . $suffix;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getOffice($office_id) {
		if (strtolower($this->config->get('config_language')) == 'bg') {
			$suffix = '';
		} else {
			$suffix = '_en';
		}

		$query = $this->db->query("SELECT *, o.name" . $suffix . " AS name, o.address" . $suffix . " AS address FROM " . DB_PREFIX . "econt_office o WHERE o.office_id = '" . (int)$office_id . "'");

		return $query->row;
	}

	public function getOfficeByOfficeCode($office_code) {
		if (strtolower($this->config->get('config_language')) == 'bg') {
			$suffix = '';
		} else {
			$suffix = '_en';
		}

		$sql = "SELECT o.*, o.name" . $suffix . " AS name, o.address" . $suffix . " AS address, c.name" . $suffix . " as city_name FROM " . DB_PREFIX . "econt_office o INNER JOIN " . DB_PREFIX . "econt_city c ON o.city_id = c.city_id WHERE o.office_code = '" . (int)$office_code . "' ";

		$query = $this->db->query($sql);
		if ($query->num_rows == 1) {
			return $query->row;
		} else {
			return false;
		}
	}

	public function getCityByCityId($city_id) {
		if (strtolower($this->config->get('config_language')) == 'bg') {
			$suffix = '';
		} else {
			$suffix = '_en';
		}

		$sql = "SELECT c.city_id, c.post_code, c.name" . $suffix . " AS name FROM " . DB_PREFIX . "econt_city c WHERE city_id = '" . (int)$city_id . "'";

		$query = $this->db->query($sql);
		if ($query->num_rows == 1) {
			return $query->row;
		} else {
			return false;
		}
	}

	public function getCityIdByPostCode($postCode)
	{
		$sql = "SELECT city_id FROM " . DB_PREFIX . "econt_city WHERE post_code = '" . (int)$postCode . "'";

		$query = $this->db->query($sql);
		if ($query->num_rows) {
			return $query->row['city_id'];
		} else {
			return 0;
		}
	}

	public function validateAddress($data) {
		$sql = "SELECT COUNT(c.city_id) AS total FROM " . DB_PREFIX . "econt_city c LEFT JOIN " . DB_PREFIX . "econt_quarter q ON (c.city_id = q.city_id) LEFT JOIN " . DB_PREFIX . "econt_street s ON (c.city_id = s.city_id) WHERE TRIM(c.post_code) = '". $this->db->escape(trim($data['postcode'])) . "' AND (LCASE(TRIM(c.name)) = '" . $this->db->escape(utf8_strtolower(trim($data['city']))) . "' OR LCASE(TRIM(c.name_en)) = '" . $this->db->escape(utf8_strtolower(trim($data['city']))) . "')";

		if ($data['quarter']) {
			$sql .= " AND (LCASE(TRIM(q.name)) = '" . $this->db->escape(utf8_strtolower(trim($data['quarter']))) . "' OR LCASE(TRIM(q.name_en)) = '" . $this->db->escape(utf8_strtolower(trim($data['quarter']))) . "')";
		}

		if ($data['street']) {
			$sql .= " AND (LCASE(TRIM(s.name)) = '" . $this->db->escape(utf8_strtolower(trim($data['street']))) . "' OR LCASE(TRIM(s.name_en)) = '" . $this->db->escape(utf8_strtolower(trim($data['street']))) . "')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
?>
