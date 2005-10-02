<?php
function dailydelicious_admin_updateconfig()
{
    if (!xarSecConfirmAuthKey()) return;
    if(!xarSecurityCheck('DailyDelicious')) return;
    if (!xarVarFetch('username', 'str:1:', $username, 'UserName')) return;
    if (!xarVarFetch('password', 'str:1:', $password, 'password')) return;
    if (!xarVarFetch('title', 'str:1:', $title, 'This Week\'s Del.icio.us bookmarks')) return;
    if (!xarVarFetch('importpubtype', 'id', $importpubtype, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaultstatus', 'id', $defaultstatus, 1, XARVAR_NOT_REQUIRED)) return;
    xarModSetVar('dailydelicious', 'username', $username);
    xarModSetVar('dailydelicious', 'password', $password);
    xarModSetVar('dailydelicious', 'title', $title);
    xarModSetVar('dailydelicious', 'importpubtype', $importpubtype);
    xarModSetVar('dailydelicious', 'defaultstatus', $defaultstatus);
    xarModCallHooks('module','updateconfig','dailydelicious', array('module' => 'dailydelicious'));
    xarResponseRedirect(xarModURL('dailydelicious', 'admin', 'modifyconfig'));
    return true;
}
?>