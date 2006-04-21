<?php
/**
 * Create a new forum topic
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
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
    // 3 Locked // TODO: remove this, as 'locked' is an option, rather than a type

    // Get arguments from argument array
    extract($args);

    $now = time();

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
        $msg = xarML('Invalid parameters: #(1)', join(', ', $invalid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Security Check
    if (!xarSecurityCheck('PostxarBB', 1, 'Forum')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xbbtopicstable = $xartable['xbbtopics'];

    // Get next ID in table
    $nextId = $dbconn->GenId($xbbtopicstable);

    // let's set times only if times are not passed in
    if (!isset($ttime) || empty($ttime)) {
        $ttime = $now;
    }

    if (!isset($tftime) || empty($tftime)) {
        $tftime = $now;
    }

    if (!isset($treplies) || empty($treplies)) {
        $treplies = 0;
    }

    if (!isset($treplier) || empty($treplier)) {
        $treplier = 0;
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

    // Set the default options.
    // TODO: can we pass any of these in for setting when the topic is first created?
    // TODO: centralise these defaults.
    $options = array(
        'lock' => false,
        'subscribers' => array(),
        'shadow' => NULL,
    );

    // FIXME: should this not be in the GUI?
    // Input should be filtered at input, but this API is about storage.
    // The ID of the item will not really be known until after it is inserted anyway.
    list($tpost, $ttitle) = xarModCallHooks(
        'item', 'transform-input', $nextId,
        array($tpost, $ttitle),
        'xarbb', $fid
    );

    // Add item to the database.
    $query = "INSERT INTO $xbbtopicstable ("
        . " xar_tid, xar_fid, xar_ttitle, xar_tpost, xar_tposter, xar_ttime,"
        . " xar_tftime, xar_treplies, xar_treplier, xar_tstatus, xar_thostname,"
        . " xar_toptions)"
        . " VALUES (?,?,?,?,?,?, ?,?,?,?,?, ?)";

    $bindvars = array(
        $nextId, (int)$fid, (string)$ttitle, (string)$tpost,(string)$tposter, (int)$ttime,
        (int)$tftime, (int)$treplies, (int)$treplier, (int)$tstatus, (string)$thostname,
        serialize($options)
    );
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $tid = $dbconn->PO_Insert_ID($xbbtopicstable, 'xar_tid');

    // Let any hooks know that we have created a new topic
    $args['module'] = 'xarbb';
    $args['itemtype'] = $fid; //Update itemtype hooks for this forum
    $args['itemid'] = $tid;
    xarModCallHooks('item', 'create', $tid, $args); 

    // Return the id of the newly created link to the calling process
    return $tid;
}

?>