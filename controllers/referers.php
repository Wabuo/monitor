<?php
	class referers_controller extends controller {
		public function execute() {
			$this->output->add_javascript("jquery/jquery.js");

			$filter = new filter($this->db, $this->output);
			$filter->to_output("requests");

			if (($referers = $this->model->get_referers($filter->hostname, $filter->webserver)) === false) {	
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$this->output->open_tag("referers");
			foreach ($referers["hostnames"] as $hostname => $count) {
				$to_output = false;

				foreach ($referers["referers"][$hostname] as $referer) {
					if ($referer["count"] < $this->settings->referer_min_count) {
						continue;
					}

					if ($to_output == false) {
						$this->output->open_tag("hostname", array("name" => $hostname, "count" => $count));
						$to_output = true;
					}

					$this->output->add_tag("referer", $referer["referer"], array("count" => $referer["count"]));
				}

				if ($to_output) {
					$this->output->close_tag();
				}
			}
			$this->output->close_tag();
		}
	}
?>
