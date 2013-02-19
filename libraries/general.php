<?php
	/* libraries/general.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	/* Convert mixed to boolean
	 *
	 * INPUT:  mixed
	 * OUTPUT: boolean
	 * ERROR:  -
	 */
	function is_true($bool) {
		return in_array($bool, array(true, YES, "1", "yes", "true", "on"), true);
	}

	/* Convert mixed to boolean
	 *
	 * INPUT:  mixed
	 * OUTPUT: boolean
	 * ERROR:  -
	 */
	function is_false($bool) {
		return (is_true($bool) === false);
	}

	/* Convert boolean to string
	 *
	 * INPUT:  boolean
	 * OUTPUT: string "yes"|"no"
	 * ERROR:  -
	 */
	function show_boolean($bool) {
		return (is_true($bool) ? "yes" : "no");
	}

	/* Convert a page path to a module path
	 *
	 * INPUT:  array / string page path
	 * OUTPUT: array / string module path
	 * ERROR:  -
	 */
	function page_to_module($page) {
		if (is_array($page) == false) {
			$page = str_replace("*/", "", $page);

			if (($pos = strrpos($page, ".")) !== false) {
				$page = substr($page, 0, $pos);
			}
		} else foreach ($page as $i => $item) {
			$page[$i] = page_to_module($item);
		}

		return $page;
	}

	/* Convert a page path to a module path
	 *
	 * INPUT:  array / string page path
	 * OUTPUT: array / string page type
	 * ERROR:  -
	 */
	function page_to_type($page) {
		if (is_array($page) == false) {
			if (($pos = strrpos($page, ".")) !== false) {
				$page = substr($page, $pos);
			} else {
				$page = "";
			}
		} else foreach ($page as $item) {
			$page[$i] = page_to_type($item);
		}

		return $page;
	}

	/* Get users in group
	 *
	 * INPUT:  string group name
	 * OUTPUT: array user
	 * ERROR:  false
	 */
	function users_in_group($db, $group) {
		$query = "select distinct u.* from users u, user_role m, roles r ".
		         "where r.name=%s and r.id=m.role_id and m.user_id=u.id";

		return $db->execute($query, $group);
	}

	/* Flatten array to new array with depth 1
	 *
	 * INPUT:  array
	 * OUTPUT: array
	 * ERROR:  -
	 */
	function array_flatten($data) {
		$result = array();
		foreach ($data as $item) {
			if (is_array($item)) {
				$result = array_merge($result, array_flatten($item));
			} else {
				array_push($result, $item);
			}
		}

		return $result;
	}

	/* Prepare string for unescaped output
	 *
	 * INPUT:  string data
	 * OUTPUT: string data
	 * ERROR:  -
	 */
	function unescaped_output($str) {
		$str = htmlentities($str);
		$str = str_replace("\r", "", $str);
		$str = str_replace("\n", "<br />", $str);

		return $str;
	}

	/* Decode a GZip encoded string
	 *
	 * INPUT:  string GZip data
	 * OUTPUT: string data
	 * ERROR:  -
	 */
	if (function_exists("gzdecode") == false) {

	function gzdecode($data) {
		$file = tempnam("/tmp", "gzip");

		@file_put_contents($file, $data);
		ob_start();
		readgzfile($file);
		$data = ob_get_clean();
		unlink($file);

		return $data;
	}

	}
?>
