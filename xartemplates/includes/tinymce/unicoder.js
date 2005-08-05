/**
 * This piece of code converts language packs to a UTF-8 firendly format.
 */

document.write("<pre>");

function enHex(aDigit) {
    return("0123456789ABCDEF".substring(aDigit, aDigit+1))
}

function toHex(n) {
   return (enHex((0x00f000 & n) >> 12) +
           enHex((0x000f00 & n) >>  8) +
           enHex((0x0000f0 & n) >>  4) +
           enHex((0x00000f & n) >>  0))
}

for (var langItemName in tinyMCELang) {
	var langItemValue = tinyMCELang[langItemName];
	var langItemValueUTF8 = "";

	for (var i=0; i<langItemValue.length; i++) {
		var chr = langItemValue.charAt(i);
		var chrCode = langItemValue.charCodeAt(i);
		if (chrCode > 127) {
			if (langItemName.indexOf('_alert') == -1 && langItemName.indexOf('_confirm') == -1)
				langItemValueUTF8 += "&#" + chrCode + ";";
			else
				langItemValueUTF8 += "\\u" + toHex(chrCode) + "";
		} else
			langItemValueUTF8 += chr;
	}

	document.write("tinyMCELang['" + langItemName + "'] = '" + langItemValueUTF8 + "';\n");
}

document.write("</pre>");
