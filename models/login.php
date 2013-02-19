<?php
	$url = ltrim($_page->url, "/");
	if (in_array($url, array("", LOGIN_MODULE, LOGOUT_MODULE)) == false) {
		$next_page = $url;
	} else {
		$next_page = $_settings->page_after_login;
	}

	if ($_user->logged_in) {
		$_page->select_module($next_page);
		if ($_page->module != LOGIN_MODULE) {
			if (file_exists($file = "../models/".$_page->module.".php")) {
				include($file);
			}
		}
	} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
		/* Login via password
		 */
		if ($_user->login_password($_POST["username"], $_POST["password"], is_true($_POST["use_cr_method"]))) {
			if (is_true($_POST["bind_ip"])) {
				$_user->bind_to_ip();
			}
			$_SERVER["REQUEST_METHOD"] = "GET";

			$_page->select_module($next_page);
			if ($_page->module != LOGIN_MODULE) {
				if (file_exists($file = "../models/".$_page->module.".php")) {
					include($file);
				}
			}
		} else {
			$_user->log_action("login failed: ".$_POST["username"]);
		}
	} else if (isset($_GET["login"])) {
		/* Login via one time key
		 */
		if ($_user->login_one_time_key($_GET["login"])) {
			$_page->select_module($next_page);
			if ($_page->module != LOGIN_MODULE) {
				if (file_exists($file = "../models/".$_page->module.".php")) {
					include($file);
				}
			}
		}
	} else if (isset($_SESSION["challenge"]) == false) {
		$_SESSION["challenge"] = md5(time());
	}
?>
