<?php
	/* libraries/oo/model.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	abstract class model {
		protected $db = null;
		protected $settings = null;
		protected $user = null;
		protected $page = null;
		protected $output = null;

		/* Constructor
		 *
		 * INPUT:  object database, object user, object page, object output
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __construct($db, $settings, $user, $page, $output) {
			$this->db = $db;
			$this->settings = $settings;
			$this->user = $user;
			$this->page = $page;
			$this->output = $output;
		}
	}
?>
