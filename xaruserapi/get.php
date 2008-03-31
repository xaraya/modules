<?php
/**
 * Hitcount
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Hitcount Module
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */

/**
 * get a hitcount for a specific item
 * @param $args['modname'] name of the module this hitcount is for
 * @param $args['itemtype'] item type of the item this hitcount is for
 * @param $args['objectid'] ID of the item this hitcount is for
 * @return int The corresponding hit count, or void if no hit exists
 */
function hitcount_userapi_get($args)
{
    // Get arguments from argument array
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'user', 'get', 'Hitcount');
        throw new Exception($msg);
    }

    // When called via hooks, modname will be empty, but we get it from the
    // extrainfo or from the current module
    if (empty($modname)) {
        if (isset($extrainfo) && is_array($extrainfo) &&
            isset($extrainfo['module']) && is_string($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        } else {
            $modname = xarModGetName();
        }
    }
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'user', 'get', 'Hitcount');
        throw new Exception($msg);
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
         if (isset($extrainfo) && is_array($extrainfo) &&
             isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
             $itemtype = $extrainfo['itemtype'];
         } else {
             $itemtype = 0;
         }
    }

// TODO: re-evaluate this for hook calls !!
    // Security check
    if(!xarSecurityCheck('ViewHitcountItems',1,'Item',"$modname:$itemtype:$objectid")) return;

    // Database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $hitcounttable = $xartable['hitcount'];

    // Get items
    $query = "SELECT hits, lasthit 
            FROM $hitcounttable
            WHERE module_id = ?
              AND itemtype = ?
              AND itemid = ?";
    $bindvars = array((int)$modid, (int)$itemtype, (int)$objectid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $hits = $result->fields[0];
    $result->close();

    return $hits;
}

?>
