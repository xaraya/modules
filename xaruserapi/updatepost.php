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
function crispbb_userapi_updatepost($args)
{
    extract($args);
    $invalid = array();
    if (!isset($pid) || empty($pid) || !is_numeric($pid)) {
      $invalid[] = 'pid';
    }

    if (isset($tid)) {
        if (!is_numeric($tid) || empty($tid)) {
            $invalid[] = 'tid';
        }
    }

    if (isset($pdesc)) {
        if (!is_string($pdesc) || strlen($pdesc) > 255) {
            $invalid[] = 'pdesc';
        }
    }

    if (isset($ptext)) {
        if (empty($ptext) || !is_string($ptext)) {
            $invalid[] = 'ptext';
        }
    }

    if (isset($pstatus)) {
        if (!is_numeric($pstatus)) {
            $invalid[] = 'pstatus';
        }
    }

    if (isset($powner)) {
        if (!is_numeric($powner) || empty($powner)) {
            $invalid[] = 'powner';
        }
    }

    if (isset($poststype)) {
        if (!is_numeric($poststype) || empty($poststype)) {
            $invalid[] = 'poststype';
        }
    }

    if (isset($psettings)) {
        if (empty($psettings) || !is_array($psettings)) {
            $invalid[] = 'psettings';
        }
    }

    if (isset($phostname)) {
        if (empty($phostname) || !is_string($phostname)) {
            $invalid[] = 'phostname';
        }
    }
    if (isset($ptime)) {
        if (empty($ptime) || !is_numeric($ptime)) {
            $invalid[] = 'ptime';
        }
    }

    if (count($invalid) > 0) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array(join(', ', $invalid), 'userapi', 'updatepost', 'crispBB');
        throw new BadParameterException($vars, $msg);
        return;
    }

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $poststable = $xartable['crispbb_posts'];
    $set = array();
    $bindvars = array();
    if (isset($tid)) {
        $set[] = 'tid = ?';
        $bindvars[] = $tid;
    }
    if (isset($pdesc)) {
        $set[] = 'pdesc = ?';
        $bindvars[] = $pdesc;
    }
    if (isset($pstatus)) {
        $set[] = 'pstatus = ?';
        $bindvars[] = $pstatus;
    }
    if (isset($powner)) {
        $set[] = 'powner = ?';
        $bindvars[] = $powner;
    }

    if (isset($ptext)) {
        $set[] = 'ptext = ?';
        $bindvars[] = $ptext;
    }

    if (isset($psettings)) {
        $set[] = 'psettings = ?';
        $bindvars[] = serialize($psettings);
    }

    if (isset($poststype)) {
        $set[] = 'poststype = ?';
        $bindvars[] = $poststype;
    }

    if (isset($phostname)) {
        $set[] = 'phostname = ?';
        $bindvars[] = $phostname;
    }

    if (isset($ptime)) {
        $set[] = 'ptime = ?';
        $bindvars[] = $ptime;
    }

    $query = "UPDATE $poststable";
    $query .= " SET " . join(',', $set);
    $query .= " WHERE id = ?";
    $bindvars[] = $pid;

    $result = $dbconn->Execute($query,$bindvars);

    if (!$result) return;

    if (!empty($nohooks)) return true;

    if (empty($poststype)) {
        $post = xarMod::apiFunc('crispbb', 'user', 'getpost',
            array('pid' => $pid));
        $poststype = $post['poststype'];
    }

    $args['module'] = 'crispbb';
    $args['itemtype'] = $poststype;
    $args['itemid'] = $pid;
    xarModHooks::call('item', 'update', $pid, $args);

    return true;
}
?>