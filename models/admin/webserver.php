<?php
	class admin_webserver_model extends tablemanager_model {
		protected $table = "webservers";
		protected $elements = array(
			"name" => array(
				"label"    => "Name",
				"type"     => "varchar",
				"overview" => true,
				"required" => true),
			"ip_address" => array(
				"label"    => "IP address",
				"type"     => "varchar",
				"overview" => true,
				"required" => true),
			"port" => array(
				"label"    => "Port",
				"type"     => "integer",
				"default"  => "80",
				"overview" => false,
				"required" => true),
			"ssl" => array(
				"label"    => "SSL",
				"type"     => "boolean",
				"overview" => false,
				"required" => true),
			"active" => array(
				"label"    => "Active",
				"type"     => "boolean",
				"default"  => true,
				"overview" => true,
				"required" => true),
			"errors" => array(
				"label"    => "Errors",
				"type"     => "integer",
				"default"  => 0,
				"overview" => false,
				"readonly" => true));

		public function delete_item($item_id) {
			$queries = array(
				array("delete from events where webserver_id=%d", $item_id),
				array("delete from host_statistics where webserver_id=%d", $item_id),
				array("delete from server_statistics where webserver_id=%d", $item_id),
				array("delete from webservers where id=%d", $item_id));

			return $this->db->transaction($queries);
		}
	}
?>
