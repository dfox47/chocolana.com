<?php
class ModelExtensionSaleEcont extends Model {
	public function getOrder($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "econt_order WHERE order_id = '" . (int)$order_id . "'");

		return $query->row;
	}

	public function getOrdersForCourier()
	{
		$query = $this->db->query("SELECT o.order_id FROM " . DB_PREFIX . "econt_order AS o JOIN " . DB_PREFIX . "econt_loading AS l ON o.order_id = l.order_id WHERE o.requested_courier = '0' AND l.loading_num != ''");
		return $query->rows;
	}

	public function setOrderRequestedCourier($order_id)
	{
		$this->db->query("UPDATE " . DB_PREFIX . "econt_order SET requested_courier = '1' WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getLoading($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "econt_loading WHERE order_id = '" . (int)$order_id . "'");

		return $query->row;
	}

	public function getLoadingNextParcels($loading_num) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "econt_loading WHERE prev_parcel_num = '" . $this->db->escape($loading_num) . "'");

		return $query->rows;
	}

	public function changeOrderStatus($order_id, $status_id) {
		$query = $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `order_status_id` = '" . (int)$status_id . "' WHERE order_id =" . (int)$order_id);
	}

	public function getLoadingTrackings($econt_loading_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "econt_loading_tracking WHERE econt_loading_id = '" . (int)$econt_loading_id . "'");

		return $query->rows;
	}

	public function addLoading($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "econt_loading SET order_id = '" . (int)$data['order_id'] . "', loading_id = '" . $this->db->escape($data['loading_id']) . "', loading_num = '" . $this->db->escape($data['loading_num']) . "', blank_yes = '" . $this->db->escape(trim($data['blank_yes'])) . "', blank_no = '" . $this->db->escape(trim($data['blank_no'])) . "', pdf_url = '" . $this->db->escape(trim($data['pdf_url'])) . "'");
	}

	public function deleteLoading($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "econt_loading WHERE order_id = '" . (int)$order_id . "'");
	}

	public function updateLoading($data) {
		$this->db->query("UPDATE " . DB_PREFIX . "econt_loading SET is_imported = '" . (int)$data['is_imported'] . "', storage = '" . $this->db->escape($data['storage']) . "', receiver_person = '" . $this->db->escape($data['receiver_person']) . "', receiver_person_phone = '" . $this->db->escape($data['receiver_person_phone']) . "', receiver_courier = '" . $this->db->escape($data['receiver_courier']) . "', receiver_courier_phone = '" . $this->db->escape($data['receiver_courier_phone']) . "', receiver_time = '" . date('Y-m-d H:i:s', strtotime($data['receiver_time'])) . "', cd_get_sum = '" . $this->db->escape($data['cd_get_sum']) . "', cd_get_time = '" . date('Y-m-d H:i:s', strtotime($data['cd_get_time'])) . "', cd_send_sum = '" . $this->db->escape($data['cd_send_sum']) . "', cd_send_time = '" . date('Y-m-d H:i:s', strtotime($data['cd_send_time'])) . "', total_sum = '" . $this->db->escape($data['total_sum']) . "', currency = '" . $this->db->escape($data['currency']) . "', sender_ammount_due = '" . $this->db->escape($data['sender_ammount_due']) . "', receiver_ammount_due = '" . $this->db->escape($data['receiver_ammount_due']) . "', other_ammount_due = '" . $this->db->escape($data['other_ammount_due']) . "', delivery_attempt_count = '" . $this->db->escape($data['delivery_attempt_count']) . "', blank_yes = '" . $this->db->escape(trim($data['blank_yes'])) . "', blank_no = '" . $this->db->escape(trim($data['blank_no'])) . "', pdf_url = '" . $this->db->escape(trim($data['pdf_url'])) . "' WHERE econt_loading_id  = '" . (int)$data['econt_loading_id'] . "'");

		if (isset($data['trackings'])) {
			foreach ($data['trackings'] as $tracking) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "econt_loading_tracking SET econt_loading_id = '" . (int)$data['econt_loading_id'] . "', loading_num = '" . $this->db->escape($data['loading_num']) . "', time = '" . date('Y-m-d H:i:s', strtotime($tracking['time'])) . "', is_receipt = '" . (int)$tracking['is_receipt'] . "', event = '" . $this->db->escape($tracking['event']) . "', name = '" . $this->db->escape($tracking['name']) . "', name_en = '" . $this->db->escape($tracking['name_en']) . "'");
			}
		}

		if (isset($data['next_parcels'])) {
			foreach ($data['next_parcels'] as $next_parcel) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "econt_loading SET loading_num = '" . $this->db->escape($next_parcel['loading_num']) . "', is_imported = '" . (int)$next_parcel['is_imported'] . "', storage = '" . $this->db->escape($next_parcel['storage']) . "', receiver_person = '" . $this->db->escape($next_parcel['receiver_person']) . "', receiver_person_phone = '" . $this->db->escape($next_parcel['receiver_person_phone']) . "', receiver_courier = '" . $this->db->escape($next_parcel['receiver_courier']) . "', receiver_courier_phone = '" . $this->db->escape($next_parcel['receiver_courier_phone']) . "', receiver_time = '" . date('Y-m-d H:i:s', strtotime($next_parcel['receiver_time'])) . "', cd_get_sum = '" . $this->db->escape($next_parcel['cd_get_sum']) . "', cd_get_time = '" . date('Y-m-d H:i:s', strtotime($next_parcel['cd_get_time'])) . "', cd_send_sum = '" . $this->db->escape($next_parcel['cd_send_sum']) . "', cd_send_time = '" . date('Y-m-d H:i:s', strtotime($next_parcel['cd_send_time'])) . "', total_sum = '" . $this->db->escape($next_parcel['total_sum']) . "', currency = '" . $this->db->escape($next_parcel['currency']) . "', sender_ammount_due = '" . $this->db->escape($next_parcel['sender_ammount_due']) . "', receiver_ammount_due = '" . $this->db->escape($next_parcel['receiver_ammount_due']) . "', other_ammount_due = '" . $this->db->escape($next_parcel['other_ammount_due']) . "', delivery_attempt_count = '" . $this->db->escape($next_parcel['delivery_attempt_count']) . "', blank_yes = '" . $this->db->escape(trim($next_parcel['blank_yes'])) . "', blank_no = '" . $this->db->escape(trim($next_parcel['blank_no'])) . "', pdf_url = '" . $this->db->escape(trim($next_parcel['pdf_url'])) . "', prev_parcel_num = '" . $this->db->escape(trim($data['loading_num'])) . "', next_parcel_reason = '" . $this->db->escape(trim($next_parcel['reason'])) . "'");

				if (isset($next_parcel['trackings'])) {
					$econt_loading_next_id = $this->db->getLastId();

					foreach ($next_parcel['trackings'] as $tracking) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "econt_loading_tracking SET econt_loading_id = '" . (int)$econt_loading_next_id . "', loading_num = '" . $this->db->escape($next_parcel['loading_num']) . "', time = '" . date('Y-m-d H:i:s', strtotime($tracking['time'])) . "', is_receipt = '" . (int)$tracking['is_receipt'] . "', event = '" . $this->db->escape($tracking['event']) . "', name = '" . $this->db->escape($tracking['name']) . "', name_en = '" . $this->db->escape($tracking['name_en']) . "'");
					}
				}
			}
		}
	}

	public function updateOrderTotal($order_id, $cost) {
		$comment = '';

		$order_query = $this->db->query("SELECT *, os.name AS status FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id AND os.language_id = o.language_id) WHERE o.order_id = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {

			$order_shipping_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' AND code = 'shipping'");

			if ($order_shipping_query->num_rows) {
				$old_shipping_value = $order_shipping_query->row['value'];
				$shipping_value = $this->currency->convert($cost, $this->config->get('shipping_econt_currency'), $order_query->row['currency_code']);
				$shipping_text = $this->currency->format($shipping_value, $order_query->row['currency_code'], $order_query->row['currency_value']);

				$this->db->query("UPDATE " . DB_PREFIX . "order_total SET value = '" . (float)$shipping_value . "' WHERE order_total_id = '" . (int)$order_shipping_query->row['order_total_id'] . "'");

				$comment .= $order_shipping_query->row['title'] . ' ' . $shipping_text;

				$order_total_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' AND code = 'total'");

				if ($order_total_query->num_rows) {
					$total_value = $order_total_query->row['value'] - $old_shipping_value + $shipping_value;
					$total_text = $this->currency->format($total_value, $order_query->row['currency_code'], $order_query->row['currency_value']);

					$this->db->query("UPDATE " . DB_PREFIX . "order_total SET value = '" . (float)$total_value . "' WHERE order_total_id = '" . (int)$order_total_query->row['order_total_id'] . "'");

					$this->db->query("UPDATE `" . DB_PREFIX . "order` SET total = '" . (float)$total_value . "' WHERE order_id = '" . (int)$order_id . "'");

					$comment .= "\n" . $order_total_query->row['title'] . ' ' . $total_text;
				}
			}
		}

		return $comment;
	}

	public function parcelImport($data) {
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

	public function courierRequest($orders, $type, $time_in, $time_from, $time_to, $auto = false)
	{
		if (!$orders) {
			return false;
		}

		$this->language->load('extension/sale/econt');
		$this->load->model('sale/order');
		$this->load->model('catalog/product');

		$orderErrors = array();
		$totalWeight = 0;
		$totalQuantity = 0;

		foreach ($orders as $orderId) {
			if (!$this->model_sale_order->getOrder($orderId)) {
				continue;
			}
			$econt_order = $this->getOrder($orderId);
			if ($econt_order) {
				$econt_loading = $this->getLoading($orderId);
				if ($econt_order['requested_courier'] || !$econt_loading) {
					$orderErrors[] = $orderId;
				} else {
					$products = $this->model_sale_order->getOrderProducts($orderId);
					foreach ($products as $productId) {
						$product = $this->model_catalog_product->getProduct($productId['product_id']);
						$totalWeight   += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('shipping_econt_weight_class_id')) * $productId['quantity'];
						$totalQuantity += $productId['quantity'];
					}
				}
			} elseif (!$auto) {
				return $this->language->get('error_choose_valid_orders');
			}
		}

		if ($orderErrors && !$auto) {
			return $this->language->get('error_orders_request_courier') . ' ' . implode(', ', $orderErrors);
		}

		if ($auto && !$totalQuantity) {
			return false;
		}

		$data = array();
		$data['system']['validate'] = 0;
		$data['system']['response_type'] = 'XML';
		$data['system']['only_calculate'] = 0;

		$data['client']['username'] = $this->config->get('shipping_econt_username');
		$data['client']['password'] = $this->config->get('shipping_econt_password');
		$data['client_software'] = 'ExtensaOpenCart3x';

		$data['loadings']['row']['shipment']['weight']     = $totalWeight;
		$data['loadings']['row']['shipment']['pack_count'] = $totalQuantity;

		if ($totalWeight >= 50) {
			$data['loadings']['row']['shipment']['shipment_type'] = 'CARGO';
			$data['loadings']['row']['shipment']['cargo_code'] = 81;
		} elseif ($totalWeight <= 20) {
			$data['loadings']['row']['shipment']['shipment_type'] = 'POST_PACK';
		} else {
			$data['loadings']['row']['shipment']['shipment_type'] = 'PACK';
		}

		$data['loadings']['row']['courier_request']['only_courier_request'] = 1;
		if ($type === 'IN') {
			$data['loadings']['row']['courier_request']['time_from'] = $time_in;
			$data['loadings']['row']['courier_request']['time_to'] = $time_in;
		} else {
			$data['loadings']['row']['courier_request']['time_from'] = $time_from;
			if (!$time_to) {
				$time_to = $time_from;
			}
			$data['loadings']['row']['courier_request']['time_to'] = $time_to;
		}

		$sender_addresses = $this->config->get('shipping_econt_address_list');
		$sender_address = $sender_addresses[$this->config->get('shipping_econt_address_id')];

		$clients = $this->config->get('shipping_econt_clients');
		$clientId = $this->config->get('shipping_econt_client_id');

		$data['loadings']['row']['sender']['name']        = $clients[$clientId]['name'];;
		$data['loadings']['row']['sender']['name_person'] = $clients[$clientId]['name'];;

		$data['loadings']['row']['sender']['city'] = $sender_address['city'];
		$data['loadings']['row']['sender']['post_code'] = $sender_address['city_post_code'];
		$data['loadings']['row']['sender']['quarter'] = $sender_address['quarter'];
		$data['loadings']['row']['sender']['street'] = $sender_address['street'];
		$data['loadings']['row']['sender']['street_num'] = $sender_address['street_num'];
		$data['loadings']['row']['sender']['phone_num'] = $this->config->get('shipping_econt_phone');

		$r = $this->parcelImport($data);
		if (isset($r->result->e->error) && (string) $r->result->e->error) {
			return $this->language->get('error_request_courier_api') . '<br />' . (string) $r->result->e->error;
		}

		foreach ($orders as $orderId) {
			$this->setOrderRequestedCourier($orderId);
		}

		return true;
	}
}
?>
