<?php
	abstract class graph_controller extends controller {
		protected $graphs = array();
		protected $hostnames = true;

		private function show_graphs() {
			$filter = new filter($this->db, $this->output, $this->user);
			$filter->to_output($this->model->table, $this->model->hostnames);

			$begin = date("Y-m-d", strtotime("-".(MONITOR_DAYS - 1)." days"));
			$end = date("Y-m-d", strtotime("tomorrow"));

			if (($statistics = $this->model->get_statistics($begin, $end, $filter->hostname, $filter->webserver)) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			foreach ($this->graphs as $key => $label) {
				$graph = new graph($this->output);
				$graph->title = $label;
				$graph->width = 960;
				$graph->height = GRAPH_HEIGHT;

				foreach ($statistics as $day => $record) {
					$timestamp = strtotime($day);
					$day = date("l j F", $timestamp);
					$weekend = date("N", $timestamp) > 5;
					$class = $weekend ? "weekend" : "week";
					$link = sprintf("%s/%s/%s", $this->page->page, $key, $timestamp);

					$graph->add_bar($day, $record[$key], $class, $link);
				}

				$graph->to_output();
				unset($graph);
			}
		}

		private function show_day_information($type, $timestamp) {	
			$filter = new filter($this->db, $this->output, $this->user);

			if (($stats = $this->model->get_day_information($type, $timestamp, $filter->hostname, $filter->webserver)) === false) {
				$this->output->add_tag("result", "Database error.");
				return false;
			}

			$this->output->open_tag("day", array(
				"hostnames" => show_boolean($this->model->hostnames),
				"day"       => date("l, j F Y", $timestamp),
				"label"     => $this->graphs[$type]));

			foreach ($stats as $stat) {
				if (($type == "requests") || ($type == "bytes_sent")) {
					$stat["count"] = $this->model->readable_number($stat["count"]);
				}

				$this->output->record($stat, "stat");
			}

			$this->output->close_tag();
		}

		public function execute() {
			$this->output->add_css("banshee/graphs.css");
			$this->output->add_css("banshee/filter.css");

			if (in_array($this->page->pathinfo[1], array_keys($this->graphs)) == false) {
				$this->show_graphs();
			} else {
				$this->show_day_information($this->page->pathinfo[1], $this->page->pathinfo[2]);
			}
		}
	}
?>
