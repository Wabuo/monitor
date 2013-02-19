<?php
	define("PIXELS", 580);

	class browser_with_os_controller extends controller {
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
			foreach ($info as $item) {
				if ($item["count"] > $max) {
					$max = $item["count"];
				}
			}

			$this->output->add_tag("max", $max);

			$this->output->open_tag("info", array("label" => "Web browser with Operating System"));
			foreach ($info as $item) {
				$count = $max == 0 ? 0 : (int)(PIXELS * $item["count"] / $max);
				if ($count < $this->settings->browser_os_min_count) {
					continue;
				}
				$this->output->add_tag("item", $item["info"], array("count" => $count));
			}
			$this->output->close_tag();
		}
	}
?>
