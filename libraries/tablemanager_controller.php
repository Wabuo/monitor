<?php
	/* libraries/oo/tablemanager_controller.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class tablemanager_controller extends controller {
		protected $name = "Table";
		protected $pathinfo_offset = 1;
		protected $back = null;
		protected $icon = null;
		protected $page_size = 25;
		protected $pagination_links = 7;
		protected $pagination_step = 1;
		protected $foreign_null = "---";

		/* Show overview
		 *
		 * INPUT:  array( string key => string value[, ...] )
		 * OUTPUT: -
		 * ERROR:  -
		 */
		protected function show_overview() {
			if (($item_count = $this->model->count_items()) === false) {
				$this->output->add_tag("result", "Error while counting items.");
			}

			$paging = new pagination($this->output, "tableadmin_".$this->model->table, $this->page_size, $item_count);

			if (($items = $this->model->get_items($paging->offset, $paging->size)) === false) {
				$this->output->add_tag("result", "Error while creating overview.");
			}

			$this->output->open_tag("overview");

			/* Labels
			 */
			$this->output->open_tag("labels", array("name" => strtolower($this->name)));
			foreach ($this->model->elements as $name => $element) {
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
					$element = $this->model->elements[$name];
					if ($element["overview"]) {
						switch ($element["type"]) {
							case "boolean":
								$value = show_boolean($value);
								break;
							case "foreignkey":
								if ($value === null) {
									$value = $this->foreign_null;
								} else if (($result = $this->db->entry($element["table"], $value)) != false) {
									$value = $result[$element["column"]];
								}
								break;
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
			$calendar_initialized = false;

			$this->output->open_tag("edit");

			$args = array("name" => strtolower($this->name));
			if (isset($item["id"])) {
				$args["id"] = $item["id"];
			}

			$this->output->open_tag("form", $args);
			foreach ($this->model->elements as $name => $element) {
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

				if ($element["type"] == "foreignkey") {
					$element["options"] = array();
					if ($element["required"] == false) {
						$element["options"][null] = $this->foreign_null;
					}
					$query = "select id,%S from %S order by %S";
					$col = $element["column"];
					if (($options = $this->db->execute($query, $col, $element["table"], $col)) != false) {
						foreach ($options as $option) {
							$element["options"][$option["id"]] = $option[$col];
						}
					}
				}

				if (($element["type"] == "datetime") && ($calendar_initialized == false)) {
					$this->output->add_css("includes/calendar.css");
					$this->output->add_javascript("calendar.js");
					$this->output->add_javascript("calendar-en.js");
					$this->output->add_javascript("calendar-setup.js");
					$calendar_initialized = true;
				}

				if (($element["type"] == "enum") || ($element["type"] == "foreignkey")) {
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
				if ($this->model->save_oke($_POST) == false) {
					$this->show_item_form($_POST);
				} else if (isset($_POST["id"]) == false) {
					/* Create item
					 */
					if ($this->model->create_item($_POST) === false) {
						$this->output->add_message("Error while creating ".$item.".");
						$this->show_item_form($_POST);
					} else {
						$this->user->log_action($this->name." ".$this->db->last_insert_id." created");
						$this->show_overview();
					}
				} else {
					/* Update item 
					 */
					if ($this->model->update_item($_POST) === false) {
						$this->output->add_message("Error while updating ".$item.".");
						$this->show_item_form($_POST);
					} else {
						$this->user->log_action($this->name." ".$_POST["id"]." updated");
						$this->show_overview();
					}
				}
			} else if ($_POST["submit_button"] == "Delete ".$item) {
				/* Delete item 
				 */
				if ($this->model->delete_oke($_POST["id"]) == false) {
					$this->show_item_form($_POST);
				} else if ($this->model->delete_item($_POST["id"]) === false) {
					$this->output->add_tag("result", "Error while deleting ".$item.".");
				} else {
					$this->user->log_action($this->name." ".$_POST["id"]." deleted");
					$this->show_overview();
				}
			} else {
				$this->output->add_tag("result", "Huh?");
			}
		}

		/* Main function
		 *
		 * INPUT:  -
		 * OUTPUT: boolean execution successful
		 * ERROR:  -
		 */
		public function execute() {
			if (is_a($this->model, "tablemanager_model") == false) {
				print "Tablemanager model has not been defined.\n";
				return false;
			}

			/* Check class settings
			 */
			if ($this->model->class_settings_oke() == false) {
				return false;
			}

			/* Start
			 */
			$this->output->add_css("includes/tablemanager.css");

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
			} else if ($this->page->pathinfo[$this->pathinfo_offset] == "new") {
				/* Show form for new item
				 */
				$item = array();
				foreach ($this->model->elements as $name => $element) {
					if (isset($element["default"])) {
						$item[$name] = $element["default"];
					} else if ($element["type"] == "datetime") {
						$item[$name] = date("Y-m-d H:i:s");
					}
				}
				$this->show_item_form($item);
			} else if (valid_input($this->page->pathinfo[$this->pathinfo_offset], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Show form for existing item
				 */
				if (($item = $this->model->get_item($this->page->pathinfo[$this->pathinfo_offset])) == false) {
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
