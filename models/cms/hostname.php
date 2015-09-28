<?php
	class cms_hostname_model extends model {
		public function get_hostnames() {
			$query = "select distinct h.* from hostnames h, host_statistics s, webservers w, webserver_user l ".
			         "where h.id=s.hostname_id and s.webserver_id=w.id and w.id=l.webserver_id and l.user_id=%d ".
			         "order by hostname";

			return $this->db->execute($query, $this->user->id);
		}

		public function update_hostnames($hostnames) {	
			if (is_array($hostnames) == false) {
				$hostnames = array();
			}

			if (($result = $this->get_hostnames()) == false) {
				return false;
			}

			$all = array();
			foreach ($result as $hostname) {
				array_push($all, (int)$hostname["id"]);
			}

			$selectors = implode(", ", array_fill(0, count($all), "%d"));
			$query = "update hostnames set visible=%d where id in (".$selectors.")";
			if ($this->db->query($query, 0, $all) === false) {
				return false;
			}

			$query = "update hostnames set visible=%d where id=%d";
			foreach ($hostnames as $hostname_id) {
				if (in_array($hostname_id, $all) == false) {
					continue;
				}

				if ($this->db->query($query, 1, $hostname_id) === false) {
					return false;
				}
			}

			return true;
		}

		public function get_hostname($hostname_id) {
			$query = "select h.hostname from hostnames h, host_statistics s, webservers w, webserver_user l ".
			         "where h.id=s.hostname_id and s.webserver_id=w.id and w.id=l.webserver_id and ".
			         "l.user_id=%d and h.id=%d";
			if (($result = $this->db->execute($query, $this->user->id, $hostname_id)) == false) {
				return false;
			}

			return $result[0]["hostname"];
		}

		public function delete_hostname($hostname_id) {
			$queries = array(
				array("delete from cgi_statistics where hostname_id=%d", $hostname_id),
				array("delete from host_statistics where hostname_id=%d", $hostname_id),
				array("delete from hostnames where id=%d", $hostname_id));

			return $this->db->transaction($queries) !== false;
		}
	}
?>
