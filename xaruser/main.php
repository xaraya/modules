<?php

function sitecontact_user_main($args)
{
   // Security Check
    if(!xarSecurityCheck('ReadSiteContact')) return;

    xarResponseRedirect(xarModURL('sitecontact', 'user', 'display',array($args)));

    return true;
}

?>
