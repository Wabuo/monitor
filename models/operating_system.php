<?php
	class operating_system_model extends model {
		public function get_information($filter_hostname, $filter_webserver) {
			$query = "select o.os, count(*) as count ".
					 "from requests r, user_agents u, user_agent_browser b, user_agent_os o ".
					 "where r.user_agent_id=u.id and u.browser_id=b.id and u.os_id=o.id";

			$filter_args = array();
			if ($filter_hostname != 0) {
				$query .= " and r.hostname_id=%d";
				array_push($filter_args, $filter_hostname);
			}
			if ($filter_webserver != 0) {
				$query .= " and r.webserver_id=%d";
				array_push($filter_args, $filter_webserver);
			}
			$query .= " group by os order by count desc";

			return $this->db->execute($query, $filter_args);
		}
	}
?>
