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
 * Count the number of HTML tags in the database
 *
 * @public
 * @author John Cox
 * @author Richard Cave
 * @param none
 * @return int number of HTML tags in the database
 * @throws none
 */
function html_userapi_countitems()
{
    // Security Check
    if(!xarSecurity::check('ReadHTML')) return;
    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $htmltable = $xartable['html'];
    // Count number of items in table
    $query = "SELECT COUNT(1)
              FROM $htmltable";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    // Get number of items
    list($numitems) = $result->fields;
    // Close result set
    $result->Close();
    // Return number of items
    return $numitems;
}
?>