<?php
	class cms_hostname_controller extends controller {
		private function show_hostnames() {
			if (($hostnames = $this->model->get_hostnames()) === false) {
				$this->output->add_tag("result", "Database error.");
			} else {
				$this->output->open_tag("hostnames");
				foreach ($hostnames as $hostname) {
					$this->output->add_tag("hostname", $hostname["hostname"], array(
						"id"      => $hostname["id"],
						"visible" => show_boolean($hostname["visible"])));
				}
				$this->output->close_tag();
			}
		}

		public function execute() {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				/* Delete hostnames
				 */
				if (is_array($_POST["delete"])) foreach ($_POST["delete"] as $hostname_id) {
					if (($hostname = $this->model->get_hostname($hostname_id)) != false) {
						if ($this->model->delete_hostname($hostname_id) == false) {
							$this->output->add_system_warning("Error while deleting hostname %s.", $hostname);
						} else {
							$this->output->add_system_message("Hostname %s has been deleted.", $hostname);
							$this->user->log_action("hostname %s deleted", $hostname);
						}
					}
				}

				/* Update hostnames
				 */
				if ($this->model->update_hostnames($_POST["hostname"]) == false) {
					$this->output->add_system_warning("Error while updating hostname visibility.");
				} else {
					$this->output->add_system_message("Hostname visibility has been updated.");
				}
			}

			$this->show_hostnames();
		}
	}
?>
