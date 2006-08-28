<?php
/**
 * Utility function to get DD item for status
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Gets items of a DynamicData object 'status'
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int status - Item id to get info for
 * @return array Item with all info obtained from dd
 */
function courses_userapi_getstatus($args)
{
    extract($args);

    $modid = xarModGetIDFromName('courses');

    $info = array();
    $info['modid'] = $modid;
    $info['itemtype'] = 1004;
    $info['itemid'] = $status;
    $info['name'] = 'studstatus';
    $item = xarModAPIFunc('dynamicdata', 'user', 'getfield', $info);

    return $item;
}
?>
