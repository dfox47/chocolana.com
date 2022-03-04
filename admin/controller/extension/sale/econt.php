<?php
class ControllerExtensionSaleEcont extends Controller {
	private $error = array();
	private $delivery_type = 'to_office';
	const MAX_COURIER_REQUEST_HOUR = 18;

	public function index() {
		$this->load->language('extension/sale/econt');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/sale/econt');

		$url = '';

		$filters = array(
			'filter_order_id',
			'filter_name',
			'filter_order_status_id',
			'filter_date_added',
			'filter_total',
			'page',
			'sort',
			'order'
		);

		foreach($filters as $filter) {
			if (isset($this->request->get[$filter])) {
				$url .= '&' . $filter . '=' . $this->request->get[$filter];
			}
		}

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		$loading_info = $this->model_extension_sale_econt->getLoading($order_id);

		if ($loading_info) {
			if ($loading_info['cd_send_sum'] && (strtotime($loading_info['cd_send_time']) > 0)) {
				$loading_info['trackings'] = $this->model_extension_sale_econt->getLoadingTrackings($loading_info['econt_loading_id']);

				$loading_info['next_parcels'] = $this->model_extension_sale_econt->getLoadingNextParcels($loading_info['loading_num']);

				foreach ($loading_info['next_parcels'] as $key => $next_parcel) {
					$loading_info['next_parcels'][$key]['trackings'] = $this->model_extension_sale_econt->getLoadingTrackings($next_parcel['econt_loading_id']);
				}
			} else {
				$data = array(
					'type' => 'shipments',
					'xml'  => "<shipments full_tracking='ON'><num>" . $loading_info['loading_num'] . '</num></shipments>'
				);

				$results = $this->serviceTool($data);

				$loading_info['trackings'] = array();
				$loading_info['next_parcels'] = array();

				if ($results) {
					if (isset($results->shipments->e->error)) {
						$this->error['warning'] = (string)$results->shipments->e->error;
					} elseif (isset($results->error)) {
						$this->error['warning'] = (string)$results->error->message;
					} elseif (isset($results->shipments->e)) {
						$additionalParameters = '';
						$formatPrint = $this->config->get('shipping_econt_format_print');
						if ($formatPrint == '100x150') {
							$additionalParameters = 'print_10x15=1';
						} elseif ($formatPrint == '90x100') {
							$additionalParameters = 'print_10x7=1';
						}

						$loading_info['is_imported'] = $results->shipments->e->is_imported;
						$loading_info['storage'] = $results->shipments->e->storage;
						$loading_info['receiver_person'] = $results->shipments->e->receiver_person;
						$loading_info['receiver_person_phone'] = $results->shipments->e->receiver_person_phone;
						$loading_info['receiver_courier'] = $results->shipments->e->receiver_courier;
						$loading_info['receiver_courier_phone'] = $results->shipments->e->receiver_courier_phone;
						$loading_info['receiver_time'] = $results->shipments->e->receiver_time;
						$loading_info['cd_get_sum'] = $results->shipments->e->CD_get_sum;
						$loading_info['cd_get_time'] = $results->shipments->e->CD_get_time;
						$loading_info['cd_send_sum'] = $results->shipments->e->CD_send_sum;
						$loading_info['cd_send_time'] = $results->shipments->e->CD_send_time;
						$loading_info['total_sum'] = $results->shipments->e->total_sum;
						$loading_info['currency'] = $results->shipments->e->currency;
						$loading_info['sender_ammount_due'] = $results->shipments->e->sender_ammount_due;
						$loading_info['receiver_ammount_due'] = $results->shipments->e->receiver_ammount_due;
						$loading_info['other_ammount_due'] = $results->shipments->e->other_ammount_due;
						$loading_info['delivery_attempt_count'] = $results->shipments->e->delivery_attempt_count;
						$loading_info['blank_yes'] = $results->shipments->e->blank_yes . $additionalParameters;
						$loading_info['blank_no'] = $results->shipments->e->blank_no . $additionalParameters;
						$loading_info['pdf_url'] = $loading_info['pdf_url'] . $additionalParameters;

						if (isset($results->shipments->e->tracking)) {
							foreach ($results->shipments->e->tracking->row as $tracking) {
								$loading_info['trackings'][] = array(
									'time'       => $tracking->time,
									'is_receipt' => $tracking->is_receipt,
									'event'      => $tracking->event,
									'name'       => $tracking->name,
									'name_en'    => $tracking->name_en
								);
							}
						}

						if (isset($results->shipments->e->next_parcels)) {
							foreach ($results->shipments->e->next_parcels->e as $next_parcel) {
								$data_next_parcel = array(
									'type' => 'shipments',
									'xml'  => "<shipments full_tracking='ON'><num>" . $next_parcel->num . '</num></shipments>'
								);

								$results_next_parcel = $this->serviceTool($data_next_parcel);

								if ($results_next_parcel) {
									if (isset($results_next_parcel->shipments->e->error)) {
										$this->error['warning'] = (string)$results_next_parcel->shipments->e->error;
									} elseif (isset($results_next_parcel->error)) {
										$this->error['warning'] = (string)$results_next_parcel->error->message;
									} elseif (isset($results_next_parcel->shipments->e)) {
										$trackings_next_parcel = array();

										if (isset($results_next_parcel->shipments->e->tracking)) {
											foreach ($results_next_parcel->shipments->e->tracking->row as $tracking) {
												$trackings_next_parcel[] = array(
													'time'       => $tracking->time,
													'is_receipt' => $tracking->is_receipt,
													'event'      => $tracking->event,
													'name'       => $tracking->name,
													'name_en'    => $tracking->name_en
												);
											}
										}

										$loading_info['next_parcels'][] = array(
											'loading_num'            => $results_next_parcel->shipments->e->loading_num,
											'is_imported'            => $results_next_parcel->shipments->e->is_imported,
											'storage'                => $results_next_parcel->shipments->e->storage,
											'receiver_person'        => $results_next_parcel->shipments->e->receiver_person,
											'receiver_person_phone'  => $results_next_parcel->shipments->e->receiver_person_phone,
											'receiver_courier'       => $results_next_parcel->shipments->e->receiver_courier,
											'receiver_courier_phone' => $results_next_parcel->shipments->e->receiver_courier_phone,
											'receiver_time'          => $results_next_parcel->shipments->e->receiver_time,
											'cd_get_sum'             => $results_next_parcel->shipments->e->CD_get_sum,
											'cd_get_time'            => $results_next_parcel->shipments->e->CD_get_time,
											'cd_send_sum'            => $results_next_parcel->shipments->e->CD_send_sum,
											'cd_send_time'           => $results_next_parcel->shipments->e->CD_send_time,
											'total_sum'              => $results_next_parcel->shipments->e->total_sum,
											'currency'               => $results_next_parcel->shipments->e->currency,
											'sender_ammount_due'     => $results_next_parcel->shipments->e->sender_ammount_due,
											'receiver_ammount_due'   => $results_next_parcel->shipments->e->receiver_ammount_due,
											'other_ammount_due'      => $results_next_parcel->shipments->e->other_ammount_due,
											'delivery_attempt_count' => $results_next_parcel->shipments->e->delivery_attempt_count,
											'blank_yes'              => $results_next_parcel->shipments->e->blank_yes . $additionalParameters,
											'blank_no'               => $results_next_parcel->shipments->e->blank_no . $additionalParameters,
											'pdf_url'                => $next_parcel->pdf_url . $additionalParameters,
											'reason'                 => $next_parcel->reason,
											'trackings'              => $trackings_next_parcel
										);
									}
								} else {
									$this->error['warning'] = $this->language->get('error_connect');
								}
							}
						}

						if (!$this->error) {
							$this->model_extension_sale_econt->updateLoading($loading_info);
						}
					}
				} else {
					$this->error['warning'] = $this->language->get('error_connect');
				}
			}

			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
			} else {
				$data['error_warning'] = '';
			}

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
				'separator' => false
			);

