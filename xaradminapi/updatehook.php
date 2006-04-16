<?php
/**
 * Owner - Tracks who creates xaraya based items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Owner Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
    Logs who created a xaraya item

    @param $args array standard xaraya hook params

    @return array standard xaraya hook extrainfo
*/
function owner_adminapi_updatehook($args)
{
    extract($args);

    // setup vars
    $modid = '';
    if( !empty($extrainfo['module']) )
    {
        $modid = xarModGetIdFromName($extrainfo['module']);
    }

    $itemtype = '';
    if( !empty($extrainfo['itemtype']) )
    {
        $itemtype = $extrainfo['itemtype'];
    }

    $itemid = '';
    if( !empty($objectid) )
    {
        $itemid = $objectid;
    }

    // Check to see if we have an entry already
    $ownerExists = xarModAPIFunc('owner', 'user', 'ownerexists',
        array(
            'modid'    => $modid,
            'itemtype' => $itemtype,
            'itemid'   => $itemid
        )
    );

    // If this has not been owned before set ownership to current user
    if( !$ownerExists )
    {
        $ownerArgs = array(
            'modid'    => $modid,
            'itemtype' => $itemtype,
            'itemid'   => $itemid
        );
        $result = xarModAPIFunc('owner', 'admin', 'create', $ownerArgs);
        if( !$result ) return false;
    }

    return $extrainfo;
}

?>