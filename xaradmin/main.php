<?php

function query_admin_main()
{
    if(!xarSecurityCheck('AdminQuery')) return;

    if (xarModVars::get('modules', 'disableoverview') == 0) {
        return array();
    } else {
        xarResponseRedirect(xarModURL('query', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}
?>