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
 * Update a forum
 *
 * This is a standard adminapi function to update a forum
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @param  int      $args['fid']        forum id
 * @param  string   $args['fname']      forum name
 * @param  string   $args['fdesc']      forum description
 * @param  int      $args['fstatus']    forum status id
 * @param  int      $args['fowner']     forum owner id
 * @param  int      $args['forder']     forum order
 * @param  array    $args['fsettings']  forum settings
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, DATABASE_ERROR
 */
function crispbb_userapi_updatetopic($args)
{
    extract($args);
    $invalid = array();
    if (!isset($tid) || empty($tid) || !is_numeric($tid)) {
      $invalid[] = 'tid';
    }

    if (isset($fid)) {
        if (!is_numeric($fid) || empty($fid)) {
            $invalid[] = 'fid';
        }
    }

    if (isset($ttype)) {
        if (!is_numeric($ttype)) {
            $invalid[] = 'ttype';
        }
    }

    if (isset($tstatus)) {
        if (!is_numeric($tstatus)) {
            $invalid[] = 'tstatus';
        }
    }


    if (isset($towner)) {
        if (!is_numeric($towner) || empty($towner)) {
            $invalid[] = 'towner';
        }
    }

    if (isset($topicstype)) {
        if (!is_numeric($topicstype) || empty($topicstype)) {
            $invalid[] = 'topicstype';
        }
    }

    if (isset($firstpid)) {
        if (!is_numeric($firstpid) || empty($firstpid)) {
            $invalid[] = 'firstpid';
        }
    }

    if (isset($lastpid)) {
        if (!is_numeric($lastpid) || empty($lastpid)) {
            $invalid[] = 'lastpid';
        }
    }

    if (isset($numreplies)) {
        if (!is_numeric($numreplies)) {
            $invalid[] = 'numreplies';
        }
    }

    if (isset($ttitle)) {
        if (empty($ttitle) || !is_string($ttitle) || strlen($ttitle) > 255) {
            $invalid[] = 'ttitle';
        }
    }

    if (isset($tsettings)) {
        if (empty($tsettings) || !is_array($tsettings)) {
            $invalid[] = 'tsettings';
        }
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'userapi', 'updatetopic', 'crispBB');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return $msg;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $topicstable = $xartable['crispbb_topics'];
    $set = array();
    $bindvars = array();

    if (isset($fid)) {
        $set[] = 'xar_fid = ?';
        $bindvars[] = $fid;
    }

    if (isset($ttype)) {
        $set[] = 'xar_ttype = ?';
        $bindvars[] = $ttype;
    }
    if (isset($tstatus)) {
        $set[] = 'xar_tstatus = ?';
        $bindvars[] = $tstatus;
    }
    if (isset($towner)) {
        $set[] = 'xar_towner = ?';
        $bindvars[] = $towner;
    }
    if (isset($topicstype)) {
        $set[] = 'xar_topicstype = ?';
        $bindvars[] = $topicstype;
    }

    if (isset($firstpid)) {
        $set[] = 'xar_firstpid = ?';
        $bindvars[] = $firstpid;
    }

    if (isset($lastpid)) {
        $set[] = 'xar_lastpid = ?';
        $bindvars[] = $lastpid;
    }

    if (isset($ttitle)) {
        $set[] = 'xar_ttitle = ?';
        $bindvars[] = $ttitle;
    }

    if (isset($tsettings)) {
        $set[] = 'xar_tsettings = ?';
        $bindvars[] = serialize($tsettings);
    }

    $numreplies = xarModAPIFunc('crispbb', 'user', 'countposts',
        array(
            'tid' => $tid,
            'pstatus' => 0
        ));
    $set[] = 'xar_numreplies = ?';
    $bindvars[] = !empty($numreplies) ? $numreplies-1 : 0;
    $numsubs = xarModAPIFunc('crispbb', 'user', 'countposts',
        array(
            'tid' => $tid,
            'pstatus' => 2
        ));
    $set[] = 'xar_numsubs = ?';
    $bindvars[] = $numsubs;
    $numdels = xarModAPIFunc('crispbb', 'user', 'countposts',
        array(
            'tid' => $tid,
            'pstatus' => 5
        ));
    $set[] = 'xar_numdels = ?';
    $bindvars[] = $numdels;

    $query = "UPDATE $topicstable";
    $query .= " SET " . join(',', $set);
    $query .= " WHERE xar_tid = ?";
    $bindvars[] = $tid;

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    if (!empty($nohooks)) return true;

    if (empty($topicstype)) {
        $topic = xarModAPIFunc('crispbb', 'user', 'gettopic', array('tid' => $tid));
        if (!$topic) return;
        $topicstype = $topic['topicstype'];
    }

    $args['module'] = 'crispbb';
    $args['itemid'] = $tid;
    $args['itemtype'] = $topicstype;
    xarModCallHooks('item', 'update', $tid, $args);

    return true;
}
?>