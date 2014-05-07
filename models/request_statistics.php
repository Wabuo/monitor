<?php
	class request_statistics_model extends graph_model {
		protected $columns = array("requests", "bytes_sent", "result_forbidden", "result_not_found", "result_internal_error");
		protected $table = "host_statistics";
	}
?>
