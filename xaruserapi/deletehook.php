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

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'module name', 'userapi', 'deletehook', 'crispBB');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'userapi', 'deletehook', 'crispBB');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
     // life goes on in hook modules, so just return false
       return $extrainfo;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $hookstable = $xartable['crispbb_hooks'];

    $query = "DELETE FROM $hookstable WHERE xar_moduleid = ?";
    $bindvars[] = $modid;
    if (!empty($itemtype)) {
        $query .= ' AND xar_itemtype = ?';
        $bindvars[] = $itemtype;
    }
    $query .= ' AND xar_itemid = ?';
    $bindvars[] = $itemid;

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    return $extrainfo;
}
?>