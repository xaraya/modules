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
    
    // Get the variables
    xarVarFetch('enablesearch','str::',$enablesearch,"");
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

    xarModCallHooks('module','updateconfig','bkview',array());
    xarResponseRedirect(xarModUrl('bkview','admin','modifyconfig'));
    return true; 
}
?>
