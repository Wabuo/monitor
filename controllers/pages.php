<?php
	define("PIXELS", 680);

	class pages_controller extends controller {
		private $skip_urls = array("/robots.txt", "/logout");
		private $skip_extensions = array("css", "gif", "ico", "jpg", "js", "png", "ttf");

		private function skip_url($url) {
			if (substr($url, 0, 6) == "/admin") {
				return true;
			}
			if (in_array($url, $this->skip_urls)) {
				return true;
			}
			$parts = explode(".", $url);
			$extension = array_pop($parts);
			if (in_array($extension, $this->skip_extensions)) {
				return true;
			}

			return false;
		}

		public function execute() {
			$filter = new filter($this->db, $this->output);
			$filter->to_output("requests");

			if (($pages = $this->model->get_pages($filter->hostname, $filter->webserver)) === false) {
				$this->output->add_tag("result", "Database error.\n");
				return;
			}

			$max = 0;
			foreach ($pages as $i => $page) {
				if ($this->skip_url($page["request_uri"])) {
					unset($pages[$i]);
				} else if ($page["count"] > $max) {
					$max = $page["count"];
				}
			}

			$this->output->open_tag("pages", array("max" => $max));
			foreach ($pages as $page) {
				$count = $max == 0 ? 0 : (int)(PIXELS * $page["count"] / $max);
				if ($count < $this->settings->pages_min_count) {
					continue;
				}

				$this->output->add_tag("page", $page["request_uri"], array("count" => $count));
			}
			$this->output->close_tag();
		}
	}
?>
