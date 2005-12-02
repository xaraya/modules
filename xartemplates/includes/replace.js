/* Call a module-based JavaScript from BlockLayout like this:
<xar:base-include-javascript module="bible" filename="replace.js" position="head" />
and in the form's onSubmit handler, do like this:
text = replace(text,unescape('%DC'),'U');
*/
function replace(string,text,by) {
// Replaces text with by in string
    var strLength = string.length, txtLength = text.length;
    if ((strLength == 0) || (txtLength == 0)) return string;

    var i = string.indexOf(text);
    if ((!i) && (text != string.substring(0,txtLength))) return string;
    if (i == -1) return string;

    var newstr = string.substring(0,i) + by;

    if (i+txtLength < strLength)
        newstr += replace(string.substring(i+txtLength,strLength),text,by);

    return newstr;
}