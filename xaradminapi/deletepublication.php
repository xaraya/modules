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
 * delete a publication
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] ID of the publication
 * @param $args['issues'] remove or reassign the issues/stories of the publication
 * @param $args['newpid'] if reassign, the id of the new publication
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_deletepublication($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'publication id';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'deletepublication', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!isset($issues) || !is_string($issues)) {
        $issues = 'remove';
    }

    if (!isset($newpid) || !is_numeric($newpid)) {
        $newpid = 0;
    }

    // Get item
    $item = xarModAPIFunc('newsletter',
                          'user',
                          'getpublication',
                          array('id' => $id));

    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $publicationsTable = $xartable['nwsltrPublications'];
    $issuesTable = $xartable['nwsltrIssues'];
    $storiesTable = $xartable['nwsltrStories'];
    $subsTable = $xartable['nwsltrSubscriptions'];
    $altsubsTable = $xartable['nwsltrAltSubscriptions'];


    // Delete the publication
    $query = "DELETE 
              FROM $publicationsTable
              WHERE xar_id = ?";

    $result =& $dbconn->Execute($query, array((int) $id));

    // Check for an error
    if (!$result) return;

    // Do we reassign or remove the issues/stories of the publication
    switch($issues) {
        case 'reassign':
            // Set all issues to publication id of new id
            $query = "UPDATE $issuesTable
                      SET xar_pid = ? 
                      WHERE xar_pid = ?";
            $result =& $dbconn->Execute($query, array((int) $newpid, (int) $id));

            // Check for an error
            if (!$result) return;
            
            // Set all stories to publication id of new id
            $query = "UPDATE $storiesTable
                      SET xar_pid = ?
                      WHERE xar_pid = ?";
            $result =& $dbconn->Execute($query, array((int) $newpid, (int) $id));

            // Check for an error
            if (!$result) return;
        
            // Delete all subscriptions for publication
            $query = "UPDATE $subsTable
                      SET xar_pid = ?
                      WHERE xar_pid = ?";
            $result =& $dbconn->Execute($query, array((int) $newpid, (int) $id));

            // Delete all altsubscriptions for publication
            $query = "UPDATE $altsubsTable
                      SET xar_pid = ?
                      WHERE xar_pid = ?";
            $result =& $dbconn->Execute($query, array((int) $newpid, (int) $id));

            // Check for an error
            if (!$result) return;

            break;

        case 'remove':
        default:
            // Delete all issues under publication
            $query = "DELETE FROM $issuesTable
                      WHERE xar_pid = ?";
            $result =& $dbconn->Execute($query, array((int) $id));

            // Check for an error
            if (!$result) return;
            
            // Delete all stories under publication
            $query = "DELETE FROM $storiesTable
                      WHERE xar_pid = ?";
            $result =& $dbconn->Execute($query, array((int) $id));

            // Check for an error
            if (!$result) return;

            // Delete all subscriptions for publication
            $query = "DELETE FROM $subsTable
                      WHERE xar_pid = ?";
            $result =& $dbconn->Execute($query, array((int) $id));

            // Check for an error
            if (!$result) return;

            // Delete all altsubscriptions for publication
            $query = "DELETE FROM $altsubsTable
                      WHERE xar_pid = ?";
            $result =& $dbconn->Execute($query, array((int) $id));

            // Check for an error
            if (!$result) return;

            break;
    }

    // Let any hooks know that we have deleted an item.  As this is a
    // delete hook we're not passing any extra info
    $item['module'] = 'newsletter';
    $item['itemid'] = $id;
    xarModCallHooks('item', 'delete', $id, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

?>
