<?php
/**
 * File: $Id$
 * 
 * Xaraya Smilies
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Smilies Module
 * @author Jim McDonald, John Cox, Mikespub
*/

/**
 * create a new smilies
 * @param $args['code'] code of the item
 * @param $args['icon'] icon of the item
 * @param $args['emotion'] description of the item
 * @returns int
 * @return smilies ID on success, false on failure
 */
function smilies_adminapi_create($args)
{

    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if ((!isset($code)) ||
        (!isset($icon)) ||
        (!isset($emotion))) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'create', 'smilies');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security Check
	if(!xarSecurityCheck('AddSmilies')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $smiliestable = $xartable['smilies'];

    // Get next ID in table
    $nextId = $dbconn->GenId($smiliestable);

    // Add item
    $query = "INSERT INTO $smiliestable (
              xar_sid,
              xar_code,
              xar_icon,
              xar_emotion)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($code) . "',
              '" . xarVarPrepForStore($icon) . "',
              '" . xarVarPrepForStore($emotion) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $sid = $dbconn->PO_Insert_ID($smiliestable, 'xar_sid');

    // Let any hooks know that we have created a new link
    xarModCallHooks('item', 'create', $sid, 'sid');

    // Return the id of the newly created link to the calling process
    return $sid;
}

/**
 * delete a smilies
 * @param $args['sid'] ID of the link
 * @returns bool
 * @return true on success, false on failure
 */
function smilies_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($sid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'delete', 'smilies');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $smiley = xarModAPIFunc('smilies',
                          'user',
                          'get',
                          array('sid' => $sid));

    if (empty($smiley)) {
        $msg = xarML('No Such Smiley Present', 'smilies');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return; 
    }

    // Security Check
	if(!xarSecurityCheck('DeleteSmilies')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $smiliestable = $xartable['smilies'];

    // Delete the item
    $query = "DELETE FROM $smiliestable
            WHERE xar_sid = " . xarVarPrepForStore($sid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $sid, '');

    // Let the calling process know that we have finished successfully
    return true;
}

/**
 * update a smilies
 * @param $args['sid'] the ID of the :)
 * @param $args['code'] the new code of the :)
 * @param $args['icon'] the new icon of the :)
 * @param $args['emotion'] the new emotion of the :)
 */
function smilies_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($sid)) ||
        (!isset($code)) ||
        (!isset($icon)) ||
        (!isset($emotion))) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'update', 'smilies');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('smilies',
                          'user',
                          'get',
                          array('sid' => $sid));

    if ($link == false) {
        $msg = xarML('No Such :) Present', 'smilies');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return; 
    }

    // Security Check
	if(!xarSecurityCheck('EditSmilies')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $smiliestable = $xartable['smilies'];

    // Update the link
    $query = "UPDATE $smiliestable
            SET xar_code    = '" . xarVarPrepForStore($code) . "',
                xar_icon    = '" . xarVarPrepForStore($icon) . "',
                xar_emotion = '" . xarVarPrepForStore($emotion) . "'
            WHERE xar_sid = " . xarVarPrepForStore($sid);
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
function smilies_adminapi_getmenulinks()
{

    if (xarSecurityCheck('AddSmilies', 0)) {

        $menulinks[] = Array('url'   => xarModURL('smilies',
                                                  'admin',
                                                  'new'),
                              'title' => xarML('Add a new :) into the system'),
                              'label' => xarML('Add'));
    }

    if (xarSecurityCheck('EditSmilies', 0)) {

        $menulinks[] = Array('url'   => xarModURL('smilies',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('View and Edit :)'),
                              'label' => xarML('View'));
    }
    /*
    if (xarSecurityCheck('AdminSmilies', 0)) {
        $menulinks[] = Array('url'   => xarModURL('smilies',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the configuration for the smilies'),
                              'label' => xarML('Modify Config'));
    }
    */
    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>