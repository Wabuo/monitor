<?php
	class admin_user_model extends model {
		public function count_users() {
			$query = "select count(*) as count from users";

			if (($result = $this->db->execute($query)) == false) {
				return false;
			}

			return $result[0]["count"];
		}

		public function get_all_users($offset, $count) {
			$query = "select * from users u order by username limit %d,%d";

			if (($users = $this->db->execute($query, $offset, $count)) === false) {
				return false;
			}

			$query = "select * from user_role where user_id=%d and role_id=%d";
			foreach ($users as $i => $user) {
				if (($role = $this->db->execute($query, $user["id"], ADMIN_ROLE_ID)) === false) {
					return false;
				}
				$users[$i]["is_admin"] = count($role) > 0;
			}

			return $users;
		}

		public function get_user($user_id) {
			if (($user = $this->db->entry("users", $user_id)) == false) {
				return false;
			}

			$query = "select role_id from user_role where user_id=%d";
			if (($roles = $this->db->execute($query, $user_id)) === false) {
				return false;
			}

			$user["roles"] = array();
			foreach ($roles as $role) {
				array_push($user["roles"], $role["role_id"]);
			}

			$query = "select webserver_id from webserver_user where user_id=%d";
			if (($webservers = $this->db->execute($query, $user_id)) === false) {
				return false;
			}

			$user["webservers"] = array();
			foreach ($webservers as $webserver) {
				array_push($user["webservers"], (int)$webserver["webserver_id"]);
			}

			return $user;
		}

		public function get_all_roles() {
			$query = "select * from roles order by name";

			return $this->db->execute($query);
		}

		public function get_all_webservers() {
			$query = "select * from webservers order by name";

			return $this->db->execute($query);
		}

		public function user_has_admin_role($roles) {
			return in_array(ADMIN_ROLE_ID, $roles);
		}

		public function user_oke($user) {
			$result = true;

			/* Non-admins cannot edit admins
			 */
			if (($this->user->is_admin == false) && isset($user["id"])) {
				if (($current = get_user($user["id"])) != false) {
					$this->output->add_message("User not found.");
					return false;
				}
				if (in_array(ADMIN_ROLE_ID, $current["roles"])) {
					$this->output->add_message("You are not allowed to edit this user.");
					return false;
				}
			}

			if (($user["username"] == "") || ($user["fullname"] == "")) {
				$this->output->add_message("The username and full name cannot be empty.");
				$result = false;
			} else if (valid_input($user["username"], VALIDATE_LETTERS.VALIDATE_NUMBERS) == false) {
				$this->output->add_message("Invalid characters in username.");
				$result = false;
			} else if (($check = $this->db->entry("users", $user["username"], "username")) != false) {
				if ($check["id"] != $user["id"]) {
					$this->output->add_message("User already exists.");
					$result = false;
				}
			}

			return $result;
		}

		public function delete_oke($user_id) {
			return true;
		}

		public function assign_roles_to_user($user) {
			if ($this->db->query("delete from user_role where user_id=%d", $user["id"]) === false) {
				return false;
			}

			if (is_array($user["roles"]) == false) {
				return true;
			}

			foreach ($user["roles"] as $role_id) {
				/* Non-admins cannot assign the admin role
				 */
				if (($this->user->is_admin == false) && ($role_id == ADMIN_ROLE_ID)) {
					continue;
				}
				if ($this->db->query("insert into user_role values (%d, %d)", $user["id"], $role_id) === false) {
					return false;
				}
			}
			
			return true;
		}

		public function assign_webservers_to_user($user) {
			if ($this->db->query("delete from webserver_user where user_id=%d", $user["id"]) === false) {
				return false;
			}

			if (is_array($user["webservers"]) == false) {
				return true;
			}

			foreach ($user["webservers"] as $webserver_id) {
				if ($this->db->query("insert into webserver_user values (%d, %d)", $webserver_id, $user["id"]) === false) {
					return false;
				}
			}
			
			return true;
		}

		public function create_user($user) {
			$keys = array("id", "username", "password", "one_time_key", "status", "fullname", "email", "prowl_key");

			$user["id"] = null;
			$user["one_time_key"] = null;
			if (is_false($user["password_hashed"])) {
				$user["password"]  = md5($user["password"]);
			}

			if ($this->db->query("begin") === false) {
				return false;
			}

			if ($this->db->insert("users", $user, $keys) === false) {
				$this->db->query("rollback");
				return false;
			}
			$user["id"] = $this->db->last_insert_id;

			if ($this->assign_roles_to_user($user) === false) {
				$this->db->query("rollback");
				return false;
			}

			if ($this->assign_webservers_to_user($user) === false) {
				$this->db->query("rollback");
				return false;
			}

			return $this->db->query("commit") !== false;
		}

		public function update_user($user) {
			$keys = array("username", "fullname", "email", "prowl_key");

			if ($user["password"] != "") {
				array_push($keys, "password");
				if (is_false($user["password_hashed"])) {
					$user["password"]  = md5($user["password"]);
				}
			}
			if (is_array($user["roles"]) == false) {
				$user["roles"] = array();
			}
			if ($this->user->id != $user["id"]) {
				array_push($keys, "status");
			} else if (($current = $this->get_user($user["id"])) == false) {
				return false;
			} else if (in_array(ADMIN_ROLE_ID, $current["roles"]) && (in_array(ADMIN_ROLE_ID, $user["roles"]) == false)) {
				array_unshift($user["roles"], ADMIN_ROLE_ID);
			}

			if ($this->db->query("begin") === false) {
				return false;
			}

			if ($this->assign_roles_to_user($user) === false) {
				$this->db->query("rollback");
				return false;
			}

			if ($this->assign_webservers_to_user($user) === false) {
				$this->db->query("rollback");
				return false;
			}

			if ($this->db->update("users", $user["id"], $user, $keys) === false) {
				$this->db->query("rollback");
				return false;
			}

			return $this->db->query("commit") !== false;
		}

		public function delete_user($user_id) {
			if ($user_id == $this->user->id) {
				return false;
			}

			$queries = array(
				array("delete from webserver_user where user_id=%d", $user_id),
				array("delete from user_role where user_id=%d", $user_id),
				array("delete from users where id=%d", $user_id));

			return $this->db->transaction($queries);
		}
	}
?>
