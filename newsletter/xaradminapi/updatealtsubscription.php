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
 * update an alternative subscription
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] id of the  subscription
 * @param $args['name'] the name of the subscription
 * @param $args['email'] the email address of the subscription
 * @param $args['pid'] publication id 
 * @param $args['htmlmail'] send mail in html or text format (1 = html, 0 = text)
 * @returns int
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_updatealtsubscription($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();

    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }
    if (!isset($email) || !is_string($email)) {
        $invalid[] = 'email';
    }
    if (!isset($pid) || !is_numeric($pid)) {
        $invalid[] = 'title';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'updatealtsubscription', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!isset($htmlmail) || !is_numeric($htmlmail)) {
        $htmlmail = 0;
    }

    // Get item
    $item = xarModAPIFunc('newsletter',
                          'user',
                          'getaltsubscription',
                          array('id' => $id));

    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrAltSubscriptions'];

    // Update the item
    $query = "UPDATE $nwsltrTable 
              SET xar_name = ?,
                  xar_email = ?,
                  xar_htmlmail = ?
              WHERE xar_id = ?";
    
    $bindvars[] = (string) $name;
    $bindvars[] = (string) $email;
    $bindvars[] = (int) $htmlmail;
    $bindvars[] = (int) $id;

    // Execute query
    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return;

    // Let any hooks know that we have updated an item.  As this is an
    // update hook we're passing the updated $item array as the extra info
    $item['module'] = 'newsletter';
    $item['id'] = $id;
    $item['name'] = $name;
    $item['email'] = $email;
    $item['htmlmail'] = $htmlmail;
    xarModCallHooks('item', 'updatealtsubscription', $id, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

?>
