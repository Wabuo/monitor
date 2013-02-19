function hash_password() {
	document.getElementById("use_cr_method").value = "yes";

	md5_password = MD5(document.getElementById("password").value);
	response = MD5(document.getElementById("challenge").value + md5_password);
	document.getElementById("password").value = response;
}

document.getElementById("username").focus();
