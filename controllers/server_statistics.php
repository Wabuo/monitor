<?php
	class server_statistics_controller extends controller {
		public function execute() {
			if (($webservers = $this->model->get_webservers()) === false) {
				$this->output->add_tag("result", "Database error.\n");
				return;
			}

			$this->output->open_tag("statistics");

			foreach ($webservers as $webserver) {
				if (($connections = $this->model->get_top_connections($webserver["id"], $this->settings->top_connections)) === false) {
					break;
				}

				$this->output->open_tag("connections", array("webserver" => $webserver["name"]));
				foreach ($connections as $connection) {
					$connection["timestamp_begin"] = date("j F Y, H:i:s", $connection["timestamp_begin"]);
					$connection["timestamp_end"] = date("j F Y, H:i:s", $connection["timestamp_end"]);
					$this->output->record($connection, "connection");
				}
				$this->output->close_tag();
			}

			$this->output->close_tag();
		}
	}
?>
