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
    // Topic Status
    // 0 Normal status 
    // 1 Announcement
    // 2 Sticky
    // 3 Locked

    // Get arguments from argument array
    extract($args);

    $invalid = array();
    if (!isset($ttitle) || !is_string($ttitle)) {
        $invalid[] = 'ttitle';
    } 
    if (!isset($tpost)) {
        $invalid[] = 'tpost';
    } 
    if (!isset($fid) || !is_numeric($fid)) {
        $invalid[] = 'fid';
    } 
    if (!isset($tposter) || !is_numeric($tposter)) {
        $invalid[] = 'fid';
    } 
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', join(', ', $invalid), 'user', 'createtopic', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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

    if (!isset($thostname)) {
        $forwarded = xarServerGetVar('HTTP_X_FORWARDED_FOR');
        if (!empty($forwarded)) {
            $thostname = preg_replace('/,.*/', '', $forwarded);
        } else {
            $thostname = xarServerGetVar('REMOTE_ADDR');
        }
    }

    list($tpost,
         $ttitle) = xarModCallHooks('item',
                                    'transform-input',
                                     $nextId,
                                     array($tpost,
                                           $ttitle),
                                           'xarbb',
                                           $fid);

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
              xar_tstatus,
              xar_thostname)
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
              '" . xarVarPrepForStore($tstatus) . "',
              '" . xarVarPrepForStore($thostname) . "')";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $tid = $dbconn->PO_Insert_ID($xbbtopicstable, 'xar_tid');

    // Let any hooks know that we have created a new topic
    $args['module'] = 'xarbb';
    $args['itemtype'] = $fid; // topic
    $args['itemid'] = $tid;



    xarModCallHooks('item', 'create', $tid, $args);

    // Return the id of the newly created link to the calling process
    return $tid;
}

?>