<?php

/*
 * Turn Security Caching on
 * @author Flavio Botelho <nuncanada@xaraya.com>
 */

function cachesecurity_admin_turnon()
{
    if (!xarModAPIFunc('cachesecurity','admin','issynchronized')) {
        xarResponseRedirect(xarModURL('cachesecurity', 'admin', 'view', array(
            'error' => xarML('Not all parts of the security cache system are synchronized.'))));
        return true;
    }

    if (!xarModAPIFunc('cachesecurity','admin','turnon')) return;

     xarResponseRedirect(xarModURL('cachesecurity', 'admin', 'view'));
     return true;
}

?>