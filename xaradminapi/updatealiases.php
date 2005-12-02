<?php
/**
 * File: $Id:
 * 
 * Update book aliases
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */
/**
 * update book aliases
 * 
 * @author the Example module development team 
 * @param  $args ['aliases'] array of aliases
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function bible_adminapi_updatealiases($args)
{ 
    extract($args); 

    $invalid = array();
    if (!isset($aliases) || !is_array($aliases)) {
        $invalid[] = 'aliases';
    } 
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'updatealiases', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    } 

    // security check - need to do both, since short name is in the security schema
    if (!xarSecurityCheck('EditBible', 1)) {
        return;
    } 

    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $aliastable = $xartable['bible_aliases'];

    // since we're updating the entire set at once, it's easier to empty the table
    // and re-insert the data

    $query = "TRUNCATE TABLE $aliastable";
    $dbconn->Execute($query);
    if ($dbconn->ErrorNo()) return; 

    $query = "INSERT INTO $aliastable
              (xar_aid, xar_sword, xar_display, xar_aliases)
              VALUES\n";
    $bindvars = $queries = array();
    foreach ($aliases as $row) {
        $queries[] = "(?, ?, ?, ?)";
        $bindvars[] = $row[0];
        $bindvars[] = $row[1];
        $bindvars[] = $row[2];
        $bindvars[] = preg_replace("/\s*,\s*/", ',', $row[3]);
    }
    $query .= join(', ', $queries);

    // insert the data
    $dbconn->Execute($query, $bindvars);
    if ($dbconn->ErrorNo()) return; 

    // Let the calling process know that we have finished successfully
    return true;
} 

?>
