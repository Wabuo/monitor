<?php
	define("PIXELS", 730);

	class web_browser_controller extends controller {
		public function execute() {
			$filter = new filter($this->db, $this->output);
			$filter->to_output("requests", true);

			if (($info = $this->model->get_information($filter->hostname, $filter->webserver)) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			if ($filter->browser_version == false) {
				$info = $this->model->remove_browser_version($info);
			}

			$max = 0;
			foreach ($info as $browser) {
				if ($browser["count"] > $max) {
					$max = $browser["count"];
				}
			}
			$this->output->add_tag("max", $max);

			/* Browsers
			 */
			$this->output->open_tag("info");
			foreach ($info as $browser) {
				$count = $max == 0 ? 0 : (int)(PIXELS * $browser["count"] / $max);
				$this->output->add_tag("item", $browser["browser"], array("count" => $count));
			}
			$this->output->close_tag();
		}
	}
?>
