function init() {
	// Give FF some time
	window.setTimeout('insertHelpIFrame();', 10);
}

function insertHelpIFrame() {
	var html = '<iframe width="100%" height="300" src="' + tinyMCE.themeURL + "/docs/" + tinyMCE.settings['docs_language'] + "/index.htm" + '"></iframe>';

	document.getElementById('iframecontainer').innerHTML = html;

	html = '';
	html += '<a href="http://sourceforge.net/donate/index.php?group_id=103281" target="_blank"><img src="http://images.sourceforge.net/images/project-support.jpg" alt="Please donate" border="0" /></a> ';
	html += '<a href="http://www.moxiecode.com" target="_blank"><img src="http://tinymce.moxiecode.com/images/gotmoxie.png" alt="Got Moxie?" border="0" /></a> ';
	html += '<a href="http://sourceforge.net/projects/tinymce/" target="_blank"><img src="http://sourceforge.net/sflogo.php?group_id=103281" alt="Hosted By Sourceforge" border="0" /></a> ';
	html += '<a href="http://www.freshmeat.net/projects/tinymce" target="_blank"><img src="http://tinymce.moxiecode.com/images/fm.gif" alt="Also on freshmeat" border="0" /></a> ';

	document.getElementById('buttoncontainer').innerHTML = html;
}
