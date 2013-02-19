<?php
	class profile_controller extends controller {
		private function show_profile_form($profile) {
			$this->output->add_javascript("md5.js");
			$this->output->add_javascript("profile.js");

			$this->output->open_tag("edit");

			$this->output->add_tag("email", $profile["email"]);
			if ($this->user->status == USER_STATUS_CHANGEPWD) {
				$this->output->add_tag("cancel", "Logout", array("page" => LOGOUT_MODULE));
			}

			/* Action log
			 */
			if (($actionlog = $this->model->last_account_logs()) !== false) {
				$this->output->open_tag("actionlog");
				foreach ($actionlog as $log) {
					$this->output->record($log, "log");
				}
				$this->output->close_tag();
			}

			$this->output->close_tag();
		}

		public function execute() {
			$this->output->description = "Profile";
			$this->output->keywords = "profile";
			$this->output->title = "Profile";

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				/* Update profile
				 */
				if ($this->model->profile_oke($_POST) == false) {
					$this->show_profile_form($_POST);
				} else if ($this->model->update_profile($_POST) === false) {
					$this->output->add_tag("result", "Error while updating profile.", array("url" => "profile"));
				} else {
					$this->output->add_tag("result", "Profile has been updated.", array("url" => $this->settings->start_page));
					$this->user->log_action("profile updated");
				}
			} else {
				$user = array("email" => $this->user->email);
				$this->show_profile_form($user);
			}
		}
	}
?>
