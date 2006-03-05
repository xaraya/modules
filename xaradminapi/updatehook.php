<?php
/**
 * Owner - Tracks who creates xaraya based items.
 *
 * @package Xaraya Modules
 * @copyright (C) 2003-2005 by Envision Net, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.envisionnet.net/
 *
 * @subpackage Owner module
 * @link http://www.envisionnet.net/home/products/security/
 * @author Brian McGilligan <brian@envisionnet.net>
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