<?php
/*
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * count subscriptions by publication
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] id of the publication
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_adminapi_countsubscriptions($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();

    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'publication id';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'countsubscriptions', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Initialize counts
    $subcount = 0;
    $altsubcount = 0;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrSubTable = $xartable['nwsltrSubscriptions'];
    $nwsltrAltSubTable = $xartable['nwsltrAltSubscriptions'];

    // Initialize bindvars
    $bindvars = array();

    // Get subscriptions count
    $query = "SELECT COUNT(*)
              FROM $nwsltrSubTable";

    if ($id != 0) {
        $query .= " WHERE xar_pid = ?";
        $bindvars[] = (int) $id;
    }

    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return;

    // Put items into result array
    if (!$result->EOF) {
        $subcount = $result->fields[0];
    }

    // Close result set
    $result->Close();

    // Initialize bindvars
    $bindvars = array();

    // Get subscriptions count
    $query = "SELECT COUNT(*)
              FROM $nwsltrAltSubTable";

    if ($id != 0) {
        $query .= " WHERE xar_pid = ?";
        $bindvars[] = (int) $id;
    }

    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return;

    // Put items into result array
    if (!$result->EOF) {
        $altsubcount = $result->fields[0];
    }

    // Close result set
    $result->Close();

    // Return the subscriptions
    return ($subcount + $altsubcount);
}

?>
