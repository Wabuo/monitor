<?php
	class admin_user_controller extends controller {
		private function show_user_overview() {
			if (($user_count = $this->model->count_users()) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$paging = new pagination($this->output, "admin_users", $this->settings->admin_page_size, $user_count);

			$users = $this->model->get_all_users($paging->offset, $paging->size);
			$roles = $this->model->get_all_roles();
			if (($users === false) || ($roles === false)) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$status = array("Disabled", "Change password", "Active");

			$this->output->open_tag("overview");

			$this->output->open_tag("users");
			foreach ($users as $user) {
				/* Non-admins cannot edit admins
				 */
				if (($this->user->is_admin == false) && $user["is_admin"]) {
					continue;
				}

				$user["status"] = $status[$user["status"]];

				$this->output->open_tag("user", array("id" => $user["id"]));
				$this->output->add_tag("username", $user["username"]);
				$this->output->add_tag("fullname", $user["fullname"]);
				$this->output->add_tag("email", $user["email"]);
				$this->output->add_tag("status", $user["status"]);
				$this->output->close_tag();
			}
			$this->output->close_tag();

			$paging->show_browse_links();

			$this->output->close_tag();
		}

		private function show_user_form($user) {
			if (isset($user["roles"]) == false) {
				$user["roles"] = array();
			}

			if (($roles = $this->model->get_all_roles()) == false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			/* Non-admins cannot edit admins
			 */
			if (($this->user->is_admin == false) && in_array(ADMIN_ROLE_ID, $user["roles"])) {
				$this->output->add_tag("result", "You are not allowed to edit this user.");
				return;
			}

			$this->output->add_javascript("md5.js");
			$this->output->add_javascript("admin/user.js");

			$this->output->open_tag("edit");

			$this->output->open_tag("status");
			$status = array(
				USER_STATUS_DISABLED =>  "Disabled",
				USER_STATUS_CHANGEPWD => "Change password",
				USER_STATUS_ACTIVE =>    "Active");
			foreach ($status as $id => $stat) {
				$this->output->add_tag("status", $stat, array("id" => $id));
			}
			$this->output->close_tag();

			$this->output->record($user, "user");
			$this->output->open_tag("roles");
			foreach ($roles as $role) {
				/* Non-admins cannot assign the admin role
				 */
				if (($this->user->is_admin == false) && ($role["id"] == ADMIN_ROLE_ID)) {
					continue;
				}

				$checked = in_array($role["id"], $user["roles"]);
				$enabled = ($this->user->id != $user["id"]) || ($role["id"] != ADMIN_ROLE_ID); /* Don't disable yourself */
				
				$this->output->add_tag("role", $role["name"], array(
					"id" => $role["id"],
					"checked" => show_boolean($checked),
					"enabled" => show_boolean($enabled)));
			}
			$this->output->close_tag();
			$this->output->close_tag();
		}

		public function execute() {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Save user") {
					/* Create or update a user
					 */
					if ($this->model->user_oke($_POST) == false) {
						$this->show_user_form($_POST);
					} else if (isset($_POST["id"]) === false) {
						if ($this->model->create_user($_POST) === false) {
							$this->output->add_message("Database error while creating user.");
							$this->show_user_form($_POST);
						} else {
							$this->user->log_action("user ".$this->db->last_insert_id(count($_POST["roles"]))." created");
							$this->show_user_overview();
						}
					} else {
						if ($this->model->update_user($_POST) === false) {
							$this->output->add_message("Database error while updating user.");
							$this->show_user_form($_POST);
						} else {
							$this->user->log_action("user ".$_POST["id"]." updated");
							$this->show_user_overview();
						}
					}
				} else if ($_POST["submit_button"] == "Delete user") {
					/* Delete a user
					 */
					if ($this->model->delete_oke($_POST["id"]) == false) {
						$this->show_user_form($_POST);
					} else if ($this->model->delete_user($_POST["id"]) == false) {
						$this->output->add_tag("result", "Database error while deleting user.");
					} else {
						$this->user->log_action("user ".$_POST["id"]." deleted");
						$this->show_user_overview();
					}
				} else {
					/* Invalid submit
					 */
					$this->output->add_tag("result", "Huh?");
				}
			} else if ($this->page->pathinfo[2] == "new") {
				/* Show the user webform
				 */
				$user = array(
					"role_ids" => array(ADMIN_ROLE_ID + 1),
					"status" => USER_STATUS_CHANGEPWD,
					"roles" => array());
				$this->show_user_form($user);
			} else if (valid_input($this->page->pathinfo[2], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Show the user webform
				 */
				if (($user = $this->model->get_user($this->page->pathinfo[2])) == false) {
					$this->output->add_tag("result", "User not found.");
				} else {
					$this->show_user_form($user);
				}
			} else {
				/* Show a list of all users
				 */
				$this->show_user_overview();
			}
		}
	}
?>
