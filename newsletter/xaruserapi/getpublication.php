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
 * Get an Newsletter publication by id
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] id of newsletter publication to get
 * @returns publication array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_userapi_getpublication($args)
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
                    join(', ',$invalid), 'userapi', 'getpublication', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrPublications'];
    $query = "SELECT xar_id,
                     xar_cid,
                     xar_altcids,
                     xar_ownerid,
                     xar_template_html,
                     xar_template_text,
                     xar_title,
                     xar_logo,
                     xar_linkexpiration,
                     xar_linkregistration,
                     xar_description,
                     xar_disclaimerid,
                     xar_introduction,
                     xar_private,
                     xar_subject,
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

    // Obtain the publication information from the result set
    $datePublished = array();

    list($id,
         $cid,
         $altcids,
         $ownerId, 
         $templateHTML,
         $templateText,
         $title,
         $logo,
         $linkExpiration,
         $linkRegistration,
         $description,
         $disclaimerId,
         $introduction,
         $private,
         $subject,
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
        $ownerEmail = "none@none.com";
    } else {
        $ownerName = $userData['name'];
        $ownerEmail = $userData['email'];
    }

    // Unserialize the altcids
    if (is_string($altcids)) {
        $altcids = unserialize($altcids);
    }
                
    // Create the publication
    $publication = array('id' => $id,
                  'cid' => $cid,
                  'altcids' => $altcids,
                  'ownerId' => $ownerId,
                  'ownerName' => $ownerName,
                  'ownerEmail' => $ownerEmail,
                  'templateHTML' => $templateHTML,
                  'templateText' => $templateText,
                  'title' => $title,
                  'logo' => $logo,
                  'linkExpiration' => $linkExpiration,
                  'linkRegistration' => $linkRegistration,
                  'description' => $description,
                  'disclaimerId' => $disclaimerId,
                  'introduction' => $introduction,
                  'private' => $private,                  
                  'subject' => $subject,
                  'fromname' => $fromname,
                  'fromemail' => $fromemail);                  

    // Return the publication array
    return $publication;
}

?>
