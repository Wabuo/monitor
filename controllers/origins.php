<?php
	define("PIXELS", 730);

	class origins_controller extends controller {
		public function execute() {
			$filter = new filter($this->db, $this->output);
			$filter->to_output("requests");

			if (($origins = $this->model->get_origins($filter->hostname, $filter->webserver)) === false) {
				$this->output->add_tag("result", "Database error.\n");
				return;
			}

			$max = 0;
			foreach ($origins as $origin) {
				if ($origin["count"] > $max) {
					$max = $origin["count"];
				}
			}

			$this->output->open_tag("origins", array("max" => $max));
			foreach ($origins as $origin) {
				$count = $max == 0 ? 0 : (int)(PIXELS * $origin["count"] / $max);
				if ($count < $this->settings->origins_min_count) {
					continue;
				}
				$this->output->add_tag("origin", $origin["country"], array("count" => $count));
			}
			$this->output->close_tag();
		}
	}
?>
