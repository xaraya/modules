// Remember the current position.
function storeCaret(text)
{
	// Only bother if it will be useful.
	if (typeof(text.createTextRange) != "undefined")
		text.caretPos = document.selection.createRange().duplicate();
}

function replaceText(text, textarea)
{
	// Attempt to create a text range (IE).
	if (typeof(textarea.caretPos) != "undefined" && textarea.createTextRange)
	{
		var caretPos = textarea.caretPos;

		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
		caretPos.select();
	}
	// Mozilla text range replace.
	else if (typeof(textarea.selectionStart) != "undefined")
	{
		var begin = textarea.value.substr(0, textarea.selectionStart);
		var end = textarea.value.substr(textarea.selectionEnd);
		var scrollPos = textarea.scrollTop;

		textarea.value = begin + text + end;

		if (textarea.setSelectionRange)
		{
			textarea.focus();
			textarea.setSelectionRange(begin.length + text.length, begin.length + text.length);
		}
		textarea.scrollTop = scrollPos;
	}
	// Just put it on the end.
	else
	{
		textarea.value += text;
		textarea.focus(textarea.value.length - 1);
	}
}