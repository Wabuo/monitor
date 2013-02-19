<?php
	require("../libraries/user_agent.php");

	class web_browser_model extends model {
		public function get_information($filter_hostname, $filter_webserver) {
			$query = "select b.browser, count(*) as count ".
					 "from requests r, user_agents u, user_agent_browser b, user_agent_os o ".
					 "where r.user_agent_id=u.id and u.browser_id=b.id and u.os_id=o.id";

			$filter_args = array();
			if ($filter_hostname != 0) {
				$query .= " and r.hostname_id=%d";
				array_push($filter_args, $filter_hostname);
			}
			if ($filter_webserver != 0) {
				$query .= " and r.webserver_id=%d";
				array_push($filter_args, $filter_webserver);
			}
			$query .= " group by browser order by count desc";

			return $this->db->execute($query, $filter_args);
		}

		private function sort_browser_list($a1, $a2) {
			return $a1["count"] > $a2["count"] ? -1 : 1;
		}

		public function remove_browser_version($info) {
			global $browser_list;

			$browsers = array_unique($browser_list);

			/* Remove version information
			 */
			foreach ($info as $i => $item) {
				foreach ($browsers as $browser) {
					if (strpos($item["browser"], $browser) !== false) {
						$info[$i]["browser"] = $browser;
					}
				}
				unset($item);
			}

			/* Combine browser records
			 */
			$result = array();
			foreach ($info as $item_old) {
				foreach ($result as $i => $item_new) {
					if ($item_old["browser"] == $item_new["browser"]) {
						$result[$i]["count"] += $item_old["count"];
						continue 2;
					}
				}
				array_push($result, $item_old);
			}

			/* Sort browser list
			 */
			uasort($result, array($this, "sort_browser_list"));

			return $result;
		}
	}
?>
