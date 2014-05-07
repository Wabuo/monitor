<?php
	class admin_controller extends controller {
		private $menu = array(
			"Authentication and authorization" => array(
				"Users"         => array("admin/user", "users.png"),
				"Roles"         => array("admin/role", "roles.png"),
				"Access"        => array("admin/access", "access.png")),
			"Support and testing" => array(
				"Action log"    => array("admin/action", "action.png"),
				"User switch"   => array("admin/switch", "switch.png")),
			"Content" => array(
				"Hostnames"     => array("admin/hostname", "hostname.gif"),
				"Menu"          => array("admin/menu", "menu.png"),
				"News"          => array("admin/news", "news.png"),
				"Pages"         => array("admin/page", "page.png"),
				"Settings"      => array("admin/settings", "settings.png"),
				"Webservers"    => array("admin/webserver", "webserver.png")));

		public function execute() {
			if (($this->user->id == 1) && ($this->user->password == "08b5411f848a2581a41672a759c87380")) {
				$this->output->add_system_message("Don't forget to change the password of the admin account!");
			}

			if ($this->page->pathinfo[1] != null) {
				$this->output->add_system_message("The administration module '%s' does not exist.", $this->page->pathinfo[1]);
			}

			if (is_true(DEBUG_MODE)) {
				$this->output->add_system_message("Website is running in debug mode. Set DEBUG_MODE in settings/website.conf to 'no'.");
			}

			$access_list = page_access_list($this->db, $this->user);
			$private_pages = config_file("private_pages");

			$this->output->open_tag("menu");

			foreach ($this->menu as $text => $section) {

				$this->output->open_tag("section", array(
					"text"  => $text,
					"class" => str_replace(" ", "_", strtolower($text))));

				foreach ($section as $text => $info) {
					list($page, $icon) = $info;

					if (in_array($page, $private_pages) == false) {
						continue;
					}

					if (isset($access_list[$page])) {
						$access = show_boolean($access_list[$page] > 0);
					} else {
						$access = show_boolean(true);
					}

					$this->output->add_tag("entry", $page, array(
						"text"   => $text,
						"access" => $access,
						"icon"   => $icon));
				}

				$this->output->close_tag();
			}

			$this->output->close_tag();
		}
	}
?>
