<?php
	class admin_hostname_model extends model {
		public function get_hostnames() {
			$query = "select * from hostnames order by hostname";

			return $this->db->execute($query);
		}

		public function update_hostnames($hostnames) {
			if (is_array($hostnames) == false) {
				return false;
			} else if (count($hostnames) == 0) {
				return false;
			}

			if ($this->db->query("update hostnames set visible=%d", 0) === false) {
				return false;
			}

			$query = "update hostnames set visible=%d where id=%d";
			foreach ($hostnames as $hostname_id) {
				if ($this->db->query($query, 1, $hostname_id) === false) {
					return false;
				}
			}

			return true;
		}
	}
?>
