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
 * update a publication
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] the id of the publication
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
 * @param $args['private'] publication is open for subscription or private
 * @param $args['subject'] email subject (title) of an issue
 * @param $args['fromname'] publication email from name (default = owner name)
 * @param $args['fromemail'] publication email from address (default = owner email)
 * @returns int
 * @return publication ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_updatepublication($args)
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
    if (!isset($disclaimerId) || !is_numeric($disclaimerId)) {
        $invalid[] = 'disclaimer ID';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'updatepublication', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    
    // Alternate cids is an array so serialize
    if (is_array($altcids)) {
        $altcids = serialize($altcids);
    }

    // Get item
    $item = xarModAPIFunc('newsletter',
                          'user',
                          'getpublication',
                          array('id' => $id));

    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrPublications'];

    // Update the item
    $query = "UPDATE $nwsltrTable 
              SET xar_cid = ?,
                  xar_altcids = ?,
                  xar_ownerid = ?,
                  xar_template_html = ?,
                  xar_template_text = ?,
                  xar_title = ?,
                  xar_logo = ?,
                  xar_linkexpiration = ?,
                  xar_linkregistration = ?,
                  xar_description = ?,
                  xar_disclaimerid = ?,
                  xar_introduction = ?,
                  xar_private = ?,
                  xar_subject = ?,
                  xar_fromname = ?,
                  xar_fromemail = ?
              WHERE xar_id = ?";

    $bindvars = array((int)     $categoryId,
                      (string)  $altcids,
                      (int)     $ownerId,
                      (string)  $templateHTML,
                      (string)  $templateText,
                      (string)  $title,
                      (string)  $logo,
                      (int)     $linkExpiration,
                      (string)  $linkRegistration,
                      (string)  $description,
                      (int)     $disclaimerId,
                      (string)  $introduction,
                      (int)     $private,
                      (int)     $subject,
                      (string)  $fromname,
                      (string)  $fromemail,
                      (int)     $id);

    // Execute query
    $result =& $dbconn->Execute($query, $bindvars);

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
