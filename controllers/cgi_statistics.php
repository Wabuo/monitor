<?php
	class cgi_statistics_controller extends graph_controller {
		protected $graphs = array(
			"time_0_1"  => "0 - 1 second",
			"time_1_3"  => "1 - 3 seconds",
			"time_3_10" => "3 - 10 seconds",
			"time_10_x" => "More than 10 seconds");
	}
?>
