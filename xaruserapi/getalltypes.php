<?php
/**
 * HTML Module
 *
 * @package modules
 * @subpackage html module
 * @category Third Party Xaraya Module
 * @version 1.5.0
 * @copyright see the html/credits.html file in this release
 * @link http://www.xaraya.com/index.php/release/779.html
 * @author John Cox
 */

/**
 * Get all tags
 *
 * @public
 * @author Richard Cave
 * @param none
 * @return array of HTML tags, or false on failure
 * @throws none
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
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();

    // Set table names
    $htmltypestable = $xartable['htmltypes'];

    // Get HTML tags
    $query = "SELECT $htmltypestable.id,
                     $htmltypestable.type
              FROM $htmltypestable
              ORDER BY $htmltypestable.type";

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
