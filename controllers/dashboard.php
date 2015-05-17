<?php
	class dashboard_controller extends controller {
		public function execute() {
			if (isset($_SESSION["latest_hiawatha_version"]) == false) {
				$hiawatha_website = new HTTPS("www.hiawatha-webserver.org");
				if (($result = $hiawatha_website->GET("/latest")) !== false) {
					$_SESSION["latest_hiawatha_version"] = $result["body"];
				}
			}

			/* Webserver
			 */
			if (($webservers = $this->model->get_webservers()) === false) {
				return;
			}

			$webservers_offline = false;

			foreach ($webservers as $webserver) {
				$webserver["address"] = ($webserver["tls"] == 0 ? "http" : "https") . "://".$webserver["ip_address"];
				if ((($webserver["tls"] == 0) && ($webserver["port"] != 80)) ||
				    (($webserver["tls"] == 1) && ($webserver["port"] != 443))) {
					$webserver["address"] .= ":".$webserver["port"];
				}
				$webserver["address"] .= "/";

				if ($webserver["active"]) {
					if ($webserver["errors"] == 0) {
						$webserver["status"] = "online";
					} else {
						$webserver["status"] = "offline";
						$webservers_offline = true;
					}
				}

				$webserver["tls"] = show_boolean($webserver["tls"]);
				$webserver["active"] = show_boolean($webserver["active"]);

				if ($webserver["version"] != "") {
					$parts = explode(",", $webserver["version"], 2);
					list(, $version) = explode("v", $parts[0], 2);
					$comparison = version_compare($version, $_SESSION["latest_hiawatha_version"], ">=");
					$webserver["uptodate"] = show_boolean($comparison);
				}

				$this->output->record($webserver, "webserver");
			}

			if ($webservers_offline) {
				$this->output->add_system_message("Warning, one or more webservers are unavailable!");
			}

			/* Alerts
			 */
			$timestamp = date("Y-m-d 00:00:00");

			$alerts = array(
				array("Top failed logins",    "get_top_failed_logins",    0),
				array("Top events",           "get_top_events",           0),
				array("Top exploit attempts", "get_top_exploit_attempts", 0),
				array("Top CGI errors",       "get_top_cgi_errors",       0),
				array("Top bad requests",     "get_top_bad_requests",     0),
				array("Top bans",             "get_top_bans",             3),
				array("Top Not Founds",       "get_top_not_founds",       5),
				array("Top Internal Errors",  "get_top_internal_errors",  1),
				array("Top Forbiddens",       "get_top_forbiddens",       3));

			foreach ($alerts as $alert) {
				list($title, $function, $minimum) = $alert;

				if (($list = $this->model->$function($timestamp)) === false) {
					return;
				}

				$this->output->open_tag("list", array("title" => $title));

				foreach ($list as $item) {
					if ($item["count"] < $minimum) {
						break;
					}
					$this->output->add_tag("item", $item["label"], array("count" => $item["count"]));
				}

				$this->output->close_tag();
			}
		}
	}
?>
