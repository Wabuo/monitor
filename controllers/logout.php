<?php
	class logout_controller extends controller {
		public function execute() {
			if (isset($_SESSION["user_switch"]) == false) {
				$this->user->logout();
			} else {
				$this->user->log_action("switched back to self");
				$_SESSION["user_id"] = $_SESSION["user_switch"];
				unset($_SESSION["user_switch"]);
			}
		}
	}
?>
