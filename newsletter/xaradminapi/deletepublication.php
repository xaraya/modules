<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://xavier.schwabfoundation.org
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
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
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
    $altsubsTable = $xartable['nwsltrAltSubscriptions'];

    // Delete the publication
    $query = "DELETE FROM $publicationsTable
              WHERE xar_id = " . xarVarPrepForStore($id);
    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) return;

    // Set all issues under publication to publication id of 0
    $query = "UPDATE FROM $issuesTable
              SET xar_pid = 0";
    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) return;
    
    // Set all stories under publication to publication id of 0
    $query = "UPDATE FROM $storiesTable
              SET xar_pid = 0";
    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) return;
    
    // Set all altsubscriptions for publication to publication id of 0
    $query = "UPDATE FROM $altsubsTable
              SET xar_pid = 0";
    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) return;
    // Let any hooks know that we have deleted an item.  As this is a
    // delete hook we're not passing any extra info
    $item['module'] = 'newsletter';
    $item['itemid'] = $id;
    xarModCallHooks('item', 'delete', $id, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

?>