			$data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_orders'),
				'href'      => $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], 'SSL'),
				'separator' => ' :: '
			);

			$data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('extension/sale/econt', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $this->request->get['order_id'] . $url, 'SSL'),
				'separator' => ' :: '
			);

			$data['request_courier'] = $this->url->link('extension/sale/econt/request_courier', 'user_token=' . $this->session->data['user_token'], 'SSL');
			$data['cancel_loading'] = $this->url->link('extension/sale/econt/cancelLoading', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $this->request->get['order_id'] . $url, 'SSL');
			$data['cancel'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

			$loading_info['receiver_time'] = (strtotime($loading_info['receiver_time']) > 0 ? date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), strtotime($loading_info['receiver_time'])) : '');
			$loading_info['cd_get_time'] = (strtotime($loading_info['cd_get_time']) > 0 ? date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), strtotime($loading_info['cd_get_time'])) : '');
			$loading_info['cd_send_time'] = (strtotime($loading_info['cd_send_time']) > 0 ? date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), strtotime($loading_info['cd_send_time'])) : '');

			foreach ($loading_info['trackings'] as $key => $tracking) {
				$loading_info['trackings'][$key] = array(
					'time'       => date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), strtotime($tracking['time'])),
					'is_receipt' => ((int)$tracking['is_receipt'] ? $this->language->get('text_yes') : $this->language->get('text_no')),
					'event'      => $this->language->get('text_' . $tracking['event']),
					'name'       => (strtolower($this->config->get('config_admin_language')) == 'bg' ? $tracking['name'] : $tracking['name_en'])
				);
			}

			foreach ($loading_info['next_parcels'] as $key => $next_parcel) {
				$loading_info['next_parcels'][$key]['receiver_time'] = (strtotime($next_parcel['receiver_time']) > 0 ? date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), strtotime($next_parcel['receiver_time'])) : '');
				$loading_info['next_parcels'][$key]['cd_get_time'] = (strtotime($next_parcel['cd_get_time']) > 0 ? date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), strtotime($next_parcel['cd_get_time'])) : '');
				$loading_info['next_parcels'][$key]['cd_send_time'] = (strtotime($next_parcel['cd_send_time']) > 0 ? date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), strtotime($next_parcel['cd_send_time'])) : '');

				foreach ($next_parcel['trackings'] as $key2 => $tracking) {
					$loading_info['next_parcels'][$key]['trackings'][$key2] = array(
						'time'       => date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), strtotime($tracking['time'])),
						'is_receipt' => ((int)$tracking['is_receipt'] ? $this->language->get('text_yes') : $this->language->get('text_no')),
						'event'      => $this->language->get('text_' . $tracking['event']),
						'name'       => (strtolower($this->config->get('config_admin_language')) == 'bg' ? $tracking['name'] : $tracking['name_en'])
					);
				}
			}

			$data['loading'] = $loading_info;

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$data['order_id'] = $order_id;

			$this->response->setOutput($this->load->view('extension/sale/econt_loading', $data));
		} else {
			if (isset($this->request->get['order_id'])) {
				$this->response->redirect($this->url->link('extension/sale/econt/generate', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $this->request->get['order_id'] . $url, 'SSL'));
			} else {
				$this->response->redirect($this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			}
		}
	}

	public function generate() {
		$this->language->load('extension/sale/econt');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
		$this->document->addStyle('view/javascript/jquery/magnific/magnific-popup.css');

		$this->load->model('extension/sale/econt');

		$url = '';

		$filters = array(
			'filter_order_id',
			'filter_name',
			'filter_order_status_id',
			'filter_date_added',
			'filter_total',
			'page',
			'sort',
			'order'
		);

		foreach($filters as $filter) {
			if (isset($this->request->get[$filter])) {
				$url .= '&' . $filter . '=' . $this->request->get[$filter];
			}
		}

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		$loading_info = $this->model_extension_sale_econt->getLoading($order_id);

		if ($loading_info) {
			$this->response->redirect($this->url->link('extension/sale/econt', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $this->request->get['order_id'] . $url, 'SSL'));
		}

		$econt_order_info = $this->model_extension_sale_econt->getOrder($order_id);

		if ($econt_order_info) {
			$this->load->model('sale/order');
			$this->load->model('extension/shipping/econt');

			$order_data = unserialize($econt_order_info['data']);
			$order_info = $this->model_sale_order->getOrder($order_id);

			if ($order_info['shipping_code'] == 'econt.shipping_econt_office') {
				$order_data_row = $order_data['loadings']['to_office']['row'];
				unset($order_data['loadings']['to_office'], $order_data['loadings']['to_door'], $order_data['loadings']['to_aps']);
				$order_data['loadings']['row'] = $order_data_row;
				$shipping_to = 'OFFICE';
			} elseif ($order_info['shipping_code'] == 'econt.shipping_econt_door') {
				$order_data_row = $order_data['loadings']['to_door']['row'];
				unset($order_data['loadings']['to_office'], $order_data['loadings']['to_door'], $order_data['loadings']['to_aps']);
				$order_data['loadings']['row'] = $order_data_row;
				$shipping_to = 'DOOR';
			} elseif ($order_info['shipping_code'] == 'econt.shipping_econt_aps') {
				$order_data_row = $order_data['loadings']['to_aps']['row'];
				unset($order_data['loadings']['to_office'], $order_data['loadings']['to_door'], $order_data['loadings']['to_aps']);
				$order_data['loadings']['row'] = $order_data_row;
				$shipping_to = 'APS';
			}

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateGenerate($order_data) && $this->generateLoading($order_data)) {
				$this->session->data['success'] = $this->language->get('text_success');
				$this->response->redirect($this->url->link('extension/sale/econt', 'order_id=' . $order_id . '&user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
			}

			$data['user_token'] = (isset($this->session->data['user_token']) ? $this->session->data['user_token'] : '');

			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
			} else {
				$data['error_warning'] = '';
			}

			if (isset($this->error['address'])) {
				$data['error_address'] = $this->error['address'];
			} else {
				$data['error_address'] = '';
			}

			if (isset($this->error['products_weight'])) {
				$data['error_products_weight'] = $this->error['products_weight'];
			} else {
				$data['error_products_weight'] = '';
			}

			if (isset($this->error['receiver_address'])) {
				$data['error_receiver_address'] = $this->error['receiver_address'];
			} else {
				$data['error_receiver_address'] = '';
			}

			if (isset($this->error['office'])) {
				$data['error_office'] = $this->error['office'];
			} else {
				$data['error_office'] = '';
			}

			if (isset($this->error['receiver_office'])) {
				$data['error_receiver_office'] = $this->error['receiver_office'];
			} else {
				$data['error_receiver_office'] = '';
			}

			if (isset($this->error['priority_time'])) {
				$data['error_priority_time'] = $this->error['priority_time'];
			} else {
				$data['error_priority_time'] = '';
			}

			if (isset($this->error['instruction'])) {
				$data['error_instruction'] = $this->error['instruction'];
			} else {
				$data['error_instruction'] = '';
			}

			if (isset($this->error['inventory'])) {
				$data['error_inventory'] = $this->error['inventory'];
			} else {
				$data['error_inventory'] = '';
			}

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
				'separator' => false
			);

			$data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_orders'),
				'href'      => $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'),
				'separator' => ' :: '
			);

			$data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('extension/sale/econt/generate', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $this->request->get['order_id'] . $url, 'SSL'),
				'separator' => ' :: '
			);

			$data['action'] = $this->url->link('extension/sale/econt/generate', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $this->request->get['order_id'] . $url, 'SSL');
			$data['cancel'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

			$data['shipping_econt_username'] = $this->config->get('shipping_econt_username');
			$data['shipping_econt_password'] = $this->config->get('shipping_econt_password');
			$data['shipping_econt_test'] = $this->config->get('shipping_econt_test');

			if (isset($this->request->post['address_id'])) {
				$data['address_id'] = $this->request->post['address_id'];
			} else {
				$data['address_id'] = $this->config->get('shipping_econt_address_id');
			}

			$data['office_locator'] = 'https://www.bgmaps.com/templates/econt?office_type=to_office_courier&shop_url=' . HTTPS_SERVER;
			$data['office_locator_domain'] = 'https://www.bgmaps.com';

			$data['shipping_from'] = $this->config->get('shipping_econt_shipping_from');

			$receiver = array();

			if (isset($this->request->post['post_code'])) {
				$receiver['post_code'] = $this->request->post['post_code'];
			} else {
				$receiver['post_code'] = $order_data['loadings']['row']['receiver']['post_code'];
			}

			if (isset($this->request->post['city'])) {
				$receiver['city'] = $this->request->post['city'];
			} else {
				$receiver['city'] = $order_data['loadings']['row']['receiver']['city'];
			}

			if (isset($this->request->post['quarter'])) {
				$receiver['quarter'] = $this->request->post['quarter'];
			} elseif (isset($order_data['loadings']['row']['receiver']['quarter'])) {
				$receiver['quarter'] = $order_data['loadings']['row']['receiver']['quarter'];
			}

			if (isset($this->request->post['street'])) {
				$receiver['street'] = $this->request->post['street'];
			} elseif (isset($order_data['loadings']['row']['receiver']['street'])) {
				$receiver['street'] = $order_data['loadings']['row']['receiver']['street'];
			}

			if (isset($this->request->post['street_num'])) {
				$receiver['street_num'] = $this->request->post['street_num'];
			} elseif (isset($order_data['loadings']['row']['receiver']['street_num'])) {
				$receiver['street_num'] = $order_data['loadings']['row']['receiver']['street_num'];
			}

			if (isset($this->request->post['other'])) {
				$receiver['other'] = $this->request->post['other'];
			} elseif (isset($order_data['loadings']['row']['receiver']['street_other'])) {
				$receiver['other'] = $order_data['loadings']['row']['receiver']['street_other'];
			}

			if (isset($this->request->post['city_id'])) {
				$receiver['city_id'] = $this->request->post['city_id'];
			} else {
				$city = $this->model_extension_shipping_econt->getCityByNameAndPostcode($receiver['city'], $receiver['post_code']);

				if ($city) {
					$receiver['city_id'] = $city['city_id'];
				}
			}

			if (isset($this->request->post['office_id'])) {
				$receiver['office_code'] = $this->request->post['office_code'];
				$receiver['office_id'] = $this->request->post['office_id'];
				$receiver['office_city_id'] = $this->request->post['office_city_id'];
			} else {
				if (!empty($order_data['loadings']['row']['receiver']['office_code'])) {
					$econt_office = $this->model_extension_shipping_econt->getOfficeByOfficeCode($order_data['loadings']['row']['receiver']['office_code']);
					if ($econt_office) {
						if ($econt_office['is_machine']) {
							$shipping_to = 'APS';
						}
						$receiver['office_code'] = $econt_office['office_code'];
						$receiver['office_id'] = $econt_office['office_id'];
						$receiver['office_city_id'] = $econt_office['city_id'];
					} else {
						$receiver['office_code'] = '';
						$receiver['office_id'] = 0;
						$receiver['office_city_id'] = 0;
					}
				} else {
					$receiver['office_code'] = '';
					$receiver['office_id'] = 0;
					$receiver['office_city_id'] = 0;
				}
			}

			if (isset($this->request->post['office_aps_id'])) {
				$receiver['office_aps_code'] = $this->request->post['office_aps_code'];
				$receiver['office_aps_id'] = $this->request->post['office_aps_id'];
				$receiver['office_city_aps_id'] = $this->request->post['office_city_aps_id'];
			} else {
				if (!empty($order_data['loadings']['row']['receiver']['office_code'])) {
					$econt_office = $this->model_extension_shipping_econt->getOfficeByOfficeCode($order_data['loadings']['row']['receiver']['office_code']);
					if ($econt_office) {
						$receiver['office_aps_code'] = $econt_office['office_code'];
						$receiver['office_aps_id'] = $econt_office['office_id'];
						$receiver['office_city_aps_id'] = $econt_office['city_id'];
					} else {
						$receiver['office_aps_code'] = '';
						$receiver['office_aps_id'] = 0;
						$receiver['office_city_aps_id'] = 0;
					}
				} else {
					$receiver['office_aps_code'] = '';
					$receiver['office_aps_id'] = 0;
					$receiver['office_city_aps_id'] = 0;
				}
			}

			if (isset($this->request->post['shipping_to'])) {
				$receiver['shipping_to'] = $this->request->post['shipping_to'];
			} else {
				$receiver['shipping_to'] = $shipping_to; //$shipping_to[1];
			}

			$receiver['to_door'] = true;
			$receiver['to_office'] = true;

			$receiver['to_aps'] = true;

			$receiver['cities'] = $this->model_extension_shipping_econt->getCitiesWithOffices($this->delivery_type, 0);

			$receiver['offices'] = $this->model_extension_shipping_econt->getOfficesByCityId($receiver['office_city_id'], $this->delivery_type, 0);

			$receiver['cities_aps'] = $this->model_extension_shipping_econt->getCitiesWithOffices($this->delivery_type, 1);

			$receiver['offices_aps'] = $this->model_extension_shipping_econt->getOfficesByCityId($receiver['office_city_aps_id'], $this->delivery_type, 1);

			$data['addresses'] = array();

			$addresses = $this->config->get('shipping_econt_address_list');

			foreach ($addresses as $address_id => $address) {

				$name = $address['city_post_code'] . ', ' . $address['city'];

				if ($address['quarter']) {
					$name .= ', ' . $address['quarter'];
				}

				if ($address['street']) {
					$name .= ', ' . $address['street'];

					if ($address['street_num']) {
						$name .= ' ' . $address['street_num'];
					}
				}

				if ($address['other']) {
					$name .= ', ' . $address['other'];
				}

				$data['addresses'][] = array(
					'address_id' => $address_id,
					'name'       => $name
				);
			}

			reset($addresses);
			$address = current($addresses);
			$receiver['sender_post_code'] = $address['city_post_code'];

			$data['receiver_address'] = $receiver;

			$this->load->model('catalog/product');

			$data['products_weight'] = array();

			$this->load->model('catalog/product');
			$order_products = $this->model_sale_order->getOrderProducts($order_id);
			foreach ($order_products as $product) {

				$product = $this->model_catalog_product->getProduct($product['product_id']);

				if ($product) {
						$product_weight = (float)$product['weight'];

						if (empty($product_weight)) {
							$data['products_weight'][] = array(
								'text' => $product['name'],
								'href' => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $product['product_id'], 'SSL')
							);
						}
				}
			}

			if (isset($this->request->post['invoice_before_cd']) && $this->request->post['invoice_before_cd']) {
				$data['invoice_before_cd'] = $this->request->post['invoice_before_cd'];
			}

			if (isset($this->request->post['oc'])) {
				$data['oc'] = $this->request->post['oc'];
			} else {
				$data['oc'] = $this->config->get('shipping_econt_oc');
			}

			if (isset($this->request->post['total_for_oc'])) {
				$data['total_for_oc'] = $this->request->post['total_for_oc'];
			} else {
				$data['total_for_oc'] = $this->config->get('shipping_econt_total_for_oc');
			}

			$data['shipping_econt_dc'] = 0;
			if (isset($this->request->post['dc']) && $this->request->post['dc'] == 1) {
				$data['shipping_econt_dc'] = 1;
			} elseif (isset($order_data['loadings']['row']['services']['dc']) && $order_data['loadings']['row']['services']['dc']) {
				$data['shipping_econt_dc'] = 1;
			}

			if (isset($this->request->post['dc']) && $this->request->post['dc'] == 2) {
				$data['shipping_econt_dc'] = 2;
			} elseif (isset($order_data['loadings']['row']['services']['dc_cp']) && $order_data['loadings']['row']['services']['dc_cp']) {
				$data['shipping_econt_dc'] = 2;
			}

			if (isset($this->request->post['pay_after_accept'])) {
				$data['pay_after_accept'] = $this->request->post['pay_after_accept'];
			} elseif (isset($order_data['loadings']['row']['shipment']['pay_after_accept'])) {
				$data['pay_after_accept'] = $order_data['loadings']['row']['shipment']['pay_after_accept'];
			}

			if (isset($this->request->post['pay_after_test'])) {
				$data['pay_after_test'] = $this->request->post['pay_after_test'];
			} elseif (isset($order_data['loadings']['row']['shipment']['pay_after_test'])) {
				$data['pay_after_test'] = $order_data['loadings']['row']['shipment']['pay_after_test'];
			}

			if (isset($this->request->post['pay_choose'])) {
				$data['pay_choose'] = $this->request->post['pay_choose'];
			} elseif (isset($order_data['loadings']['row']['shipment']['pay_choose'])) {
				$data['pay_choose'] = $order_data['loadings']['row']['shipment']['pay_choose'];
			}

			if (isset($this->request->post['priority_time_cb'])) {
				$data['priority_time_cb'] = $this->request->post['priority_time_cb'];
			} elseif (isset($order_data['loadings']['row']['services']['p']) && $order_data['loadings']['row']['services']['p']['type'] && $order_data['loadings']['row']['services']['p']['value']) {
				$data['priority_time_cb'] = true;
			} else {
				$data['priority_time_cb'] = false;
			}

			if (isset($this->request->post['priority_time_type_id'])) {
				$data['priority_time_type_id'] = $this->request->post['priority_time_type_id'];
			} elseif (isset($order_data['loadings']['row']['services']['p']) && $order_data['loadings']['row']['services']['p']['type']) {
				$data['priority_time_type_id'] = $order_data['loadings']['row']['services']['p']['type'];
			} else {
				$data['priority_time_type_id'] = 'BEFORE';
			}

			if (isset($this->request->post['priority_time_hour_id'])) {
				$data['priority_time_hour_id'] = $this->request->post['priority_time_hour_id'];
			} elseif (isset($order_data['loadings']['row']['services']['p']) && $order_data['loadings']['row']['services']['p']['value']) {
				$data['priority_time_hour_id'] = $order_data['loadings']['row']['services']['p']['value'];
			} else {
				$data['priority_time_hour_id'] = '';
			}

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

			if (isset($this->request->post['pack_count'])) {
				$data['pack_count'] = $this->request->post['pack_count'];
			} else {
				$data['pack_count'] = 1; //$order_data['loadings']['row']['shipment']['pack_count'];
			}


			if (isset($this->request->post['partial_delivery'])) {
				$data['partial_delivery'] = $this->request->post['partial_delivery'];
			} else {
				$data['partial_delivery'] = $this->config->get('shipping_econt_partial_delivery');
			}

			if (isset($this->request->post['partial_delivery_instruction'])) {
				$data['partial_delivery_instruction'] = $this->request->post['partial_delivery_instruction'];
			} else {
				$data['partial_delivery_instruction'] = $this->config->get('shipping_econt_partial_delivery_instruction');
			}

			$data['partial_delivery_instructions'] = array(
				array('code' => 'ACCEPT', 'title' => $this->language->get('text_partial_delivery_accept')),
				array('code' => 'TEST', 'title' => $this->language->get('text_partial_delivery_test'))
			);

			if (isset($this->request->post['inventory'])) {
				$data['inventory'] = $this->request->post['inventory'];
			} else {
				$data['inventory'] = $this->config->get('shipping_econt_inventory');
			}

			if (isset($this->request->post['inventory_type'])) {
				$data['inventory_type'] = $this->request->post['inventory_type'];
			} else {
				$data['inventory_type'] = $this->config->get('shipping_econt_inventory_type');
			}

			$data['inventory_types'] = array(
				array('code' => 'DIGITAL', 'title' => $this->language->get('text_digital')),
				array('code' => 'LOADING', 'title' => $this->language->get('text_loading'))
			);

			if (isset($order_data['loadings']['row']['shipment']['delivery_day']) && $order_data['loadings']['row']['shipment']['delivery_day']) {
				$data['shipping_restday'] = true;
			} else {
				$data['shipping_restday'] = false;
			}

			$this->load->model('sale/order');

			$products = $this->model_sale_order->getOrderProducts($order_id);

			if (isset($this->request->post['products_count'])) {
				$data['products_count'] = $this->request->post['products_count'];
			} else {
				$data['products_count'] = count($products);
			}

			if (isset($this->request->post['products'])) {
				$data['products'] = $this->request->post['products'];
			} else {
				$data['products'] = array();

				$order_info = $this->model_sale_order->getOrder($order_id);

				foreach ($products as $product) {
					$product_info = $this->model_catalog_product->getProduct($product['product_id']);
					$price = $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value'], '');
					for ($i = 0; $i < $product['quantity']; $i++) {
						$data['products'][] = array(
							'product_id' => $product['product_id'],
							'name'       => $product['name'],
							'weight'     => $this->weight->convert($product_info['weight'], $product_info['weight_class_id'], $this->config->get('shipping_econt_weight_class_id')),
							'price'      => round($this->currency->convert($price, $order_info['currency_code'], $this->config->get('shipping_econt_currency')), 2)
						);
					}
				}
			}

			$data['instructions_types'] = array(
				array('code' => 'take', 'title' => $this->language->get('text_instructions_take')),
				array('code' => 'give', 'title' => $this->language->get('text_instructions_give')),
				array('code' => 'return', 'title' => $this->language->get('text_instructions_return')),
			);

			foreach ($data['instructions_types'] as $instructionType) {
				if (isset($this->request->post['shipping_econt_instructions_select'][$instructionType['code']])) {
					$data['shipping_econt_instructions_select'][$instructionType['code']] = $this->request->post['shipping_econt_instructions_select'][$instructionType['code']];
				} else {
					$instructionsConfig = $this->config->get('shipping_econt_instructions_select');
					$data['shipping_econt_instructions_select'][$instructionType['code']] = $instructionsConfig[$instructionType['code']];
				}
			}

			if (isset($this->request->post['shipping_econt_office_id'])) {
				$data['shipping_econt_office_id'] = $this->request->post['shipping_econt_office_id'];
			} else {
				$data['shipping_econt_office_id'] = $this->config->get('shipping_econt_office_id');
			}

			$data['cities'] = $this->model_extension_shipping_econt->getCitiesWithOffices('from_office');

			$office = $this->model_extension_shipping_econt->getOffice($data['shipping_econt_office_id']);

			if ($office) {
				$data['shipping_econt_office_city_id'] = $office['city_id'];
				$data['shipping_econt_office_code'] = $office['office_code'];
			} else {
				$data['shipping_econt_office_city_id'] = 0;
				$data['shipping_econt_office_code'] = '';
			}

			$data['offices'] = $this->model_extension_shipping_econt->getOfficesByCityId($data['shipping_econt_office_city_id'], 'from_office');

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

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('extension/sale/econt_generate', $data));
		} else {
			$this->language->load('error/not_found');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
				'separator' => false
			);

			$data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_orders'),
				'href'      => $this->url->link('error/not_found', 'user_token=' . $this->session->data['user_token'], 'SSL'),
				'separator' => ' :: '
			);

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	protected function validateGenerate($data) {
		if (!$this->user->hasPermission('modify', 'extension/sale/econt')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->request->post['shipping_to'] == 'DOOR' && $this->request->post['post_code'] && $this->request->post['city'] && ($this->request->post['quarter'] && $this->request->post['other'] || $this->request->post['street'] && $this->request->post['street_num'])) {
			if (!$this->model_extension_shipping_econt->validateAddress($this->request->post)) {
				$this->error['receiver_address'] = $this->language->get('error_receiver_address');
			}
		} elseif ($this->request->post['shipping_to'] == 'DOOR') {
			$this->error['receiver_address'] = $this->language->get('error_receiver_address');
		}

		if ($this->request->post['shipping_to'] == 'OFFICE') {
			if (!$this->request->post['office_id']) {
				$this->error['receiver_office'] = $this->language->get('error_receiver_office');
			}
		}

		if ($this->request->post['shipping_to'] == 'APS') {
			if (!$this->request->post['office_aps_id']) {
				$this->error['receiver_office'] = $this->language->get('error_receiver_office');
			}
		}

		if (!isset($this->request->post['address_id']) && !isset($this->request->post['shipping_econt_office_code'])) {
			$this->error['address'] = $this->language->get('error_address');
		}

		if ($this->request->post['shipping_to'] == 'APS' && $this->request->post['instruction']) {
			$this->error['instruction'] = $this->language->get('error_instruction');
		}

		if ($this->request->post['shipping_to'] == 'APS' && $this->request->post['inventory']) {
			$this->error['inventory'] = $this->language->get('error_inventory');
		}

		$this->load->model('sale/order');
		$this->load->model('catalog/product');

		$order_products = $this->model_sale_order->getOrderProducts($this->request->get['order_id']);
		foreach ($order_products as $product) {

			$product = $this->model_catalog_product->getProduct($product['product_id']);

			if ($product['shipping']) {
					$product_weight = (float)$product['weight'];

					if (empty($product_weight)) {
						$this->error['products_weight'] = $this->language->get('error_products_weight');

						break;
					}
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

	private function generateLoading($data) {
		$this->load->model('extension/shipping/econt');
		$this->load->model('sale/order');

		$order_id = $this->request->get['order_id'];
		$order = $this->model_sale_order->getOrder($order_id);

		$data['system']['validate'] = 0;
		$data['system']['only_calculate'] = 0;

		$receiver_name_person = $order['shipping_firstname'] . ' ' . $order['shipping_lastname'];
		if (!empty($order['shipping_company'])) {
			$company = $order['shipping_company'];
		} else {
			$company = $receiver_name_person;
		}

		$data['loadings']['row']['receiver']['name'] = $company;
		$data['loadings']['row']['receiver']['name_person'] = $receiver_name_person;
		$data['loadings']['row']['receiver']['receiver_email'] = $order['email'];
		$data['loadings']['row']['receiver']['phone_num'] = $order['telephone'];

		$isAps = false;
		if (isset($this->request->post['shipping_econt_office_code'])) {
			$sender_office = $this->model_extension_shipping_econt->getOfficeByOfficeCode($this->request->post['shipping_econt_office_code']);
			$isAps = $sender_office['is_machine'];
			if (isset($sender_office) && $sender_office) {
				$econt_city = $this->model_extension_shipping_econt->getCityByCityId($sender_office['city_id']);
				if (isset($econt_city) && $econt_city) {
					$data['loadings']['row']['sender']['office_code'] = $sender_office['office_code'];
					$data['loadings']['row']['sender']['city'] = $econt_city['name'];
					$data['loadings']['row']['sender']['post_code'] = $econt_city['post_code'];
					if ($isAps) {
						$data['loadings']['row']['services']['oc'] = '';
						$data['loadings']['row']['services']['oc_currency'] = '';
					}
				}
			}
		} else {
			$addresses = $this->config->get('shipping_econt_address_list');

			$address_id = $this->request->post['address_id'];
			if (isset($addresses[$address_id])) {
				$address = $addresses[$address_id];

				$data['loadings']['row']['sender']['city'] = $address['city'];
				$data['loadings']['row']['sender']['post_code'] = $address['city_post_code'];
				$data['loadings']['row']['sender']['quarter'] = $address['quarter'];
				$data['loadings']['row']['sender']['street'] = $address['street'];
				$data['loadings']['row']['sender']['street_num'] = $address['street_num'];
				$data['loadings']['row']['sender']['street_other'] = $address['other'];
			}
		}

		if ($this->request->post['shipping_to'] == 'OFFICE') {
			if (isset($this->request->post['office_city_id'])) {
				$econt_city = $this->model_extension_shipping_econt->getCityByCityId($this->request->post['office_city_id']);
				$data['loadings']['row']['receiver']['city'] = $econt_city['name'];
				$data['loadings']['row']['receiver']['post_code'] = $econt_city['post_code'];
			}

			$receiver_office_code = '';

			if (isset($this->request->post['office_id'])) {
				$receiver_office = $this->model_extension_shipping_econt->getOffice($this->request->post['office_id']);
				if ($receiver_office) {
					$receiver_office_code = $receiver_office['office_code'];
				}
			}

			$data['loadings']['row']['receiver']['office_code'] = $receiver_office_code;
			$data['loadings']['row']['receiver']['quarter'] = '';
			$data['loadings']['row']['receiver']['street'] = '';
			$data['loadings']['row']['receiver']['street_num'] = '';
			$data['loadings']['row']['receiver']['street_other'] = '';

		} elseif ($this->request->post['shipping_to'] == 'APS') {
			if (isset($this->request->post['office_city_aps_id'])) {
				$econt_city_aps = $this->model_extension_shipping_econt->getCityByCityId($this->request->post['office_city_aps_id']);
				$data['loadings']['row']['receiver']['city'] = $econt_city_aps['name'];
				$data['loadings']['row']['receiver']['post_code'] = $econt_city_aps['post_code'];
			}

			$receiver_office_code_aps = '';

			if (isset($this->request->post['office_aps_id'])) {
				$receiver_office_aps = $this->model_extension_shipping_econt->getOffice($this->request->post['office_aps_id']);
				if ($receiver_office_aps) {
					$receiver_office_code_aps = $receiver_office_aps['office_code'];
				}
			}

			$data['loadings']['row']['receiver']['office_code'] = $receiver_office_code_aps;
			$data['loadings']['row']['receiver']['quarter'] = '';
			$data['loadings']['row']['receiver']['street'] = '';
			$data['loadings']['row']['receiver']['street_num'] = '';
			$data['loadings']['row']['receiver']['street_other'] = '';

		} else {
			if (isset($this->request->post['city'])) {
				$data['loadings']['row']['receiver']['city'] = $this->request->post['city'];
			}

			if (isset($this->request->post['post_code'])) {
				$data['loadings']['row']['receiver']['post_code'] = $this->request->post['post_code'];
			}

			if (isset($this->request->post['quarter'])) {
				$data['loadings']['row']['receiver']['quarter'] = $this->request->post['quarter'];
			}

			if (isset($this->request->post['street'])) {
				$data['loadings']['row']['receiver']['street'] = $this->request->post['street'];
			}

			if (isset($this->request->post['street_num'])) {
				$data['loadings']['row']['receiver']['street_num'] = $this->request->post['street_num'];
			}

			if (isset($this->request->post['other'])) {
				$data['loadings']['row']['receiver']['street_other'] = $this->request->post['other'];
			}
		}

		$weight = 0;
		$width = $height = $length = 0;
		$description = array();
		$product_count = 0;
		$total = 0;

		$this->load->model('catalog/product');
		$order_products = $this->model_sale_order->getOrderProducts($order_id);
		$productQty = 0;
		foreach ($order_products as $product) {
			$description[] = $product['name'];
			$product_count += (int)$product['quantity'];

			$product_info = $this->model_catalog_product->getProduct($product['product_id']);

			if ($product_info) {
				$w = $this->length->convert($product_info['width'], $product_info['length_class_id'], 1) * $product['quantity'];
				$h = $this->length->convert($product_info['height'], $product_info['length_class_id'], 1) * $product['quantity'];
				$l = $this->length->convert($product_info['length'], $product_info['length_class_id'], 1) * $product['quantity'];

				if (!$w || !$h || !$l) {
					$width = $height = $length = -1;
				}

				if ($width > -1) {
					$width += $w;
					$height += $h;
					$length += $l;
				}

				$option_weight = 0;
				$product_options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);
				if ($product_options) {
					foreach ($product_options as $value) {
						$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$value['product_option_id'] . "' AND po.product_id = '" . (int)$product['product_id'] . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

						if ($option_query->num_rows) {
							if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
								$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value['product_option_value_id'] . "' AND pov.product_option_id = '" . (int)$value['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($option_value_query->num_rows) {
									if ($option_value_query->row['weight_prefix'] == '+') {
										$option_weight += $option_value_query->row['weight'];
									} elseif ($option_value_query->row['weight_prefix'] == '-') {
										$option_weight -= $option_value_query->row['weight'];
									}
								}
							} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
								foreach ($value as $product_option_value_id) {
									$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$value['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

									if ($option_value_query->num_rows) {
										if ($option_value_query->row['weight_prefix'] == '+') {
											$option_weight += $option_value_query->row['weight'];
										} elseif ($option_value_query->row['weight_prefix'] == '-') {
											$option_weight -= $option_value_query->row['weight'];
										}
									}
								}
							}

						}
					}
				}
				$weight += $this->weight->convert(($product_info['weight'] + $option_weight) * $product['quantity'], $product_info['weight_class_id'], $this->config->get('shipping_econt_weight_class_id'));
			}
		}

		if ($width === -1) {
			$width = $height = $length = 0;
		}

		$less60cm = false;
		if ($width && $height && $length && $width < 60 && $height < 60 && $length < 60) {
			$less60cm = true;
		}

		$order_totals = $this->model_sale_order->getOrderTotals($order_id);
		foreach ($order_totals as $order_total) {
			if ($order_total['code'] == 'shipping') {
				$order_totals_shipping = (float)$order_total['value'];
			}
			if ($order_total['code'] == 'total') {
				$order_totals_total = (float)$order_total['value'];
			}
		}
		$total = $order_totals_total - $order_totals_shipping;

		$data['loadings']['row']['shipment']['description'] = implode(', ', $description);

		$data['loadings']['row']['shipment']['weight'] = $weight;
		$data['loadings']['row']['shipment']['shipment_pack_dimensions_l'] = $length;
		$data['loadings']['row']['shipment']['shipment_pack_dimensions_w'] = $width;
		$data['loadings']['row']['shipment']['shipment_pack_dimensions_h'] = $height;
		$data['loadings']['row']['shipment']['size_under_60cm'] = 0;

		$total = round($this->currency->format($total, $this->config->get('shipping_econt_currency'), '', false), 2);

		$order_info = $this->model_sale_order->getOrder($order_id);

		if ($order_info['payment_code'] == 'econt_cod') {
			$cd_type = 'GET';
			$cd_value = $total;
			$cd_currency = $this->config->get('shipping_econt_currency');

			if (strpos($this->config->get('shipping_econt_cd'), 'cd') === 0) {
				$cd_agreement_num = substr($this->config->get('shipping_econt_cd'), 2);
			} else {
				$cd_agreement_num = '';
			}
		} else {
			$cd_type = '';
			$cd_value = '';
			$cd_currency = '';
			$cd_agreement_num = '';
		}

		$data['loadings']['row']['services']['cd'] = array('type' => $cd_type, 'value' => $cd_value);
		$data['loadings']['row']['services']['cd_currency'] = $cd_currency;
		$data['loadings']['row']['services']['cd_agreement_num'] = $cd_agreement_num;

		$data['loadings']['row']['payment']['side'] = $this->config->get('shipping_econt_side');
		$data['loadings']['row']['payment']['method'] = $this->config->get('shipping_econt_payment_method');

		$receiver_share_sum = '';
		$receiver_share_sum_door = '';
		$receiver_share_sum_office = '';
		$receiver_share_sum_aps = '';

		if ($this->config->get('shipping_econt_shipping_payment_enabled') && $data['loadings']['row']['payment']['side'] == 'RECEIVER') {
			$shipping_payments = $this->config->get('shipping_econt_shipping_payments');
			if ($shipping_payments && is_array($shipping_payments)) {
				usort($shipping_payments, array('ControllerExtensionSaleEcont', 'sortByPriority'));
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
							$shippingCondition = ($product_count < $shippingPayment['criteria_value']);
						} else {
							$shippingCondition = ($product_count > $shippingPayment['criteria_value']);
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
		} elseif ($data['loadings']['row']['payment']['side'] == 'SENDER') {
			$receiver_share_sum_door = $receiver_share_sum_office = $receiver_share_sum_aps = 0;
		}

		if ($this->request->post['shipping_to'] == 'OFFICE') {
			$receiver_share_sum = $receiver_share_sum_office;
		} elseif ($this->request->post['shipping_to'] == 'APS') {
			$receiver_share_sum = $receiver_share_sum_aps;
		} else {
			$receiver_share_sum = $receiver_share_sum_door;
		}

		if ($receiver_share_sum !== '') {
			$data['loadings']['row']['payment']['side'] = 'SENDER';
		}

		$data['loadings']['row']['payment']['receiver_share_sum'] = $receiver_share_sum;
		$data['loadings']['row']['payment']['share_percent'] = '';

		if ($order_info['payment_code'] != 'econt_cod' && (float)$order_totals_shipping > 0) {
			$data['loadings']['row']['payment']['side'] = 'SENDER';
			$data['loadings']['row']['payment']['receiver_share_sum'] = '';
			$receiver_share_sum = '';
		}

		if ($this->config->get('shipping_econt_side') == 'RECEIVER') {
			$data['loadings']['row']['payment']['method'] = 'CASH';
		}

		if ($data['loadings']['row']['payment']['method'] == 'CREDIT') {
			$key_word = $this->config->get('shipping_econt_key_word');
		} else {
			$key_word = '';
		}

		$data['loadings']['row']['payment']['key_word'] = $key_word;

		if ($this->config->get('shipping_econt_side') == 'RECEIVER') {
			$data['loadings']['row']['payment']['method'] = 'CASH';
		}

		$data['loadings']['row']['shipment']['invoice_before_pay_CD'] = (int)$this->request->post['invoice_before_cd'];

		if ($this->config->get('shipping_econt_shipping_from') != 'APS') {
			$tariff_sub_code_prefix = $this->config->get('shipping_econt_shipping_from');
		} else {
			$tariff_sub_code_prefix = 'OFFICE';
		}

		if ($this->request->post['shipping_to'] != 'APS') {
			$tariff_sub_code_suffix = $this->request->post['shipping_to'];
		} else {
			$tariff_sub_code_suffix = 'OFFICE';
		}

		$tariff_sub_code = $tariff_sub_code_prefix . '_' . $tariff_sub_code_suffix;

		$tariff_code = 0;

		if ($tariff_sub_code == 'OFFICE_OFFICE') {
			$tariff_code = 2;
		} elseif ($tariff_sub_code == 'OFFICE_DOOR' || $tariff_sub_code == 'DOOR_OFFICE') {
			$tariff_code = 3;
		} elseif ($tariff_sub_code == 'DOOR_DOOR') {
			$tariff_code = 4;
		}

		$data['loadings']['row']['shipment']['tariff_code'] = $tariff_code;
		$data['loadings']['row']['shipment']['tariff_sub_code'] = $tariff_sub_code;

		if ($data['loadings']['row']['shipment']['weight'] >= 50) {
			$data['loadings']['row']['shipment']['shipment_type'] = 'CARGO';
			$data['loadings']['row']['shipment']['cargo_code'] = 81;
		} elseif ($data['loadings']['row']['shipment']['weight'] <= 20 && $tariff_sub_code == 'OFFICE_OFFICE' && $this->config->get('shipping_econt_post_pack_enabled') && $less60cm) {
			$data['loadings']['row']['shipment']['shipment_type'] = 'POST_PACK';
			$data['loadings']['row']['shipment']['size_under_60cm'] = 1;
		} else {
			$data['loadings']['row']['shipment']['shipment_type'] = 'PACK';
		}

		if ($this->request->post['shipping_to'] == 'APS') {
			if ($weight < 5) {
				$data['loadings']['row']['shipment']['aps_box_size'] = 'Small';
			} else if ($weight < 10) {
				$data['loadings']['row']['shipment']['aps_box_size'] = 'Medium';
			} else {
				$data['loadings']['row']['shipment']['aps_box_size'] = 'Large';
			}
		} else {
			if (isset($data['loadings']['row']['shipment']['aps_box_size'])) {
				unset($data['loadings']['row']['shipment']['aps_box_size']);
			}
		}

		if (isset($this->request->post['pay_after_accept'])) {
			$pay_after_accept = (int)$this->request->post['pay_after_accept'];
		} else {
			$pay_after_accept = 0;
		}

		$data['loadings']['row']['shipment']['pay_after_accept'] = $pay_after_accept;

		if (isset($this->request->post['pay_after_test'])) {
			$pay_after_test = (int)$this->request->post['pay_after_test'];
		} else {
			$pay_after_test = 0;
		}

		$data['loadings']['row']['shipment']['pay_after_test'] = $pay_after_test;
		unset($data['loadings']['row']['packing_list']);

		if (isset($data['loadings']['row']['shipment']['delivery_day']) && $data['loadings']['row']['shipment']['delivery_day'] == 'half_day') {
			if (!isset($this->request->post['shipping_restday']) || !$this->request->post['shipping_restday']) {
				unset($data['loadings']['row']['shipment']['delivery_day']);
				unset($data['loadings']['row']['shipment']['send_date']);
			}
		}

		if ($isAps || $this->request->post['shipping_to'] == 'APS') {
			$data['loadings']['row']['shipment']['pack_count'] = 1;
		} else {
			$data['loadings']['row']['shipment']['pack_count'] = (int)$this->request->post['pack_count'];
		}

		if (isset($this->request->post['priority_time_cb']) && $this->request->post['shipping_to'] == 'DOOR') {
			$priority_time_type = $this->request->post['priority_time_type_id'];
			$priority_time_value = $this->request->post['priority_time_hour_id'];
		} else {
			$priority_time_type = '';
			$priority_time_value = '';
		}

		$data['loadings']['row']['services']['p'] = array('type' => $priority_time_type, 'value' => $priority_time_value);

		if ($this->request->post['shipping_econt_dc'] == 1) {
			$dc = 'ON';
		} else {
			$dc = '';
		}

		$data['loadings']['row']['services']['dc'] = $dc;

		if ($this->request->post['shipping_econt_dc'] == 2) {
			$dc_cp = 'ON';
		} else {
			$dc_cp = '';
		}

		$data['loadings']['row']['services']['dc_cp'] = $dc_cp;


		if ($this->request->post['inventory'] || isset($this->request->post['pay_choose'])) {
			if (isset($this->request->post['pay_choose'])) {
				$data['loadings']['row']['packing_list']['partial_delivery'] = 1;
				$this->request->post['inventory_type'] = 'DIGITAL';
			}
			$data['loadings']['row']['packing_list']['type'] = $this->request->post['inventory_type'];

			if ($this->request->post['inventory_type'] == 'DIGITAL') {
				foreach ($this->request->post['products'] as $product) {
					$data['loadings']['row']['packing_list']['row'][]['e'] = array(
						'inventory_num' => $product['product_id'],
						'description'   => $product['name'],
						'weight'        => $product['weight'],
						'price'         => $product['price']
					);
				}
			}
		}

		if ($this->request->post['instruction'] && $this->request->post['shipping_econt_instructions_select']) {
			if ($this->request->post['shipping_econt_instructions_select']['take']) {
				$data['loadings']['row']['instructions'][]['e'] = array(
					'type'     => 'take',
					'template' => $this->request->post['shipping_econt_instructions_select']['take']
				);
			}
			if ($this->request->post['shipping_econt_instructions_select']['give']) {
				$data['loadings']['row']['instructions'][]['e'] = array(
					'type'     => 'give',
					'template' => $this->request->post['shipping_econt_instructions_select']['give']
				);
			}
			if ($this->request->post['shipping_econt_instructions_select']['return']) {
				$data['loadings']['row']['instructions'][]['e'] = array(
					'type'     => 'return',
					'template' => $this->request->post['shipping_econt_instructions_select']['return']
				);
			}
		}

		$results = $this->parcelImport($data);

		if ($results) {
			if (!empty($results->result->e->error)) {
				$this->error['warning'] = (string)$results->result->e->error;
			} elseif (isset($results->result->e->loading_price->total)) {
				$loading_data = array(
					'order_id'    => $order_id,
					'loading_id'  => $results->result->e->loading_id,
					'loading_num' => $results->result->e->loading_num,
					'pdf_url'     => $results->result->e->pdf_url
				);

				if (isset($results->pdf)) {
					$loading_data['blank_yes'] = $results->pdf->blank_yes;
					$loading_data['blank_no'] = $results->pdf->blank_no;
				} else {
					$loading_data['blank_yes'] = '';
					$loading_data['blank_no'] = '';
				}

				$this->model_extension_sale_econt->addLoading($loading_data);

				if ($data['loadings']['row']['payment']['receiver_share_sum'] != '') {
					$order_total = (float)$data['loadings']['row']['payment']['receiver_share_sum'];
				} else {
					$order_total = (float)$results->result->e->loading_price->total;
				}

				$comment = $this->model_extension_sale_econt->updateOrderTotal($order_id, (float)$order_total);
				$this->model_extension_sale_econt->changeOrderStatus($order_id, $this->config->get('shipping_econt_order_status_id'));

				if ((!empty($data['error']['no_weight']) || !empty($data['error']['weight'])) && $this->config->get('shipping_econt_side') == 'RECEIVER') {
					unset($data['error']['no_weight']);
					unset($data['error']['weight']);
					$mail = new Mail($this->config->get('config_mail_engine'));
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
					$mail->smtp_username = $this->config->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port = $this->config->get('config_mail_smtp_port');
					$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

					$mail->setTo($order_info['email']);
					$mail->setFrom($this->config->get('config_email'));
					$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
					$mail->setSubject(html_entity_decode($this->language->get('text_subject'), ENT_QUOTES, 'UTF-8'));
					$mail->setText(sprintf($this->language->get('text_changed_shipping_price_text'), $order_id). $comment);
					$mail->send();
				}
			}
		} else {
			$this->error['warning'] = $this->language->get('error_connect');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	private function sortByPriority($a, $b) {
        return $a['priority'] - $b['priority'];
	}

	private function prepareXML($data) {
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

	private function serviceTool($data) {
		if (!$this->config->get('shipping_econt_test')) {
			$url = 'http://www.econt.com/e-econt/xml_service_tool.php';
		} else {
			$url = 'http://demo.econt.com/e-econt/xml_service_tool.php';
		}

		$request = '<?xml version="1.0" ?>
					<request>
						<client>
							<username>' . $this->config->get('shipping_econt_username') . '</username>
							<password>' . $this->config->get('shipping_econt_password') . '</password>
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

		libxml_use_internal_errors(TRUE);
		return simplexml_load_string($response);
	}

	private function parcelImport($data) {
		if (!$this->config->get('shipping_econt_test')) {
			$url = 'http://www.econt.com/e-econt/xml_parcel_import2.php';
		} else {
			$url = 'http://demo.econt.com/e-econt/xml_parcel_import2.php';
		}

		foreach ($data['loadings'] as $key => $row) {
			$data['loadings'][$key]['mediator'] = 'extensa';
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

	public function requestCourierMassAction()
	{
		$this->load->language('extension/sale/econt');
		return $this->url->link('extension/sale/econt/request_courier', 'user_token=' . $this->session->data['user_token'], true);
	}

	public function request_courier()
	{
		$orders = isset($this->request->post['selected']) ? $this->request->post['selected'] : false;
		if ($this->request->server['REQUEST_METHOD'] != 'POST' || !$orders) {
			$this->response->redirect($this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true));
			return;
		}

		$this->language->load('extension/sale/econt');

		$data = array();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['action'] = $this->url->link('extension/sale/econt/request_courier_post', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true);

		$data['orders'] = base64_encode(serialize($orders));

		$hours = array();
		for ($hour = 9; $hour <= self::MAX_COURIER_REQUEST_HOUR - 1; $hour++) {
			for ($minutes = 0; $minutes <= 45; $minutes += 15) {
				$hourStr = $hour . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
				if (($hour == 9 && $minutes < 30) || ($hour == 17 && $minutes > 30)) {
					continue;
				}
				if (date('H') >= self::MAX_COURIER_REQUEST_HOUR - 1 || $this->checkTime($hour, $minutes)) {
					$hours[] = $hourStr;
				}
			}
		}

		$hours2 = $hours;
		if (count($hours2) > 1) {
			array_shift($hours2);
		}

		$data['hours_between1'] = $hours;
		$data['hours_between2'] = $hours2;

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$this->response->setOutput($this->load->view('extension/sale/econt_request_courier', $data));
	}

	private function checkTime($hour, $minutes)
	{
		$time1 = $hour * 60 + $minutes;
		$time2 = date('H') * 60 + date('i');
		return ($time1 - $time2 >= 30);
	}

	public function request_courier_post()
	{
		if (!isset($this->request->post['orders'])) {
			$this->response->redirect($this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true));
			return;
		}

		$orders     = unserialize(base64_decode($this->request->post['orders']));
		$type       = $this->request->post['courier_request_type'];
		$time_in    = $this->request->post['courier_hours_in'];
		$time_from  = $this->request->post['courier_hours_between1'];
		$time_to    = $this->request->post['courier_hours_between2'];
		$isDefault  = (isset($this->request->post['courier_request_is_default']) && $this->request->post['courier_request_is_default']) ? 1 : 0;

		$this->language->load('extension/sale/econt');
		$this->load->model('extension/sale/econt');

		$result = $this->model_extension_sale_econt->courierRequest($orders, $type, $time_in, $time_from, $time_to);
		if ($result !== true) {
			if ($result !== false) {
				$this->session->data['econt_error'] = $result;
			}
			$this->response->redirect($this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true));
			return;
		}

		if ($isDefault) {
			$this->load->model('setting/setting');

			$dataSettings = $this->model_setting_setting->getSetting('shipping_econt');
			$dataSettings['shipping_econt_request_courier'] = 1;
			$dataSettings['shipping_econt_request_courier_type'] = $type;
			if ($type === 'IN') {
				$dataSettings['shipping_econt_request_courier_time_from'] = $time_in;
			} else {
				$dataSettings['shipping_econt_request_courier_time_from'] = $time_from;
				$dataSettings['shipping_econt_request_courier_time_to'] = $time_to;
			}

			$this->model_setting_setting->editSetting('shipping_econt', $dataSettings);
		}

		$this->session->data['success'] = $this->language->get('text_success_request_courier');
		$this->response->redirect($this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function cancelLoading() {
		$order_id = $this->request->get['order_id'];
		$this->load->model('extension/sale/econt');
		$loading_info = $this->model_extension_sale_econt->getLoading($order_id);
		if ($loading_info) {
			$data = array(
				'type' => 'cancel_shipments',
				'xml'  => "<cancel_shipments><num>" . $loading_info['loading_num'] . '</num></cancel_shipments>'
			);
			$result = $this->serviceTool($data);
			if ($result->cancel_shipments->e->success == 1) {
				$this->model_extension_sale_econt->deleteLoading($order_id);
			}
		}
		$this->response->redirect($this->url->link('extension/sale/econt/generate', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $this->request->get['order_id'], 'SSL'));
	}
}
?>
