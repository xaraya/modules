<?php
/**
* Count the number of issues
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * utility function to count the number of issues on record
 *
 * @author the ebulletin module development team
 * @return int number of items held by this module
 * @throws DATABASE_ERROR
 */
function ebulletin_userapi_countissues($args)
{
    extract($args);

    // set defaults
    if (empty($pid))       $pid = '';
    if (empty($published)) $published = NULL;

    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $issuestable = $xartable['ebulletin_issues'];

    // get count
    $bindvars = array();
    $query = "SELECT COUNT(1) FROM $issuestable WHERE 1\n";

    if (!empty($pid)) {
        $query .= "AND xar_pid = ?\n";
        $bindvars[] = $pid;
    }
    if (!is_null($published)) {
        if ($published) {
            $query .= "AND xar_published = ?\n";
            $bindvars[] = 1;
        } else {
            $query .= "AND xar_published = ?\n";
            $bindvars[] = 0;
        }
    }

    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;
    list($numitems) = $result->fields;
    $result->Close();

    // success
    return $numitems;
}

?>
