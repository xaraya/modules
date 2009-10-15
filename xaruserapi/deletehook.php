<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
/**
 * Delete hook function
 *
 * @author crisp <crisp@crispcreations.co.uk>
 */
function crispbb_userapi_deletehook($args)
{

    extract($args);

    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    if (empty($modname)) {
        if (empty($extrainfo['module'])) {
            $modname = xarModGetName();
        } else {
            $modname = $extrainfo['module'];
        }
    }

    $modid = xarMod::getRegID($modname);
    if (empty($modid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('module name', 'userapi', 'deletehook', 'crispBB');
        //throw new BadParameterException($vars, $msg);
        // don't throw an error here, life in hooks goes on...
        return $extrainfo;
    }

    if (empty($itemtype)) {
        $itemtype = 0;
        if (isset($extrainfo['itemtype'])) {
            $itemtype = $extrainfo['itemtype'];
        }
    }


    if (isset($objectid) && is_numeric($objectid)) {
        $itemid = $objectid;
    } else {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('object ID', 'userapi', 'deletehook', 'crispBB');
        //throw new BadParameterException($vars, $msg);
     // life goes on in hook modules, so just return false
       return $extrainfo;
    }

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $hookstable = $xartable['crispbb_hooks'];

    $query = "DELETE FROM $hookstable WHERE moduleid = ?";
    $bindvars[] = $modid;
    if (!empty($itemtype)) {
        $query .= ' AND itemtype = ?';
        $bindvars[] = $itemtype;
    }
    $query .= ' AND itemid = ?';
    $bindvars[] = $itemid;

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    return $extrainfo;
}
?>