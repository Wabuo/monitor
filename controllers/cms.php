<?php
	class cms_controller extends controller {
		public function execute() {
			$menu = array(
				"Authentication, authorization & system" => array(
					"Users"         => array("cms/user", "users.png"),
					"Roles"         => array("cms/role", "roles.png"),
					"Organisations" => array("cms/organisation", "organisations.png"),
					"Access"        => array("cms/access", "access.png"),
					"User switch"   => array("cms/switch", "switch.png"),
					"Action log"    => array("cms/action", "action.png"),
					"Settings"      => array("cms/settings", "settings.png")),
				"Content" => array(
					"Files"         => array("cms/file", "file.png"),
					"Hostnames"     => array("cms/hostname", "hostname.gif"),
					#"Languages"     => array("cms/language", "language.png"),
					"Menu"          => array("cms/menu", "menu.png"),
					"Pages"         => array("cms/page", "page.png"),
					"Webservers"    => array("cms/webserver", "webserver.png")));

			if (($this->user->id == 1) && ($this->user->password == "610706e9a48f85476e04d270bd6dc7492cdcd9ad7e91878007dff629ab11f195")) {
				$this->output->add_system_warning("Don't forget to change the password of the admin account!");
			}

			if ($this->settings->secret_website_code == "CHANGE_ME_INTO_A_RANDOM_STRING") {
				$this->output->add_system_warning("Don't forget to change the secret_website_code setting.");
			}

			if (is_true(DEBUG_MODE)) {
				$this->output->add_system_warning("Website is running in debug mode. Set DEBUG_MODE in settings/website.conf to 'no'.");
			}

			if ($this->page->pathinfo[1] != null) {	
				$this->output->add_system_warning("The administration module '%s' does not exist.", $this->page->pathinfo[1]);
			}

			if (is_false(MULTILINGUAL)) {
				unset($menu["Content"]["Languages"]);
			}

			$access_list = page_access_list($this->db, $this->user);
			$private_pages = config_file("private_pages");

			$this->output->open_tag("menu");

			foreach ($menu as $text => $section) {

				$this->output->open_tag("section", array(
					"text"  => $text,
					"class" => strtr(strtolower($text), " &", "__")));

				foreach ($section as $text => $info) {
					list($page, $icon) = $info;

					if (in_array($page, $private_pages) == false) {
						continue;
					}

					if (isset($access_list[$page])) {
						$access = $access_list[$page] > 0;
					} else {
						$access = true;
					}

					$this->output->add_tag("entry", $page, array(
						"text"   => $text,
						"access" => show_boolean($access),
						"icon"   => $icon));
				}

				$this->output->close_tag();
			}

			$this->output->close_tag();
		}
	}
?>
