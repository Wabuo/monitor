<?php
	/* libraries/oo/tablemanager_model.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	abstract class tablemanager_model extends model {
		private $valid_types = array("integer", "varchar", "text", "boolean", "datetime", "enum", "foreignkey");
		protected $table = null;
		protected $order = "id";
		protected $elements = null;

		/* Constructor
		 *
		 * INPUT:  core objects
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __construct() {
			$args = func_get_args();
			call_user_func_array(array(parent, "__construct"), $args);

			if (isset($this->elements["id"]) == false) {
				$this->elements = array_merge(
					array("id" => array(
						"label"    => "Id",
						"type"     => "integer",
						"overview" => false)),
					$this->elements);
			}
		}

		/* Magic method get
		 *
		 * INPUT:  string key
		 * OUTPUT: mixed value
		 * ERROR:  null
		 */
		public function __get($key) {
			switch ($key) {
				case "table": return $this->table;
				case "elements": return $this->elements;
			}

			return null;
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
		public function get_item($item_id) {
			return $this->db->entry($this->table, $item_id);
		}

		/* Count all items
		 * INPUT:  -
		 * OUTPUT: int number of items
		 * ERROR:  false;
		 */
		public function count_items() {
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
		public function get_items($offset, $count) {
			if (is_array($this->order) == false) {
				$order = "%S";
			} else {
				$order = implode(", ", array_fill(0, count($this->order), "%S"));
			}

			$query = "select * from %S order by ".$order." limit %d,%d";

			return $this->db->execute($query, $this->table, $this->order, $offset, $count);
		}

		/* Validate user input for saving
		 *
		 * INPUT:  array( string key => string value[, ...] )
		 * OUTPUT: boolean item validation oke
		 * ERROR:  -
		 */
		public function save_oke($item) {
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
		public function delete_oke($item_id) {
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
		public function create_item($item) {
			$keys = array_keys($this->elements);

			$item = $this->fix_variables($item);
			$item["id"] = null;

			foreach ($keys as $key) {
				$element = $this->elements[$key];
				if (($element["type"] == "foreignkey") && ($element["required"] == false)) {
					if ($item[$key] == "") {
						$item[$key] = null;
					}
				}
			}

			return $this->db->insert($this->table, $item, $keys);
		}

		/* Update item in database
		 *
		 * INPUT:  array( string key => string value[, ...] )
		 * OUTPUT: boolean update successful
		 * ERROR:  -
		 */
		public function update_item($item) {
			$keys = array_keys($this->elements);
			array_shift($keys);

			$item = $this->fix_variables($item);

			foreach ($keys as $key) {
				$element = $this->elements[$key];
				if (($element["type"] == "foreignkey") && ($element["required"] == false)) {
					if ($item[$key] == "") {
						$item[$key] = null;
					}
				}
			}

			return $this->db->update($this->table, $item["id"], $item, $keys);
		}

		/* Delete item from database
		 *
		 * INPUT:  int item identifier
		 * OUTPUT: boolean deletion successful
		 * ERROR:  -
		 */
		public function delete_item($item_id) {
			return $this->db->delete($this->table, $item_id);
		}

		/* Check class settings
		 *
		 * INPUT:  -
		 * OUTPUT: boolean class settings oke
		 * ERROR:  -
		 */
		public function class_settings_oke() {
			$class_oke = true;

			if ($this->table == null) {
				print "Table not set in ".get_class($this)."\n";
				$class_oke = false;
			}
			if (is_array($this->elements) == false) {
				print "Elements not set in ".get_class($this)."\n";
				$class_oke = false;
			} else foreach ($this->elements as $name => $element) {
				if (is_int($name)) {
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
				switch ($element["type"]) {
					case "enum":
						if (is_array($element["options"]) == false) {
							print "Options in element '".$name."' not set in ".get_class($this)."\n";
							$class_oke = false;
						}
						break;
					case "foreignkey":
						if ((isset($element["table"]) == false) || (isset($element["column"]) == false)) {
							print "Table or column in element '".$name."' not set in ".get_class($this)."\n";
							$class_oke = false;
						}
						break;
				}
			}

			return $class_oke;
		}
	}
?>
