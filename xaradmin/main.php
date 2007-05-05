<?php

function members_admin_main()
{
    if(!xarSecurityCheck('AdminMembers')) return;

    if (xarModVars::get('modules', 'disableoverview') == 0) {
        return array();
    } else {
       xarResponseRedirect(xarModURL('members','admin', 'view'));
    }
    return true;
}
?>