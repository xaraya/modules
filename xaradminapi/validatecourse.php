<?php
/**
 * Validate a course: retrieve an array based on the name and number supplied
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Check for already existing name and/or coursecode
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param name coursename
 * @param number coursecode
 * @return array
 */
function courses_adminapi_validatecourse($args)
{
    extract($args);
    if (!xarVarFetch('name', 'str:1:', $name)) return;
    if (!xarVarFetch('number', 'str:1:', $number)) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $coursestable = $xartable['courses'];
    // Get item
    $query = "SELECT xar_name, xar_number
            FROM $coursestable
            WHERE xar_name = ? OR xar_number = ?";
    $result = &$dbconn->Execute($query, array($name, $number));
    // Obtain the item information from the result set

    if (!$result) return;
    // List results
    list($foundname, $foundnumber) = $result->fields;
    $result->Close();

    // Create the item array
    $item = array('foundname' => $foundname,
                  'foundnumber' => $foundnumber);
    // Return the item array
    return $item;
}

?>
