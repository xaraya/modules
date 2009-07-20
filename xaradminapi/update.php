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
function crispbb_adminapi_update($args)
{
    extract($args);
    $invalid = array();

    if (!isset($fid) || empty($fid) || !is_numeric($fid)) {
      $invalid[] = 'fid';
    }

    if (isset($fname)) {
        if (!is_string($fname) || empty($fname) || strlen($fname) > 100) {
            $invalid[] = 'fname';
        }
    }

    if (isset($fdesc)) {
        if (!is_string($fdesc) || strlen($fdesc) > 255) {
            $invalid[] = 'fdesc';
        }
    }

    if (isset($fstatus)) {
        if (!is_numeric($fstatus)) {
            $invalid[] = 'fstatus';
        }
    }

    if (isset($fowner)) {
        if (!is_numeric($fowner) || empty($fowner)) {
            $invalid[] = 'fowner';
        }
    }

    if (isset($forder)) {
        if (!is_numeric($forder)) {
            $invalid[] = 'forder';
        }
    }

    if (isset($fsettings)) {
        if (empty($fsettings) || !is_array($fsettings)) {
            $invalid[] = 'fsettings';
        }
    }

    if (isset($fprivileges)) {
        if (empty($fprivileges) || !is_array($fprivileges)) {
            $invalid[] = 'fprivileges';
        }
    }

    if (isset($lasttid)) {
        if (!is_numeric($lasttid)) {
            $invalid[] = 'lasttid';
        }
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'adminapi', 'update', 'crispBB');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return $msg;
    }

    if (!empty($cids) && count($cids) > 0) {
        $cids = array_values(preg_grep('/\d+/',$cids));
    } elseif (!empty($catid) && is_numeric($catid)) {
        $cids = array($catid);
    } else {
        $cids = array();
    }

    if (empty($cids)) {
        // never call hooks if cids are empty
        $nohooks = true;
    } else {
        $nohooks = false;
        // we only accept cids from admin modify function
        // so we make sure user has admin privileges to update the forum
        foreach ($cids as $cid) {
            if (empty($cid)) continue;
            $foundcid = $cid;
            break;
        }
        $forumLevel = xarModAPIFunc('crispbb', 'user', 'getseclevel', array('fid' => $fid, 'catid' => $foundcid));
        if ($forumLevel < 600) { // gotta have at least edit (600) privs
            $allowed = false;
        } elseif ($forumLevel < 800) { // has edit or delete privs
            $forum = xarModAPIFunc('crispbb', 'user', 'getforum', array('fid' => $fid));
            $privs = $forum['fprivileges'][$forumLevel];
            // has edit or delete privs for this forum
            if (!empty($privs['editforum']) || !empty($privs['deleteforum'])) {
                $allowed = true;
            } else {
                $allowed = false;
            }
        } else { // must be a forum admin (800)
            $allowed = true;
        }
        if (!$allowed) { // No privs to update forum
            $errorMsg['message'] = xarML('You do not have the privileges required for this action');
            // $errorMsg['return_url'] = empty($catid) ? xarServerGetBaseURL() : xarModURL('crispbb', 'user', 'main');
            $errorMsg['type'] = 'NO_PRIVILEGES';
            $errorMsg['pageTitle'] = xarML('No Privileges');
            xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
            return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
        }
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $forumstable = $xartable['crispbb_forums'];
    $set = array();
    $bindvars = array();
    if (isset($fname)) {
        $set[] = 'xar_fname = ?';
        $bindvars[] = $fname;
    }
    if (isset($fdesc)) {
        $set[] = 'xar_fdesc = ?';
        $bindvars[] = $fdesc;
    }
    if (isset($fstatus)) {
        $set[] = 'xar_fstatus = ?';
        $bindvars[] = $fstatus;
    }
    if (isset($fowner)) {
        $set[] = 'xar_fowner = ?';
        $bindvars[] = $fowner;
    }

    if (isset($forder)) {
        $set[] = 'xar_forder = ?';
        $bindvars[] = $forder;
    }

    if (isset($fsettings)) {
        $set[] = 'xar_fsettings = ?';
        $bindvars[] = serialize($fsettings);
    }

    if (isset($fprivileges)) {
        $set[] = 'xar_fprivileges = ?';
        $bindvars[] = serialize($fprivileges);
    }

    if (isset($lasttid)) {
        $set[] = 'xar_lasttid = ?';
        $bindvars[] = $lasttid;
    }

    $query = "UPDATE $forumstable";
    $query .= " SET " . join(',', $set);
    $query .= " WHERE xar_fid = ?";
    $bindvars[] = $fid;

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    if (!empty($nohooks)) return true;

    if (empty($itemtype)) {
        $itemtype = xarModAPIFunc('crispbb', 'user', 'getitemtype',
            array('fid' => $fid, 'component' => 'forum'));
    }

    $args['module'] = 'crispbb';
    $args['itemid'] = $fid;
    $args['cids'] = $cids;
    $args['itemtype'] = $itemtype;
    xarModCallHooks('item', 'update', $fid, $args);

    return true;
}
?>