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
 * Update an Newsletter owner 
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] id of the owner (uid in roles)
 * @param $args['rid'] group of the owner
 * @param $args['signature'] the owner's signature
 * @returns true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_updateowner($args)
{
    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();

    if (!isset($id) || !is_numeric($id))
        $invalid[] = 'owner ID';

    if (!isset($rid) || !is_numeric($rid))
        $invalid[] = 'user group';

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'updateowner', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Call user API
    $item = xarModAPIFunc('newsletter',
            'user',
            'getowner',
            array('id' => $id));

    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrOwners'];

    // Update the item
    $query = "UPDATE $nwsltrTable 
              SET xar_rid = ?,
                  xar_signature = ?
              WHERE xar_uid = ?";

    $bindvars = array((int) $rid, (string) $signature, (int) $id);

    // Execute query
    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return;

    // Let any hooks know that we have updated an item.  As this is an
    // update hook we're passing the updated $item array as the extra info
    $item['module'] = 'newsletter';
    $item['itemid'] = $id;
    $item['id'] = $id;
    $item['signature'] = $signature;
    xarModCallHooks('item', 'update', $id, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

?>
