<?php
	class admin_hostname_controller extends controller {
		public function execute() {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				/* Handle form submit
				 */
				if ($this->model->update_hostnames($_POST["hostname"]) == false) {
					$this->output->add_tag("result", "Error while updating hostname.");
				} else {
					$this->output->add_tag("result", "Hostnames have been updated.");
				}
			} else {
				/* Show hostname form
				 */
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
		}
	}
?>
