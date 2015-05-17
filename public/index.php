<?php
	/* public/index.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 *
	 * Don't change this file, unless you know what you are doing.
	 */

	ob_start();
	require("../libraries/error.php");
	require("../libraries/configuration.php");
	require("../libraries/security.php");
	require("../libraries/general.php");

	/* Abort on dangerous PHP settings
	 */
	check_PHP_setting("register_globals", 0);
	check_PHP_setting("allow_url_include", 0);

	/* Undo magic quotes
	 */
	if (ini_get("magic_quotes_gpc") == 1) {
		$superglobals = array(&$_REQUEST, &$_GET, &$_POST, &$_COOKIE);
		foreach ($superglobals as $i => $superglobal) {
			$superglobals[$i] = remove_magic_quotes($superglobal);
		}
	}

	/* Load core modules
	 */
	$_database = new MySQLi_connection(DB_HOSTNAME, DB_DATABASE, DB_USERNAME, DB_PASSWORD);
	$_session  = new session($_database);
	$_settings = new settings($_database);
	$_user     = new user($_database, $_settings, $_session);
	$_page     = new page($_database, $_settings, $_user);
	$_output   = new output($_database, $_settings, $_page);

	/* Include the model
	 */
	if (file_exists($file = "../models/".$_page->module.".php")) {
		include($file);
	}

	$_output->open_tag("output", array("url" => $_page->url));

	if ($_page->ajax_request == false) {
		$_output->add_tag("banshee_version", BANSHEE_VERSION);
		$_output->add_tag("monitor_version", MONITOR_VERSION);
		$_output->add_tag("website_url", $_SERVER["SERVER_NAME"]);

		/* Page information
		 */
		$_output->add_tag("page", $_page->page, array(
			"module" => $_page->module,
			"type"   => $_page->type));

		/* User information
		 */
		if ($_user->logged_in) {
			$params = array("id" => $_user->id, "admin" => show_boolean($_user->is_admin));
			$_output->add_tag("user", $_user->fullname, $params);
		}

		/* Main menu
		 */
		if (is_true(WEBSITE_ONLINE) && $_user->logged_in) {
			$menu = new menu($_database, $_output);
			$menu->set_user($_user);
			$menu->set_depth(2);
			$menu->to_output();
		}

		/* Stylesheet
		 */
		$_output->add_css($_page->module.".css");

		$_output->open_tag("content");
	}

	/* Include the controller
	 */
	if (file_exists($file = "../controllers/".$_page->module.".php")) {
		include($file);

		$controller_class = str_replace("/", "_", $_page->module)."_controller";
		if (class_exists($controller_class) == false) {
			print "Controller class '".$controller_class."' does not exist.\n";
		} else if (is_subclass_of($controller_class, "controller") == false) {
			print "Controller class '".$controller_class."' does not extend 'controller'.\n";
		} else {
			$_controller = new $controller_class($_database, $_settings, $_user, $_page, $_output);
			$_controller->execute();
			unset($_controller);

			if ($_output->disabled) {
				print ob_get_clean();
				exit;
			}

			while ($_output->depth > 2) {
				print "System error: controller didn't close an open tag.";
				$_output->close_tag();
			}
		}
	}

	if ($_page->ajax_request == false) {
		$_output->close_tag();
	}

	/* Errors
	 */
	$errors = ob_get_contents();
	ob_clean();

	if ($errors != "") {
		$error_handler = new website_error_handler($_output, $_settings);
		$error_handler->execute($errors);
		unset($error_handler);
	}

	/* Close output
	 */
	$_output->close_tag();

	/* Output content
	 */
	$output = $_output->generate();
	$last_errors = ob_get_clean();
	print $output;
?>
