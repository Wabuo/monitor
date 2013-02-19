<?php
	class admin_role_model extends model {
		public function get_all_roles() {
			$query = "select *, (select count(*) from user_role where role_id=r.id) as users from roles r order by name";

			return $this->db->execute($query);
		}

		public function get_role($role_id) {
			return $this->db->entry("roles", $role_id);
		}

		public function get_role_members($role_id) {
			$query = "select u.id, u.fullname from users u, user_role m ".
					 "where u.id=m.user_id and m.role_id=%d order by u.fullname";

			return $this->db->execute($query, $role_id);
		}

		public function get_restricted_pages() {
			if (($columns = $this->db->execute("show columns from %S", "roles")) === false) {
				return false;
			}

			$result = array();
			foreach ($columns as $column) {
				if (strstr($column["Type"], "tinyint") !== false) {
					array_push($result, $column["Field"]);
				}
			}
			sort($result);

			return $result;
		}

		public function role_oke($role) {
			$result = true;

			if ($role["id"] == ADMIN_ROLE_ID) {
				$this->output->add_message("This role cannot be changed.");
				$result = false;
			}
			
			if ($role["name"] == "") {
				$this->output->add_message("The name cannot be empty.");
				$result = false;
			}

			return $result;
		}

		private function fix_role_data($keys, $role) {
			/* Work-around for PHP's nasty dot-to-underscore replacing
			 */
			foreach ($keys as $key) {
				if (isset($role[$key]) == false) {
					$alt_key = str_replace(".", "_", $key);
					$role[$key] = $role[$alt_key];
				}
			}

			return $role;
		}

		public function create_role($role) {
			$keys = $this->get_restricted_pages();
			$role = $this->fix_role_data($keys, $role);

			foreach ($keys as $key) {
				$role[$key] = is_true($role[$key]) ? YES : NO;
			}
			array_unshift($keys, "id", "name");

			$role["id"] = null;

			return $this->db->insert("roles", $role, $keys) !== false;
		}

		public function update_role($role) {
			$keys = $this->get_restricted_pages();
			$role = $this->fix_role_data($keys, $role);

			foreach ($keys as $key) {
				$role[$key] = is_true($role[$key]) ? YES : NO;
			}

			array_unshift($keys, "name");

			return $this->db->update("roles", $role["id"], $role, $keys) !== false;
		}

		public function delete_role($role_id) {
			$queries = array(
				array("delete from page_access where role_id=%d", $role_id),
				array("delete from user_role where role_id=%d", $role_id),
				array("delete from roles where id=%d", $role_id));

			return $this->db->transaction($queries);
		}
	}
?>
