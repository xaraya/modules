<?php
/**
 * @package ie7
 * @copyright (C) 2004 by Ninth Avenue Software Pty Ltd
 * @link http://www.ninthave.net
 * @author Roger Keays <roger.keays@ninthave.net>
 */

/**
 * Modify ie7 configuration.
 */
function ie7_admin_updateconfig($args)
{ 
    /* locals */
    extract($args);
    $data = array();

    /* input variables */
    if (!xarVarFetch('enabled', 'isset', $enabled, null, XARVAR_NOT_REQUIRED)) {
        return;
    }

    /* security check */
    if (!xarSecurityCheck('AdminIE7')) return; 

    /* set values */
    if (empty($enabled)) {
        xarModSetVar('ie7', 'enabled', false);
    } else {
        xarModSetVar('ie7', 'enabled', true);
    }

    /* show modifyconfig page */
    xarResponseRedirect(xarModURL('ie7', 'admin', 'modifyconfig'));
    return true;
} 
?>
