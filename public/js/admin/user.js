function hash_passwords() {
	document.getElementById("password_hashed").value = "yes";

	password = document.getElementById("password");
	if (password.value != "") {
		password.value = MD5(password.value);
	}
}
