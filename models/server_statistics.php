<?php
	class server_statistics_model extends graph_model {
		protected $columns = array("connections", "result_bad_request");
		protected $table = "server_statistics";
		protected $hostnames = false;
	}
?>
