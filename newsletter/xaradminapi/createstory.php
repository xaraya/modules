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
 * Create an story
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['ownerId'] owner id of the story owner
 * @param $args['publicationId'] publication id of the story
 * @param $args['categoryId'] category id of the story
 * @param $args['title'] title of the story
 * @param $args['source'] source of the story
 * @param $args['content'] content of the story
 * @param $args['priority'] priorityof the story
 * @param $args['tstmpStoryDate'] date of the story as UNIX timestamp
 * @param $args['altDate'] alternative date of the story
 * @param $args['tstmpDatePublished'] date story posted as UNIX timestamp
 * @param $args['fullTextLink'] full text link of the story
 * @param $args['registerLink'] does the link require registration to view?(0=no, 1=yes)
 * @param $args['linkExpiration'] override of default publication link expiration
 * @param $args['commentary'] commentary for the story
 * @param $args['commentarySource'] commentary source for the story
 * @param $args['articleid'] article ID for the story
 * @returns int
 * @return story id , or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_adminapi_createstory($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($ownerId) || !is_numeric($ownerId)) {
        $invalid[] = 'ownerId';
    }
    if (!isset($title) || !is_string($title)) {
        $invalid[] = 'title';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'createstory', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return false;
    }

    // If no publication, then set to 0
    if (!isset($publicationId))
        $publicationId = 0;

    // If no category, then set to 0
    if (!isset($categoryId))
        $categoryId = 0;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrStories'];

    // We actually don't care if there are duplicate stories as
    // the same story could be used in different issues.
    /*
    // Check if that story already exists
    $query = "SELECT xar_id FROM $nwsltrTable
              WHERE xar_title = ?
              AND xar_cid = ?
              AND xar_ownerid = ?";
    
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return false; 

    if ($result->RecordCount() > 0) {
        return false;  // story already exists
    }
    */

    // Get next ID in table
    $nextId = $dbconn->GenId($nwsltrTable);

    // Add story
    $query = "INSERT INTO $nwsltrTable (
              xar_id,
              xar_ownerid,
              xar_pid,
              xar_cid,
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
              xar_commentarysrc,
              xar_articleid)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $bindvars = array((int) $nextId,
                      (int) $ownerId,
                      (int) $publicationId,
                      (int) $categoryId,
                      (string) $title,
                      (string) $source,
                      (string) $content,
                      (int) $priority,
                      (int) $tstmpStoryDate,
                      (string) $altDate,
                      (int) $tstmpDatePublished,
                      (string) $fullTextLink,
                      (int) $registerLink,
                      (int) $linkExpiration,
                      (string) $commentary,
                      (string) $commentarySource,
                      (int) $articleid);

    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return false;

    // Get the ID of the item that was inserted
    $storyId = $dbconn->PO_Insert_ID($nwsltrTable, 'xar_id');

    // Let any hooks know that we have created a new item
    xarModCallHooks('item', 'create', $storyId, 'storyId');

    // Return the id of the newly created item to the calling process
    return $storyId;
}

?>
