<?php
	class events_model extends model {
		public function count_events($filter_webserver) {
			$query = "select count(*) as count from events e, webserver_user a ".
					 "where e.webserver_id=a.webserver_id and a.user_id=%d";
			$args = array($this->user->id);

			if ($filter_webserver != 0) {
				$query .= " and e.webserver_id=%d";
				array_push($args, $filter_webserver);
			}

			if (($result = $this->db->execute($query, $args)) === false) {
				return false;
			}

			return $result[0]["count"];
		}

		public function get_events($offset, $limit, $filter_webserver) {
			$query = "select e.event, UNIX_TIMESTAMP(e.timestamp) as timestamp, w.name ".
					 "from events e, webservers w, webserver_user a ".
					 "where e.webserver_id=w.id and w.id=a.webserver_id and a.user_id=%d";
			$args = array($this->user->id);

			if ($filter_webserver != 0) {
				$query .= " and e.webserver_id=%d";
				array_push($args, $filter_webserver);
			}

			$query .= " order by timestamp desc, name limit %d,%d";
			array_push($args, $offset, $limit);

			return $this->db->execute($query, $args);
		}
	}
?>
