<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
    Check if a record exists for a xaraya module item

    @param $args['modid']
    @param $args['itemtype'] (optional)
    @param $args['itemid']

    @return boolean true if security exists otherwise false;
*/
function security_userapi_securityexists($args)
{
    $result = xarModAPIFunc('security', 'user', 'get', $args);

    if( count($result) > 0 )
        return true;

    return false;
}
?>