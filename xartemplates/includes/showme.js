function showMe (it, box) {
	var vis = (box.checked) ? "block" : "none";
	document.getElementById(it).style.display = vis;
}