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
 * Create a publication
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['ownerId'] owner id of the publication
 * @param $args['categoryId'] category id of the publication
 * @param $args['altcids'] array of alternate category ids for the publication
 * @param $args['title'] title of the publication
 * @param $args['introduction'] introduction for the publication 
 * @param $args['templateHTML'] name of the HTML template for the publication 
 * @param $args['templateText'] name of the text template for the publication 
 * @param $args['logo'] logo for the publication
 * @param $args['linkExpiration'] default number of days before a story link expires
 * @param $args['linkRegistration'] default text for link registration
 * @param $args['disclaimerId'] disclaimer for the publication
 * @param $args['description'] description of the publication 
 * @param $args['introduction'] introduction of the publication 
 * @param $args['private'] publication is open for subscription or private
 * @param $args['subject'] email subject (title) of an issue
 * @param $args['fromname'] publication email from name (default = owner name)
 * @param $args['fromemail'] publication email from address (default = owner email)
 * @returns int
 * @return publication ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_createpublication($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($ownerId) || !is_numeric($ownerId)) {
        $invalid[] = 'ownerId';
    }
    if (!isset($disclaimerId) || !is_numeric($disclaimerId)) {
        $invalid[] = 'disclaimerId';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'createpublication', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return false;
    }
    
    // Alternate cids is an array so serialize
    if (is_array($altcids)) {
        $altcids = serialize($altcids);
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrPublications'];

    // Check if the publication already exists
    $query = "SELECT xar_id FROM $nwsltrTable
              WHERE xar_title = ?";

    $result =& $dbconn->Execute($query, array((string) $title));
    if (!$result) return false; 

    if ($result->RecordCount() > 0) {
        $msg = xarML('The publication title already exists.  Please click on back in your browser and enter a different title.',
                    'adminapi', 'createpublication', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
        return false;  // publication already exists
    }

    // Get next ID in table
    $nextId = $dbconn->GenId($nwsltrTable);

    // Add item
    $query = "INSERT INTO $nwsltrTable (
              xar_id,
              xar_ownerid,
              xar_cid,
              xar_altcids,
              xar_title,
              xar_template_html,
              xar_template_text,
              xar_logo,
              xar_linkexpiration,
              xar_linkregistration,
              xar_description,
              xar_disclaimerid,
              xar_introduction,
              xar_private,
              xar_subject,
              xar_fromname,
              xar_fromemail)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $bindvars = array((int)     $nextId,
                      (int)     $ownerId,
                      (int)     $categoryId,
                      (string)  $altcids,
                      (string)  $title,
                      (string)  $templateHTML,
                      (string)  $templateText,
                      (string)  $logo,
                      (int)     $linkExpiration,
                      (string)  $linkRegistration,
                      (string)  $description,
                      (int)     $disclaimerId,
                      (string)  $introduction,
                      (int)     $private,
                      (int)     $subject,
                      (string)  $fromname,
                      (string)  $fromemail);

    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return false;

    // Get the ID of the item that was inserted
    $publicationId = $dbconn->PO_Insert_ID($nwsltrTable, 'xar_id');

    // Let any hooks know that we have created a new item
    xarModCallHooks('item', 'create', $publicationId, 'publicationId');

    // Return the id of the newly created item to the calling process
    return $publicationId;
}

?>
