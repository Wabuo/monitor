<?php
	abstract class graph_controller extends controller {
		protected $graphs = array();

		private function show_graphs() {
			$filter = new filter($this->db, $this->output);
			$filter->to_output($this->model->table);

			$begin = date("Y-m-d", strtotime("-".(MONITOR_DAYS - 1)." days"));
			$end = date("Y-m-d", strtotime("tomorrow"));

			if (($statistics = $this->model->get_statistics($begin, $end, $filter->hostname, $filter->webserver)) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$this->output->open_tag("graphs", array(
				"date_begin "  => date("j F Y", strtotime($begin)),
				"date_end"     => date("j F Y", strtotime($end) - DAY),
				"graph_height" => GRAPH_HEIGHT,
				"bar_width"    => sprintf("%0.2f", 810 / MONITOR_DAYS)));

			foreach ($this->graphs as $key => $label) {
				$max = 100;
				foreach ($statistics as $day => $record) {
					if ($record[$key] > $max) {
						$max = $record[$key];
					}
				}

				$this->output->open_tag("graph", array(
					"type"  => $key,
					"label" => $label,
					"max"   => $this->model->readable_number($max)));

				foreach ($statistics as $day => $record) {
					$value = ($max == 0) ? 0 : (int)(GRAPH_HEIGHT * $record[$key] / $max);
					$timestamp = strtotime($day);
					$day = date("l j F", $timestamp);
					$weekend = show_boolean(date("N", $timestamp) > 5);
					$this->output->add_tag("day", $value, array(
						"timestamp" => $timestamp,
						"label"     => $day,
						"weekend"   => $weekend,
						"count"     => $this->model->readable_number($record[$key])));
				}

				$this->output->close_tag();
			}

			$this->output->close_tag();
		}

		private function show_day_information($type, $timestamp) {	
			$filter = new filter($this->db, $this->output);

			if (($stats = $this->model->get_day_information($type, $timestamp, $filter->hostname, $filter->webserver)) === false) {
				$this->output->add_tag("result", "Database error.");
				return false;
			}

			$this->output->open_tag("day", array("label" => $this->graphs[$type]));
			foreach ($stats as $stat) {
				if (($type == "requests") || ($type == "bytes_sent")) {
					$stat["count"] = $this->model->readable_number($stat["count"]);
				}

				$this->output->record($stat, "stat");
			}
			$this->output->close_tag();
		}

		public function execute() {
			$this->output->add_css("includes/graph.css");

			if (in_array($this->page->pathinfo[1], array_keys($this->graphs)) == false) {
				$this->show_graphs();
			} else {
				$this->show_day_information($this->page->pathinfo[1], $this->page->pathinfo[2]);
			}
		}
	}
?>
