<?php
	class events_controller extends controller {
		public function execute() {
			if (($count = $this->model->count_events()) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$paging = new pagination($this->output, "events", $this->settings->event_page_size, $count);

			if (($events = $this->model->get_events($paging->offset, $paging->size)) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$this->output->open_tag("events");

			foreach ($events as $event) {
				$event["timestamp"] = date("j F Y, H:i:s", $event["timestamp"]);
				$this->output->record($event, "event");
			}

			$paging->show_browse_links();

			$this->output->close_tag();
		}
	}
?>