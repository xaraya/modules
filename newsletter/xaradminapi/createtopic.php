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
 * create a topic - list of stories within a publication
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['issueId'] issue id
 * @param $args['storyId'] story id 
 * @param $args['cid'] category id of the story
 * @param $args['storyOrder'] order of the story in the issue
 * @returns int
 * @return topic ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_createtopic($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();

    if (!isset($issueId)) {
        $invalid[] = 'Issue ID';
    }
    if (!isset($storyId)) {
        $invalid[] = 'Story ID';
    }
    if (!isset($cid)) {
        $invalid[] = 'Category ID';
    }
    if (!isset($storyOrder)) {
        $storyOrder=0;
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'createtopic', 'Newsletter');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return false;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrTopics'];
    
    // Check if that topic already exists
    $query = "SELECT xar_issueid FROM $nwsltrTable
              WHERE  xar_issueid = '".xarVarPrepForStore($issueId)."'
              AND    xar_storyid = '".xarVarPrepForStore($storyId)."'";

    $result =& $dbconn->Execute($query);
    if (!$result) return false;

    if ($result->RecordCount() > 0) {
        return false;  // topic already exists
    }

    // Add topic
    $query = "INSERT INTO $nwsltrTable (
                xar_issueid,
                xar_storyid,
                xar_cid,
                xar_order)
              VALUES (
                " . xarVarPrepForStore($issueId) .",
                " . xarVarPrepForStore($storyId) .",
                " . xarVarPrepForStore($cid) .",
                " . xarVarPrepForStore($storyOrder) . ")";

    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) return false;

    // Let any hooks know that we have created a new item
    //xarModCallHooks('item', 'create', $disclaimerId, 'disclaimerId');

    // Return true
    return true;
}

?>
