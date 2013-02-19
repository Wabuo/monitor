function hash_passwords() {
	document.getElementById("password_hashed").value = "yes";

	document.getElementById("current").value  = MD5(document.getElementById("current").value);
	if (document.getElementById("password").value != "") {
		document.getElementById("password").value = MD5(document.getElementById("password").value);
		document.getElementById("repeat").value = MD5(document.getElementById("repeat").value);
	}
}
