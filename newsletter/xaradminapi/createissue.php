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
 * create a issue
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['publicationId'] publication id of the issue
 * @param $args['title'] title of the issue
 * @param $args['ownerId'] owner if of the issue
 * @param $args['external'] flag if issue is internal/external (1 = true, 0 = false)
 * @param $args['editorNote'] editor note for the issue
 * @param $args['tstmpDatePublished'] issue date of the issue as UNIX timestamp
 * @returns int
 * @return issue ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_createissue($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($ownerId) || !is_numeric($ownerId)) {
        $invalid[] = 'ownerId';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'createissue', 'Newsletter');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return false;
    }

    // Set publicationId if not set
    if (!isset($publicationId))
        $publicationId = 0;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrIssues'];

    // Check if the issue already exists
    $query = "SELECT xar_id FROM $nwsltrTable
              WHERE xar_title = '".xarVarPrepForStore($title)."'";

    $result =& $dbconn->Execute($query);
    if (!$result) return false; 

    if ($result->RecordCount() > 0) {
        return false;  // owner already exists
    }

    // Get next ID in table
    $nextId = $dbconn->GenId($nwsltrTable);

    // Add item
    $query = "INSERT INTO $nwsltrTable (
              xar_id,
              xar_pid,
              xar_title,
              xar_ownerid,
              xar_external,
              xar_editornote,
              xar_datepublished)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($publicationId) ."',
              '" . xarVarPrepForStore($title) ."',
              " . xarVarPrepForStore($ownerId) .",
              '" . xarVarPrepForStore($external) . "',
              '" . xarVarPrepForStore($editorNote) . "',
              '" . xarVarPrepForStore($tstmpDatePublished) . "')";

    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) return false;

    // Get the ID of the item that was inserted
    $issueId = $dbconn->PO_Insert_ID($nwsltrTable, 'xar_id');

    // Let any hooks know that we have created a new item
    xarModCallHooks('item', 'create', $issueId, 'issueId');

    // Return the id of the newly created item to the calling process
    return $issueId;
}

?>
