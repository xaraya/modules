<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http//www.gnu.org/licenses/gpl.html}
 * @link http//www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http//xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
/**
 * Obtain list of hooks (optionally for a particular module)
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB module
 */
/**
 * Obtain list of hooks (optionally for a particular module)
 *
 * @author Xaraya Development Team
 * @param $args['modName'] optional module we're looking for
 * @returns array
 * @return array of known hooks
 * @throws NONE
 */
function crispbb_userapi_gethooklist($args)
{
// Security Check
    //if(!xarSecurityCheck('AdminModules')) return;

    // Get arguments from argument array
    extract($args);

    // Argument check
    if (empty($modName)) {
        $modName = '';
    }

    $dbconn =& xarDB::getConn();
    $xartable      =& xarDB::getTables();

    // TODO: allow finer selection of hooks based on type etc., and
    //       filter out irrelevant ones (like module remove, search...)
    $bindvars = array();
    $query = "SELECT DISTINCT s_module_id, s_type, t_module_id,
                            object, action, t_area, t_type,
                            t_func
            FROM $xartable[hooks] ";

    if (!empty($modName)) {
        $query .= " WHERE s_module_id=''
                       OR s_module_id = ?
                 ORDER BY t_module_id,
                          s_module_id DESC";
        $bindvars[] = $modName;
    } else {
        $query .= " ORDER BY t_module_id";
    }
    $result =& $dbconn->Execute($query,$bindvars);
    if(!$result) return;

    // hooklist will hold the available hooks
    $hooklist = array();
    for (; !$result->EOF; $result->MoveNext()) {
        list($smodName, $itemType, $tmodName,$object,$action,$area,$tmodType,$tmodFunc) = $result->fields;

        // Avoid single-space module names e.g. for mssql
        if (!empty($smodName)) {
            $smodName = trim($smodName);
        }
        // Avoid single-space item types e.g. for mssql
        if (!empty($itemType)) {
            $itemType = trim($itemType);
        }

        // Let's check to make sure this isn't a stale hook
        // if it is, unregister it and continue onto the next iteration in the for loop
        if (is_null(xarModGetIdFromName($tmodName))) {
            xarModUnregisterHook($object, $action, $area, $tmodName, $tmodType, $tmodFunc);
            continue;
        }

        if (!isset($hooklist[$tmodName])) $hooklist[$tmodName] = array();
        if (!isset($hooklist[$tmodName]["$object:$action:$area"])) $hooklist[$tmodName]["$object:$action:$area"] = array();
        // if the smodName has a value the hook is active
        if (!empty($smodName)) {
            if (!isset($hooklist[$tmodName]["$object:$action:$area"][$smodName])) $hooklist[$tmodName]["$object:$action:$area"][$smodName] = array();
            if (empty($itemType)) $itemType = 0;
            $hooklist[$tmodName]["$object:$action:$area"][$smodName][$itemType] = 1;
        }
    }
    $result->Close();

    return $hooklist;
}

?>