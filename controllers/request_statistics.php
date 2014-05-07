<?php
	class request_statistics_controller extends graph_controller {
		protected $graphs = array(
			"requests"              => "Requests",
			"bytes_sent"            => "Bytes sent",
			"result_forbidden"      => "Result: 403 Forbidden",
			"result_not_found"      => "Result: 404 Not Found",
			"result_internal_error" => "Result: 500 Internal Server Error");
	}
?>
