<?php
	class dashboard_model extends model {
		private $today = null;
		private $hour = null;
		private $change = null;

		public function __construct() {
			$arguments = func_get_args();
			call_user_func_array(array("parent", "__construct"), $arguments);

			$this->today = date("Y-m-d");
			$this->hour = (int)date("G");
			$this->change = 1 - ((int)date("i") / 59);
		}

		public function get_webservers() {
			$query = "select * from webservers w, webserver_user a ".
			         "where w.id=a.webserver_id and a.user_id=%d ".
			         "order by name";

			return $this->db->execute($query, $this->user->id);
		}

		private function get_median($data, $name) {
			$values = array();

			foreach ($data as $item) {
				if ($item["name"] != $name) {
					continue;
				}

				if ($item["date"] == $this->today) {
					continue;
				}
				$values[$item["date"]] = $item["count"];
			}
			sort($values);

			$count = count($values);
			if ($count & 1 == 1) {
				$center = ($count + 1) / 2;
				$median = $values[$center];
			} else {
				$center = $count / 2;
				$median = round(($values[$center] + $values[$center + 1]) / 2);
			}

			return $median;
		}

		private function sort_statistics($a, $b) {
			if ($a["change"] == $b["change"]) {
				return strcmp($a["name"], $b["name"]);
			}

			return ($a["change"] > $b["change"]) ? -1 : 1;
		}

		private function create_statistics($result) {
			$names = array();
			foreach ($result as $i => $item) {
				$result[$i]["count"] -= round($item["same_hour"] * $this->change);
				array_push($names, $item["name"]);
			}
			$names = array_unique($names);

			$stats = array();
			foreach ($names as $name) {
				$new = array();
				$new["median"] = $this->get_median($result, $name);
				$new["today"] = 0;

				foreach ($result as $item) {
					if (($item["name"] == $name) && ($item["date"] == $this->today)) {
						$new["today"] += $item["count"];
					}
				}

				if ($new["median"] == 0) {
					$new["change"] = $new["today"] * 100;
				} else {
					$new["change"] = round(($new["today"] - $new["median"]) / $new["median"]) * 100;
				}

				if (($new["today"] >= $this->settings->dashboard_threshold_value) && ($new["change"] >= $this->settings->dashboard_threshold_change)) {
					$stats[$name] = $new;
				}
			}

			uasort($stats, array($this, "sort_statistics"));

			return $stats;
		}

		private function get_hostname_statistics($table, $column) {
			$query = "select h.id, h.hostname as name, s.date, sum(%S) as count, ".
			           "(select sum(%S) from %S where hostname_id=s.hostname_id and date=s.date and hour=%d) as same_hour ".
			         "from %S s, webservers w, webserver_user u, hostnames h ".
			         "where s.webserver_id=w.id and w.id=u.webserver_id and u.user_id=%d and s.hostname_id=h.id and h.visible=%d and hour<=%d ".
			         "group by s.hostname_id,date";
			$args = array($column, $column, $table, $this->hour, $table, $this->user->id, YES, $this->hour);

			if (($result = $this->db->execute($query, $args)) === false) {
				return false;
			}

			return $this->create_statistics($result);
		}

		public function get_cgi_statistics($column) {
			return $this->get_hostname_statistics("cgi_statistics", $column);
		}

		public function get_host_statistics($column) {
			return $this->get_hostname_statistics("host_statistics", $column);
		}

		public function get_server_statistics($column) {
			$query = "select w.id, w.name, s.date, sum(%S) as count, ".
			           "(select sum(%S) from server_statistics where id=s.id and date=s.date and hour=%d) as same_hour ".
			         "from server_statistics s, webservers w, webserver_user u ".
			         "where s.webserver_id=w.id and w.id=u.webserver_id and u.user_id=%d and hour<=%d ".
			         "group by s.webserver_id,date";
			$args = array($column, $column, $this->hour, $this->user->id, $this->hour);

			if (($result = $this->db->execute($query, $args)) === false) {
				return false;
			}

			return $this->create_statistics($result);
		}
	}
?>
