<?php
/**
 * Search System - Present searches via hooks
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Search Module
 * @link http://xaraya.com/index.php/release/32.html
 * @author Search Module Development Team
 */
/**
 * When the search module is hooked into a module, the item:search:gui
 * hook provides a generic search form. The search parameters are then
 * passed to the search module for further handling
 *
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
    $data['object_id'] = $objectid;
    return $data;
}

?>
