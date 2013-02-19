/* Banshee AJAX library
 *
 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
 * This file is part of the Banshee PHP framework
 * http://www.hiawatha-webserver.org/banshee
 */

function ajax() {
	var xmlhttp = null;
	var result_handler = null;
	var obj = this;
	var no_support = "Your browser does not support XMLHTTP.";

	this.state_change = state_change;
	this.get = get;
	this.post = post;
	this.hasValue = hasValue;
	this.getValue = getValue;
	this.getRecords = getRecords;

	function url_encode(plaintext) {
		var hex = "0123456789ABCDEF";

		var encoded = "";
		for (var i = 0; i < plaintext.length; i++ ) {
			var c = plaintext.charCodeAt(i);

			if (c == 32) {
				encoded += "+";
			} else if (((c >= 48) && (c <= 57)) || ((c >= 65) && (c <= 90)) || ((c >= 97) && (c <= 122))) {
				encoded += plaintext.charAt(i);
			} else {
				encoded += "%";
				encoded += hex.charAt((c >> 4) & 15);
				encoded += hex.charAt(c & 15);
			}
		}

		return encoded;
	}

	function state_change() {
		if (xmlhttp.readyState == 4) {
			if (xmlhttp.status == 200) {
				if (result_handler != null) {
					result_handler(obj);
				}
			} else {
				alert("Problem retrieving XML data:" + xmlhttp.statusText)
			}
		}
	}

	function get(page, data, handler) {
		if (xmlhttp != null) {
			if (data == null) {
				data = "";
			} else if (data != "") {
				data = "&" + data;
			}

			result_handler = handler;
			xmlhttp.open("GET", "/ajax.php?page=" + page + data, true);
			xmlhttp.onreadystatechange = (result_handler != null) ? state_change : null;
			xmlhttp.send(null);
		} else {
			alert(no_support);
		}
	}

	function post(page, form_id, handler) {
		if (xmlhttp != null) {
			var form_obj = document.getElementById(form_id);
			var post_data = new Array();
			var p = 0;
			
			for (i = 0; i < form_obj.elements.length; i++) {
				if (form_obj.elements[i].name != "") {
					name = url_encode(form_obj.elements[i].name);
					value = url_encode(form_obj.elements[i].value);

					switch (form_obj.elements[i].type) {
						case "submit":
							if (form_obj.elements[i].clicked) {
								form_obj.elements[i].clicked = false;
								post_data[p++] = name + "=" + value;
							}
							break;
						case "checkbox":
							post_data[p++] = name + "=" + (form_obj.elements[i].checked ? "on" : "");
							break;
						case "radio":
							if (form_obj.elements[i].checked) {
								post_data[p++] = name + "=" + value;
							}
							break;
						default:
							post_data[p++] = name + "=" + value;
					}
				}
			}

			result_handler = handler;
			xmlhttp.open("POST", "/ajax.php?page=" + page, true);
			xmlhttp.onreadystatechange = (result_handler != null) ? state_change : null;
			xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xmlhttp.send(post_data.join("&"));
		} else {
			alert(no_support);
		}
	}

	function hasValue(key) {
		return typeof(xmlhttp.responseXML.getElementsByTagName(key)[0]) != "undefined";
	}

	function getValue(key) {
		if (xmlhttp != null) {
			return xmlhttp.responseXML.getElementsByTagName(key)[0].firstChild.nodeValue;
		}

		return null;
	}

	function getRecords(tag) {
		if (xmlhttp != null) {
			var tag_obj = xmlhttp.responseXML.getElementsByTagName(tag);
			var result = new Array();

			for (record = 0; record < tag_obj.length; record++) {
				result[record] = new Array();
				index = 0;
				for (entry = 0; entry < tag_obj[record].childNodes.length; entry++) {
					key = tag_obj[record].childNodes[entry].nodeName;
					if (key[0] != "#") {
						if (tag_obj[record].childNodes[entry].hasChildNodes()) {
							result[record][index] = tag_obj[record].childNodes[entry].firstChild.nodeValue;
						} else {
							result[record][index] = "";
						}
						index++;
					}
				}
			}

			return result;
		} else {
			return null;
		}
	}

	function mouse_click(e) {
		if (!e) {
			if (window.event.srcElement.type == "submit") {
				window.event.srcElement.clicked = true;
			}
		} else if (e.target.type == "submit") {
			e.target.clicked = true;
		}
	}

	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		xmlhttp = null;
	}

	if (xmlhttp == null) {
		alert(no_support);
	}

	document.onmousedown = mouse_click;
}

function ajax_clear(name) {
	document.getElementById(name).innerHTML = "";
}

function ajax_print(name, data) {
	document.getElementById(name).innerHTML += data;
}

function ajax_getvalue(name) {
	return document.getElementById(name).value;
}

function ajax_setvalue(name, data) {
	document.getElementById(name).value = data;
}

function ajax_focus(name) {
	document.getElementById(name).focus();
}

function ajax_hide(name) {
	document.getElementById(name).style.display = "none";
}

function ajax_show(name) {
	document.getElementById(name).style.display = "inline";
}

function ajax_formvalue(formname, name, data) {
	var obj = document.getElementById(formname);
	for (var j=0; j<obj.elements.length; j++) {
		if (obj.elements[j].name == name) {
			obj.elements[j].value = data;
		}
	}
}

function ajax_formget(formname, name) {
	var obj = document.getElementById(formname);
	for (var j=0; j<obj.elements.length; j++) {
		if (obj.elements[j].name == name) {
			return obj.elements[j].value;
		}
	}

	return null;
}
