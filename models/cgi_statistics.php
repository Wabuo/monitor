<?php
	class cgi_statistics_model extends graph_model {
		protected $columns = array("time_0_1", "time_1_3", "time_3_10", "time_10_x");
		protected $table = "cgi_statistics";
	}
?>
