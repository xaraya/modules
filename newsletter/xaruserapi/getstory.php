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
 * Get an Newsletter story by id
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] id of newsletter story to get
 * @returns story array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_userapi_getstory($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'userapi', 'getstory', 'Newsletter');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrStories'];

    $query = "SELECT xar_pid, 
                     xar_cid,
                     xar_ownerid,
                     xar_title,
                     xar_source,
                     xar_content,
                     xar_priority,
                     xar_storydate,
                     xar_altdate,
                     xar_datepublished,
                     xar_fulltextlink,
                     xar_registerlink,
                     xar_linkexpiration,
                     xar_commentary,
                     xar_commentarysrc
              FROM $nwsltrTable
              WHERE xar_id = " . xarVarPrepForStore($id);

    // Process query
    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) return;

    // Check for no rows found
    if ($result->EOF) {
        $result->Close();
        return;
    }

    // Obtain the story information from the result set
    $storyDate = array();
    $datePublished = array();

    list($pid,
         $cid, 
         $ownerId, 
         $title, 
         $source,
         $content,
         $priority,
         $storyDate['timestamp'],
         $altDate,
         $datePublished['timestamp'],
         $fullTextLink,
         $registerLink,
         $linkExpiration,
         $commentary,
         $commentarySource) = $result->fields;

    // Close result set
    $result->Close();

    // The user API function is called.
    $userData = xarModAPIFunc('roles',
                              'user',
                              'get',
                              array('uid' => $ownerId));

    if ($userData == false) {
        // If this user does not exist in xar_roles table
        // then show as unknown
        $ownerName = "Unknown User";
    } else {
        $ownerName = $userData['name'];
    }

    // Change date formats from UNIX timestamp to something readable
    if ($storyDate['timestamp'] == 0) {
        $storyDate['mon'] = "";
        $storyDate['day'] = "";
        $storyDate['year'] = "";
    } else {
        $storyDate['mon'] = date('m', $storyDate['timestamp']);
        $storyDate['day'] = date('d', $storyDate['timestamp']);
        $storyDate['year'] = date('Y', $storyDate['timestamp']);
    }

    if ($datePublished['timestamp'] == 0) {
        $datePublished['mon'] = "";
        $datePublished['day'] = "";
        $datePublished['year'] = "";
    } else {
        $datePublished['mon'] = date('m', $datePublished['timestamp']);
        $datePublished['day'] = date('d', $datePublished['timestamp']);
        $datePublished['year'] = date('Y', $datePublished['timestamp']);
    }
                
    // Create the story array
    $story = array('id' => $id,
                   'pid' => $pid,
                   'cid' => $cid,
                   'ownerId' => $ownerId,
                   'ownerName' => $ownerName,
                   'title' => $title,
                   'source' => $source,
                   'content' => $content,
                   'priority' => $priority,
                   'storyDate' => $storyDate,
                   'altDate' => $altDate,
                   'datePublished' => $datePublished,
                   'fullTextLink' => $fullTextLink,
                   'registerLink' => $registerLink,
                   'linkExpiration' => $linkExpiration,
                   'commentary' => $commentary,
                   'commentarySource' => $commentarySource);

    // Return the story array
    return $story;
}

?>
