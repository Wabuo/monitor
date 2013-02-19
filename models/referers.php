<?php
	class referers_model extends model {
		private function google_sort($a, $b) {
			return $a["count"] > $b["count"] ? -1 : 1;
		}

		public function get_referers($filter_hostname, $filter_webserver) {
			$query = "select count(*) as count, f.referer from requests r, referers f ".
					 "where r.referer_id=f.id and substring(f.referer, 1, 4)=%s";

			$filter_args = array();
			if ($filter_hostname != 0) {
				$query .= " and r.hostname_id=%d";
				array_push($filter_args, $filter_hostname);
			}
			if ($filter_webserver != 0) {
				$query .= " and r.webserver_id=%d";
				array_push($filter_args, $filter_webserver);
			}

			$query .= " group by f.referer order by count desc";

			if (($entries = $this->db->execute($query, "http", $filter_args)) === false) {
				return false;
			}

			$result = array(
				"hostnames" => array(),
				"referers"  => array());
			foreach ($entries as $entry) {
				list(,, $hostname) = explode("/", $entry["referer"], 4);
				$hostname = str_replace("www.", "", $hostname);

				/* Handle Google referers
				 */
				$hostname = preg_replace("/^google.[a-z]{2,3}(.[a-z]{2,3})?$/", "Google", $hostname);
				if (strpos($entry["referer"], "google.") !== false) {
					list(, $referer) = explode("?", $entry["referer"]);
					$items = explode("&", $referer);
					$found = false;
					foreach ($items as $item) {	
						if (substr($item, 0, 2) == "q=") {	
							$entry["referer"] = "http://www.google.com/search?".$item;
							$found = true;
							break;
						}
					}

					if ($found == false) {
						$entry["referer"] = "http://www.google.com/";
					}
				}

				$result["hostnames"][$hostname] += $entry["count"];
				if (is_array($result["referers"][$hostname]) == false) {
					$result["referers"][$hostname] = array();
				}

				$found = false;
				foreach ($result["referers"][$hostname] as &$elem) {
					if ($elem["referer"] == $entry["referer"]) {
						$elem["count"] += $entry["count"];
						$found = true;
						break;
					}
					unset($elem);
				}

				if ($found == false) {
					array_push($result["referers"][$hostname], $entry);
				}
			}

			if (is_array($result["referers"]["Google"])) {
				usort($result["referers"]["Google"], array($this, "google_sort"));
			}

			arsort($result["hostnames"]);

			return $result;
		}
	}
?>
