<?php
	class filter {
		private $db = null;
		private $output = null;

		public function __construct($db, $output) {
			$this->db = $db;
			$this->output= $output;
		}

		public function __get($key) {
			switch ($key) {
				case "webserver":
					return is_array($_SESSION["filter"]) ? $_SESSION["filter"]["webserver"] : 0;
				case "hostname":
					return is_array($_SESSION["filter"]) ? $_SESSION["filter"]["hostname"] : 0;
				case "browser_version":
					return $_SESSION["filter"]["browser_version"];
			}

			return null;
		}

		public function to_output($type, $browser_version = false) {
			if (in_array($type, array("requests", "host_statistics")) == false) {
				return false;
			}

			if (is_array($_SESSION["filter"]) == false) {
				$_SESSION["filter"] = array(
					"webserver"       => 0,
					"hostname"        => 0,
					"browser_version" => false);
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "filter") {
					if ($_SESSION["filter"]["webserver"] != $_POST["webserver"]) {
						$_SESSION["filter"]["hostname"] = 0;
						$selected_hostname = $_POST["hostname"];
					} else {
						$_SESSION["filter"]["hostname"] = $_POST["hostname"];
					}
					$_SESSION["filter"]["webserver"] = $_POST["webserver"];
					$_SESSION["filter"]["browser_version"] = is_true($_POST["browser_version"]);
				}
			}

			$this->output->open_tag("filter");

			/* Browser version filter
			 */
			if ($browser_version) {
				$this->output->add_tag("browser_version", show_boolean($_SESSION["filter"]["browser_version"]));
			}

			/* Webserver filter
			 */
			$query = "select * from webservers order by name";
			if (($webservers = $this->db->execute($query)) != false) {
				$this->output->open_tag("webservers");
				array_unshift($webservers, array("id" => 0, "name" => "All"));
				foreach ($webservers as $webserver) {
					$this->output->add_tag("webserver", $webserver["name"], array(
						"id"       => $webserver["id"],
						"selected" => show_boolean($webserver["id"] == $_SESSION["filter"]["webserver"])));
				}
				$this->output->close_tag();
			}

			/* Hostname filter
			 */
			if ($_SESSION["filter"]["webserver"] == 0) {
				if ($type == "requests") {
					$query = "select * from hostnames where visible=1 and id in ".
					         "(select distinct hostname_id from requests) ".
					         "order by hostname";
				} else {
					$query = "select * from hostnames where visible=1 order by hostname";
				}
				$args = array();
			} else {
				$query = "select * from hostnames where visible=1 and id in ".
						 "(select distinct hostname_id from ".$type." where webserver_id=%d) ".
						 "order by hostname";
				$args = array($_SESSION["filter"]["webserver"]);
			}
			if (($hostnames = $this->db->execute($query, $args)) != false) {
				$this->output->open_tag("hostnames");
				array_unshift($hostnames, array("id" => 0, "hostname" => "All"));

				$hostname_found = false;
				foreach ($hostnames as $hostname) {
					if ($selected_hostname != null) {
						if ($selected_hostname == $hostname["id"]) {
							$_SESSION["filter"]["hostname"] = $selected_hostname;
						}
					}
					if ($hostname["id"] == $_SESSION["filter"]["hostname"]) {
						$hostname_found = true;
					}
					$this->output->add_tag("hostname", $hostname["hostname"], array(
						"id"       => $hostname["id"],
						"selected" => show_boolean($hostname["id"] == $_SESSION["filter"]["hostname"])));
				}
				$this->output->close_tag();

				if ($hostname_found == false) {
					$_SESSION["filter"]["hostname"] = 0;
				}
			}

			$this->output->close_tag();

			return true;
		}
	}
?>
