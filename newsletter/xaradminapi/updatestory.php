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
 * update an story
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] id of the story
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
 * @param $args['articleid'] id of the article to use in place of the story
 * @returns bool
 * @return true on success , or false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_updatestory($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();

    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'story ID';
    }
    if (!isset($title) || !is_string($title)) {
        $invalid[] = 'title';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'updatestory', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get item
    $item = xarModAPIFunc('newsletter',
                          'user',
                          'getstory',
                          array('id' => $id));

    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrStories'];

    // Update the item
    $query = "UPDATE $nwsltrTable 
              SET xar_ownerid = ?,
                  xar_pid = ?,
                  xar_cid = ?,
                  xar_title = ?,
                  xar_source = ?,
                  xar_content = ?,
                  xar_priority = ?,
                  xar_storydate = ?,
                  xar_altdate = ?,
                  xar_datepublished = ?,
                  xar_fulltextlink = ?,
                  xar_registerlink = ?,
                  xar_linkexpiration = ?,
                  xar_commentary = ?,
                  xar_commentarysrc = ?,
                  xar_articleid = ?
              WHERE xar_id = ?";

    $bindvars = array((int)     $ownerId,
                      (int)     $publicationId,
                      (int)     $categoryId,
                      (string)  $title,
                      (string)  $source,
                      (string)  $content,
                      (int)     $priority,
                      (int)     $tstmpStoryDate,
                      (string)  $altDate,
                      (int)     $tstmpDatePublished,
                      (string)  $fullTextLink,
                      (int)     $registerLink,
                      (int)     $linkExpiration,
                      (string)  $commentary,
                      (string)  $commentarySource,
                      (int)     $articleid,
                      (int)     $id);

    // Execute query
    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return;

    // Let any hooks know that we have updated an item.  As this is an
    // update hook we're passing the updated $item array as the extra info
    $item['module'] = 'newsletter';
    $item['itemid'] = $id;
    $item['title'] = $title;
    xarModCallHooks('item', 'update', $id, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

?>
