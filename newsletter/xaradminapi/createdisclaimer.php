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
 * Create a disclaimer
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['title'] title of the disclaimer
 * @param $args['disclaimer'] text of the disclaimer
 * @returns int
 * @return disclaimer ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_createdisclaimer($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($title) || !is_string($title)) {
        $invalid[] = 'title';
    }
    if (!isset($disclaimer) || !is_string($disclaimer)) {
        $invalid[] = 'disclaimer';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'createdisclaimer', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return false;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrDisclaimers'];

    // Check if that disclaimer already exists
    $query = "SELECT xar_id FROM $nwsltrTable
              WHERE xar_title = ?";

    $result =& $dbconn->Execute($query, array((string) $title));
    if (!$result) return false;

    if ($result->RecordCount() > 0) {
        return false;  // disclaimer already exists
    }

    // Get next ID in table
    $nextId = $dbconn->GenId($nwsltrTable);

    // Add item
    $query = "INSERT INTO $nwsltrTable (
              xar_id,
              xar_title,
              xar_text)
            VALUES (?, ?, ?)";

    $bindvars = array((int) $nextId, (string) $title, (string) $disclaimer);

    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return false;

    // Get the ID of the item that was inserted
    $disclaimerId = $dbconn->PO_Insert_ID($nwsltrTable, 'xar_id');

    // Let any hooks know that we have created a new item
    xarModCallHooks('item', 'create', $disclaimerId, 'disclaimerId');

    // Return the id of the newly created item to the calling process
    return $disclaimerId;
}

?>
