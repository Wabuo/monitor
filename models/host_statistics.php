<?php
	class host_statistics_model extends model {
		private $types = array("requests", "bytes_sent", "bans", "exploit_attempts",
				"result_forbidden", "result_not_found", "result_internal_error");

		public function get_statistics($begin, $end, $filter_hostname, $filter_webserver) {
			$query = "select * from host_statistics where ".
					 "((timestamp_begin>%s and timestamp_begin<%s) or ".
					 "(timestamp_end>%s and timestamp_end<%s))";

			$filter_args = array();
			if ($filter_hostname != 0) {
				$query .= " and hostname_id=%d";
				array_push($filter_args, $filter_hostname);
			}
			if ($filter_webserver != 0) {
				$query .= " and webserver_id=%d";
				array_push($filter_args, $filter_webserver);
			}

			$query .= " limit %d,%d";

			$timestamp_begin = strtotime($begin." 00:00:00");
			$timestamp_end = strtotime($end." 00:00:00");

			$stats = array();
			foreach ($this->types as $key) {
				$stats[$key] = 0;
			}

			$result = array();
			$timestamp = $timestamp_begin;
			while ($timestamp < $timestamp_end) {
				$day = date("Y-m-d", $timestamp);
				$result[$day] = $stats;
				$timestamp += DAY;
			}
			
			$offset = 0;
			$limit = 3000;
			do {
				if (($entries = $this->db->execute($query, $begin, $end, $begin, $end, $filter_args, $offset, $limit)) === false) {
					return false;
				}

				foreach ($entries as $entry) {
					$entry["timestamp_begin"] = strtotime($entry["timestamp_begin"]);
					$entry["timestamp_end"] = strtotime($entry["timestamp_end"]);

					$day = date("Y-m-d", $entry["timestamp_begin"]);
					$day_end = date("Y-m-d", $entry["timestamp_end"]);

					if ($day == $day_end) {
						/* Entry within one day
						 */
						$percentage = 1;
					} else if ($entry["timestamp_begin"] < $timestamp_begin) {
						/* Entry begins before begin time
						 */
						if (($timespan = $entry["timestamp_end"] - $entry["timestamp_begin"]) == 0) {
							continue;
						}
						$percentage = ($entry["timestamp_end"] - $timestamp_begin) / $timespan;
						$day = $day_end;
					} else if ($entry["timestamp_end"] > $timestamp_end) {
						/* Entry ends after end time
						 */
						if (($timespan = $entry["timestamp_end"] - $entry["timestamp_begin"]) == 0) {
							continue;
						}
						$percentage = ($timestamp_end - $entry["timestamp_begin"]) / $timespan;
					} else {
						/* Entry spans two days
						 */
						if (($timespan = $entry["timestamp_end"] - $entry["timestamp_begin"]) == 0) {
							continue;
						}
						$break = strtotime($day_end." 00:00:00");
						$percentage = ($break - $entry["timestamp_begin"]) / $timespan;

						foreach ($this->types as $key) {
							$result[$day_end][$key] += (1 - $percentage) * $entry[$key];
						}
					}

					foreach ($this->types as $key) {
						$result[$day][$key] += $percentage * $entry[$key];
					}
				}

				$offset += $limit;
			} while (count($entries) > 0);

			foreach ($result as $d => $day) {
				foreach ($day as $v => $value) {
					$result[$d][$v] = round($value);
				}
			}
			ksort($result);

			return $result;
		}

		public function readable_number($number) {
			if ($number > 1000000000) {
				return sprintf("%0.1f G", $number / 1000000000);
			} else if ($number > 1000000) {
				return sprintf("%0.1f M", $number / 1000000);
			} else if ($number > 1000) {
				return sprintf("%0.1f k", $number / 1000);
			}

			return $number;
		}

		public function get_day_information($type, $timestamp, $filter_hostname, $filter_webserver) {
			if (in_array($type, $this->types) == false) {
				return false;
			}

			$begin = date("Y-m-d 00:00:00", $timestamp);
			$end = date("Y-m-d 23:59:59", $timestamp);

			$query = "select sum(s.%S) as count, h.hostname, w.name as webserver ".
				"from host_statistics s, hostnames h, webservers w ".
				"where s.hostname_id=h.id and s.webserver_id=w.id and ".
					"timestamp_begin>=%s and timestamp_begin<=%s and %S>0 ";
			$args = array($type, $begin, $end, $type);

			if ($filter_hostname != 0) {
				$query .= "and hostname_id=%d ";
				array_push($args, $filter_hostname);
			}
			if ($filter_webserver != 0) {
				$query .= "and webserver_id=%d ";
				array_push($args, $filter_webserver);
			}

			$query .= "group by hostname_id order by count desc";

			return $this->db->execute($query, $args);
		}
	}
?>
