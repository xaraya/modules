<?php
/**
 * File: $Id$
 * 
 * Create a new forum topic
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * create a new forum
 * @param $args['fname'] name of forum
 * @param $args['fdesc'] description of forum
 * @returns int
 * @return autolink ID on success, false on failure
 */
function xarbb_userapi_createtopic($args)
{

    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if ((!isset($fid)) ||
        (!isset($ttitle)) ||
        (!isset($tpost)) ||
        (!isset($tposter))) {
        $msg = xarML('Invalid Parameter Count',
                    '', 'admin', 'create', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security Check
    if(!xarSecurityCheck('PostxarBB',1,'Forum')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xbbtopicstable = $xartable['xbbtopics'];

    // Get next ID in table
    $nextId = $dbconn->GenId($xbbtopicstable);
    // let's set times only if times are not passed in
    if (!isset($ttime) || empty($ttime)) {
        $ttime = time();
    }
    if (!isset($tftime) || empty($tftime)) {
        $tftime = time();
    }
    if (!isset($treplies) || empty($treplies)) {
        $treplies=0;
    }
    if (!isset($treplier) || empty($treplier)) {
        $treplier=0;
    }
    if (!isset($tstatus) || empty($tstatus)) {
        $tstatus = 0;
    }
    // Add item
    $query = "INSERT INTO $xbbtopicstable (
              xar_tid,
              xar_fid,
              xar_ttitle,
              xar_tpost,
              xar_tposter,
              xar_ttime,
              xar_tftime,
              xar_treplies,
              xar_treplier,
              xar_tstatus)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($fid) . "',
              '" . xarVarPrepForStore($ttitle) . "',
              '" . xarVarPrepForStore($tpost) . "',
              '" . xarVarPrepForStore($tposter) . "',
              '$ttime',
              '$tftime',
              '" . xarVarPrepForStore($treplies) . "',
              '" . xarVarPrepForStore($treplier) . "',
              '" . xarVarPrepForStore($tstatus) . "')";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $tid = $dbconn->PO_Insert_ID($xbbtopicstable, 'xar_tid');

    // Let any hooks know that we have created a new topic
    $args['module'] = 'xarbb';
    $args['itemtype'] = 2; // topic
    $args['itemid'] = $tid;
    xarModCallHooks('item', 'create', $tid, $args);

    // Return the id of the newly created link to the calling process
    return $tid;
}

?>
