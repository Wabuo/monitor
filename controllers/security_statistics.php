<?php
	class security_statistics_controller extends graph_controller {
		protected $graphs = array(
			"exploit_attempts"      => "Exploit attempts",
			"failed_logins"         => "Failed logins",
			"bans"                  => "Clients banned");
	}
?>
