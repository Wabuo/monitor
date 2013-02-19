<?php
	class origins_model extends model {
		public function get_origins($filter_hostname, $filter_webserver) {
			$query = "select count(*) as count, c.country ".
					 "from requests r, ip2nationCountries c ".
					 "where r.country_id=c.code";

			$filter_args = array();
			if ($filter_hostname != 0) {
				$query .= " and r.hostname_id=%d";
				array_push($filter_args, $filter_hostname);
			}
			if ($filter_webserver != 0) {
				$query .= " and r.webserver_id=%d";
				array_push($filter_args, $filter_webserver);
			}

			$query .= " group by country order by count desc";

			return $this->db->execute($query, $filter_args);
		}
	}
?>
