<?php
	define("PIXELS", 730);

	class operating_system_controller extends controller {
		public function execute() {
			$filter = new filter($this->db, $this->output);
			$filter->to_output("requests");

			if (($info = $this->model->get_information($filter->hostname, $filter->webserver)) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$max = 0;
			foreach ($info as $browser) {
				if ($browser["count"] > $max) {
					$max = $browser["count"];
				}
			}
			$this->output->add_tag("max", $max);

			/* Operating Systems
			 */
			$this->output->open_tag("info", array("label" => "Operating Systems"));
			foreach ($info as $os) {
				$count = $max == 0 ? 0 : (int)(PIXELS * $os["count"] / $max);
				$this->output->add_tag("item", $os["os"], array("count" => $count));
			}
			$this->output->close_tag();
		}
	}
?>
