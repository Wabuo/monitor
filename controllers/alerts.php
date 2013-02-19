<?php
	if (valid_input($_page->pathinfo[1], VALIDATE_NUMBERS, VALIDATE_NONEMPTY) == false) {
		$_output->add_tag("result", "Host missing.", array("url" => "homepage"));
	} else if (($hostname = get_hostname($db, $_page->pathinfo[1])) == false) {
		$_output->add_tag("result", "Host not found.", array("url" => "homepage"));
	} else {
		$period = ALERTS_PERIOD." hours";
		$timestamp = date("Y-m-d H:i:s", strtotime("-".$period));

		if (($uris = get_not_founds($db, $_page->pathinfo[1], $timestamp)) !== false) {
			$_output->open_tag("uris");
			foreach ($uris as $uri) {
				$_output->record($uri, "uri");
			}
			$_output->close_tag();
		}
	}
?>
