<?php
/**
 * File: $Id$
 * 
 * Xaraya Censor
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Censor Module
 * @author John Cox
*/

/**
 * create a new censored word
 * @param $args['keyword'] keyword of the item
 * @returns int
 * @return censor ID on success, false on failure
 */
function censor_adminapi_create($args)
{

    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if (!isset($keyword)){
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'create', 'censor');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecAuthAction(0, 'censor::', "$keyword::", ACCESS_ADD)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $censortable = $xartable['censor'];

    // Get next ID in table
    $nextId = $dbconn->GenId($censortable);

    // Add item
    $query = "INSERT INTO $censortable (
              xar_cid,
              xar_keyword)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($keyword) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $cid = $dbconn->PO_Insert_ID($censortable, 'xar_cid');

    // Let any hooks know that we have created a new link
    xarModCallHooks('item', 'create', $cid, 'cid');

    // Return the id of the newly created link to the calling process
    return $cid;
}

/**
 * delete an censored word
 * @param $args['cid'] ID of the link
 * @returns bool
 * @return true on success, false on failure
 */
function censor_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($cid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'delete', 'censor');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('censor',
                         'user',
                         'get',
                         array('cid' => $cid));

    if ($link == false) {
        $msg = xarML('No Such Link Present',
                    'censor');
        xarExceptionSet(XAR_USER_EXCEPTION, 
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return; 
    }

    // Security check
    if (!xarSecAuthAction(0, 'censor::', "$link[keyword]::$cid", ACCESS_DELETE)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $censortable = $xartable['censor'];

    // Delete the item
    $query = "DELETE FROM $censortable
            WHERE xar_cid = " . xarVarPrepForStore($cid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $cid, '');

    // Let the calling process know that we have finished successfully
    return true;
}

/**
 * update an censored word
 * @param $args['cid'] the ID of the link
 * @param $args['keyword'] the new keyword of the link
 */
function censor_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);

    if (!isset($comment)) {
        $comment = '';
    }
    // Argument check
    if ((!isset($cid)) ||
        (!isset($keyword))) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'update', 'censor');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('censor',
                         'user',
                         'get',
                         array('cid' => $cid));

    if ($link == false) {
        $msg = xarML('No Such Link Present',
                    'censor');
        xarExceptionSet(XAR_USER_EXCEPTION, 
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return; 
    }

    if (!xarSecAuthAction(0, 'censor::', "$link[keyword]::$cid", ACCESS_EDIT)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $censortable = $xartable['censor'];

    // Update the link
    $query = "UPDATE $censortable
            SET xar_keyword = '" . xarVarPrepForStore($keyword) . "'
            WHERE xar_cid = " . xarVarPrepForStore($cid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let the calling process know that we have finished successfully
    return true;
}

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function censor_adminapi_getmenulinks()
{

    if (xarSecAuthAction(0, 'censor::', '::', ACCESS_ADD)) {

        $menulinks[] = Array('url'   => xarModURL('censor',
                                                   'admin',
                                                   'new'),
                              'title' => xarML('Add a new censored word into the system'),
                              'label' => xarML('Add'));
    }

    if (xarSecAuthAction(0, 'censor::', '::', ACCESS_EDIT)) {

        $menulinks[] = Array('url'   => xarModURL('censor',
                                                   'admin',
                                                   'view'),
                              'title' => xarML('View and Edit Censored Words'),
                              'label' => xarML('View'));
    }

    if (xarSecAuthAction(0, 'censor::', '::', ACCESS_ADMIN)) {
        $menulinks[] = Array('url'   => xarModURL('censor',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the configuration for the Censor Module'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>