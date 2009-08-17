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
function crispbb_userapi_createtopic($args)
{
    extract($args);

    $invalid = array();

    if (!isset($fid) || empty($fid) || !is_numeric($fid)) {
        $invalid[] = 'fid';
    }

    if (!isset($ttitle) || !is_string($ttitle) || empty($ttitle) || strlen($ttitle) > 255) {
        $invalid[] = 'ttitle';
    }

    if (empty($firstpid)) {
        if (isset($pdesc) && (!is_string($pdesc) || strlen($pdesc) > 255)) {
            $invalid[] = 'pdesc';
        }

        if (!isset($ptext) || !is_string($ptext) || empty($ptext)) {
            $invalid[] = 'ptext';
        }
    }
    if (!isset($topicstype) || empty($topicstype) || !is_numeric($topicstype)) {
        $topicstype = xarModAPIFunc('crispbb', 'user', 'getitemtype', array('fid' => $fid, 'component' => 'topics'));
        if (empty($topicstype)) {
            $invalid[] = 'topicstype';
        }
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'createtopic', 'crispBB');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    if (!isset($tstatus) || !is_numeric($tstatus)) {
        $tstatus = 0;
    }

    if (!isset($ttype) || !is_numeric($ttype)) {
        $ttype = 0;
    }

    if (!isset($towner) || empty($towner) || !is_numeric($towner)) {
        $towner = xarUserGetVar('uid');
    }

    if (!isset($tsettings) || empty($tsettings) || !is_array($tsettings)) {
        $tsettings = array();
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $topicstable = $xartable['crispbb_topics'];

    $nextId = $dbconn->GenId($topicstable);

    $query = "INSERT INTO $topicstable (
              xar_tid,
              xar_fid,
              xar_ttype,
              xar_tstatus,
              xar_towner,
              xar_topicstype,
              xar_ttitle,
              xar_tsettings
              )
            VALUES (?,?,?,?,?,?,?,?)";

    $bindvars = array();
    $bindvars[] = $nextId;
    $bindvars[] = $fid;
    $bindvars[] = $ttype;
    $bindvars[] = $tstatus;
    $bindvars[] = $towner;
    $bindvars[] = $topicstype;
    $bindvars[] = $ttitle;
    $bindvars[] = serialize($tsettings);

    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $tid = $dbconn->PO_Insert_ID($topicstable, 'xar_tid');

    if (!$tid) return;


    if (empty($firstpid)) {

        // log ip
        if (!isset($phostname) || empty($phostname)) {
            $forwarded = xarServerGetVar('HTTP_X_FORWARDED_FOR');

            if (!empty($forwarded)) {
                $phostname = preg_replace('/,.*/', '', $forwarded);
            } else {
                $phostname = xarServerGetVar('REMOTE_ADDR');
            }
        }

        $powner = $towner;

        $pstatus = !isset($pstatus) || !is_numeric($pstatus) ? 0 : $pstatus;

        $ptime = empty($ptime) || !is_numeric($ptime) ? time() : $ptime;

        $poststype = xarModAPIFunc('crispbb', 'user', 'getitemtype',
            array('fid' => $fid, 'component' => 'posts'));

        if (!isset($psettings) || !is_array($psettings) || empty($psettings)) {
            $psettings = array();
        }

        if (!$pid = xarModAPIFunc('crispbb', 'user', 'createpost',
            array(
                'tid' => $tid,
                'powner' => $powner,
                'pstatus' => $pstatus,
                'ptime' => $ptime,
                'poststype' => $poststype,
                'pdesc' => $pdesc,
                'ptext' => $ptext,
                'psettings' => $psettings,
                'fid' => $fid,
                'tstatus' => $tstatus
            ))) return;
    } else {
        $pid = $firstpid;
    }

    if (!xarModAPIFunc('crispbb', 'user', 'updatetopic',
        array(
            'tid' => $tid,
            'firstpid' => $pid,
            'nohooks' => true
        ))) return;

    // synch hooks
    $itemtypes = xarModAPIFunc('crispbb', 'user', 'getitemtypes');

    // call hooks for new topic
    $item = $args;
    $item['module'] = 'crispbb';
    $item['itemtype'] = $topicstype;
    $item['itemid'] = $tid;
    // don't let subscribers know about this topic if its status is submitted
    if (xarModIsAvailable('crispsubs') && $tstatus == 2) {
        $item['crispsubs_notifycreate'] = false;
    }

    xarModCallHooks('item', 'create', $tid, $item);

    /* create post updated the tracker already
    //let the tracker know this forum was updated
    $fstring = xarModGetVar('crispbb', 'ftracking');
    $ftracking = (!empty($fstring)) ? unserialize($fstring) : array();
    $ftracking[$fid] = $ptime;
    xarModSetVar('crispbb', 'ftracking', serialize($ftracking));
    */
    // return the new topic id
    return $tid;
}
?>