<?php
	/* libraries/configuration.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	/* For internal usage. Only change if you know what you're doing!
	 */
	define("BANSHEE_VERSION", "3.0");
	define("MONITOR_VERSION", "0.7");
	define("ADMIN_ROLE_ID", 1);
	define("YES", 1);
	define("NO", 0);
	define("USER_STATUS_DISABLED", 0);
	define("USER_STATUS_CHANGEPWD", 1);
	define("USER_STATUS_ACTIVE", 2);
	define("SESSION_NAME", "WebsiteSessionID");
	define("DAY", 86400);
	define("PAGE_MODULE", "system/page");
	define("ERROR_MODULE", "system/error");
	define("LOGIN_MODULE", "login");
	define("LOGOUT_MODULE", "logout");

	/* Hiawatha Monitor settings
	 */
	define("MONITOR_DAYS", 31);

	/* Pre-defined validation strings for valid_input()
	 */
	define("VALIDATE_CAPITALS",		"ABCDEFGHIJKLMNOPQRSTUVWXYZ");
	define("VALIDATE_NONCAPITALS",	"abcdefghijklmnopqrstuvwxyz");
	define("VALIDATE_LETTERS",		VALIDATE_CAPITALS.VALIDATE_NONCAPITALS);
	define("VALIDATE_PHRASE",		VALIDATE_LETTERS." ,.?!:;-'");
	define("VALIDATE_NUMBERS",		"0123456789");
	define("VALIDATE_EMAIL",		VALIDATE_LETTERS.VALIDATE_NUMBERS."_-@.");
	define("VALIDATE_SYMBOLS",		"!@#$%^&*()_-+={}[]|\:;\"'`~<>,./?");
	define("VALIDATE_URL",          VALIDATE_LETTERS.VALIDATE_NUMBERS."-_/.");

	define("VALIDATE_NONEMPTY",     0);

	$preload_settings = array("start_page", "default_language",
		"head_title", "head_description", "head_keywords");
	$allowed_uploads = array("jpg", "jpeg", "gif", "png", "pdf", "doc", "xls", "zip", "txt");

	$months_of_year = array("january", "february", "march", "april", "may", "june",
		"july", "august", "september", "october", "november", "december");
	$days_of_week = array("monday", "tuesday", "wednesday", "thursday", "friday",
		"saturday", "sunday");

	/* Auto class loader
	 *
	 * INPUT:  string class name
	 * OUTPUT: -
	 * ERROR:  -
	 */
	function __autoload($class_name) {
		$rename = array(
			"https"               => "http");

		$class_name = strtolower($class_name);
		if (isset($rename[$class_name])) {
			$class_name = $rename[$class_name];
		}

		$locations = array("libraries", "libraries/database");
		foreach ($locations as $location) {
			if (file_exists($file = "../".$location."/".$class_name.".php")) {
				include_once($file);
				break;
			}
		}
	}

	/* Load configuration file
	 *
	 * INPUT:  string configuration
	 * OUTPUT: array( key => value[, ...] )
	 * ERROR:  -
	 */
	function config_file($file) {
		static $cache = array();

		if (isset($cache[$file])) {
			return $cache[$file];
		}

		$config_file = "../settings/".$file.".conf";
		if (file_exists($config_file) == false) {
			return array();
		}

		$config = array();
		foreach (file($config_file) as $line) {
			if (($line = trim(preg_replace("/#.*/", "", $line))) !== "") {
				array_push($config, $line);
			}
		}

		$cache[$file] = $config;

		return $config;
	}

	/* Parse website.conf
	 */
	foreach (config_file("website") as $line) {
		list($key, $value) = explode("=", chop($line), 2);
		define(trim($key), trim($value));
	}

	/* PHP settings
	 */
	ini_set("magic_quotes_runtime", 0);
?>
