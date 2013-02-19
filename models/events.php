<?php
	class events_model extends model {
		public function count_events() {
			$query = "select count(*) as count from events";

			if (($result = $this->db->execute($query)) === false) {
				return false;
			}

			return $result[0]["count"];
		}

		public function get_events($offset, $limit) {
			$query = "select e.event, UNIX_TIMESTAMP(e.timestamp) as timestamp, w.name ".
					 "from events e, webservers w ".
					 "where e.webserver_id=w.id order by timestamp desc, name limit %d,%d";

			return $this->db->execute($query, $offset, $limit);
		}
	}
?>
