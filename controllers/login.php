<?php
	class login_controller extends controller {
		public function execute() {
			$this->output->set_layout(LAYOUT_SITE);

			$this->output->add_tag("url", $_SERVER["REQUEST_URI"]);
			if ($_SERVER["REQUEST_METHOD"] != "POST") {
				$this->output->add_tag("bind");
			} else if (is_true($_POST["bind_ip"])) {
				$this->output->add_tag("bind");
			}
			$this->output->add_tag("remote_addr", $_SERVER["REMOTE_ADDR"]);
			$this->output->add_tag("challenge", $_SESSION["challenge"]);
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$this->output->add_message("Login incorrect");
			}
		}
	}
?>
