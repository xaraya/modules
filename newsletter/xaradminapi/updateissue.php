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
 * update an issue
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] id of the issue
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
function newsletter_adminapi_updateissue($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();

    if (!isset($title) || !is_string($title)) {
        $invalid[] = 'title';
    }
    if (!isset($ownerId) || !is_numeric($ownerId)) {
        $invalid[] = 'owner ID';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'updateissue', 'Newsletter');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get item
    $item = xarModAPIFunc('newsletter',
                          'user',
                          'getissue',
                          array('id' => $id));

    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrIssues'];

    // Update the item
    $query = "UPDATE $nwsltrTable SET
              xar_pid = " .  xarVarPrepForStore($publicationId) . ",
              xar_ownerid = " .  xarVarPrepForStore($ownerId) . ",
              xar_title = '" .  xarVarPrepForStore($title) . "',
              xar_external = " .  xarVarPrepForStore($external) . ",
              xar_editornote = '" .  xarVarPrepForStore($editorNote) . "',
              xar_datepublished = " .  xarVarPrepForStore($tstmpDatePublished) . "
              WHERE xar_id = " . xarVarPrepForStore($id);

    // Execute query
    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) return;

    // Let any hooks know that we have updated an item.  As this is an
    // update hook we're passing the updated $item array as the extra info
    $item['module'] = 'newsletter';
    $item['itemid'] = $id;
    $item['id'] = $id;
    xarModCallHooks('item', 'update', $id, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

?>
