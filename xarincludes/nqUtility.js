var nqWin = null;
function NQpopup(strURL, strType, strHeight, strWidth)
{
  if (nqWin != null && !nqWin.closed) nqWin.close();
  var strOptions = "";
  if (strType == "fixed") strOptions = "status,height=" + strHeight + ",width=" + strWidth;
  if (strType == "console") strOptions = "scrollbars,resizable,height=" + strHeight + ",width=" + strWidth;
  if (strType == "elastic") strOptions = "toolbar,menubar,scrollbars,resizable,location,height=" + strHeight+",width="+strWidth;
  nqWin = window.open(strURL, 'nqWin', strOptions);
  nqWin.focus();
}
function NQremote(strExec, strParam, strTarget)
{
  var remoteSite = strExec + '?' + strParam + '=' + strTarget;
  NQpopup(remoteSite, 'console', '400', '600');
}
function NQcheckall_1(chk)
{
  for (var i=0;i < document.forms[1].elements.length;i++)
  {
    var e = document.forms[1].elements[i];
    if (e.type == "checkbox")
    {
      e.checked = chk.checked
    }
  }
}
function NQcheckall_2(chk)
{
  for (var i=0;i < document.forms[2].elements.length;i++)
  {
    var e = document.forms[2].elements[i];
    if (e.type == "checkbox")
    {
      e.checked = chk.checked
    }
  }
}
function NQcheckall_3(chk)
{
  for (var i=0;i < document.forms[3].elements.length;i++)
  {
    var e = document.forms[3].elements[i];
    if (e.type == "checkbox")
    {
      e.checked = chk.checked
    }
  }
}
function NQcheckall_4(chk)
{
  for (var i=0;i < document.forms[4].elements.length;i++)
  {
    var e = document.forms[4].elements[i];
    if (e.type == "checkbox")
    {
      e.checked = chk.checked
    }
  }
}
function NQcheckall(formname,checkname,thestate)
{
  var el_collection=eval("document.forms."+formname+"."+checkname)
  for (c=0;c<el_collection.length;c++)
  El_collection[c].checked=thestate
}
