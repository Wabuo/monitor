<?php
	define("PIXELS", 200);

	class host_statistics_controller extends controller {
		private $graphs = array(
			"requests"              => "Requests",
			"bytes_sent"            => "Bytes sent",
			"bans"                  => "Clients banned",
			"exploit_attempts"      => "Exploit attempts",
			"result_forbidden"      => "Result: 403 Forbidden",
			"result_not_found"      => "Result: 404 Not Found",
			"result_internal_error" => "Result: 500 Internal Server Error");

		private function show_graphs() {
			$filter = new filter($this->db, $this->output);
			$filter->to_output("host_statistics");

			$begin = date("Y-m-d", strtotime("-".MONITOR_DAYS." days"));
			$end = date("Y-m-d", strtotime("tomorrow"));

			if (($statistics = $this->model->get_statistics($begin, $end, $filter->hostname, $filter->webserver)) === false) {
				$this->output("result", "Database error.");
				return;
			}

			$this->output->open_tag("graphs", array(
				"begin" => date("j F Y", strtotime($begin)),
				"end"   => date("j F Y", strtotime($end) - DAY)));

			foreach ($this->graphs as $key => $label) {
				$max = 0.5 * PIXELS;
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
					$value = ($max == 0) ? 0 : (int)(PIXELS * $record[$key] / $max);
					$timestamp = strtotime($day);
					$day = date("j F", $timestamp);
					$this->output->add_tag("day", $value, array(
						"timestamp" => $timestamp,
						"label"     => $day,
						"count"     => $this->model->readable_number($record[$key])));
				}

				$this->output->close_tag();
			}

			$this->output->close_tag();
		}

		private function show_day_information($type, $timestamp) {	
			$filter = new filter($this->db, $this->output);

			$stats = $this->model->get_day_information($type, $timestamp, $filter->hostname, $filter->webserver);

			$this->output->open_tag("day", array("label" => $this->graphs[$type]));
			foreach ($stats as $stat) {
				switch ($type) {
					case "requests":
					case "bytes_sent":
						$stat["count"] = $this->model->readable_number($stat["count"]);
						break;
				}

				$this->output->record($stat, "stat");
			}
			$this->output->close_tag();
		}

		public function execute() {
			if (in_array($this->page->pathinfo[1], array_keys($this->graphs)) == false) {
				$this->show_graphs();
			} else {
				$this->show_day_information($this->page->pathinfo[1], $this->page->pathinfo[2]);
			}
		}
	}
?>
