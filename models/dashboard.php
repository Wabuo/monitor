<?php
	class dashboard_model extends model {
		public function get_webservers() {
			$query = "select * from webservers order by name";

			return $this->db->execute($query);
		}

		public function get_top_exploit_attempts($timestamp) {
			$query = "select sum(exploit_attempts) as attempts, h.hostname ".
					 "from host_statistics s, hostnames h ".
					 "where s.hostname_id=h.id and s.timestamp_begin>%s and exploit_attempts>0 ".
					 "group by hostname order by attempts desc";

			return $this->db->execute($query, $timestamp);
		}

		public function get_top_bans($timestamp) {
			$query = "select sum(bans) as bans, h.hostname ".
					 "from host_statistics s, hostnames h ".
					 "where s.hostname_id=h.id and s.timestamp_begin>%s and bans>0 ".
					 "group by hostname order by bans desc";

			return $this->db->execute($query, $timestamp);
		}

		public function get_top_forbiddens($timestamp) {
			$query = "select sum(result_forbidden) as forbidden, h.id, h.hostname ".
					 "from host_statistics s, hostnames h ".
					 "where s.hostname_id=h.id and s.timestamp_begin>%s and result_forbidden>0 ".
					 "group by hostname order by forbidden desc";

			return $this->db->execute($query, $timestamp);
		}

		public function get_top_not_founds($timestamp) {
			$query = "select h.id, sum(result_not_found) as not_found, h.id, h.hostname ".
					 "from host_statistics s, hostnames h ".
					 "where s.hostname_id=h.id and s.timestamp_begin>%s and result_not_found>0 ".
					 "group by hostname order by not_found desc";

			return $this->db->execute($query, $timestamp);
		}

		public function get_top_internal_errors($timestamp) {
			$query = "select sum(result_internal_error) as errors, h.id, h.hostname ".
					 "from host_statistics s, hostnames h ".
					 "where s.hostname_id=h.id and s.timestamp_begin>%s and result_internal_error>0 ".
					 "group by hostname order by errors desc";

			return $this->db->execute($query, $timestamp);
		}
	}
?>
