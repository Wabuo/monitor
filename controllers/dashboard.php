<?php
	class dashboard_controller extends controller {
		public function execute() {
			/* Webserver
			 */
			$webservers = $this->model->get_webservers();
			foreach ($webservers as $webserver) {
				$webserver["address"] = ($webserver["ssl"] == 0 ? "http" : "https") . "://".$webserver["ip_address"];
				if ((($webserver["ssl"] == 0) && ($webserver["port"] != 80)) ||
				    (($webserver["ssl"] == 1) && ($webserver["port"] != 443))) {
					$webserver["address"] .= ":".$webserver["port"];
				}
				$webserver["address"] .= "/";

				$webserver["ssl"] = show_boolean($webserver["ssl"]);
				$webserver["active"] = show_boolean($webserver["active"]);

				$this->output->record($webserver, "webserver");
			}

			/* Alerts
			 */
			$timestamp = date("Y-m-d 00:00:00");

			/* Exploit attempts
			 */
			$this->output->open_tag("list", array("title" => "Top exploit attempts"));
			$list = $this->model->get_top_exploit_attempts($timestamp);
			foreach ($list as $item) {
				if ($item["attempts"] <= 2) {
					break;
				}
				$this->output->add_tag("item", $item["hostname"], array("count" => $item["attempts"]));
			}
			$this->output->close_tag();

			/* Bans
			 */
			$this->output->open_tag("list", array("title" => "Top bans"));
			$list = $this->model->get_top_bans($timestamp);
			foreach ($list as $item) {
				if ($item["bans"] <= 2) {
					break;
				}
				$this->output->add_tag("item", $item["hostname"], array("count" => $item["bans"]));
			}
			$this->output->close_tag();

			/* Forbiddens
			 */
			$this->output->open_tag("list", array("title" => "Top Forbiddens"));
			$list = $this->model->get_top_forbiddens($timestamp);
			foreach ($list as $item) {
				if ($item["forbidden"] <= 2) {
					break;
				}
				$this->output->add_tag("item", $item["hostname"], array("count" => $item["forbidden"]));
			}
			$this->output->close_tag();

			/* Not founds
			 */
			$this->output->open_tag("list", array("title" => "Top Not Founds"));
			$list = $this->model->get_top_not_founds($timestamp);
			foreach ($list as $item) {
				if ($item["not_found"] <= 2) {
					break;
				}
				$this->output->add_tag("item", $item["hostname"], array("count" => $item["not_found"]));
			}
			$this->output->close_tag();

			/* Internal Errors
			 */
			$this->output->open_tag("list", array("title" => "Top Internal Errors"));
			$list = $this->model->get_top_internal_errors($timestamp);
			foreach ($list as $item) {
				if ($item["errors"] <= 2) {
					break;
				}
				$this->output->add_tag("item", $item["hostname"], array("count" => $item["errors"]));
			}
			$this->output->close_tag();
		}
	}
?>
