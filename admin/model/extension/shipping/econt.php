<?php
class ModelExtensionShippingEcont extends Model {
	private $_allowedZones = array(1, 2, 3, 27, 28, 29, 30, 51, 52, 53, 54, 55, 56, 57, 59);

	public function createTables() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "econt_city` (
		  `city_id` int(11) NOT NULL AUTO_INCREMENT,
		  `post_code` varchar(10) NOT NULL DEFAULT '',
		  `type` varchar(3) NOT NULL DEFAULT '' COMMENT '‘гр.’ или ‘с.’',
		  `name` varchar(255) NOT NULL DEFAULT '',
		  `name_en` varchar(255) NOT NULL DEFAULT '',
		  `zone_id` int(11) NOT NULL DEFAULT '3' COMMENT '3 - Зона В',
		  `country_id` int(11) NOT NULL DEFAULT '1033' COMMENT '1033 - България',
		  `office_id` int(11) NOT NULL DEFAULT '0' COMMENT 'главния офис',
		  PRIMARY KEY (`city_id`),
		  KEY `post_code` (`post_code`),
		  KEY `name` (`name`),
		  KEY `name_en` (`name_en`),
		  KEY `office_id` (`office_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "econt_city_office` (
		  `city_office_id` int(11) NOT NULL AUTO_INCREMENT,
		  `office_code` varchar(10) NOT NULL DEFAULT '',
		  `shipment_type` varchar(32) NOT NULL DEFAULT '',
		  `delivery_type` varchar(32) NOT NULL DEFAULT '',
		  `city_id` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`city_office_id`),
		  KEY `office_code` (`office_code`),
		  KEY `city_id` (`city_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "econt_country` (
		  `country_id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(255) NOT NULL DEFAULT '',
		  `name_en` varchar(255) NOT NULL DEFAULT '',
		  `zone_id` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`country_id`),
		  KEY `zone_id` (`zone_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "econt_customer` (
		  `customer_id` int(11) NOT NULL,
		  `shipping_to` varchar(32) NOT NULL DEFAULT '',
		  `postcode` varchar(10) NOT NULL DEFAULT '',
		  `city` varchar(255) NOT NULL DEFAULT '',
		  `quarter` varchar(255) NOT NULL DEFAULT '',
		  `street` varchar(255) NOT NULL DEFAULT '',
		  `street_num` varchar(10) NOT NULL DEFAULT '',
		  `other` varchar(255) NOT NULL DEFAULT '',
		  `city_id` int(11) NOT NULL DEFAULT '0',
		  `office_id` int(11) NOT NULL DEFAULT '0',
		  `office_aps_id` int(11) NOT NULL DEFAULT '0',
		  KEY `customer_id` (`customer_id`),
		  KEY `city_id` (`city_id`),
		  KEY `office_id` (`office_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "econt_loading` (
		  `econt_loading_id` int(11) NOT NULL AUTO_INCREMENT,
		  `order_id` int(11) NOT NULL DEFAULT '0',
		  `loading_id` varchar(32) NOT NULL DEFAULT '',
		  `loading_num` varchar(32) NOT NULL DEFAULT '',
		  `is_imported` tinyint(1) NOT NULL DEFAULT '0',
		  `storage` varchar(255) NOT NULL DEFAULT '',
		  `receiver_person` varchar(255) NOT NULL DEFAULT '',
		  `receiver_person_phone` varchar(255) NOT NULL DEFAULT '',
		  `receiver_courier` varchar(255) NOT NULL DEFAULT '',
		  `receiver_courier_phone` varchar(255) NOT NULL DEFAULT '',
		  `receiver_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `cd_get_sum` varchar(32) NOT NULL DEFAULT '',
		  `cd_get_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `cd_send_sum` varchar(32) NOT NULL DEFAULT '',
		  `cd_send_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `total_sum` varchar(32) NOT NULL DEFAULT '',
		  `currency` varchar(10) NOT NULL DEFAULT '',
		  `sender_ammount_due` varchar(32) NOT NULL DEFAULT '',
		  `receiver_ammount_due` varchar(32) NOT NULL DEFAULT '',
		  `other_ammount_due` varchar(32) NOT NULL DEFAULT '',
		  `delivery_attempt_count` varchar(10) NOT NULL DEFAULT '',
		  `blank_yes` varchar(255) NOT NULL DEFAULT '',
		  `blank_no` varchar(255) NOT NULL DEFAULT '',
		  `pdf_url` varchar(255) NOT NULL DEFAULT '',
		  `prev_parcel_num` varchar(32) NOT NULL DEFAULT '',
		  `next_parcel_reason` varchar(32) NOT NULL DEFAULT '',
		  `is_returned` tinyint(1) NOT NULL DEFAULT '0',
		  `returned_blank_yes` varchar(255) NOT NULL DEFAULT '',
		  PRIMARY KEY (`econt_loading_id`),
		  KEY `order_id` (`order_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "econt_office` (
		  `office_id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(255) NOT NULL DEFAULT '',
		  `name_en` varchar(255) NOT NULL DEFAULT '',
		  `office_code` varchar(10) NOT NULL DEFAULT '',
		  `address` varchar(255) NOT NULL DEFAULT '',
		  `address_en` varchar(255) NOT NULL DEFAULT '',
		  `phone` varchar(32) NOT NULL DEFAULT '',
		  `work_begin` time DEFAULT '09:00:00',
		  `work_end` time DEFAULT '18:00:00',
		  `work_begin_saturday` time DEFAULT '09:00:00',
		  `work_end_saturday` time DEFAULT '13:00:00',
		  `time_priority` time DEFAULT '12:00:00' COMMENT 'минимален приоритетен час',
		  `city_id` int(11) NOT NULL DEFAULT '0',
		  `is_machine` tinyint(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`office_id`),
		  KEY `office_code` (`office_code`),
		  KEY `city_id` (`city_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "econt_order` (
		  `econt_order_id` int(11) NOT NULL AUTO_INCREMENT,
		  `order_id` int(11) NOT NULL DEFAULT '0',
		  `data` text NOT NULL,
		  `requested_courier` enum('0','1') NOT NULL DEFAULT '1',
		  PRIMARY KEY (`econt_order_id`),
		  KEY `order_id` (`order_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "econt_quarter` (
		  `quarter_id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(255) NOT NULL DEFAULT '',
		  `name_en` varchar(255) NOT NULL DEFAULT '',
		  `city_id` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`quarter_id`),
		  KEY `name` (`name`),
		  KEY `name_en` (`name_en`),
		  KEY `city_id` (`city_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "econt_region` (
		  `region_id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(255) NOT NULL DEFAULT '',
		  `code` varchar(10) NOT NULL DEFAULT '',
		  `city_id` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`region_id`),
		  KEY `name` (`name`),
		  KEY `code` (`code`),
		  KEY `city_id` (`city_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "econt_street` (
		  `street_id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(255) NOT NULL DEFAULT '',
		  `name_en` varchar(255) NOT NULL DEFAULT '',
		  `city_id` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`street_id`),
		  KEY `name` (`name`),
		  KEY `name_en` (`name_en`),
		  KEY `city_id` (`city_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "econt_zone` (
		  `zone_id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(255) NOT NULL DEFAULT '',
		  `name_en` varchar(255) NOT NULL DEFAULT '',
		  `national` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - в България; 0 - международна',
		  `is_ee` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - обслужва се от Еконт Експрес; 0 - от подизпълнител',
		  PRIMARY KEY (`zone_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "econt_loading_tracking` (
		  `econt_loading_tracking_id` int(11) NOT NULL AUTO_INCREMENT,
		  `econt_loading_id` int(11) NOT NULL DEFAULT '0',
		  `loading_num` varchar(32) NOT NULL DEFAULT '',
		  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `is_receipt` tinyint(1) NOT NULL DEFAULT '0',
		  `event` varchar(32) NOT NULL DEFAULT '',
		  `name` varchar(255) NOT NULL DEFAULT '',
		  `name_en` varchar(255) NOT NULL DEFAULT '',
		  PRIMARY KEY (`econt_loading_tracking_id`),
		  KEY `econt_loading_id` (`econt_loading_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");
	}

	public function importData() {
		$dir = DIR_APPLICATION . 'controller/extension/econt_sql/';

		$sqls = array(
			array('file' => 'extensa_econt_city.sql', 'table' => DB_PREFIX . 'econt_city', 'insert' => '(`city_id`, `post_code`, `type`, `name`, `name_en`, `zone_id`, `country_id`, `office_id`)'),
			array('file' => 'extensa_econt_city_office.sql', 'table' => DB_PREFIX . 'econt_city_office', 'insert' => '(`city_office_id`, `office_code`, `shipment_type`, `delivery_type`, `city_id`)'),
			array('file' => 'extensa_econt_country.sql', 'table' => DB_PREFIX . 'econt_country', 'insert' => '(`country_id`, `name`, `name_en`, `zone_id`)'),
			array('file' => 'extensa_econt_office.sql', 'table' => DB_PREFIX . 'econt_office', 'insert' => '(`office_id`, `name`, `name_en`, `office_code`, `address`, `address_en`, `phone`, `work_begin`, `work_end`, `work_begin_saturday`, `work_end_saturday`, `time_priority`, `city_id`, `is_machine`)'),
			array('file' => 'extensa_econt_quarter.sql', 'table' => DB_PREFIX . 'econt_quarter', 'insert' => '(`quarter_id`, `name`, `name_en`, `city_id`)'),
			array('file' => 'extensa_econt_region.sql', 'table' => DB_PREFIX . 'econt_region', 'insert' => '(`region_id`, `name`, `code`, `city_id`)'),
			array('file' => 'extensa_econt_street.sql', 'table' => DB_PREFIX . 'econt_street', 'insert' => '(`street_id`, `name`, `name_en`, `city_id`)'),
			array('file' => 'extensa_econt_zone.sql', 'table' => DB_PREFIX . 'econt_zone', 'insert' => '(`zone_id`, `name`, `name_en`, `national`, `is_ee`)'),
		);

		foreach ($sqls as $sql) {
			$handle = @fopen($dir . $sql['file'], 'r');
			if ($handle) {
				$this->db->query('TRUNCATE TABLE ' . $sql['table'] . ';');

				$sqlStr = '';
				$counter = 0;
				while (($line = fgets($handle)) !== false) {
					$counter++;
					$sqlStr .= '(' . $line . '),';
					if ($counter == 1000) {
						$this->db->query("INSERT INTO " . $sql['table'] . " " . $sql['insert'] . " VALUES " . rtrim($sqlStr, ','));
						$counter = 0;
						$sqlStr = '';
					}
				}
				if ($counter) {
					$this->db->query("INSERT INTO " . $sql['table'] . " " . $sql['insert'] . " VALUES " . rtrim($sqlStr, ','));
				}
				fclose($handle);
			}
		}
	}

	public function deleteTables() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "econt_city`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "econt_city_office`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "econt_country`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "econt_customer`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "econt_loading`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "econt_office`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "econt_order`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "econt_quarter`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "econt_region`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "econt_street`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "econt_zone`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "econt_loading_tracking`");
	}

	public function deleteCountries() {
		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "econt_country");
	}

	public function addCountry($data) {
		$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "econt_country SET country_id = '" . (int)$data['country_id'] . "', name = '" . $this->db->escape($data['name']) . "', name_en = '" . $this->db->escape($data['name_en']) . "', zone_id = '" . (int)$data['zone_id'] . "'");
	}

	public function deleteZones() {
		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "econt_zone");
	}

	public function addZone($data) {
		$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "econt_zone SET zone_id = '" . (int)$data['zone_id'] . "', name = '" . $this->db->escape($data['name']) . "', name_en = '" . $this->db->escape($data['name_en']) . "', national = '" . (int)$data['national'] . "', is_ee = '" . (int)$data['is_ee'] . "'");
	}

	public function deleteRegions() {
		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "econt_region");
	}

	public function addRegion($data) {
		$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "econt_region SET region_id = '" . (int)$data['region_id'] . "', name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', city_id = '" . (int)$data['city_id'] . "'");
	}

	public function deleteQuarters() {
		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "econt_quarter");
	}

	public function addQuarter($data) {
		$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "econt_quarter SET quarter_id = '" . (int)$data['quarter_id'] . "', name = '" . $this->db->escape($data['name']) . "', name_en = '" . $this->db->escape($data['name_en']) . "', city_id = '" . (int)$data['city_id'] . "'");
	}

	public function deleteStreets() {
		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "econt_street");
	}

	public function addStreet($data) {
		$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "econt_street SET street_id = '" . (int)$data['street_id'] . "', name = '" . $this->db->escape($data['name']) . "', name_en = '" . $this->db->escape($data['name_en']) . "', city_id = '" . (int)$data['city_id'] . "'");
	}

	public function deleteOffices() {
		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "econt_office");
	}

	public function addOffice($data) {
		$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "econt_office SET office_id = '" . (int)$data['office_id'] . "', name = '" . $this->db->escape($data['name']) . "', name_en = '" . $this->db->escape($data['name_en']) . "', office_code = '" . $this->db->escape($data['office_code']) . "', address = '" . $this->db->escape($data['address']) . "', address_en = '" . $this->db->escape($data['address_en']) . "', phone = '" . $this->db->escape($data['phone']) . "', work_begin = '" . $this->db->escape($data['work_begin']) . "', work_end = '" . $this->db->escape($data['work_end']) . "', work_begin_saturday = '" . $this->db->escape($data['work_begin_saturday']) . "', work_end_saturday = '" . $this->db->escape($data['work_end_saturday']) . "', time_priority = '" . $this->db->escape($data['time_priority']) . "', city_id = '" . (int)$data['city_id'] . "', is_machine = '" . (int)$data['is_machine'] . "'");
	}

	public function deleteCities() {
		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "econt_city");
	}

	public function addCity($data) {
		$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "econt_city SET city_id = '" . (int)$data['city_id'] . "', post_code = '" . $this->db->escape($data['post_code']) . "', type = '" . $this->db->escape($data['type']) . "', name = '" . $this->db->escape($data['name']) . "', name_en = '" . $this->db->escape($data['name_en']) . "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$data['country_id'] . "', office_id = '" . (int)$data['office_id'] . "'");
	}

	public function deleteCitiesOffices() {
		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "econt_city_office");
	}

	public function addCityOffice($data) {
		$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "econt_city_office SET office_code = '" . $this->db->escape($data['office_code']) . "', shipment_type = '" . $this->db->escape($data['shipment_type']) . "', delivery_type = '" . $this->db->escape($data['delivery_type']) . "', city_id = '" . (int)$data['city_id'] . "'");
	}

	public function getZones($zone = 0, $limit = 'all')
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "econt_zone WHERE zone_id >= " . $zone . " ORDER BY zone_id ASC" . (($limit != 'all') ? ' LIMIT ' . ((int)$limit) : ''));
		return $query->rows;
	}

	public function getCitiesByName($name, $limit = 10) {
		if (strtolower($this->config->get('config_admin_language')) == 'bg') {
			$suffix = '';
		} else {
			$suffix = '_en';
		}

		$sql = "SELECT *, c.name" . $suffix . " AS name FROM " . DB_PREFIX . "econt_city c";

		if ($name) {
			$sql .= " WHERE (LCASE(c.name) LIKE '%" . $this->db->escape(utf8_strtolower($name)) . "%' OR LCASE(c.name_en) LIKE '%" . $this->db->escape(utf8_strtolower($name)) . "%')";
		}

		$sql .= " ORDER BY c.name" . $suffix;

		$sql .= " LIMIT " . (int)$limit;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getCityByNameAndPostcode($name, $postcode) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "econt_city c WHERE (LCASE(TRIM(c.name)) = '" . $this->db->escape(utf8_strtolower(trim($name))) . "' OR LCASE(TRIM(c.name_en)) = '" . $this->db->escape(utf8_strtolower(trim($name))) . "') AND TRIM(c.post_code) = '" . $this->db->escape(trim($postcode)) . "'");

		return $query->row;
	}

	public function getQuartersByName($name, $city_id, $limit = 10) {
		if (strtolower($this->config->get('config_admin_language')) == 'bg') {
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
		if (strtolower($this->config->get('config_admin_language')) == 'bg') {
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

	public function getCitiesWithOffices($delivery_type = '', $aps = null) {
		if (strtolower($this->config->get('config_admin_language')) == 'bg') {
			$suffix = '';
		} else {
			$suffix = '_en';
		}

		$sql = "SELECT c.city_id, c.name" . $suffix . " AS name FROM " . DB_PREFIX . "econt_city c INNER JOIN " . DB_PREFIX . "econt_office o ON (c.city_id = o.city_id) ";

		if ($delivery_type) {
			$sql .= " INNER JOIN " . DB_PREFIX . "econt_city_office eco ON o.office_code = eco.office_code AND o.city_id = eco.city_id AND eco.delivery_type = '" . $delivery_type . "' ";
		}

		if ($aps !== null) {
			$sql .= " WHERE o.is_machine = '" . (int)$aps . "' " ;
		}

		$sql .= " GROUP BY c.city_id ORDER BY c.country_id = 1033 DESC, c.country_id ASC, c.name" . $suffix;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getOfficesByCityId($city_id, $delivery_type = '', $aps = null) {
		if (strtolower($this->config->get('config_admin_language')) == 'bg') {
			$suffix = '';
		} else {
			$suffix = '_en';
		}

		$sql = "SELECT *, o.name" . $suffix . " AS name, o.address" . $suffix . " AS address FROM " . DB_PREFIX . "econt_office o ";

		if ($delivery_type) {
			$sql .= " INNER JOIN " . DB_PREFIX . "econt_city_office eco ON o.office_code = eco.office_code AND o.city_id = eco.city_id AND eco.delivery_type = '" . $delivery_type . "' ";
		}

		$sql .= " WHERE o.city_id = '" . (int)$city_id . "'";

		if ($aps !== null) {
			$sql .= " AND o.is_machine = '" . (int)$aps . "'";
		}

		$sql .= " GROUP BY o.office_id ORDER BY o.name" . $suffix;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getOffice($office_id) {
		if (strtolower($this->config->get('config_admin_language')) == 'bg') {
			$suffix = '';
		} else {
			$suffix = '_en';
		}

		$query = $this->db->query("SELECT *, o.name" . $suffix . " AS name, o.address" . $suffix . " AS address FROM " . DB_PREFIX . "econt_office o WHERE o.office_id = '" . (int)$office_id . "'");

		return $query->row;
	}

	public function getCityByCityId($city_id) {
		if (strtolower($this->config->get('config_admin_language')) == 'bg') {
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

	public function getOfficeByOfficeCode($office_code) {
		if (strtolower($this->config->get('config_admin_language')) == 'bg') {
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

	public function validateAddress($data) {
		$sql = "SELECT COUNT(c.city_id) AS total FROM " . DB_PREFIX . "econt_city c LEFT JOIN " . DB_PREFIX . "econt_quarter q ON (c.city_id = q.city_id) LEFT JOIN " . DB_PREFIX . "econt_street s ON (c.city_id = s.city_id) WHERE TRIM(c.post_code) = '". $this->db->escape(trim($data['post_code'])) . "' AND (LCASE(TRIM(c.name)) = '" . $this->db->escape(utf8_strtolower(trim($data['city']))) . "' OR LCASE(TRIM(c.name_en)) = '" . $this->db->escape(utf8_strtolower(trim($data['city']))) . "')";

		if ($data['quarter']) {
			$sql .= " AND (LCASE(TRIM(q.name)) = '" . $this->db->escape(utf8_strtolower(trim($data['quarter']))) . "' OR LCASE(TRIM(q.name_en)) = '" . $this->db->escape(utf8_strtolower(trim($data['quarter']))) . "')";
		}

		if ($data['street']) {
			$sql .= " AND (LCASE(TRIM(s.name)) = '" . $this->db->escape(utf8_strtolower(trim($data['street']))) . "' OR LCASE(TRIM(s.name_en)) = '" . $this->db->escape(utf8_strtolower(trim($data['street']))) . "')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	// Update steps

	public function serviceTool($data) {
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


	public function updateStep0($data, $zoneUpdate = 'all') {
		$data['type'] = 'countries';
		$results = $this->serviceTool($data);
		if ($results) {
			if (isset($results->error)) {
				return false;
			} else {
				if (isset($results->e)) {
					$this->deleteCountries();

					foreach ($results->e as $country) {
						if (!in_array($country->id_zone, $this->_allowedZones)) {
							continue;
						}
						$country_data = array(
							'country_id' => $country->id,
							'name'       => $country->country_name,
							'name_en'    => $country->country_name_en,
							'zone_id'    => $country->id_zone
						);

						$this->addCountry($country_data);
					}
				}
			}
		} else {
			return false;
		}

		return true;
	}

	public function updateStep1($data, $zoneUpdate = 'all') {
		$data['type'] = 'cities_zones';

		$results = $this->serviceTool($data);
		if ($results) {
			if (isset($results->error)) {
				return false;
			} else {
				if (isset($results->zones)) {
					$this->deleteZones();

					foreach ($results->zones->e as $zone) {
						if (!in_array($zone->id, $this->_allowedZones)) {
							continue;
						}
						$zone_data = array(
							'zone_id'  => $zone->id,
							'name'     => $zone->name,
							'name_en'  => $zone->name_en,
							'national' => $zone->national,
							'is_ee'    => $zone->is_ee
						);

						$this->addZone($zone_data);
					}
				}
			}
		} else {
			return false;
		}

		return true;
	}

	public function updateStep2($data, $zoneUpdate = 'all') {
		$data['type'] = 'cities_regions';
		$truncate = true;
		$zones = $this->getZones();
		foreach ($zones as $zone) {
			if ($zoneUpdate != 'all' && $zone['zone_id'] != $zoneUpdate) {
				continue;
			}
			$data['xml'] = '<cities><id_zone>' . $zone['zone_id'] . '</id_zone></cities>';
			$results = $this->serviceTool($data);
			if ($results) {
				if (isset($results->error)) {
					return false;
				} else {
					if (isset($results->cities_regions)) {
						if (($truncate && $zoneUpdate == 'all') || $zoneUpdate == 1) {
							$this->deleteRegions();
							$truncate = false;
						}

						foreach ($results->cities_regions->e as $region) {
							$region_data = array(
								'region_id' => $region->id,
								'name'      => $region->name,
								'code'      => $region->code,
								'city_id'   => $region->id_city
							);

							$this->addRegion($region_data);
						}
					}
				}
			} else {
				return false;
			}
		}

		return true;
	}

	public function updateStep3($data, $zoneUpdate = 'all') {
		$data['type'] = 'cities_quarters';
		$truncate = true;
		$zones = $this->getZones();
		foreach ($zones as $zone) {
			if ($zoneUpdate != 'all' && $zone['zone_id'] != $zoneUpdate) {
				continue;
			}
			$data['xml'] = '<cities><id_zone>' . $zone['zone_id'] . '</id_zone></cities>';
			$results = $this->serviceTool($data);
			if ($results) {
				if (isset($results->error)) {
					return false;
				} else {
					if (isset($results->cities_quarters)) {
						if (($truncate && $zoneUpdate == 'all') || $zoneUpdate == 1) {
							$this->deleteQuarters();
							$truncate = false;
						}
	
						foreach ($results->cities_quarters->e as $quarter) {
							$quarter_data = array(
								'quarter_id'     => $quarter->id,
								'name'           => $quarter->name,
								'name_en'        => $quarter->name_en,
								'city_id'        => $quarter->id_city
							);
	
							$this->addQuarter($quarter_data);
						}
					}
				}
			} else {
				return false;
			}
		}

		return true;
	}

	public function updateStep4($data, $zoneUpdate = 'all') {
		$data['type'] = 'cities_streets';
		$truncate = true;
		$zones = $this->getZones();
		foreach ($zones as $zone) {
			if ($zoneUpdate != 'all' && $zone['zone_id'] != $zoneUpdate) {
				continue;
			}
			$data['xml'] = '<cities><id_zone>' . $zone['zone_id'] . '</id_zone></cities>';
			$results = $this->serviceTool($data);
			if ($results) {
				if (isset($results->error)) {
					return false;
				} else {
					if (isset($results->cities_street)) {
						if (($truncate && $zoneUpdate == 'all') || $zoneUpdate == 1) {
							$this->deleteStreets();
							$truncate = false;
						}
	
						foreach ($results->cities_street->e as $street) {
							$street_data = array(
								'street_id'      => $street->id,
								'name'           => $street->name,
								'name_en'        => $street->name_en,
								'city_id'        => $street->id_city
							);
	
							$this->addStreet($street_data);
						}
					}
				}
			} else {
				return false;
			}
		}

		return true;
	}

	public function updateStep5($data, $zoneUpdate = 'all') {
		$data['type'] = 'offices';
		$truncate = true;
		$zones = $this->getZones();
		foreach ($zones as $zone) {
			if ($zoneUpdate != 'all' && $zone['zone_id'] != $zoneUpdate) {
				continue;
			}
			$data['xml'] = '<cities><id_zone>' . $zone['zone_id'] . '</id_zone></cities>';
			$results = $this->serviceTool($data);
			if ($results) {
				if (isset($results->error)) {
					return false;
				} else {
					if (isset($results->offices)) {
						if (($truncate && $zoneUpdate == 'all') || $zoneUpdate == 1) {
							$this->deleteOffices();
							$truncate = false;
						}
	
						foreach ($results->offices->e as $office) {
							$office_data = array(
								'office_id'           => $office->id,
								'name'                => $office->name,
								'name_en'             => $office->name_en,
								'office_code'         => $office->office_code,
								'address'             => $office->address,
								'address_en'          => $office->address_en,
								'phone'               => $office->phone,
								'work_begin'          => $office->work_begin,
								'work_end'            => $office->work_end,
								'work_begin_saturday' => $office->work_begin_saturday,
								'work_end_saturday'   => $office->work_end_saturday,
								'time_priority'       => $office->time_priority,
								'city_id'             => $office->id_city,
								'is_machine'          => $office->is_machine,
							);
	
							$this->addOffice($office_data);
						}
					}
				}
			} else {
				return false;
			}
		}

		return true;
	}

	public function updateStep6($data, $zoneUpdate = 'all') {
		$data['type'] = 'cities';
		$truncate = true;
		$zones = $this->getZones();
		foreach ($zones as $zone) {
			if ($zoneUpdate != 'all' && $zone['zone_id'] != $zoneUpdate) {
				continue;
			}
			$data['xml'] = '<cities><id_zone>' . $zone['zone_id'] . '</id_zone></cities>';
			$results = $this->serviceTool($data);
			if ($results) {
				if (isset($results->error)) {
					return false;
				} else {
					if (isset($results->cities)) {
						if (($truncate && $zoneUpdate == 'all') || $zoneUpdate == 1) {
							$this->deleteCities();
							$this->deleteCitiesOffices();
							$truncate = false;
						}
	
						foreach ($results->cities->e as $city) {
							$city_data = array(
								'city_id'      => $city->id,
								'post_code'    => $city->post_code,
								'type'         => $city->type,
								'name'         => $city->name,
								'name_en'      => $city->name_en,
								'zone_id'      => $city->id_zone,
								'country_id'   => $city->id_country,
								'office_id'    => $city->id_office
							);
	
							$this->addCity($city_data);
	
							if (isset($city->attach_offices)) {
								foreach ($city->attach_offices->children() as $shipment_type) {
									foreach ($shipment_type->children() as $delivery_type) {
										foreach ($delivery_type->office_code as $office_code) {
											$city_office_data = array(
												'office_code' => $office_code,
												'shipment_type' => $shipment_type->getName(),
												'delivery_type' => $delivery_type->getName(),
												'city_id' => $city->id
											);
	
											$this->addCityOffice($city_office_data);
										}
									}
								}
							}
						}
					}
				}
			} else {
				return false;
			}
		}

		return true;
	}

	public function updateData($data)
	{
		$this->load->model('setting/setting');

		$success = false;
		
		$dataEcont = $this->model_setting_setting->getSetting('shipping_econt');

		$clients      = array();
		$keywords     = array();
		$addresses    = array();
		$agreements   = array();
		$instructions = array();

		$data['type'] = 'profile';
		$results = $this->serviceTool($data);
		if ($results && !isset($results->error)) {
			if (isset($results->client_info)) {
				if (isset($results->client_info->id)) {
					$dataEcont['shipping_econt_profile_id'] = (string)$results->client_info->id;
                }
				if (isset($results->client_info->name)) {
					$dataEcont['shipping_econt_name_person'] = (string)$results->client_info->name;
				}
				if (isset($results->client_info->mol)) {
					$dataEcont['shipping_econt_name'] = (string)$results->client_info->mol;
				}
				if (isset($results->client_info->business_phone)) {
					$dataEcont['shipping_econt_phone'] = (string)$results->client_info->business_phone;
				}
				if (isset($results->client_info->international)) {
					$dataEcont['shipping_econt_international'] = (string)$results->client_info->international;
				}
			}

			if (isset($results->addresses)) {
				foreach ($results->addresses->e as $address) {
					if (isset($address->city) && isset($address->city_post_code)) {
						$row = $this->getCityByNameAndPostcode($address->city, $address->city_post_code);
						if ($row) {
							$address->city_id = $row['city_id'];
						}
					}

					$addresses[] = json_decode(json_encode($address), true);
				}
			}
		}

		$data['type'] = 'access_clients';
		$results = $this->serviceTool($data);
		if ($results && !isset($results->error) && isset($results->clients)) {
			foreach ($results->clients->client as $client) {
				$success = true;

				$clients[(string)$client->id] = array(
					'id'      => (string)$client->id,
					'ein'     => (string)$client->ein,
					'name'    => (string)$client->name,
					'name_en' => (string)$client->name_en
				);

				$keywords[(string)$client->id] = (string)$client->key_word;

				if (isset($client->cd_agreements)) {
					foreach ($client->cd_agreements->cd_agreement as $cd_agreement) {
						$agreements[(string)$client->id][] = (string)$cd_agreement->num;
					}
				}

				if (isset($client->instructions)) {
					foreach ($client->instructions->e as $instruction) {
						$instructions[(string)$client->id][(string)$instruction->type][] = (string)$instruction->template;
					}
				}
			}
		}

		if ($success) {
			$dataEcont['shipping_econt_clients'] = $clients;
			$dataEcont['shipping_econt_keyword_list'] = $keywords;
			$dataEcont['shipping_econt_address_list'] = $addresses;
			$dataEcont['shipping_econt_agreement_list'] = $agreements;
			$dataEcont['shipping_econt_instruction_list'] = $instructions;
		}

		$this->model_setting_setting->editSetting('shipping_econt', $dataEcont);
	}
}
?>