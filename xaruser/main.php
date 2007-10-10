<?php

function sitecontact_user_main()
{
   // Security Check
    if(!xarSecurityCheck('ReadSiteContact')) return;

    if (xarModVars::get('modules', 'disableoverview') == 0) {
        return array();
    } else {
        xarResponseRedirect(xarModURL('sitecontact', 'user', 'display'));
    }
    return true;
}

?>
