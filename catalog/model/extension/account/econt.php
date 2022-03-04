<?php
class ModelExtensionAccountEcont extends Model {
	public function getOrder($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "econt_order WHERE order_id = '" . (int)$order_id . "'");

		return $query->row;
	}

	public function getLoading($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "econt_loading WHERE order_id = '" . (int)$order_id . "'");

		return $query->row;
	}

	public function updateLoading($data) {
		$this->db->query("UPDATE " . DB_PREFIX . "econt_loading SET is_imported = '" . (int)$data['is_imported'] . "', storage = '" . $this->db->escape($data['storage']) . "', receiver_person = '" . $this->db->escape($data['receiver_person']) . "', receiver_person_phone = '" . $this->db->escape($data['receiver_person_phone']) . "', receiver_courier = '" . $this->db->escape($data['receiver_courier']) . "', receiver_courier_phone = '" . $this->db->escape($data['receiver_courier_phone']) . "', receiver_time = '" . date('Y-m-d H:i:s', str\totime($data['receiver_time'])) . "', cd_get_sum = '" . $this->db->escape($data['cd_get_sum']) . "', cd_get_time = '" . date('Y-m-d H:i:s', strtotime($data['cd_get_time'])) . "', cd_send_sum = '" . $this->db->escape($data['cd_send_sum']) . "', cd_send_time = '" . date('Y-m-d H:i:s', strtotime($data['cd_send_time'])) . "', total_sum = '" . $this->db->escape($data['total_sum']) . "', currency = '" . $this->db->escape($data['currency']) . "', sender_ammount_due = '" . $this->db->escape($data['sender_ammount_due']) . "', receiver_ammount_due = '" . $this->db->escape($data['receiver_ammount_due']) . "', other_ammount_due = '" . $this->db->escape($data['other_ammount_due']) . "', delivery_attempt_count = '" . $this->db->escape($data['delivery_attempt_count']) . "', blank_yes = '" . $this->db->escape(trim($data['blank_yes'])) . "', blank_no = '" . $this->db->escape(trim($data['blank_no'])) . "' WHERE econt_loading_id  = '" . (int)$data['shipping_econt_loading_id'] . "'");

		if (isset($data['trackings'])) {
			foreach ($data['trackings'] as $tracking) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "econt_loading_tracking SET econt_loading_id = '" . (int)$data['shipping_econt_loading_id'] . "', loading_num = '" . $this->db->escape($data['loading_num']) . "', time = '" . date('Y-m-d H:i:s', strtotime($tracking['time'])) . "', is_receipt = '" . (int)$tracking['is_receipt'] . "', event = '" . $this->db->escape($tracking['event']) . "', name = '" . $this->db->escape($tracking['name']) . "', name_en = '" . $this->db->escape($tracking['name_en']) . "'");
			}
		}

		if (isset($data['next_parcels'])) {
			foreach ($data['next_parcels'] as $next_parcel) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "econt_loading SET loading_num = '" . $this->db->escape($next_parcel['loading_num']) . "', is_imported = '" . (int)$next_parcel['is_imported'] . "', storage = '" . $this->db->escape($next_parcel['storage']) . "', receiver_person = '" . $this->db->escape($next_parcel['receiver_person']) . "', receiver_person_phone = '" . $this->db->escape($next_parcel['receiver_person_phone']) . "', receiver_courier = '" . $this->db->escape($next_parcel['receiver_courier']) . "', receiver_courier_phone = '" . $this->db->escape($next_parcel['receiver_courier_phone']) . "', receiver_time = '" . date('Y-m-d H:i:s', strtotime($next_parcel['receiver_time'])) . "', cd_get_sum = '" . $this->db->escape($next_parcel['cd_get_sum']) . "', cd_get_time = '" . date('Y-m-d H:i:s', strtotime($next_parcel['cd_get_time'])) . "', cd_send_sum = '" . $this->db->escape($next_parcel['cd_send_sum']) . "', cd_send_time = '" . date('Y-m-d H:i:s', strtotime($next_parcel['cd_send_time'])) . "', total_sum = '" . $this->db->escape($next_parcel['total_sum']) . "', currency = '" . $this->db->escape($next_parcel['currency']) . "', sender_ammount_due = '" . $this->db->escape($next_parcel['sender_ammount_due']) . "', receiver_ammount_due = '" . $this->db->escape($next_parcel['receiver_ammount_due']) . "', other_ammount_due = '" . $this->db->escape($next_parcel['other_ammount_due']) . "', delivery_attempt_count = '" . $this->db->escape($next_parcel['delivery_attempt_count']) . "', blank_yes = '" . $this->db->escape(trim($next_parcel['blank_yes'])) . "', blank_no = '" . $this->db->escape(trim($next_parcel['blank_no'])) . "', prev_parcel_num = '" . $this->db->escape(trim($data['loading_num'])) . "', next_parcel_reason = '" . $this->db->escape(trim($next_parcel['reason'])) . "'");

				if (isset($next_parcel['trackings'])) {
					$econt_loading_next_id = $this->db->getLastId();

					foreach ($next_parcel['trackings'] as $tracking) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "econt_loading_tracking SET econt_loading_id = '" . (int)$econt_loading_next_id . "', loading_num = '" . $this->db->escape($next_parcel['loading_num']) . "', time = '" . date('Y-m-d H:i:s', strtotime($tracking['time'])) . "', is_receipt = '" . (int)$tracking['is_receipt'] . "', event = '" . $this->db->escape($tracking['event']) . "', name = '" . $this->db->escape($tracking['name']) . "', name_en = '" . $this->db->escape($tracking['name_en']) . "'");
					}
				}
			}
		}
	}

	public function updateLoadingReturn($econt_loading_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "econt_loading SET is_returned = '" . (int)$data['is_returned'] . "', returned_blank_yes = '" . $this->db->escape($data['blank_yes']) . "' WHERE econt_loading_id = '" . (int)$econt_loading_id . "'");
	}
}
?>
