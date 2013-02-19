<?php
	class server_statistics_model extends model {
		public function get_webservers() {
			$query = "select * from webservers order by name";

			return $this->db->execute($query);
		}

		public function get_top_connections($webserver_id, $limit) {
			$query = "select UNIX_TIMESTAMP(timestamp_begin) as timestamp_begin, ".
					 "UNIX_TIMESTAMP(timestamp_end) as timestamp_end, simult_conns ".
					 "from server_statistics where webserver_id=%d ".
					 "order by simult_conns desc, timestamp_begin desc limit %d";

			return $this->db->execute($query, $webserver_id, $limit);
		}
	}
?>
