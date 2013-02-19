<?php
	class admin_page_model extends model {
		public function get_pages() {
			$query = "select id, url, private, title, visible from pages order by url";

			return $this->db->execute($query);
		}

		public function get_page($page_id) {
			if (($page = $this->db->entry("pages", $page_id)) == false) {
				return false;
			}

			$query = "select role_id,level from page_access where page_id=%d";
			if (($roles = $this->db->execute($query, $page_id)) === false) {
				return false;
			}

			$page["roles"] = array();
			foreach ($roles as $role) {
				$page["roles"][$role["role_id"]] = $role["level"];
			}

			return $page;
		}

		public function get_roles() {
			$query = "select id, name from roles order by name";

			return $this->db->execute($query);
		}

		public function page_oke($page) {
			$result = true;

			if (valid_input(trim($page["url"]), VALIDATE_URL, VALIDATE_NONEMPTY) == false) {
				$this->output->add_message("URL is empty or contains invalid characters.");
				$result = false;
			} else if ((strpos($page["url"], "//") !== false) || ($page["url"][0] !== "/")) {
				$this->output->add_message("Invalid URL.");
				$result = false;
			}

			if (trim($page["title"]) == "") {
				$this->output->add_message("Empty title not allowed.");
				$result = false;
			}

			if (valid_input($page["language"], VALIDATE_NONCAPITALS, 2) == false) {
				$this->output->add_message("Invalid language code.");
				$result = false;
			}

			$alias_config = config_file("page_aliases");
			$aliases = array();
			foreach ($alias_config as $line) {
				list($alias) = explode("->", $line);
				array_push($aliases, trim($alias));
			}

			$module = ltrim($page["url"], "/");
			$public_pages = page_to_module(config_file("public_pages"));
			$private_pages = page_to_module(config_file("private_pages"));
			if (in_array($module, $public_pages) || in_array($module, $private_pages) || in_array($module, $aliases)) {
				$this->output->add_message("URL belongs to a module.");
				$result = false;
			} else {
				$query = "select * from pages where id!=%d and url=%s limit 1";
				if (($page = $this->db->execute($query, $page["id"], $page["url"])) != false) {
					if (count($page) > 0) {
						$this->output->add_message("URL belongs to another page.");
						$result = false;
					}
				}
			}

			return $result;
		}

		public function save_access($page_id, $roles) {
			if ($this->db->query("delete from page_access where page_id=%d", $page_id) === false) {
				return false;
			}

			if (is_array($roles) == false) {
				return true;
			}

			foreach ($roles as $role_id => $has_role) {
				if (is_false($has_role) || ($role_id == ADMIN_ROLE_ID)) {
					continue;
				}

				$values = array(
					"page_id" => (int)$page_id,
					"role_id" => (int)$role_id,
					"level"   => 1);
				if ($this->db->insert("page_access", $values) == false) {
					return false;
				}
			}

			return true;
		}

		public function create_page($page) {
			$keys = array("id", "url", "language", "private", "style",
						  "title", "description", "keywords", "content", "visible");
			$page["id"] = null;
			$page["private"] = is_true($page["private"]) ? 1 : 0;
			$page["visible"] = is_true($page["visible"]) ? 1 : 0;

			if ($this->db->query("begin") == false) {
				return false;
			} else if ($this->db->insert("pages", $page, $keys) === false) {
				$this->db->query("rollback");
				return false;
			} else if ($this->save_access($this->db->last_insert_id, $page["roles"]) == false) {
				$this->db->query("rollback");
				return false;
			}

			return $this->db->query("commit") != false;
		}

		public function update_page($page, $page_id) {
			$keys = array("url", "language", "private", "style", "title",
						  "description", "keywords", "content", "visible");
			$page["private"] = is_true($page["private"]) ? 1 : 0;
			$page["visible"] = is_true($page["visible"]) ? 1 : 0;

			if ($this->db->query("begin") == false) {
				return false;
			} else if ($this->db->update("pages", $page_id, $page, $keys) === false) {
				$this->db->query("rollback");
				return false;
			} else if ($this->save_access($page_id, $page["roles"]) == false) {
				$this->db->query("rollback");
				return false;
			}

			return $this->db->query("commit") != false;
		}


		public function delete_page($page_id) {
			$queries = array(
				array("delete from page_access where page_id=%d", $page_id),
				array("delete from pages where id=%d", $page_id));

			return $this->db->transaction($queries);
		}
	}
?>
