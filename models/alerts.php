<?php
	function get_hostname($db, $host_id) {
		return $db->entry("hostnames", $host_id);
	}

	function get_not_founds($db, $host_id, $timestamp) {
		$query = "select u.request_uri, count(u.request_uri) as count ".
		         "from requests r, request_uris u ".
		         "where r.request_uri_id=u.id and r.hostname_id=%d and r.timestamp>%s and r.return_code=404 ".
		         "group by u.request_uri order by count desc limit 50";

		return $db->execute($query, $host_id, $timestamp);
	}
?>
