function show_date(nr, value, count) {
	document.getElementById("date_" + nr).innerHTML = value;
	document.getElementById("count_" + nr).innerHTML = count;
}

function clear_date(nr) {
	document.getElementById("date_" + nr).innerHTML = "";
	document.getElementById("count_" + nr).innerHTML = "";
}
