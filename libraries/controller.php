<?php
	/* libraries/oo/controller.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	abstract class controller {
		protected $db = null;
		protected $settings = null;
		protected $user = null;
		protected $page = null;
		protected $output = null;
		protected $model = null;

		/* Constructor
		 *
		 * INPUT:  object database, object settings, object user, object page, object output
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __construct($db, $settings, $user, $page, $output) {
			$this->db = $db;
			$this->settings = $settings;
			$this->user = $user;
			$this->page = $page;
			$this->output = $output;

			$class = str_replace("/", "_", $page->module)."_model";
			if (class_exists($class)) {
				$this->model = new $class($db, $settings, $user, $page, $output);
			}
		}

		/* Default execute function
		 *
		 * INPUT:  -
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function execute() {
			if ($this->page->ajax_request == false) {
				print "Page controller has no execute() function.\n";
			}
		}
	}
?>
