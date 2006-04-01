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
