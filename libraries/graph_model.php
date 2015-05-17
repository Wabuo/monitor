<?php
	abstract class graph_model extends model {
		protected $columns = array();
		protected $table = null;
		protected $hostnames = true;
		private $select = "";
		private $from = "";
		private $where = "";

		public function __construct() {
			$arguments = func_get_args();
			call_user_func_array(array(parent, "__construct"), $arguments);

			if ($this->hostnames) {
				$this->select = "h.hostname, ";
				$this->from = "hostnames h, ";
				$this->where = "and t.hostname_id=h.id and h.visible=1 ";
			}
		}

		public function __get($key) {
			switch ($key) {
				case "table": return $this->table;
				case "hostnames": return $this->hostnames;
			}

			return null;
		}

		public function get_statistics($begin, $end, $filter_hostname, $filter_webserver) {
			$query = "select * from %S t, ".$this->from."webserver_user a ".
			         "where t.webserver_id=a.webserver_id and a.user_id=%d ".$this->where.
			         	"and ((timestamp_begin>%s and timestamp_begin<%s) or (timestamp_end>%s and timestamp_end<%s))";
			$args = array($this->table, $this->user->id, $begin, $end, $begin, $end);

			if ($this->hostnames && ($filter_hostname != 0)) {
				$query .= " and t.hostname_id=%d";
				array_push($args, $filter_hostname);
			}
			if ($filter_webserver != 0) {
				$query .= " and t.webserver_id=%d";
				array_push($args, $filter_webserver);
			}

			$query .= " limit %d,%d";

			$timestamp_begin = strtotime($begin." 00:00:00");
			$timestamp_end = strtotime($end." 00:00:00");

			$stats = array();
			foreach ($this->columns as $column) {
				$stats[$column] = 0;
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
				if (($entries = $this->db->execute($query, $args, $offset, $limit)) === false) {
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

						foreach ($this->columns as $column) {
							$result[$day_end][$column] += (1 - $percentage) * $entry[$column];
						}
					}

					foreach ($this->columns as $column) {
						$result[$day][$column] += $percentage * $entry[$column];
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

		static public function readable_number($number) {
			if ($number > 1000000000) {
				return sprintf("%0.1f G", $number / 1000000000);
			} else if ($number > 1000000) {
				return sprintf("%0.1f M", $number / 1000000);
			} else if ($number > 1000) {
				return sprintf("%0.1f k", $number / 1000);
			}

			return $number;
		}

		public function get_day_information($column, $timestamp, $filter_hostname, $filter_webserver) {
			if (in_array($column, $this->columns) == false) {
				return false;
			}

			$begin = date("Y-m-d 00:00:00", $timestamp);
			$end = date("Y-m-d 23:59:59", $timestamp);

			$query = "select sum(t.%S) as count, ".$this->select."w.name as webserver ".
			         "from %S t, ".$this->from."webservers w, webserver_user a ".
			         "where t.webserver_id=w.id and w.id=a.webserver_id and a.user_id=%d ".$this->where.
			         	"and timestamp_begin>=%s and timestamp_begin<=%s and %S>0 ";
			$args = array($column, $this->table, $this->user->id, $begin, $end, $column);

			if ($this->hostnames && ($filter_hostname != 0)) {
				$query .= "and t.hostname_id=%d ";
				array_push($args, $filter_hostname);
			}
			if ($filter_webserver != 0) {
				$query .= "and t.webserver_id=%d ";
				array_push($args, $filter_webserver);
			}

			$query .= "group by ".$this->select."w.name order by count desc";

			return $this->db->execute($query, $args);
		}
	}
?>
