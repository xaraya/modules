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
 * Delete all links for a specific Item ID
 * @param $args['itemid'] the ID of the item
 * @param $args['modid'] ID of the module
 * @param $args['itemtype'] item type
 * @param $args['confirm'] from unlinkhooks GUI
 */
function crispbb_adminapi_unlinkhooks($args)
{
    // Get arguments from argument array
    extract($args);

    if (!empty($confirm)) {
        if (!xarSecurityCheck('AdminCrispBB')) return;
    }

    // Get datbase setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $hookstable = $xartable['crispbb_hooks'];

    // Delete the link
    $bindvars = array();
    $query = "DELETE FROM $hookstable";

    if (!empty($modid)) {
        if (!is_numeric($modid)) {
            $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
            $vars = array('module id', 'admin', 'unlinkhooks', 'crispbb');
            throw new BadParameterException($vars, $msg);
            return;
        }
        if (empty($itemtype) || !is_numeric($itemtype)) {
            $itemtype = 0;
        }
        $query .= " WHERE xar_moduleid = ? AND xar_itemtype = ?";
        $bindvars[] = $modid; $bindvars[] = $itemtype;
        if (!empty($itemid)) {
            $query .= " AND xar_itemid = ?";
            $bindvars[] =  $itemid;
        }
    }

    $result = $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    return true;
}

?>
