function highlight(id) 
{

var id = id;
var obj = document.getElementById('linkid' + id);

if(obj.getAttribute('className') != null) { // IE bullshit
	obj.setAttribute('className','highlight');
} else { // real browsers
	obj.setAttribute('class','highlight');
}
	return true;

}

function unhighlight(id) 
{

var id = id;
var obj = document.getElementById('linkid' + id);

if(obj.getAttribute('className') != null) { // IE bullshit
	obj.setAttribute('className','linkid');
} else { // real browsers
	obj.setAttribute('class','linkid');
}
	return true;

}