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
        xarErrorSet(SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return $msg;
    }

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $topicstable = $xartable['crispbb_topics'];
    $set = array();
    $bindvars = array();

    if (isset($fid)) {
        $set[] = 'fid = ?';
        $bindvars[] = $fid;
    }

    if (isset($ttype)) {
        $set[] = 'ttype = ?';
        $bindvars[] = $ttype;
    }
    if (isset($tstatus)) {
        $set[] = 'tstatus = ?';
        $bindvars[] = $tstatus;
    }
    if (isset($towner)) {
        $set[] = 'towner = ?';
        $bindvars[] = $towner;
    }
    if (isset($topicstype)) {
        $set[] = 'topicstype = ?';
        $bindvars[] = $topicstype;
    }

    if (isset($firstpid)) {
        $set[] = 'firstpid = ?';
        $bindvars[] = $firstpid;
    }

    if (isset($lastpid)) {
        $set[] = 'lastpid = ?';
        $bindvars[] = $lastpid;
    }

    if (isset($ttitle)) {
        $set[] = 'ttitle = ?';
        $bindvars[] = $ttitle;
    }

    if (isset($tsettings)) {
        $set[] = 'tsettings = ?';
        $bindvars[] = serialize($tsettings);
    }

    $numreplies = xarMod::apiFunc('crispbb', 'user', 'countposts',
        array(
            'tid' => $tid,
            'pstatus' => 0
        ));
    $set[] = 'numreplies = ?';
    $bindvars[] = !empty($numreplies) ? $numreplies-1 : 0;
    $numsubs = xarMod::apiFunc('crispbb', 'user', 'countposts',
        array(
            'tid' => $tid,
            'pstatus' => 2
        ));
    $set[] = 'numsubs = ?';
    $bindvars[] = $numsubs;
    $numdels = xarMod::apiFunc('crispbb', 'user', 'countposts',
        array(
            'tid' => $tid,
            'pstatus' => 5
        ));
    $set[] = 'numdels = ?';
    $bindvars[] = $numdels;

    $query = "UPDATE $topicstable";
    $query .= " SET " . join(',', $set);
    $query .= " WHERE id = ?";
    $bindvars[] = $tid;

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    if (!empty($nohooks)) return true;

    if (empty($topicstype)) {
        $topic = xarMod::apiFunc('crispbb', 'user', 'gettopic', array('tid' => $tid));
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