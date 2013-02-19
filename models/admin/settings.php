<?php
	class admin_settings_model extends tablemanager_model {
		protected $table = "settings";
		protected $order = "key";
		protected $elements = array(
			"key" => array(
				"label"    => "Key",
				"type"     => "varchar",
				"unique"   => true,
				"overview" => true,
				"required" => true),
			"type" => array(
				"label"    => "Type",
				"type"     => "enum",
				"options"  => array(),
				"default"  => "string",
				"overview" => true),
			"value" => array(
				"label"    => "Value",
				"type"     => "varchar",
				"overview" => true,
				"required" => false));
		private $hidden_keys = array();

		public function __construct() {
			$arguments = func_get_args();
			call_user_func_array(array(parent, "__construct"), $arguments);

			$types = $this->settings->supported_types();
			sort($types);
			foreach ($types as $type) {
				$this->elements["type"]["options"][$type] = $type;
			}
		}

		private function fix_key_type($item) {
			switch ($item["type"]) {
				case "boolean": $item["value"] = is_true($item["value"]) ? "true" : "false"; break;
				case "integer": $item["value"] = (int)$item["value"]; break;
			}

			return $item;
		}

		public function create_item($item) {
			$item = $this->fix_key_type($item);
			return parent::create_item($item);
		}

		public function update_item($item) {
			$item = $this->fix_key_type($item);
			return parent::update_item($item);
		}
	}
?>
