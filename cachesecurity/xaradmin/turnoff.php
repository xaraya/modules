<?php

/*
 * Turn Security Caching on
 * @author Flavio Botelho <nuncanada@xaraya.com>
 */

function cachesecurity_admin_turnoff()
{
    $filename = xarModAPIFunc('cachesecurity','admin','filename', array('part'=>'on'));

    if (!xarModAPIFunc('cachesecurity','admin','turnoff')) return;
  
     xarResponseRedirect(xarModURL('cachesecurity', 'admin', 'view'));
     return true;
}

?>