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
 * Set the publication date of an issue and its stories
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['issue'] the issue array
 * @param $args['date'] the date that the issue/story was published
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_setdatepublished($args)
{
    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($issue) || !isset($date)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'Newsletter ID', 'adminapi', 'setdatepublished', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrIssuesTable = $xartable['nwsltrIssues'];
    $nwsltrStoriesTable = $xartable['nwsltrStories'];

    // Update the issue
    $query = "UPDATE $nwsltrIssuesTable 
                 SET xar_datepublished = ?
               WHERE xar_id = ?";

    $bindvars = array((int) $date, (int) $issue['id']);

    // Execute query
    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return;

    // Get topics for issue - these are the stories
    // associated with issue
    $topics = xarModAPIFunc('newsletter', 'user', 'get',
                             array('id' => $issue['id'],
                                   'phase' => 'topic'));

    // Check for exceptions
    if (!isset($topics) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    foreach ($topics as $topic) {
        // Update story
        $query = "UPDATE $nwsltrStoriesTable 
                     SET xar_datepublished = ?
                   WHERE xar_id = ?";
        
        $bindvars = array((int) $date, (int) $topic['storyId']);

        // Execute query
        $result =& $dbconn->Execute($query, $bindvars);

        // Check for an error
        if (!$result) return;
    }

    return true;
}

?>
