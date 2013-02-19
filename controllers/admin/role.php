<?php
	class admin_role_controller extends controller {
		public function show_role_overview() {
			if (($roles = $this->model->get_all_roles()) === false) {
				$this->output->add_tag("result", "Database error.");
			} else {
				$this->output->open_tag("overview");

				$this->output->open_tag("roles");
				foreach ($roles as $role) {
					$this->output->add_tag("role", $role["name"], array("id" => $role["id"], "users" => $role["users"]));
				}
				$this->output->close_tag();

				$this->output->close_tag();
			}
		}

		public function show_role_form($role) {
			if (isset($role["id"]) == false) {
				$editable = true;
				$params = array();
			} else {
				$editable = ($role["id"] != ADMIN_ROLE_ID);
				$params = array("id" => $role["id"]);
			}

			if (($pages = $this->model->get_restricted_pages()) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}
			sort($pages);

			$this->output->set_xslt_parameter("admin_role_id", ADMIN_ROLE_ID);

			$this->output->open_tag("edit");

			$this->output->add_tag("role", $role["name"], $params);
			$this->output->open_tag("pages");
			foreach ($pages as $page) {
				$this->output->add_tag("page", $page, array("checked" => show_boolean($role[$page])));
			}
			$this->output->close_tag();

			$this->output->open_tag("members");
			if (($users = $this->model->get_role_members($role["id"])) !== false) {
				foreach ($users as $user) {
					$this->output->add_tag("member", $user["fullname"], array("id" => $user["id"]));
				}
			}
			$this->output->close_tag();

			$this->output->close_tag();
		}

		public function execute() {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Save role") {
					/* Create or update an role
					 */
					if ($this->model->role_oke($_POST) == false) {
						$this->show_role_form($_POST);
					} else if (isset($_POST["id"]) == false) {
						if ($this->model->create_role($_POST) === false) {
							$this->output->add_message("Database error while creating role.");
							$this->show_role_form($_POST);
						} else {
							$this->user->log_action("role ".$this->db->last_insert_id." created");
							$this->show_role_overview();
						}
					} else {
						if ($this->model->update_role($_POST) === false) {
							$this->output->add_message("Database error while updating role.");
							$this->show_role_form($_POST);
						} else {
							$this->user->log_action("role ".$_POST["id"]." updated");
							$this->show_role_overview();
						}
					}
				} else if ($_POST["submit_button"] == "Delete role") {
					/* Delete a role
					 */
					if ($_POST["id"] == ADMIN_ROLE_ID) {
						$this->output->add_tag("result", "This role cannot be deleted.");
					} else if ($this->model->delete_role($_POST["id"]) == false) {
						$this->output->add_tag("result", "Database error while deleting role.");
					} else {
						$this->user->log_action("role ".$_POST["id"]." deleted");
						$this->show_role_overview();
					}
				} else {
					$this->output->add_tag("result", "Huh?");
				}
			} else if (valid_input($this->page->pathinfo[2], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Show the role webform
				 */
				if (($role = $this->model->get_role($this->page->pathinfo[2])) != false) {
					$this->show_role_form($role);
				} else {
					$this->output->add_tag("result", "Role not found.");
				}
			} else if ($this->page->pathinfo[2] == "new") {
				/* Show the role webform
				 */
				$role = array("profile" => true);
				$this->show_role_form($role);
			} else {
				/* Show a list of all roles
				 */
				$this->show_role_overview();
			}
		}
	}
?>
