<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * delete a subscription
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['uid'] uid address of the subscription 
 * @param $args['pid'] publication id of the subscription - optional
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_deletesubscription($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($uid) || !is_numeric($uid)) {
        $invalid[] = 'uid';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'deletesubscription', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrSubscriptions'];

    // Delete the subscription
    $query = "DELETE 
                FROM $nwsltrTable
               WHERE xar_uid = ?";
    $bindvars[] = (int) $uid;

    // Check if $pid also sent
    if (isset($pid)) {
        $query .= " AND xar_pid = ?"; 
        $bindvars[] = (int) $pid;
    }

    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return;

    // Let any hooks know that we have deleted an item.  As this is a
    // delete hook we're not passing any extra info
    $item['module'] = 'newsletter';
    $item['uid']  = $uid;
    xarModCallHooks('item', 'deletesubscription', $uid, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

?>
