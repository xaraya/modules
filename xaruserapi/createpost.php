<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
 /**
 * Create a new forum
 *
 * This is a standard adminapi function to create a forum
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @param  string   $args['fname']      forum name
 * @param  string   $args['fdesc']      forum description
 * @param  int      $args['fstatus']    forum status id
 * @param  int      $args['fowner']     forum owner id
 * @param  int      $args['forder']     forum order
 * @param  array    $args['fsettings']  forum settings
 * @return int forum id on success, false on failure
 * @throws BAD_PARAM, DATABASE_ERROR
 */
function crispbb_userapi_createpost($args)
{
    extract($args);

    $invalid = array();

    if (!isset($tid) || empty($tid) || !is_numeric($tid)) {
        $invalid[] = 'tid';
    }

    if (isset($pdesc) && (!is_string($pdesc) || strlen($pdesc) > 255)) {
        $invalid[] = 'pdesc';
    }

    if (!isset($ptext) || !is_string($ptext) || empty($ptext)) {
        $invalid[] = 'ptext';
    }

    if (!isset($poststype) || empty($poststype) || !is_numeric($poststype)) {
            $invalid[] = 'poststype';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'createpost', 'crispBB');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // log ip
    if (!isset($phostname) || empty($phostname)) {
        $forwarded = xarServerGetVar('HTTP_X_FORWARDED_FOR');

        if (!empty($forwarded)) {
            $phostname = preg_replace('/,.*/', '', $forwarded);
        } else {
            $phostname = xarServerGetVar('REMOTE_ADDR');
        }
    }

    if (!isset($powner) || empty($powner) || !is_numeric($powner)) {
        $powner = xarUserGetVar('uid');
    }

    $pstatus = 0;

    if (!isset($ptime) || empty($ptime) || !is_numeric($ptime)) {
        $ptime = time();
    }

    if (!isset($psettings) || !is_array($psettings) || empty($psettings)) {
        $psettings = array();
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $poststable = $xartable['crispbb_posts'];

    $nextId = $dbconn->GenId($poststable);

    $query = "INSERT INTO $poststable (
              xar_pid,
              xar_tid,
              xar_powner,
              xar_pstatus,
              xar_ptime,
              xar_poststype,
              xar_phostname,
              xar_pdesc,
              xar_ptext,
              xar_psettings
              )
            VALUES (?,?,?,?,?,?,?,?,?,?)";

    $bindvars = array();
    $bindvars[] = $nextId;
    $bindvars[] = $tid;
    $bindvars[] = $powner;
    $bindvars[] = $pstatus;
    $bindvars[] = $ptime;
    $bindvars[] = $poststype;
    $bindvars[] = $phostname;
    $bindvars[] = $pdesc;
    $bindvars[] = $ptext;
    $bindvars[] = serialize($psettings);

    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $pid = $dbconn->PO_Insert_ID($poststable, 'xar_pid');

    // synch hooks
    $itemtypes = xarModAPIFunc('crispbb', 'user', 'getitemtypes');

    // call hooks for new post
    $item = $args;
    $item['module'] = 'crispbb';
    $item['itemtype'] = $poststype;
    $item['itemid'] = $pid;
    xarModCallHooks('item', 'create', $pid, $item);

    // update the topic
    if (!xarModAPIFunc('crispbb', 'user', 'updatetopic',
        array(
            'tid' => $tid,
            'lastpid' => $pid,
            'nohooks' => true
        ))) return;

    // update the forum
    if (empty($fid)) {
        $topic = xarModAPIFunc('crispbb', 'user', 'gettopic', array('tid' => $tid));
        $fid = $topic['fid'];
    }

    if (!xarModAPIFunc('crispbb', 'admin', 'update',
        array(
            'fid' => $fid,
            'lasttid' => $tid,
            'nohooks' => true
        ))) return;

    // let the tracker know the forum was updated
    $fstring = xarModGetVar('crispbb', 'ftracking');
    $ftracking = (!empty($fstring)) ? unserialize($fstring) : array();
    $ftracking[$fid] = $ptime;
    xarModSetVar('crispbb', 'ftracking', serialize($ftracking));

    // return the new forum id
    return $pid;
}
?>