<?php

/**
 * File: $Id$
 *
 * Updating bkview configuration
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function bkview_admin_updateconfig($args) 
{
    // Security check
    if (!xarSecurityCheck('AdminAllRepositories')) return;
    
    // Get the variables
    if(!xarVarFetch('enablesearch','str::',$enablesearch,"")) return;
    if(!xarVarFetch('enablexmlhttp','str::',$enablexmlhttp,"")) return;
    extract($args);

    // Process
    if(xarModIsAvailable('search')) {
        $action = 'disablehooks';
        if($enablesearch == 'on') {
            $action = 'enablehooks';
        }

        // Process
        xarModAPIFunc('modules','admin',$action,
                      array('callerModName' => 'bkview', 'hookModName' => 'search'));
        xarModAPIFunc('modules','admin',$action,
                      array('callerModName' => 'search', 'hookModName' => 'bkview'));

    }
    
    xarModSetVar('bkview','xmlhttp_enabled',strtolower($enablexmlhttp));

    xarModCallHooks('module','updateconfig','bkview',array());
    xarResponseRedirect(xarModUrl('bkview','admin','modifyconfig'));
    return true; 
}
?>
