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
			call_user_func_array(array("parent", "__construct"), $arguments);

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
			$select = "";
			foreach ($this->columns as $column) {
				$select .= ", sum(t.".$column.") as ".$column;
			}

			$query = "select *".$select." from %S t, ".$this->from."webserver_user a ".
			         "where t.webserver_id=a.webserver_id and a.user_id=%d ".$this->where."and ((date>=%s and date<=%s))";
			$args = array($this->table, $this->user->id, $begin, $end);

			if ($this->hostnames && ($filter_hostname != 0)) {
				$query .= " and t.hostname_id=%d";
				array_push($args, $filter_hostname);
			}
			if ($filter_webserver != 0) {
				$query .= " and t.webserver_id=%d";
				array_push($args, $filter_webserver);
			}

			$query .= " group by date limit %d,%d";

			$stats = array();
			foreach ($this->columns as $column) {
				$stats[$column] = 0;
			}

			$result = array();
			$timestamp = strtotime($begin." 00:00:00");
			$timestamp_end = strtotime($end." 00:00:00");
			while ($timestamp <= $timestamp_end) {
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
					foreach ($this->columns as $column) {
						$result[$entry["date"]][$column] = $entry[$column];
					}
				}

				$offset += $limit;
			} while (count($entries) == $limit);

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

		public function get_day_statistics($column, $date, $filter_hostname, $filter_webserver) {
			if (in_array($column, $this->columns) == false) {
				return false;
			}

			$query = "select sum(t.%S) as count, hour from %S t, ".$this->from."webservers w, webserver_user a ".
			         "where t.webserver_id=w.id and w.id=a.webserver_id and a.user_id=%d ".$this->where."and date=%s ";
			$args = array($column, $this->table, $this->user->id, $date);

			if ($this->hostnames && ($filter_hostname != 0)) {
				$query .= "and t.hostname_id=%d ";
				array_push($args, $filter_hostname);
			}
			if ($filter_webserver != 0) {
				$query .= "and t.webserver_id=%d ";
				array_push($args, $filter_webserver);
			}

			$query .= "group by t.hour order by hour";

			if (($result = $this->db->execute($query, $args)) === false) {	
				return false;
			}

			$stats = array();
			for ($i = 0; $i < 24; $i++) {
				$stats[$i] = 0;
			}

			foreach ($result as $item) {
				$stats[$item["hour"]] = $item["count"];
			}

			return $stats;
		}

		public function get_day_information($column, $date, $filter_hostname, $filter_webserver) {
			if (in_array($column, $this->columns) == false) {
				return false;
			}

			$query = "select sum(t.%S) as count, ".$this->select."w.name as webserver ".
			         "from %S t, ".$this->from."webservers w, webserver_user a ".
			         "where t.webserver_id=w.id and w.id=a.webserver_id and a.user_id=%d ".$this->where.
			         	"and date=%s and %S>0 ";
			$args = array($column, $this->table, $this->user->id, $date, $column);

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
