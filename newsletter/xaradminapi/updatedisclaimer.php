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
 * update a disclaimer
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] the id of the disclaimer
 * @param $args['title'] title of the disclaimer
 * @param $args['disclaimer'] text of the disclaimer
 * @returns bool
 * @return true on success , or false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_updatedisclaimer($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();

    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'disclaimer ID';
    }
    if (!isset($title) || !is_string($title)) {
        $invalid[] = 'title';
    }
    if (!isset($disclaimer) || !is_string($disclaimer)) {
        $invalid[] = 'disclaimer';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'updatedisclaimer', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get item
    $item = xarModAPIFunc('newsletter',
                          'user',
                          'getdisclaimer',
                          array('id' => $id));

    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrDisclaimers'];

    // Update the item
    $query = "UPDATE $nwsltrTable 
              SET xar_title = ?,
                  xar_text = ?
              WHERE xar_id = ?";

    $bindvars[] = (string) $title;
    $bindvars[] = (string) $disclaimer;
    $bindvars[] = (int) $id;

    // Execute query
    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return;

    // Let any hooks know that we have updated an item.  As this is an
    // update hook we're passing the updated $item array as the extra info
    $item['module'] = 'newsletter';
    $item['itemid'] = $id;
    $item['title'] = $title;
    $item['disclaimer'] = $disclaimer;
    xarModCallHooks('item', 'update', $id, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

?>
