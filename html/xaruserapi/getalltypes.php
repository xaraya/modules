<?php
/**
 * File: $Id$
 *
 * Xaraya HTML Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage HTML Module
 * @author John Cox
*/

/**
 * Get all tags
 *
 * @public
 * @author Richard Cave 
 * @param none 
 * @returns array
 * @return array of HTML tags, or false on failure
 * @raise none
 */
function html_userapi_getalltypes($args)
{
    // Extract arguments
    extract($args);

    // Set empty array
    $types = array();

    // Security Check
	if (!xarSecurityCheck('ReadHTML')) {
        return $types;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Set table names
    $htmltypestable = $xartable['htmltypes'];

    // Get HTML tags
    $query = "SELECT $htmltypestable.xar_id,
                     $htmltypestable.xar_type
              FROM $htmltypestable
              ORDER BY $htmltypestable.xar_type";

    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) return;

    // Put types into an array
    for (; !$result->EOF; $result->MoveNext()) {
        list($id, $type) = $result->fields;

         $types[] = array('id'        => $id,
                          'type'      => $type);
    }

    // Close result set
    $result->Close();

    return $types;
}

?>
