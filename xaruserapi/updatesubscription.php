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
 * update a subscription
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['uid'] user id
 * @param $args['pid'] publication id 
 * @param $args['htmlmail'] send mail in html or text format (1 = html, 0 = text)
 * @returns int
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_userapi_updatesubscription($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();

    if (!isset($uid) || !is_numeric($uid)) {
        $invalid[] = 'User ID';
    }
    if (!isset($pid) || !is_numeric($pid)) {
        $invalid[] = 'title';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'userapi', 'updatesubscription', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!isset($htmlmail) || !is_numeric($htmlmail)) {
        $htmlmail = 0;
    }

    // Get item
    $item = xarModAPIFunc('newsletter',
                          'user',
                          'getsubscription',
                          array('id' => $id));

    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrSubscriptions'];

    // Update the item
    $query = "UPDATE $nwsltrTable 
                 SET xar_htmlmail = ?
               WHERE xar_uid = ?
                 AND xar_pid = ?";

    $bindvars[] = (int) $htmlmail;
    $bindvars[] = (int) $uid;
    $bindvars[] = (int) $pid;

    // Execute query
    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return;

    // Let any hooks know that we have updated an item.  As this is an
    // update hook we're passing the updated $item array as the extra info
    $item['module'] = 'newsletter';
    $item['pid'] = $pid;
    $item['htmlmail'] = $htmlmail;
    xarModCallHooks('item', 'update', $pid, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

?>
