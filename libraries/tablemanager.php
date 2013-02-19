<?php
	/* libraries/tablemanager.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.hiawatha-webserver.org/banshee
	 */

	abstract class tablemanager {
		private $valid_types = array("integer", "varchar", "text", "boolean", "datetime", "enum");
		protected $db = null;
		protected $user = null;
		protected $output = null;
		protected $table = null;
		protected $order = "id";
		protected $name = null;
		protected $pathinfo_offset = 1;
		protected $elements = null;
		protected $back = null;
		protected $icon = null;
		protected $page_size = 25;
		protected $pagination_links = 7;
		protected $pagination_step = 1;

		/* Constructor
		 *
		 * INPUT:  object database, object output
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __construct($db, $user, $output) {
			$this->db = $db;
			$this->user = $user;
			$this->output = $output;

			if (isset($this->elements["id"]) == false) {
				$this->elements = array_merge(
					array("id" => array(
						"label"    => "Id",
						"type"     => "integer",
						"overview" => false)),
					$this->elements);
			}

			$this->output->add_css("includes/tablemanager.css");
			$this->output->add_css("includes/calendar.css");
		}

		/* Fix variables
		 *
		 * INPUT:  array( string key => string value )
		 * OUTPUT: array( string key => mixed value )
		 * ERROR:  -
		 */
		private function fix_variables($item) {
			foreach ($this->elements as $name => $element) {
				switch ($element["type"]) {
					case "boolean":
						$item[$name] = is_true($item[$name]) ? 1 : 0;
						break;
					case "integer":
						$item[$name] = (integer)$item[$name];
						break;
				}
			}

			return $item;
		}

		/* Get item by its id
		 *
		 * INPUT:  int item indentifier
		 * OUTPUT: array( string key => string value[, ...] )
		 * ERROR:  false
		 */
		protected function get_item($item_id) {
			return $this->db->entry($this->table, $item_id);
		}

		/* Count all items
		 * INPUT:  -
		 * OUTPUT: int number of items
		 * ERROR:  false;
		 */
		protected function count_items() {
			$query = "select count(*) as count from %S";

			if (($result = $this->db->execute($query, $this->table)) == false) {
				return false;
			}

			return $result[0]["count"];
		}

		/* Get all items
		 *
		 * INPUT:  -
		 * OUTPUT: array( string key => string value[, ...] )
		 * ERROR:  false
		 */
		protected function get_items($offset, $count) {
			if (is_array($this->order) == false) {
				$order = "%S";
			} else {
				$order = implode(", ", array_fill(0, count($this->order), "%S"));
			}

			$query = "select * from %S order by ".$order." limit %d,%d";

			return $this->db->execute($query, $this->table, $this->order, $offset, $count);
		}

		/* Show overview
		 *
		 * INPUT:  array( string key => string value[, ...] )
		 * OUTPUT: -
		 * ERROR:  -
		 */
		protected function show_overview() {
			if (($item_count = $this->count_items()) === false) {
				$this->output->add_tag("result", "Error while counting items.");
			}

			$paging = new pagination($this->output, "tableadmin_".$this->table, $this->page_size, $item_count);

			if (($items = $this->get_items($paging->offset, $paging->size)) === false) {
				$this->output->add_tag("result", "Error while creating overview.");
			}

			$this->output->open_tag("overview");

			/* Labels
			 */
			$this->output->open_tag("labels", array("name" => strtolower($this->name)));
			foreach ($this->elements as $name => $element) {
				$args = array(
					"name" => $name,
					"overview" => show_boolean($element["overview"]));
				if ($element["overview"]) {
					$this->output->add_tag("label", $element["label"], $args);
				}
			}
			$this->output->close_tag();

			/* Values
			 */
			$this->output->open_tag("items");
			foreach ($items as $item) {
				$this->output->open_tag("item", array("id" => $item["id"]));
				foreach ($item as $name => $value) {
					if ($this->elements[$name]["overview"]) {
						if ($this->elements[$name]["type"] == "boolean") {
							$value = show_boolean($value);
						}
						$this->output->add_tag("value", $value, array("name" => $name));
					}
				}
				$this->output->close_tag();
			}
			$this->output->close_tag();

			$paging->show_browse_links($this->pagination_links, $this->pagination_step);

			$this->output->close_tag();
		}

		/* Show create / update form
		 *
		 * INPUT:  array( string key => string value[, ...] )
		 * OUTPUT: -
		 * ERROR:  -
		 */
		protected function show_item_form($item) {
			$this->output->open_tag("edit");

			$args = array("name" => strtolower($this->name));
			if (isset($item["id"])) {
				$args["id"] = $item["id"];
			}

			$this->output->open_tag("form", $args);
			foreach ($this->elements as $name => $element) {
				if ($name == "id") {
					continue;
				}

				$this->output->open_tag("element", array(
					"name" => $name,
					"type" => $element["type"]));

				if (isset($element["label"])) {
					$this->output->add_tag("label", $element["label"]);
				}
				$this->output->add_tag("value", $item[$name]);
				if ($element["type"] == "enum") {
					$this->output->open_tag("options");
					foreach ($element["options"] as $value => $label) {
						$this->output->add_tag("option", $label, array("value" => $value));
					}
					$this->output->close_tag();
				}

				$this->output->close_tag();
			}
			$this->output->close_tag();

			$this->output->close_tag();
		}

		/* Handle user submit
		 *
		 * INPUT:  -
		 * OUTPUT: -
		 * ERROR:  -
		 */
		protected function handle_submit() {
			$item = strtolower($this->name);

			if ($_POST["submit_button"] == "Save ".$item) {
				/* Save item
				 */
				if ($this->save_oke($_POST) == false) {
					$this->show_item_form($_POST);
				} else if (isset($_POST["id"]) == false) {
					/* Create item
					 */
					if ($this->create_item($this->fix_variables($_POST)) === false) {
						$this->output->add_message("Error while creating ".$item.".");
						$this->show_item_form($_POST);
					} else {
						$this->show_overview();
					}
				} else {
					/* Update item 
					 */
					if ($this->update_item($this->fix_variables($_POST)) === false) {
						$this->output->add_message("Error while updating ".$item.".");
						$this->show_item_form($_POST);
					} else {
						$this->show_overview();
					}
				}
			} else if ($_POST["submit_button"] == "Delete ".$item) {
				/* Delete item 
				 */
				if ($this->delete_oke($_POST["id"]) == false) {
					$this->show_item_form($_POST);
				} else if ($this->delete_item($_POST["id"]) === false) {
					$this->output->add_tag("result", "Error while deleting ".$item.".");
				} else {
					$this->show_overview();
				}
			} else {
				$this->output->add_tag("result", "Huh?");
			}
		}

		/* Validate user input for saving
		 *
		 * INPUT:  array( string key => string value[, ...] )
		 * OUTPUT: boolean item validation oke
		 * ERROR:  -
		 */
		protected function save_oke($item) {
			$result = true;

			foreach ($this->elements as $name => $element) {
				if ($name == "id") {	
					continue;
				}

				if (($element["required"]) && ($element["type"] != "boolean") && (trim($item[$name]) == "")) {
					$this->output->add_message("The field ".$element["label"]." cannot be empty.");
					$result = false;
				}
				switch ($element["type"]) {
					case "datetime":
						if (valid_timestamp($item[$name]) == false) {
							$this->output->add_message("The field ".$element["label"]." doesn't contain a valid timestamp.");
							$result = false;
						}
						break;
					case "enum":
						if (in_array($item[$name], array_keys($element["options"])) == false) {
							$this->output->add_message("The field ".$element["label"]." doesn't contain a valid value.");
							$result = false;
						}
						break;
					case "integer":
						if (is_numeric($item[$name]) == false) {
							$this->output->add_message("The field ".$element["label"]." should be numerical.");
							$result = false;
						}
						break;
				}
			}

			return $result;
		}

		/* Validate user input for deleting
		 *
		 * INPUT:  int item identifier
		 * OUTPUT: boolean deletion successful
		 * ERROR:  -
		 */
		protected function delete_oke($item_id) {
			if (valid_input($item_id, VALIDATE_NUMBERS, VALIDATE_NONEMPTY) == false) {
				$this->output->add_message("Invalid item id.");
				return false;
			}

			return true;
		}

		/* Create item in database
		 *
		 * INPUT:  array( string key => string value[, ...] )
		 * OUTPUT: boolean creating successful
		 * ERROR:  -
		 */
		protected function create_item($item) {
			$keys = array_keys($this->elements);

			$item["id"] = null;

			if ($this->db->insert($this->table, $item, $keys) === false) {
				return false;
			}

			$this->user->log_action($this->name." ".$this->db->last_insert_id." created");

			return true;
		}

		/* Update item in database
		 *
		 * INPUT:  array( string key => string value[, ...] )
		 * OUTPUT: boolean update successful
		 * ERROR:  -
		 */
		protected function update_item($item) {
			$keys = array_keys($this->elements);
			array_shift($keys);

			if ($this->db->update($this->table, $item["id"], $item, $keys) === false) {
				return false;
			}

			$this->user->log_action($this->name." ".$item["id"]." updated");

			return true;
		}

		/* Delete item from database
		 *
		 * INPUT:  int item identifier
		 * OUTPUT: boolean deletion successful
		 * ERROR:  -
		 */
		protected function delete_item($item_id) {
			if ($this->db->delete($this->table, $item_id) === false) {
				return false;
			}

			$this->user->log_action($this->name." ".$item_id." deleted");

			return true;
		}

		/* Check class settings
		 *
		 * INPUT:  -
		 * OUTPUT: boolean class settings oke
		 * ERROR:  -
		 */
		private function class_settings_oke() {
			$class_oke = true;

			if ($this->name == null) {
				print "Name not set in ".get_class($this)."\n";
				$class_oke = false;
			}
			if ($this->table == null) {
				print "Table not set in ".get_class($this)."\n";
				$class_oke = false;
			}
			if (is_array($this->elements) == false) {
				print "Elements not set in ".get_class($this)."\n";
				$class_oke = false;
			} else foreach ($this->elements as $name => $element) {
				if (is_integer($name)) {
					print "Numeric element names are not allowed in ".get_class($this)."\n";
					$class_oke = false;
				}
				if (isset($element["label"]) == false) {
					print "Label in element '".$name."' not set in ".get_class($this)."\n";
					$class_oke = false;
				}
				if (in_array($element["type"], $this->valid_types) == false) {
					print "Type in element '".$name."' not set in ".get_class($this)."\n";
					$class_oke = false;
				}
			}

			return $class_oke;
		}

		/* Main function
		 *
		 * INPUT:  -
		 * OUTPUT: boolean execution successful
		 * ERROR:  -
		 */
		public function execute() {
			global $_page;

			/* Check class settings
			 */
			if ($this->class_settings_oke() == false) {
				return false;
			}

			/* Start
			 */
			$this->output->open_tag("tablemanager");

			$this->output->add_tag("name", $this->name);
			if ($this->back !== null) {
				$this->output->add_tag("back", $this->back);
			}
			if ($this->icon !== null) {
				$this->output->add_tag("icon", $this->icon);
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				/* Handle forum submit
				 */
				$this->handle_submit();
			} else if ($_page->pathinfo[$this->pathinfo_offset] == "new") {
				/* Show form for new item
				 */
				$item = array();
				foreach ($this->elements as $name => $element) {
					if (isset($element["default"])) {
						$item[$name] = $element["default"];
					} else if ($element["type"] == "datetime") {
						$item[$name] = date("Y-m-d H:i:s");
					}
				}
				$this->show_item_form($item);
			} else if (valid_input($_page->pathinfo[$this->pathinfo_offset], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Show form for existing item
				 */
				if (($item = $this->get_item($_page->pathinfo[$this->pathinfo_offset])) == false) {
					$this->output->add_tag("result", $this->name." not found.");
				} else {
					$this->show_item_form($item);
				}
			} else {
				/* Show item overview
				 */
				$this->show_overview();
			}

			$this->output->close_tag();

			return true;
		}
	}
?>
