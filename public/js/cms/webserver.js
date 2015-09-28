function set_port_number() {
	if ((port = document.getElementById("port")) == undefined) {
		return;
	}

	if (document.getElementById("tls").checked) {
		if (port.value == "80") {
			port.value = "443";
		}
	} else {
		if (port.value == "443") {
			port.value = "80";
		}
	}
}
