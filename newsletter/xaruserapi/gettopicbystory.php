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
 * Get an Newsletter topic by a story id
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['storyId'] id of story to get
 * @returns topic array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_userapi_gettopicbystory($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($storyId) || !is_numeric($storyId)) {
        $invalid[] = 'story id';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'userapi', 'gettopicbystory', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $topicsTable = $xartable['nwsltrTopics'];

    $query = "SELECT xar_issueid,
                     xar_storyid,
                     xar_cid,
                     xar_order
              FROM $topicsTable
              WHERE xar_storyid = ?";

    // Process query
    $result =& $dbconn->Execute($query, array($storyId));

    // Check for an error
    if (!$result) return;

    // Check for no rows found
    if ($result->EOF) {
        $result->Close();
        return;
    }

    // Obtain the topic information from the result set
    list($issueId, $storyId, $cid, $order) = $result->fields;

    // Close result set
    $result->Close();

    // Create the topic
    $topic = array('issueId' => $issueId,
                   'storyId' => $storyId,
                   'cid' => $cid,
                   'order' => $order);

    // Return the topic array
    return $topic;
}

?>
