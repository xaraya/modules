<?php
/**
 * File: $Id$
 * 
 * Ephemerids
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Ephemerids Module
 * @author Volodymyr Metenchuk
*/

// add ephemerids to db
function ephemerids_adminapi_add($args)
{
    // Get arguments 
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if ((!isset($did)) ||
        (!isset($mid)) ||
        (!isset($yid)) ||
        (!isset($content))) {
        $msg = xarML('Invalid Parameter Count', join(', ',$invalid), 'admin', 'add', 'empherids');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Security Check
    if(!xarSecurityCheck('AddEphemerids')) return;

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $elanguage = 'all';
    $ephemtable = $xartable['ephem'];

    $nextId = $dbconn->GenId($ephemtable);

    $did = xarVarPrepForStore($did);
    $mid = xarVarPrepForStore($mid);
    $yid = xarVarPrepForStore($yid);
    $content = xarVarPrepForStore($content);
    $elanguage = xarVarPrepForStore($elanguage);
    
    $query = "INSERT INTO $ephemtable (xar_eid, 
                                       xar_did, 
                                       xar_mid, 
                                       xar_yid, 
                                       xar_content, 
                                       xar_elanguage)
                                VALUES ($nextId, 
                                        '$did', 
                                        '$mid', 
                                        '$yid', 
                                        '$content', 
                                        '$elanguage')";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the item that we inserted.  
    $eid = $dbconn->PO_Insert_ID($ephemtable, 'xar_eid');

    // Let any hooks know that we have created a new link
    xarModCallHooks('item', 'create', $eid, 'eid');

    // Return the id of the newly created link to the calling process
    return $eid;
}

// update ephemerids
function ephemerids_adminapi_update($args)
{
    // Get arguments 
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if ((!isset($did)) ||
        (!isset($eid)) ||
        (!isset($mid)) ||
        (!isset($yid)) ||
        (!isset($content))) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'add', 'empherids');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security Check
    if(!xarSecurityCheck('EditEphemerids')) return;
    $elanguage = 'all';

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $ephemtable = $xartable['ephem'];

    $query = "UPDATE $ephemtable
              SET xar_yid = '" . xarVarPrepForStore($yid) . "',
                  xar_mid = '" . xarVarPrepForStore($mid) . "',
                  xar_did = '" . xarVarPrepForStore($did) . "',
                  xar_content = '" . xarVarPrepForStore($content) . "',
                  xar_elanguage = '" . xarVarPrepForStore($elanguage) . "'
              WHERE xar_eid = $eid";

    $result =& $dbconn->Execute($query);
    if (!$result) return;
    return true;
}

// delete ephemerids
function ephemerids_adminapi_delete($args)
{
    extract($args);

    if (!isset($eid)) {
        xarSessionSetVar('errormsg', _EPHEM_ARGSERROR);
        return false;
    }

    // Argument check
    if (!isset($eid) || !is_numeric($eid)) {
        $msg = xarML('Invalid argument',
                    'eid', 'admin', 'delete', 'ephemerid');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security Check
    if(!xarSecurityCheck('DeleteEphemerids')) return;

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $ephemtable = $xartable['ephem'];

    $query = "DELETE FROM $ephemtable WHERE xar_eid = " . xarVarPrepForStore($eid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $eid, '');

    // Let the calling process know that we have finished successfully
    return true;
}

// return an array containing ephemerids data
function ephemerids_adminapi_display()
{
    // Security Check
    if(!xarSecurityCheck('EditEphemerids')) return;

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $ephemtable = $xartable['ephem'];

    $query = "SELECT xar_eid,
                     xar_did, 
                     xar_mid, 
                     xar_yid,
                     xar_content,
                     xar_elanguage
    FROM $ephemtable ORDER BY xar_eid DESC";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $resarray = array();

    while(list($eid, $did, $mid, $yid, $content, $elanguage) = $result->fields) {
    $result->MoveNext();

    $resarray[] = array('eid' => $eid,
                'did' => $did,
                'mid' => $mid,
                'yid' => $yid,
                'content' => $content,
                'elanguage' => $elanguage);
    }
    $result->Close();

    return $resarray;
}

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function ephemerids_adminapi_getmenulinks()
{

// Security Check
    if (xarSecurityCheck('AddEphemerids',0)) {

        $menulinks[] = Array('url'   => xarModURL('ephemerids',
                                                   'admin',
                                                   'new'),
                              'title' => xarML('Add a new ephemerids into the system'),
                              'label' => xarML('Add'));
    }

// Security Check
    if (xarSecurityCheck('EditEphemerids',0)) {

        $menulinks[] = Array('url'   => xarModURL('ephemerids',
                                                   'admin',
                                                   'view'),
                              'title' => xarML('View and Edit Ephemerids'),
                              'label' => xarML('View'));
    }

// Security Check
    if (xarSecurityCheck('AdminEphemerids',0)) {
        $menulinks[] = Array('url'   => xarModURL('ephemerids',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the configuration for the Ephemerids'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>