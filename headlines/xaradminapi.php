<?php 
/**
 * File: $Id$
 * 
 * Xaraya Headlines
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Headlines Module
 * @author John Cox
*/

/**
 * create a new headline
 * @param $args['url'] url of the item
 * @returns int
 * @return headline ID on success, false on failure
 */
function headlines_adminapi_create($args)
{

    // Get arguments from argument array
    extract($args);


    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if (!isset($url)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'create', 'Headlines');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security Check
	if(!xarSecurityCheck('AddHeadlines')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $headlinestable = $xartable['headlines'];

    // Get next ID in table
    $nextId = $dbconn->GenId($headlinestable);

    // Add item
    $query = "INSERT INTO $headlinestable (
              xar_hid,
              xar_url,
              xar_order)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($url) . "',
              $nextId)";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $hid = $dbconn->PO_Insert_ID($headlinestable, 'xar_hid');

    // Let any hooks know that we have created a new link
    xarModCallHooks('item', 'create', $hid, 'hid');

    // Return the id of the newly created link to the calling process
    return $hid;
}

/**
 * delete an headlines
 * @param $args['hid'] ID of the headline
 * @returns bool
 * @return true on success, false on failure
 */
function headlines_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($hid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'delete', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));

    if ($link == false) return;

    // Security Check
	if(!xarSecurityCheck('DeleteHeadlines')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $headlinestable = $xartable['headlines'];

    // Delete the item
    $query = "DELETE FROM $headlinestable
            WHERE xar_hid = " . xarVarPrepForStore($hid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $hid, '');

    // Let the calling process know that we have finished successfully
    return true;
}

/**
 * update an headline
 * @param $args['hid'] the ID of the link
 * @param $args['url'] the new url of the link
 */
function headlines_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($hid)) ||
        (!isset($url))) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'update', 'Headlines');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));

    if ($link == false) return;

    // Security Check
	if(!xarSecurityCheck('EditHeadlines')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $headlinestable = $xartable['headlines'];

    // Update the link
    $query = "UPDATE $headlinestable
            SET xar_url = '" . xarVarPrepForStore($url) . "',
                xar_title = '" . xarVarPrepForStore($title) . "',
                xar_desc = '" . xarVarPrepForStore($desc) . "',
                xar_order = '" . xarVarPrepForStore($order) . "'
            WHERE xar_hid = " . xarVarPrepForStore($hid);
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
function headlines_adminapi_getmenulinks()
{

    // Security Check
	if(xarSecurityCheck('AddHeadlines')) {

        $menulinks[] = Array('url'   => xarModURL('headlines',
                                                  'admin',
                                                  'new'),
                              'title' => xarML('Add a new Headline into the system'),
                              'label' => xarML('Add'));
    }

    if(xarSecurityCheck('EditHeadlines')) {

        $menulinks[] = Array('url'   => xarModURL('headlines',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('View and Edit Headlines'),
                              'label' => xarML('View'));
    }

    if(xarSecurityCheck('AdminHeadlines')) {

        $menulinks[] = Array('url'   => xarModURL('headlines',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Edit the Headlines Configuration'),
                              'label' => xarML('Modify Config'));
    }


    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>