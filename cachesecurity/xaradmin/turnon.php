<?php

/*
 * Turn Security Caching on
 * @author Flavio Botelho <nuncanada@xaraya.com>
 */

function cachesecurity_admin_turnon()
{
    if (!xarModAPIFunc('logconfig','admin','issynchronized')) {
        xarResponseRedirect(xarModURL('cachesecurity', 'admin', 'view', array(
            'error' => xarML('Not all parts of the security cache system are synchronized.'))));
        return true;
    }

    if (!xarModAPIFunc('logconfig','admin','turnon')) {
        xarResponseRedirect(xarModURL('cachesecurity', 'admin', 'view', array(
            'error' => xarML('Unable to create the file (#(1)) to turn on the security cache.'))));
    }
  
     return true;
}

?>