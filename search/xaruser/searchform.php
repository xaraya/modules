<?php

/**
 * File: $Id$
 *
 * item:search:gui hook handler function
 *
 * When the search module is hooked into a module, the item:search:gui 
 * hook provides a generic search form. The search parameters are then
 * passed to the search module for further handling
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage search
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function search_user_searchform($args) 
{
    extract($args);

    // The extrainfo now contains guaranteed the calling module
    $callingmodule = $extrainfo['module'];
    
    // Get the itemtypes for which the hook is enabled, but don't throw exception
    $itemtypes = xarModApiFunc($callingmodule,'user','getitemtypes',array(),false);

    $data = array(); $typestoinclude = array();
    if(!empty($itemtypes)) {
        foreach($itemtypes as $itemtype => $itemtype_info) {
            if(xarModIsHooked('search',$callingmodule,$itemtype)) {
                $typestoinclude[$itemtype] = $itemtype_info;
            }
        }
    } else {
        // Apparently the hooked module has no itemtypes
    }

    // The form data is handled by the search module
    $data['formaction'] = xarModUrl('search','user','handlesearch');
    
    // Pass information to the template
    $data['callingmodule'] = $callingmodule;
    $data['itemtypes'] = $typestoinclude;
    return $data;
}

?>
