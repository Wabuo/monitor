<?php
	class admin_switch_model extends model {
		public function get_user($user_id) {
			return $this->db->entry("users", $user_id);
		}

		public function get_users() {
			$query = "select * from users where id>0 and id!=%d order by username";

			return $this->db->execute($query, $this->user->id);
		}
	}
?>
