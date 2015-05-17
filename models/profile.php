<?php
	class profile_model extends model {
		public function last_account_logs() {
			if (($fp = fopen("../logfiles/actions.log", "r")) == false) {
				return false;
			}

			$result = array();

			while (($line = fgets($fp)) !== false) {	
				list($ip, $timestamp, $user_id, $message) = explode("|", chop($line));

				if ($user_id == "-") {
					continue;
				} else if ($user_id != $this->user->id) {
					continue;
				}

				array_push($result, array(
					"ip"        => $ip,
					"timestamp" => $timestamp,
					"message"   => $message));
				if (count($result) > 15) {
					array_shift($result);
				}
			}

			fclose($fp);

			return array_reverse($result);
		}

		public function profile_oke($profile) {
			global $notification_methods;

			$result = true;

			if (is_false($profile["password_hashed"])) {
				$profile["current"]  = md5($profile["current"]);
				$profile["password"] = md5($profile["password"]);
				$profile["repeat"]   = md5($profile["repeat"]);
			}

			if ($profile["current"] != $this->user->password) {
				$this->output->add_message("Password is incorrect.");
				$result = false;
			}

			if ($profile["password"] != $profile["repeat"]) {
				$this->output->add_message("New passwords do not match.");
				$result = false;
			}

			if ($this->user->password == $profile["password"]) {
				$this->output->add_message("New password must be different from current password.");
				$result = false;
			}

			if (in_array($profile["notification_method"], array_keys($notification_methods)) == false) {
				$this->output->add_message("Invalid notification method.");
				$result = false;
			} else if (($profile["notification_method"] != "none") && ($profile["notification_key"] == "")) {
				$this->output->add_message("Specify a notification key.");
				$result = false;
			}

			return $result;
		}

		public function update_profile($profile) {
			$profile["status"] = USER_STATUS_ACTIVE;

			$keys = array("email", "notification_key", "notification_method", "daily_report");
			if ($profile["password"] != "") {
				array_push($keys, "password");
				array_push($keys, "status");
				if (is_false($profile["password_hashed"])) {
					$profile["password"]  = md5($profile["password"]);
				}
			}
			$profile["daily_report"] = is_true($profile["daily_report"]) ? YES : NO;

			return $this->db->update("users", $this->user->id, $profile, $keys);
		}
	}
?>
