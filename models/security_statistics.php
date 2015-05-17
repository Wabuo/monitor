<?php
	class security_statistics_model extends graph_model {
		protected $columns = array("bans", "exploit_attempts", "failed_logins");
		protected $table = "host_statistics";
	}
?>
