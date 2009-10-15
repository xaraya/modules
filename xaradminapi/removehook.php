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
 * Remove hook function
 *
 * @author crisp <crisp@crispcreations.co.uk>
 */
function crispbb_adminapi_removehook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarMod::getRegID($modname);
    if (empty($modid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('module name', 'adminapi', 'removehook', 'crispBB');
        //throw new BadParameterException($vars, $msg);
        return $extrainfo;
    }

    $itemtype = 0;
    if (!empty($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    }

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $hookstable = $xartable['crispbb_hooks'];

    $query = "DELETE FROM $hookstable WHERE xar_moduleid = ?";
    $bindvars[] = $modid;

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    return $extrainfo;
}
?>