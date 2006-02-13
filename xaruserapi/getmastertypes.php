<?php
/**
 * Legislation Master Doc Types
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * get the name and description of all Master types
 * @returns array
 */
function legis_userapi_getmastertypes($args)
{
    static $mastertypes = array();

    if (count($mastertypes) > 0) {
        return $mastertypes;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    $LegisMasterTable = $xarTables['legis_master'];

    // Get item
    $query = "SELECT xar_mdid,
                   xar_mdname,
                   xar_mdorder,
                   xar_mddef
            FROM $LegisMasterTable ";
    $result = $dbconn->Execute($query);
    if (!$result) return;

    if ($result->EOF) {
        return $mastertypes;
    }
    while (!$result->EOF) {
        list($mdid, $mdname, $mdorder, $mddef) = $result->fields;
        $mastertypes[$mdid] = array('mdid' => (int)$mdid,
                                  'mdname' => $mdname,
                                  'mdorder' => $mdorder,
                                  'mddef'   => $mddef);
        $result->MoveNext();
    }

    return $mastertypes;
}

?>
