<?php
	/* libraries/output.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 *
	 * Don't change this file, unless you know what you are doing.
	 */

	final class output extends XML {
		private $settings = null;
		private $page = null;
		private $mode = null;
		private $language = null;
		private $description = null;
		private $keywords = null;
		private $system_messages = arraY();
		private $messages = array();
		private $javascripts = array();
		private $onload_javascript = array();
		private $alternates = array();
		private $title = null;
		private $css_links = array();
		private $inline_css = null;
		private $content_type = "text/html; charset=utf-8";
		private $layout = LAYOUT_SITE;
		private $disabled = false;

		/* Constructor
		 *
		 * INPUT:  object database, object settings, object page
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __construct($db, $settings, $page) {
			parent::__construct($db);
			$this->settings = $settings;
			$this->page = $page;

			if (isset($_GET["output"])) {
				$this->mode = $_GET["output"];
			} else if ($this->page->ajax_request) {
				$this->mode = "xml";
			}

			$this->language = $settings->default_language;
			$this->description = $settings->head_description;
			$this->keywords = $settings->head_keywords;

			$this->set_layout();
		}

		/* Constructor
		 *
		 * INPUT:  object database, boolean AJAX request
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __destruct() {
			$_SESSION["previous_layout"] = $this->layout;
		}

		/* Magic method get
		 *
		 * INPUT:  string key
		 * OUTPUT: mixed value
		 * ERROR:  null
		 */
		public function __get($key) {
			switch ($key) {
				case "mode": return $this->mode;
				case "language": return $this->language;
				case "description": return $this->description;
				case "keywords": return $this->keywords;
				case "title": return $this->title;
				case "inline_css": return $this->inline_css;
				case "content_type": return $this->content_type;
				case "layout": return $this->layout;
				case "disabled": return $this->disabled;
			}

			return parent::__get($key);
		}

		/* Magic method set
		 *
		 * INPUT:  string key, string value
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __set($key, $value) {
			switch ($key) {
				case "mode": $this->mode = $value; break;
				case "language": $this->language = $value; break;
				case "description": $this->description = $value; break;
				case "keywords": $this->keywords = $value; break;
				case "title": $this->title = $value; break;
				case "inline_css": $this->inline_css = $value; break;
				case "content_type": $this->content_type = $value; break;
				case "disabled": $this->disabled = $value; break;
				default: trigger_error("Unknown output variable: ".$key);
			}
		}

		/* Add CSS link to output
		 *
		 * INPUT:  string CSS filename
		 * OUTPUT: boolean CSS file exists
		 * ERROR:  -
		 */
		public function add_css($css) {
			if (file_exists("css/".$css) == false) {
				return false;
			}
			$css = "/css/".$css;

			if (in_array($css, $this->css_links) == false) {
				array_push($this->css_links, $css);
			}

			return true;
		}

		/* Add javascript link
		 *
		 * INPUT:  string link
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function add_javascript($script) {
			if ((substr($script, 0, 7) != "http://") && (substr($script, 0, 8) != "https://")) {
				if (file_exists("js/".$script) == false) {
					return false;
				}

				$script = "/js/".$script;
			}

			if (in_array($script, $this->javascripts) == false) {
				array_push($this->javascripts, $script);
			}

			return true;
		}

		/* Set onload function of body tag
		 *
		 * INPUT:  string javascript function
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function onload_javascript($function) {
			array_push($this->onload_javascript, rtrim($function, ";").";");
		}

		/* Add alternate link
		 *
		 * INPUT:  string title, string type, string url
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function add_alternate($title, $type, $url) {
			array_push($this->alternates, array(
				"title" => $title,
				"type"  => $type,
				"url"   => $url));
		}

		/* Set page layout
		 *
		 * INPUT:  string layout
		 * OUTPUT: bool layout accepted
		 * ERROR:  -
		 */
		public function set_layout($layout = null) {
			if ($layout == null) {
				$inherit_layout = array(LOGOUT_MODULE, "password");
				if (substr($this->page->url, 0, 6) == "/admin") {
					$this->layout = LAYOUT_CMS;
				} else if (in_array($this->page->module, $inherit_layout)) {
					if ($_SESSION["previous_layout"] == LAYOUT_CMS) {
						$this->layout = LAYOUT_CMS;
					}
				}
			} else {
				if (file_exists("../views/includes/".$layout.".xslt") == false) {
					return false;
				}

				$this->layout = $layout;
			}

			return true;
		}

		/* Add system message to message buffer
		 *
		 * input:  string message
		 * output: -
		 * error:  -
		 */
		public function add_system_message($message) {
			array_push($this->system_messages, $message);
		}

		/* Add message to message buffer
		 *
		 * input:  string message
		 * output: -
		 * error:  -
		 */
		public function add_message($message) {
			array_push($this->messages, $message);
		}

		/* Close XML tag
		 *
		 * INPUT:  -
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function close_tag() {
			if (($this->page->ajax_request == false) && ($this->depth == 1)) {
				/* Messages
				 */
				if (count($this->system_messages) > 0) {
					$this->open_tag("system_messages");
					foreach ($this->system_messages as $message) {
						$this->add_tag("message", $message);
					}
					$this->close_tag();
				}

				if (count($this->messages) > 0) {
					$this->open_tag("messages");
					foreach ($this->messages as $message) {
						$this->add_tag("message", $message);
					}
					$this->close_tag();
				}

				$this->open_tag($this->layout);

				/* Header information
				 */
				$this->add_tag("description", $this->description);
				$this->add_tag("keywords", $this->keywords);
				$this->add_tag("title", $this->settings->head_title, array("page" => $this->title));
				$this->add_tag("language", $this->language);

				/* Cascading Style Sheets
				 */
				$this->open_tag("styles");
				foreach ($this->css_links as $css) {
					$this->add_tag("style", $css);
				}
				$this->close_tag();
				$this->add_tag("inline_css", $this->inline_css);

				/* Javascripts
				 */
				$params = array();
				if (count($this->onload_javascript) > 0) {
					$params["onload"] = implode(" ", $this->onload_javascript);
				}
				$this->open_tag("javascripts", $params);
				foreach ($this->javascripts as $javascript) {
					$this->add_tag("javascript", $javascript);
				}
				$this->close_tag();

				/* Alternates
				 */
				$this->open_tag("alternates");
				foreach ($this->alternates as $alternate) {
					$this->add_tag("alternate", $alternate["title"], array(
						"type"  => $alternate["type"],
						"url" => $alternate["url"]));
				}
				$this->close_tag();

				$this->close_tag();
			}

			parent::close_tag();
		}

		/* Mask transform function
		 *
		 * INPUT:  string XSLT filename
		 * OUTPUT: false
		 * ERROR:  -
		 */
		public function transform($xslt_file) {
			return false;
		}

		/* Generate output via XSLT
		 *
		 * INPUT:  string output type, string XSLT file
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function generate() {
			if ($this->disabled) {
				return;
			}

			switch ($this->mode) {
				case "xml":
					header("Content-Type: text/xml");
					$result = $this->document;
					break;
				case "data":
					header("Content-Type: text/plain");
					$result = $this->document;
					break;
				case null:
					$xslt_file = "../views/".$this->page->view.".xslt";
					if (($result = parent::transform($xslt_file)) === false) {
						$result = "XSL Transformation error";
						break;
					}

					/* GZip content encoding
					 */
					$encodings = $_SERVER["HTTP_ACCEPT_ENCODING"];
					$php_gzip = ini_get("zlib.output_compression");
					if (($encodings !== null) && (strlen($result) >= 1024) && is_false($php_gzip) && (headers_sent() == false)) {
						$encodings = explode(",", $encodings);
						foreach ($encodings as $encoding) {
							$encoding = trim($encoding);
							if ($encoding == "gzip") {
								header("Content-Encoding: gzip");
								$result = gzencode($result, 6);
								break;
							}
						}
					}

					/* Print output
					 */
					header("Content-Type: ".$this->content_type);
					header("Content-Language: ".$this->language);
					if (is_false($php_gzip)) {
						header("Content-Length: ".strlen($result));
					}
					break;
				default:
					$result = "Unknown output type";
			}

			return $result;
		}
	}
?>
