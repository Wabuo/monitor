<?php
	class dashboard_model extends model {
		public function get_webservers() {
			$query = "select * from webservers w, webserver_user a ".
			         "where w.id=a.webserver_id and a.user_id=%d ".
			         "order by name";

			return $this->db->execute($query, $this->user->id);
		}

		public function get_top_bad_requests($timestamp) {
			$query = "select sum(result_bad_request) as count, w.name as label ".
					 "from server_statistics s, webserver_user a, webservers w ".
					 "where s.webserver_id=a.webserver_id and a.webserver_id=w.id ".
					 "and a.user_id=%d and s.timestamp_begin>%s and result_bad_request>0 ".
					 "group by name order by count desc";

			return $this->db->execute($query, $this->user->id, $timestamp);
		}

		public function get_top_bans($timestamp) {
			$query = "select sum(bans) as count, h.hostname as label ".
					 "from host_statistics s, hostnames h, webserver_user a ".
					 "where s.hostname_id=h.id and s.webserver_id=a.webserver_id ".
					 "and a.user_id=%d and s.timestamp_begin>%s and bans>0 ".
			         "and h.visible=%d group by hostname order by count desc";

			return $this->db->execute($query, $this->user->id, $timestamp, YES);
		}

		public function get_top_cgi_errors($timestamp) {
			$query = "select sum(cgi_errors) as count, h.hostname as label ".
					 "from cgi_statistics s, hostnames h, webserver_user a ".
					 "where s.hostname_id=h.id and s.webserver_id=a.webserver_id and a.user_id=%d ".
					 "and s.timestamp_begin>%s and cgi_errors>0 and h.visible=%d ".
					 "group by hostname order by count desc";

			return $this->db->execute($query, $this->user->id, $timestamp, YES);
		}

		public function get_top_events($timestamp) {
			$query = "select count(event) as count, w.name as label ".
					 "from events e, webserver_user a, webservers w ".
					 "where e.webserver_id=a.webserver_id and a.webserver_id=w.id ".
					 "and a.user_id=%d and e.timestamp>%s ".
					 "group by name order by count desc";

			return $this->db->execute($query, $this->user->id, $timestamp);
		}

		public function get_top_exploit_attempts($timestamp) {
			$query = "select sum(exploit_attempts) as count, h.hostname as label ".
					 "from host_statistics s, hostnames h, webserver_user a ".
					 "where s.hostname_id=h.id and s.webserver_id=a.webserver_id and a.user_id=%d ".
					 "and s.timestamp_begin>%s and exploit_attempts>0 and h.visible=%d ".
					 "group by hostname order by count desc";

			return $this->db->execute($query, $this->user->id, $timestamp, YES);
		}

		public function get_top_forbiddens($timestamp) {
			$query = "select sum(result_forbidden) as count, h.hostname as label ".
					 "from host_statistics s, hostnames h, webserver_user a ".
					 "where s.hostname_id=h.id and s.webserver_id=a.webserver_id ".
					 "and a.user_id=%d and s.timestamp_begin>%s and result_forbidden>0 ".
			         "and h.visible=%d group by hostname order by count desc";

			return $this->db->execute($query, $this->user->id, $timestamp, YES);
		}

		public function get_top_internal_errors($timestamp) {
			$query = "select sum(result_internal_error) as count, h.hostname as label ".
			         "from host_statistics s, hostnames h, webserver_user a ".
					 "where s.hostname_id=h.id and s.webserver_id=a.webserver_id ".
					 "and a.user_id=%d and s.timestamp_begin>%s and result_internal_error>0 ".
					 "and h.visible=%d group by hostname order by count desc";

			return $this->db->execute($query, $this->user->id, $timestamp, YES);
		}

		public function get_top_not_founds($timestamp) {
			$query = "select h.id, sum(result_not_found) as count, h.hostname as label ".
			         "from host_statistics s, hostnames h, webserver_user a ".
					 "where s.hostname_id=h.id and s.webserver_id=a.webserver_id ".
					 "and a.user_id=%d and s.timestamp_begin>%s and result_not_found>0 ".
			         "and h.visible=%d group by hostname order by count desc";

			return $this->db->execute($query, $this->user->id, $timestamp, YES);
		}
	}
?>
