<?php
	/* libraries/security.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	/* Abort execution upon dangerous PHP setting
	 *
	 * INPUT:  string key, mixed value
	 * OUTPUT: -
	 * ERROR:  -
	 */
	function check_PHP_setting($key, $value) {
		if (ini_get($key) != $value) {
			exit("Please, set the PHP flag '".$key."' to '".$value."'!\n");
		}
	}

	/* Remove magic quotes from string
	 *
	 * INPUT:  array/string data
	 * OUTPUT: array/string data
	 * ERROR:  -
	 */
	function remove_magic_quotes($data) {
		if (is_array($data) == false) {
			$data = stripslashes($data);
		} else foreach ($data as $i => $value) {
			$data[$i] = remove_magic_quotes($value);
		}

		return $data;
	}

    /* Remove dangerous characters from string
     *
     * INPUT:  string text
     * OUTPUT: string text
     * ERROR:  -
     */
	function secure_input($data) {
		if (is_array($data) == false) {
			$data = str_replace(chr(0), "", $data);
			$special_chars = "/[".chr(1)."-".chr(8)."]|".
							 "[".chr(11).chr(12)."]|".
							 "[".chr(14)."-".chr(31)."]/";
			$data = preg_replace($special_chars, "", $data);
		} else foreach ($data as $i => $value) {
			$data[$i] = secure_input($value);
		}

		return $data;
	}

	/* Validate input
	 *
	 * INPUT:  string input, string valid characters[, int length]
	 * OUTPUT: boolean input oke
	 * ERROR:  -
	 */
	function valid_input($data, $allowed, $length = null) {
		if (is_array($data) == false) {
			$data_len = strlen($data);

			if ($length !== null) {
				if ($length == VALIDATE_NONEMPTY) {
					if ($data_len == 0) {
						return false;
					}
				} else if ($data_len !== $length) {
					return false;
				}
			} else if ($data_len == 0) {
				return true;
			}

			$data = str_split($data);
			$allowed = str_split($allowed);
			$diff = array_diff($data, $allowed);

			return count($diff) == 0;
		} else foreach ($data as $item) {
			if (valid_input($item, $allowed, $length) == false) {
				return false;
			}
		}

		return true;
	}

	/* Validate an e-mail address
	 *
	 * INPUT:  string e-mail address
	 * OUTPUT: boolean e-mail address oke
	 * ERROR:  -
	 */
	function valid_email($email) {
		return email::valid_address($email);
	}

	/* Validate a date string
	 *
	 * INPUT:  string date
	 * OUTPUT: boolean date oke
	 * ERROR:  -
	 */
	function valid_date($date) {
		return preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $date) === 1;
	}

	/* Validate a time string
	 *
	 * INPUT:  string time
	 * OUTPUT: boolean time oke
	 * ERROR:  -
	 */
	function valid_time($time) {
		return preg_match("/^(([01]?[0-9])|(2[0-3])):[0-5][0-9](:[0-5][0-9])?$/", $time) === 1;
	}

	/* Validate a timestamp
	 *
	 * INPUT:  string timestamp
	 * OUTPUT: boolean timestamp oke
	 * ERROR:  -
	 */
	function valid_timestamp($timestamp) {
		list($date, $time) = explode(" ", $timestamp, 2);
		return valid_date($date) && valid_time($time);
	}

	/* Validate a telephone number
	 *
	 * INPUT:  string telephone number
	 * OUTPUT: boolean telephone number oke
	 * ERROR:  -
	 */
	function valid_phonenumber($phonenr) {
		$phonenr = str_replace(" ", "", $phonenr);
		return preg_match("/^(\+31|0)([0-9]{9}|6-?[0-9]{8}|[0-9]{2}-?[0-9]{7}|[0-9]{3}-?[0-9]{6})$/", $phonenr) === 1;
	}

	/* Return a per-page overview of the access levels
	 *
	 * INPUT:  object database
	 * OUTPUT: array( string page => int access level[, ....] )
	 * ERROR:  false
	 */
	function page_access_list($db, $user) {
		$access_rights = array();

		/* Public pages on disk
		 */
		$public = page_to_module(config_file("public_pages"));
		foreach ($public as $page) {
			$access_rights[$page] = 1;
		}

		/* Private pages on disk
		 */
		$private_pages = page_to_module(config_file("private_pages"));
		foreach ($private_pages as $page) {
			$access_rights[$page] = $user->is_admin ? YES : NO;
		}
		$access_rights["logout"] = $user->logged_in ? YES : NO;

		if ($user->logged_in && ($user->is_admin == false)) {
			$query = "select * from roles where id in ".
					 "(select role_id from user_role where user_id=%d)";
			if (($roles = $db->execute($query, $user->id)) === false) {
				return false;
			}
			foreach ($roles as $role) {
				$role = array_slice($role, 2);
				foreach ($role as $page => $level) {
					$level = (int)$level;
					if ($user->is_admin && ($level == 0)) {
						$level = 1;
					}
					if (isset($access_rights[$page]) == false) {
						$access_rights[$page] = $level;
					} else if ($access_rights[$page] < $level) {
						$access_rights[$page] = $level;
					}
				}
			}
		}

		/* Pages in database
		 */
		if (($pages = $db->execute("select * from pages")) === false) {
			return false;
		}
		foreach ($pages as $page) {
			$access_rights[ltrim($page["url"], "/")] = is_false($page["private"]) || $user->is_admin ? YES : NO;
		}

		if ($user->logged_in && ($user->is_admin == false)) {
			$conditions = $rids = array();
			foreach ($user->role_ids as $rid) {
				array_push($conditions, "role_id=%d");
				array_push($rids, $rid);
			}

			$query = "select p.url,a.level from pages p, page_access a ".
					 "where p.id=a.page_id and (".implode(" or ", $conditions).")";
			if (($pages = $db->execute($query, $rids)) === false) {
				return false;
			}

			foreach ($pages as $page) {
				$url = ltrim($page["url"], "/");
				if ($access_rights[$url] < $page["level"]) {
					$access_rights[$url] = $page["level"];
				}
			}
		}

		return $access_rights;
	}

	/* Generate random string
	 *
	 * INPUT:  [int length]
	 * OUTPUT: string random string
	 * ERROR:  -
	 */
	function random_string($length = 32) {
		$characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890";
		$max_pos = strlen($characters) - 1;

		$result = "";
		while ($length-- > 0) {
			$result .= $characters[mt_rand(0, $max_pos)];
		}

		return $result;
	}

	/* Get user's one time key
	 *
	 * INPUT:  object database, int user identifier
	 * OUTPUT: string one time key
	 * ERROR:  false
	 */
	function get_one_time_key($db, $user_id) {
		if (($user = $db->entry("users", $user_id)) == false) {
			return false;
		}

		if ($user["one_time_key"] != null) {
			return $user["one_time_key"];
		}

		$key = random_string();
		if ($db->update("users", $user_id, array("one_time_key" => $key)) == false) {
			return false;
		}

		return $key;
	}

	/* Validate captcha code
	 *
	 * INPUT:  string captcha code
	 * OUTPUT: boolean captcha code valid
	 * ERROR:  -
	 */
	function valid_captcha_code($code) {
		if (isset($_SESSION["captcha_code"]) == false) {
			return false;
		}

		return $_SESSION["captcha_code"] === $code;
	}
?>
