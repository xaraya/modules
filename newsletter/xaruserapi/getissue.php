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
 * Get an Newsletter issue by id
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] id of newsletter issue to get
 * @returns issue array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_userapi_getissue($args)
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
                    join(', ',$invalid), 'userapi', 'getissue', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrIssues'];
    $query = "SELECT xar_pid,
                     xar_title,
                     xar_ownerid,
                     xar_external,
                     xar_editornote,
                     xar_datepublished,
                     xar_fromname,
                     xar_fromemail
                FROM $nwsltrTable
               WHERE xar_id = ?";

    // Process query
    $result =& $dbconn->Execute($query, array((int) $id));

    // Check for an error
    if (!$result) return;

    // Check for no rows found
    if ($result->EOF) {
        $result->Close();
        return;
    }

    // Obtain the issue information from the result set
    $datePublished = array();

    list($pid,
         $title, 
         $ownerId, 
         $external,
         $editorNote,
         $datePublished['timestamp'],
         $fromname,
         $fromemail) =  $result->fields;

    // Close result set
    $result->Close();

    // The user API function is called.
    $userData = xarModAPIFunc('roles',
                              'user',
                              'get',
                              array('uid' => $ownerId));

    if ($userData == false) {
        // If this user does not exist in xar_roles table
        // then show that it's unknown
        $ownerName = "Unknown User";
    } else {
        $ownerName = $userData['name'];
    }

    // Change date formats from UNIX timestamp to something readable
    if ($datePublished['timestamp'] == 0) {
        $datePublished['mon'] = "";
        $datePublished['day'] = "";
        $datePublished['year'] = "";
    } else {
        $datePublished['mon'] = date('m', $datePublished['timestamp']);
        $datePublished['day'] = date('d', $datePublished['timestamp']);
        $datePublished['year'] = date('Y', $datePublished['timestamp']);
    }
                
    // Create the issue
    $issue = array('id' => $id,
                   'pid' => $pid,
                   'title' => $title,
                   'ownerId' => $ownerId,
                   'ownerName' => $ownerName,
                   'external' => $external,
                   'editorNote' => $editorNote,
                   'datePublished' => $datePublished,
                   'fromname' => $fromname,
                   'fromemail' => $fromemail);

    // Return the issue array
    return $issue;
}

?>
