<?php
	class cms_webserver_controller extends controller {
		private function show_overview() {
			if (($webserver_count = $this->model->count_webservers()) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$paging = new pagination($this->output, "webservers", $this->settings->admin_page_size, $webserver_count);

			if (($webservers = $this->model->get_webservers($paging->offset, $paging->size)) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$this->output->open_tag("overview");

			$this->output->open_tag("webservers");
			foreach ($webservers as $webserver) {
				$webserver["tls"] = show_boolean($webserver["tls"]);
				$webserver["active"] = show_boolean($webserver["active"]);
				$this->output->record($webserver, "webserver");
			}
			$this->output->close_tag();

			$paging->show_browse_links();

			$this->output->close_tag();
		}

		private function show_webserver_form($webserver) {
			if (($users = $this->model->get_users()) === false) {
				return;
			}

			$this->output->add_javascript("cms/webserver.js");

			$this->output->open_tag("edit");

			$webserver["tls"] = show_boolean($webserver["tls"]);
			$webserver["active"] = show_boolean($webserver["active"]);
			if (is_array($webserver["users"]) == false) {
				$webserver["users"] = array();
			}

			$this->output->record($webserver, "webserver");

			$this->output->open_tag("users");
			foreach ($users as $user) {
				$attr = array(
					"id"      => $user["id"],
					"checked" => show_boolean(in_array($user["id"], $webserver["users"])));
				$this->output->add_tag("user", $user["fullname"], $attr);
			}
			$this->output->close_tag();

			$this->output->close_tag();
		}

		public function execute() {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Save webserver") {
					/* Save webserver
					 */
					if ($this->model->save_oke($_POST) == false) {
						$this->show_webserver_form($_POST);
					} else if (isset($_POST["id"]) === false) {
						/* Create webserver
						 */
						if ($this->model->create_webserver($_POST) === false) {
							$this->output->add_message("Error creating webserver.");
							$this->show_webserver_form($_POST);
						} else {
							$this->user->log_action("webserver created");
							$this->show_overview();
						}
					} else {
						/* Update webserver
						 */
						if ($this->model->update_webserver($_POST) === false) {
							$this->output->add_message("Error updating webserver.");
							$this->show_webserver_form($_POST);
						} else {
							$this->user->log_action("webserver updated");
							$this->show_overview();
						}
					}
				} else if ($_POST["submit_button"] == "Delete webserver") {
					/* Delete webserver
					 */
					if ($this->model->delete_oke($_POST) == false) {
						$this->show_webserver_form($_POST);
					} else if ($this->model->delete_webserver($_POST["id"]) === false) {
						$this->output->add_message("Error deleting webserver.");
						$this->show_webserver_form($_POST);
					} else {
						$this->user->log_action("webserver %d deleted", $_POST["id"]);
						$this->show_overview();
					}
				} else {
					$this->show_overview();
				}
			} else if ($this->page->pathinfo[2] === "new") {
				/* New webserver
				 */
				$webserver = array("active" => true, "port" => "80");
				$this->show_webserver_form($webserver);
			} else if (valid_input($this->page->pathinfo[2], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Edit webserver
				 */
				if (($webserver = $this->model->get_webserver($this->page->pathinfo[2])) === false) {
					$this->output->add_tag("result", "webserver not found.\n");
				} else {
					$this->show_webserver_form($webserver);
				}
			} else {
				/* Show overview
				 */
				$this->show_overview();
			}
		}
	}
?>
