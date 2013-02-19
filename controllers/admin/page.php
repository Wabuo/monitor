<?php
	class admin_page_controller extends controller {
		private function show_page_overview() {
			if (($pages = $this->model->get_pages()) === false) {
				$this->output->add_tag("result", "Database error.");
			} else {
				$this->output->open_tag("overview");
				$this->output->open_tag("pages");
				foreach ($pages as $page) {
					$page["visible"] = show_boolean($page["visible"]);
					$this->output->record($page, "page");
				}
				$this->output->close_tag();
				$this->output->close_tag();
			}
		}

		private function show_page_form($page) {	
			$this->output->set_xslt_parameter("admin_role_id", ADMIN_ROLE_ID);

			$page["private"] = show_boolean($page["private"]);
			$page["visible"] = show_boolean($page["visible"]);

			$args = array();
			if (isset($page["id"])) {
				$args["id"] = $page["id"];
			}

			$this->output->open_tag("edit");

			$this->output->open_tag("page", $args);
			$this->output->record($page);

			$this->output->open_tag("roles");
			if (($roles = $this->model->get_roles()) != false) {
				foreach ($roles as $role) {
					$this->output->add_tag("role", $role["name"], array(
						"id" => $role["id"],
						"checked" => show_boolean($page["roles"][$role["id"]])));
				}
			}
			$this->output->close_tag();

			$this->output->close_tag();

			$this->output->close_tag();
		}

		public function execute() {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Save page") {
					/* Create or update page
					 */
					$_POST["url"] = rtrim($_POST["url"], "/ ");
					if ($this->model->page_oke($_POST) == false) {
						$this->show_page_form($_POST);
					} else if (isset($_POST["id"]) == false) {
						if ($this->model->create_page($_POST) === false) {
							$this->output->add_message("Database error while creating page.");
							$this->show_page_form($_POST);
						} else {
							$this->user->log_action("page ".$_POST["url"]." created");
							$this->show_page_overview();
						}
					} else {
						if ($this->model->update_page($_POST, $_POST["id"]) === false) {
							$this->output->add_message("Database error while updating page.");
							$this->show_page_form($_POST);
						} else {
							$this->user->log_action("page ".$_POST["id"]." updated");
							$this->show_page_overview();
						}
					}
				} else if ($_POST["submit_button"] == "Delete page") {
					/* Delete page
					 */
					if ($this->model->delete_page($_POST["id"]) == false) {
						$this->output->add_tag("result", "Database error while deleting page.");
					} else {
						$this->user->log_action("page ".$_POST["url"]." deleted");
						$this->show_page_overview();
					}
				} else {
					$this->output->add_tag("result", "Huh?");
				}
			} else if ($this->page->pathinfo[2] == "new") {
				/* Show the user webform
				 */
				$page = array(
					"url"      => "/",
					"language" => $this->settings->default_language,
					"visible"  => 1,
					"roles"    => array());
				$this->show_page_form($page);
			} else if (valid_input($this->page->pathinfo[2], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Show the user webform
				 */
				if (($page = $this->model->get_page($this->page->pathinfo[2])) == false) {
					$this->output->add_tag("result", "Page not found.");
				} else {
					$this->show_page_form($page);
				}
			} else {
				/* Show a list of all users
				 */
				$this->show_page_overview();
			}
		}
	}
?>
