<?php
	$browsers = array(
		"Firefox ?",
		"Firefox/?" => "Firefox",
		"Iceweasel/?" => "Firefox",
		"Camino",
		"Thunderbird",
		"Chrome/?" => "Chrome",
		"Chrome ?",
		"Safari",
		"Opera",
		"Konqueror",
		"Googlebot" => "Google searchbot",
		"Feedfetcher-Google" => "Google searchbot",
		"msnbot" => "MSN searchbot",
		"Yahoo! Slurp" => "Yahoo searchbot",
		"Yandex" => "Yandex searchbot",
		"Twiceler" => "Twiceler searchbot",
		"Wget",
		"libfetch",
		"robot" => "searchbot",
		"spider" => "searchbot",
		"crawler" => "searchbot",
		"robot" => "searchbot",
		"AppleWebKit" => "WebKit based browser",
		"Gecko" => "Gecko based browser",
		"MSIE ?" => "Internet Explorer");

	$oses = array(
		"Windows NT 6.2" => "Windows 8",
		"Windows NT 6.1" => "Windows 7",
		"Windows NT 6.0" => "Windows Vista",
		"Windows NT 5.2" => "Windows Server 2003",
		"Windows NT 5.1" => "Windows XP",
		"Windows NT 5.0" => "Windows 2000",
		"Windows NT 4.0" => "Windows NT",
		"Windows 98",
		"Windows 95",
		"Linux",
		"FreeBSD",
		"NetBSD",
		"OpenBSD",
		"Macintosh" => "Mac PowerPC",
		"PowerPC " => "Mac PowerPC",
		"iPhone",
		"Nokia",
		"Windows CE",
		"Mac OS X ?" => "MacOS X",
		"Mac OS X" => "MacOS X",
		"SunOS",
		"Googlebot" => "searchbot",
		"Feedfetcher-Google" => "searchbot",
		"msnbot" => "searchbot",
		"Yahoo! Slurp" => "searchbot",
		"Yandex" => "searchbot",
		"Twiceler" => "searchbot",
		"robot" => "searchbot",
		"spider" => "searchbot",
		"crawler" => "searchbot",
		"robot" => "searchbot");
	
	function find_str($user_agent, $list) {
		$pos = false;
		foreach ($list as $key => $value) {
			$seek_version = substr($key, -1) == "?";
			if ($seek_version) {
				$key = substr($key, 0, -1);
			}

			if (($pos = strpos($user_agent, $key)) !== false) {
				break;
			}
		}

		if ($pos === false) {
			return "Other";
		}

		if ($seek_version) {
			$rest = substr($user_agent, $pos + strlen($key));
			$rest = str_replace(";", " ", $rest);
			list($version) = explode(" ", $rest, 2);
			list($version) = explode(".", $version, 2);

			if (is_numeric($version)) {
				$value .= " ".$version;
			}
		}

		return $value;
	}

	function guess_browser($user_agent) {
		global $browser_list;

		return find_str($user_agent, $browser_list);
	}

	function guess_os($user_agent) {
		global $os_list;

		return find_str($user_agent, $os_list);
	}

	function ua_list($list) {
		$result = array();

		foreach ($list as $key => $value) {
			if (is_int($key)) {
				$result[$value] = rtrim(str_replace("?", "", $value));
			} else {
				$result[$key] = $value;
			}
		}

		return $result;
	}

	$browser_list = ua_list($browsers);
	$os_list = ua_list($oses);
?>
