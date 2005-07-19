<?php
function xartelnet_admin_updateconfig()
{
    if (!xarSecConfirmAuthKey()) return;
    if(xarVarFetch('host', 'str:1:', $host, '',XARVAR_NOT_REQUIRED)) xarModSetVar('xartelnet','host', $host);
    if(xarVarFetch('port', 'str:1:', $port, '',XARVAR_NOT_REQUIRED)) xarModSetVar('xartelnet','port', $port);
    if(xarVarFetch('debug', 'str:1:', $debug, '',XARVAR_NOT_REQUIRED)) xarModSetVar('xartelnet','debug', $debug);
    if(xarVarFetch('timeout', 'str:1:', $timeout, '',XARVAR_NOT_REQUIRED)) xarModSetVar('xartelnet','timeout', $timeout);
    if(xarVarFetch('add_html_to_newline', 'str:1:', $add_html_to_newline, '',XARVAR_NOT_REQUIRED)) xarModSetVar('xartelnet','add_html_to_newline', $add_html_to_newline);
    if(xarVarFetch('prompt', 'str:1:', $prompt, '',XARVAR_NOT_REQUIRED)) xarModSetVar('xartelnet','prompt', $prompt);
    xarResponseRedirect(xarModURL('xartelnet', 'admin', 'modifyconfig'));
    return true;
}

?>
