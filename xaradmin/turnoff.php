<?php

/*
 * Turn Security Caching on
 * @author Flavio Botelho <nuncanada@xaraya.com>
 */

function cachesecurity_admin_turnoff()
{
    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('cachesecurity','admin','turnoff')) return;
  
     xarResponseRedirect(xarModURL('cachesecurity', 'admin', 'view'));
     return true;
}

?>