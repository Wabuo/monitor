<?php
	class filter {
		private $db = null;
		private $output = null;
		private $user = null;

		public function __construct($db, $output, $user) {
			$this->db = $db;
			$this->output= $output;
			$this->user = $user;
		}

		public function __get($key) {
			switch ($key) {
				case "webserver":
					return is_array($_SESSION["filter"]) ? $_SESSION["filter"]["webserver"] : 0;
				case "hostname":
					return is_array($_SESSION["filter"]) ? $_SESSION["filter"]["hostname"] : 0;
			}

			return null;
		}

		private function valid_webserver($webserver_id) {
			if ($webserver_id == 0) {
				return true;
			}

			$query = "select count(*) as count from webserver_user where webserver_id=%d and user_id=%d";
			if (($result = $this->db->execute($query, $webserver_id, $this->user->id)) === false) {
				return false;
			}

			return $result[0]["count"] > 0;
		}

		public function to_output($table, $show_hostnames = true) {
			if (is_array($_SESSION["filter"]) == false) {
				$_SESSION["filter"] = array(
					"webserver"       => 0,
					"hostname"        => 0);
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if (($_POST["submit_button"] == "filter") && $this->valid_webserver($_POST["webserver"])) {
					if ($_SESSION["filter"]["webserver"] != $_POST["webserver"]) {
						$_SESSION["filter"]["hostname"] = 0;
						$selected_hostname = $_POST["hostname"];
					} else {
						$_SESSION["filter"]["hostname"] = $_POST["hostname"];
					}
					$_SESSION["filter"]["webserver"] = $_POST["webserver"];
				}
			}

			$this->output->open_tag("filter");

			/* Webserver filter
			 */
			$query = "select w.* from webservers w, webserver_user a ".
			         "where w.id=a.webserver_id and a.user_id=%d and w.active=%d ".
			         "order by name";
			if (($webservers = $this->db->execute($query, $this->user->id, YES)) != false) {
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
			if ($show_hostnames) {
				if ($_SESSION["filter"]["webserver"] == 0) {
					$query = "select distinct h.* from hostnames h, %S t, webserver_user a ".
							 "where h.id=t.hostname_id and t.webserver_id=a.webserver_id ".
							 "and a.user_id=%d and h.visible=%d order by hostname";
					$args = array($table, $this->user->id, YES);
				} else {
					$query = "select distinct * from hostnames where visible=%d and id in ".
							 "(select distinct hostname_id from %S where webserver_id=%d) ".
							 "order by hostname";
					$args = array(YES, $table, $_SESSION["filter"]["webserver"]);
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
			}

			$this->output->close_tag();

			return true;
		}
	}
?>
