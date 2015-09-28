<?php
	class events_controller extends controller {
		public function execute() {
			if (isset($_SERVER["hide_ss"]) == false) {
				$_SERVER["hide_ss"] = true;
			}

			if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST["submit_button"] == "hidess")) {
				$_SERVER["hide_ss"] = is_true($_POST["hide_ss"]);
			}

			$this->output->add_css("banshee/filter.css");

			$filter = new filter($this->db, $this->output, $this->user);
			$filter->to_output($this->model->table, false);

			if (($count = $this->model->count_events($filter->webserver, $_SERVER["hide_ss"])) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$paging = new pagination($this->output, "events", $this->settings->event_page_size, $count);
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$paging->reset();
			}

			if (($events = $this->model->get_events($paging->offset, $paging->size, $filter->webserver, $_SERVER["hide_ss"])) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$this->output->open_tag("events", array("hide_ss" => show_boolean($_SERVER["hide_ss"])));

			foreach ($events as $event) {
				$event["timestamp"] = date("j F Y, H:i:s", $event["timestamp"]);
				$event["event"] = $this->output->secure_string($event["event"], "_");
				$this->output->record($event, "event");
			}

			$paging->show_browse_links();

			$this->output->close_tag();
		}
	}
?>
