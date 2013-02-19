<?php
	class system_page_controller extends controller {
		public function execute() {
			if (($page = $this->model->get_page($this->page->url)) != false) {
				/* Page header
				 */
				if (trim($page["description"]) != "") {	
					$this->output->description = $page["description"];
				}
				if (trim($page["keywords"]) != "") {
					$this->output->keywords = $page["keywords"];
				}
				$this->output->title = $page["title"];
				$this->output->inline_css = $page["style"];
				$this->output->language = $page["language"];

				/* Page content
				 */
				$this->output->open_tag("page");
				$this->output->add_tag("title", $page["title"]);
				$this->output->add_tag("content", $page["content"]);
				$this->output->close_tag();
			} else {
				$this->output->add_tag("website_error", 500);
			}
		}
	}
?>
