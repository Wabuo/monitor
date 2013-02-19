<?php
	/* libraries/email.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class email {
		protected $to = array();
		protected $cc = array();
		protected $bcc = array();
		protected $from = null;
		protected $reply_to = null;
		protected $subject = null;
		protected $messages = array();
		protected $attachments = array();
		protected $sender_address = null;

		/* Constructor
		 *
		 * INPUT:  string subject[, string e-mail][, string name]
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __construct($subject, $from_address = null, $from_name = null) {
			$parts = explode("\n", $subject);
			$this->subject = trim(array_shift($parts));

			if ($this->valid_address($from_address)) {
				$this->from = $this->make_address($from_address, $from_name);
				$this->sender_address = $from_address;
			}
		}

		/* Validate an e-mail address
		*
		* INPUT:  string e-mail address
		* OUTPUT: boolean e-mail address oke
		* ERROR:  -
		*/
		public static function valid_address($email) {
			return preg_match("/^[0-9A-Za-z]([-_.~]?[0-9A-Za-z])*@[0-9A-Za-z]([-.]?[0-9A-Za-z])*\\.[A-Za-z]{2,4}$/", $email) === 1;
		}

		/* Combine name and e-mail address
		 *
		 * INPUT:  string e-mail address, string name
		 * OUTPUT: string combined name and address
		 * ERROR:  -
		 */
		protected function make_address($address, $name) {
			$address = strtolower($address);

			if ($name === null) {
				return $address;
			}

			$parts = explode("\n", $name);
			$name = trim(array_shift($parts));

			return $name." <".$address.">";
		}

		/* Set reply-to
		 *
		 * INPUT:  string e-mail address[, string name]
		 * OUTPUT: boolean valid e-mail address
		 * ERROR:  -
		 */
		public function reply_to($address, $name = null) {
			if ($this->valid_address($address) == false) {
				return false;
			}

			$this->reply_to = $this->make_address($address, $name);
			$this->sender_address = $address;

			return true;
		}

		/* Add recipient
		 *
		 * INPUT:  string e-mail address[, string name]
		 * OUTPUT: boolean valid e-mail address
		 * ERROR:  -
		 */
		public function to($address, $name = null) {
			if ($this->valid_address($address) == false) {
				return false;
			}

			array_push($this->to, $this->make_address($address, $name));

			return true;
		}

		/* Add recipient from database
		 *
		 * INPUT:  object database, int user id
		 * OUTPUT: boolean valid user id and valid e-mail address
		 * ERROR:  -
		 */
		public function to_user_id($db, $user_id) {
			if (($user = $db->entry("users", $user_id)) == false) {
				return false;
			}

			return $this->to($user["email"], $user["fullname"]);
		}

		/* Add Carbon Copy recipient
		 *
		 * INPUT:  string e-mail address[, string name]
		 * OUTPUT: boolean valid e-mail address
		 * ERROR:  -
		 */
		public function cc($address, $name = null) {
			if ($this->valid_address($address) == false) {
				return false;
			}

			array_push($this->cc, $this->make_address($address, $name));

			return true;
		}

		/* Add Blind Carbon Copy recipient
		 *
		 * INPUT:  string e-mail address[, string name]
		 * OUTPUT: boolean valid e-mail address
		 * ERROR:  -
		 */
		public function bcc($address, $name = null) {
			if ($this->valid_address($address) == false) {
				return false;
			}

			array_push($this->bcc, $this->make_address($address, $name));

			return true;
		}

		/* Set e-mail message
		 *
		 * INPUT:  string message[, string content type]
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function message($message, $content_type = null) {
			$message = str_replace("\r\n", "\n", $message);

			/* Determine message mimetype
			 */
			if ($content_type === null) {
				if ((substr($message, 0, 6) == "<html>") && (substr(rtrim($message), -7) == "</html>")) {
					$content_type = "text/html";
				} else {
					$content_type = "text/plain";
				}
			}

			array_push($this->messages, array(
				"message"      => $message,
				"content_type" => $content_type));
		}

		/* Add e-mail attachment
		 *
		 * INPUT:  string filename[, string content][, string content type]
		 * OUTPUT: true
		 * ERROR:  false
		 */
		public function add_attachment($filename, $content = null, $content_type = null) {
			if ($content == null) {
				/* Load content from file
				 */
				if (file_exists($filename) == false) {
					return false;
				}
				if (($content = file_get_contents($filename, FILE_BINARY)) == false) {	
					return false;
				}
				$content_type = mime_content_type($filename);
				$filename = basename($filename);
			}

			if ($content_type == null) {
				/* Determine content mimetype
				 */
				#$finfo = new finfo(FILEINFO_MIME);
				#$content_type = $finfo->buffer($content);
				$content_type = "application/octet-stream";
			}

			/* Add attachment
			 */
			array_push($this->attachments, array(
				"filename"     => $filename,
				"content"      => $content,
				"content_type" => $content_type));

			return true;
		}

		/* Send e-mail
		 *
		 * INPUT:  [string e-mail address recipient][, string name recipient]
		 * OUTPUT: true
		 * ERROR:  false
		 */
		public function send($to_address = null, $to_name = null) {
			if ($to_address !== null) {
				if ($this->to($to_address, $to_name) == false) {
					return false;
				}
			}

			if (count($this->to) == 0) {
				return false;
			}

			if (count($this->messages) == 0) {
				$this->message("");
			}

			$message_count = count($this->messages);
			$attachment_count = count($this->attachments);
			$email_boundary = substr(md5(time()), 0, 20);

			/* E-mail content
			 */
			if ($attachment_count == 0) {
				/* No attachments
				 */
				if ($message_count == 1) {
					/* One message
					 */
					$headers = array("Content-Type: ".$this->messages[0]["content_type"]);
					$message = $this->messages[0]["message"];
				} else {
					/* Multiple messages
					 */
					$headers = array("Content-Type: multipart/alternative; boundary=".$email_boundary);
					$message = "This is a multi-part message in MIME format.\n";
					foreach ($this->messages as $mesg) {
						$message .=
							"--".$email_boundary."\n".
							"Content-Type: ".$mesg["content_type"]."\n".
							"Content-Transfer-Encoding: 7bit\n\n".
							$mesg["message"]."\n\n";
					}
				}
			} else {
				/* With attachments
				 */
				$headers = array("Content-Type: multipart/mixed; boundary=".$email_boundary);
				$message = "This is a multi-part message in MIME format.\n";

				if ($message_count == 1) {
					/* One message
					 */
					$message .=
						"--".$email_boundary."\n".
						"Content-Type: ".$this->messages[0]["content_type"]."\n".
						"Content-Transfer-Encoding: 7bit\n\n".
						$this->messages[0]["message"]."\n\n";
				} else {
					/* Multiple messages
					 */
					$message_boundary = substr(md5($email_boundary), 0, 20);
					$message .= "--".$email_boundary."\n".
						"Content-Type: multipart/alternative; boundary=".$message_boundary."\n\n";
					foreach ($this->messages as $mesg) {
						$message .=
							"--".$message_boundary."\n".
							"Content-Type: ".$mesg["content_type"]."\n".
							"Content-Transfer-Encoding: 7bit\n\n".
							$mesg["message"]."\n\n";
					}
					$message .= "--".$message_boundary."--\n\n";
				}

				/* Add attachments
				 */
				foreach ($this->attachments as $attachment) {
					$content = base64_encode($attachment["content"]);
					$content = wordwrap($content, 70, "\n", true);
					$message .=
						"--".$email_boundary."\n".
						"Content-Disposition: attachment;\n".
						"\tfilename=\"".$attachment["filename"]."\"\n".
						"Content-Type: ".$attachment["content_type"].";\n".
						"\tname=\"".$attachment["filename"]."\"\n".
						"Content-Transfer-Encoding: base64\n\n".
						$content."\n\n";
				}
			}

			if (($message_count > 1) || ($attachment_count > 0)) {
				$message .= "--".$email_boundary."--\n";
			}

			array_push($headers, "MIME-Version: 1.0");
			array_push($headers, "User-Agent: Banshee PHP framework e-mail library (http://www.banshee-php.org/)");

			/* Sender
			 */
			if ($this->from != null) {
				array_push($headers, "From: ".$this->from);
			}
			if ($this->reply_to != null) {
				array_push($headers, "Reply-To: ".$this->reply_to);
			}
			$sender = ($this->sender_address !== null) ? "-f".$this->sender_address : null;

			/* Carbon Copies
			 */
			if (count($this->cc) > 0) {	
				array_push($headers, "CC: ".implode(", ", $this->cc));
			}

			/* Blind Carbon Copies
			 */
			if (count($this->bcc) > 0) {	
				array_push($headers, "BCC: ".implode(", ", $this->bcc));
			}

			/* Secure mail headers
			 */
			foreach ($headers as &$header) {
				$header = str_replace("\n", "", $header);
				$header = str_replace("\r", "", $header);
				unset($header);
			}

			/* Send the e-mail
			 */
			if (mail(implode(", ", $this->to), $this->subject, $message, implode("\n", $headers), $sender) == false) {
				return false;
			}

			$this->to = array();
			$this->cc = array();
			$this->bcc = array();

			return true;
		}
	}
?>
