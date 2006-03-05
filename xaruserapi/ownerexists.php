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
    Check if a record exists for a xaraya module item

    @param $args['modid'] (required)
    @param $args['itemtype'] (optional)
    @param $args['itemid'] (required)

    @return boolean true if exists otherwise false
*/
function owner_userapi_ownerexists($args)
{
    $result = xarModAPIFunc('owner', 'user', 'get', $args);

    if( count($result) > 0 )
        return true;

    return false;
}
?>