<?php
	class dashboard_controller extends controller {
		public function execute() {
			/* Webserver
			 */
			if (($webservers = $this->model->get_webservers()) === false) {
				return;
			}

			if (($status = $this->model->get_webserver_status()) === false) {
				return;
			}

			$webservers_offline = false;

			foreach ($webservers as $webserver) {
				$webserver["address"] = ($webserver["ssl"] == 0 ? "http" : "https") . "://".$webserver["ip_address"];
				if ((($webserver["ssl"] == 0) && ($webserver["port"] != 80)) ||
				    (($webserver["ssl"] == 1) && ($webserver["port"] != 443))) {
					$webserver["address"] .= ":".$webserver["port"];
				}
				$webserver["address"] .= "/";

				if ($webserver["active"]) {
					if (isset($status[$webserver["id"]]) == false) {
						$webserver["status"] = "unknown";
					} else if (($webserver["status"] = $status[$webserver["id"]]) == "offline") {
						$webservers_offline = true;
					}
				}

				$webserver["ssl"] = show_boolean($webserver["ssl"]);
				$webserver["active"] = show_boolean($webserver["active"]);

				$this->output->record($webserver, "webserver");
			}

			if ($webservers_offline) {
				$this->output->add_system_message("Warning, one or more webservers are offline!");
			}

			/* Alerts
			 */
			$timestamp = date("Y-m-d 00:00:00");

			$alerts = array(
				array("Top exploit attempts", "get_top_exploit_attempts", 0),
				array("Top bad requests",     "get_top_bad_requests",     0),
				array("Top events",           "get_top_events",           0),
				array("Top CGI errors",       "get_top_cgi_errors",       0),
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
