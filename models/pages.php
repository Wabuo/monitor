<?php
	class pages_model extends model {
		 public function get_pages($filter_hostname, $filter_webserver) {
		 	$query = "select count(*) as count, u.request_uri ".
			         "from requests r, request_uris u ".
			         "where r.request_uri_id=u.id and return_code=%d";
		 	
			$filter_args = array(200);
			if ($filter_hostname != 0) {
				$query .= " and r.hostname_id=%d";
				array_push($filter_args, $filter_hostname);
			}
			if ($filter_webserver != 0) {
				$query .= " and r.webserver_id=%d";
				array_push($filter_args, $filter_webserver);
			}

			$query .= " group by u.request_uri order by count desc";

			return $this->db->execute($query, $filter_args);
		}
	}
?>
