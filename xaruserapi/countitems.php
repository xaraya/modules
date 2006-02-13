<?php
/**
 * Utility function to count the number of items held by this module
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
 * Utility function to count the number of documents held by this module
 * 
 * @author jojodee
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function legis_userapi_countitems()
{ 
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $LegisCompiledTable = $xartable['legis_compiled'];
    $query = "SELECT COUNT(1)
            FROM $LegisCompiledTable";
    $result = &$dbconn->Execute($query,array());
    if (!$result) return;
    /* Obtain the number of items */
    list($numitems) = $result->fields;
    $result->Close();
    /* Return the number of items */
    return $numitems;
}
?>
