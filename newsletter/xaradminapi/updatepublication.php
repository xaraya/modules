<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://xavier.schwabfoundation.org
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
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
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
    $query = "UPDATE $nwsltrTable SET
              xar_ownerid = " .  xarVarPrepForStore($ownerId) . ",
              xar_cid = " .  xarVarPrepForStore($categoryId) . ",
              xar_altcids = '" .  xarVarPrepForStore($altcids) . "',
              xar_title = '" .  xarVarPrepForStore($title) . "',
              xar_template_html = '" .  xarVarPrepForStore($templateHTML) . "',
              xar_template_text = '" .  xarVarPrepForStore($templateText) . "',
              xar_logo = '" .  xarVarPrepForStore($logo) . "',
              xar_linkexpiration = " .  xarVarPrepForStore($linkExpiration) . ",
              xar_linkregistration = '" .  xarVarPrepForStore($linkRegistration) . "',
              xar_description = '" .  xarVarPrepForStore($description) . "',
              xar_disclaimerid = " .  xarVarPrepForStore($disclaimerId) . ",
              xar_introduction = '" .  xarVarPrepForStore($introduction) . "',
              xar_private = " .  xarVarPrepForStore($private) . "
              WHERE xar_id = " . xarVarPrepForStore($id);

    // Execute query
    $result =& $dbconn->Execute($query);

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
