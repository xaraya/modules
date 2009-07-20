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
 *//**
 * Standard function to do something
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_userapi_getposters($args)
{
    extract($args);
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $forumstable = $xartable['crispbb_forums'];
    $topicstable = $xartable['crispbb_topics'];
    $poststable = $xartable['crispbb_posts'];
    //$hookstable = $xartable['crispbb_hooks'];

    $select = array();
    $where = array();
    $groupby = array();
    $orderby = array();
    $bindvars = array();

    $fields = array('powner', 'phostname');

    $select[] = 'DISTINCT ' . $poststable . '.xar_powner';
    $select[] = $poststable . '.xar_phostname';

    $from = $poststable;

    $rolesdef = xarModAPIFunc('roles', 'user', 'leftjoin');

    $rolesfields = array('name','uname','uid');
    foreach ($rolesfields as $rfield) {
        $select[] = $rolesdef['table'] . '.xar_' . $rfield;
        $fields[] = $rfield;
    }

    // Add the LEFT JOIN ... ON ... roles for the powner info
    $from .= ' LEFT JOIN ' . $rolesdef['table'];
    $from .= ' ON ' . $rolesdef['table'] . '.xar_uid' . ' = ' . $poststable . '.xar_powner';

    if ($dbconn->databaseType != 'sqlite') {
        $from = '(' . $from . ')';
    }
    $from .= ' LEFT JOIN ' . $topicstable . ' AS topics';
    $from .= ' ON topics.xar_towner' . ' = ' . $poststable . '.xar_powner';
    $select[] = 'COUNT(DISTINCT topics.xar_tid) AS numtopics';
    $fields[] = 'numtopics';
    $groupby[] = $poststable . '.xar_powner';
    if (isset($tstatus)) {
        if (is_numeric($tstatus)) {
            $from .= ' AND topics.xar_tstatus = ' . $tstatus;
        } elseif (is_array($tstatus) && count($tstatus) > 0) {
            $seentstatus = array();
            foreach ($tstatus as $id) {
                if (!is_numeric($id)) continue;
                $seentstatus[$id] = 1;
            }
            if (count($seentstatus) == 1) {
                $tstatuses = array_keys($seentstatus);
                $from .= ' AND topics.xar_tstatus = ' . $tstatuses[0];
            } elseif (count($seentstatus) > 1) {
                $tstatuses = join(', ', array_keys($seentstatus));
                $from .= ' AND topics.xar_tstatus IN (' . $tstatuses . ')';
            }
        }
    }

    if ($dbconn->databaseType != 'sqlite') {
        $from = '(' . $from . ')';
    }
    $from .= ' LEFT JOIN ' . $forumstable;
    $from .= ' ON ' . $forumstable . '.xar_fid = topics.xar_fid';
    if (!empty($fid)) {
        if (is_numeric($fid)) {
            $where[] = $forumstable . '.xar_fid = ' . $fid;
        } elseif (is_array($fid) && count($fid) > 0) {
            $seenfid = array();
            foreach ($fid as $id) {
                if (empty($id) || !is_numeric($id)) continue;
                $seenfid[$id] = 1;
            }
            if (count($seenfid) == 1) {
                $fids = array_keys($seenfid);
                $where[] = $forumstable . '.xar_fid = ' . $fids[0];
            } elseif (count($seenfid) > 1) {
                $fids = join(', ', array_keys($seenfid));
                $where[] = $forumstable . '.xar_fid IN (' . $fids . ')';
            }
        }
    }

    // Add the LEFT JOIN ... ON ... posts for the reply count
    $from .= ' LEFT JOIN ' . $poststable . ' AS posts';
    $from .= ' ON posts.xar_tid = topics.xar_tid';
    //$from .= ' AND ' . $topicstable . '.xar_fid = ' . $forumstable . '.xar_fid';
    $from .= ' AND posts.xar_pstatus IN (0,1)';
    $from .= ' AND topics.xar_firstpid != topics.xar_lastpid';
    $from .= ' AND topics.xar_firstpid != posts.xar_pid';
    $select[] = 'COUNT(DISTINCT posts.xar_pid) AS numreplies';
    $fields[] = 'numreplies';
    if (isset($tstatus)) {
        if (is_numeric($tstatus)) {
            $from .= ' AND topics.xar_tstatus = ' . $tstatus;
        } elseif (is_array($tstatus) && count($tstatus) > 0) {
            $seentstatus = array();
            foreach ($tstatus as $id) {
                if (!is_numeric($id)) continue;
                $seentstatus[$id] = 1;
            }
            if (count($seentstatus) == 1) {
                $tstatuses = array_keys($seentstatus);
                $from .= ' AND topics.xar_tstatus = ' . $tstatuses[0];
            } elseif (count($seentstatus) > 1) {
                $tstatuses = join(', ', array_keys($seentstatus));
                $from .= ' AND topics.xar_tstatus IN (' . $tstatuses . ')';
            }
        }
    }



    if (isset($sort)) {
        if ($sort == 'numtopics') {
            $orderby[] = 'numtopics DESC'; //'COUNT(DISTINCT topics.xar_tid) DESC';
        } elseif ($sort == 'numreplies') {
            $orderby[] = 'numreplies DESC'; //'COUNT(DISTINCT posts.xar_pid) DESC';
        }
    }

    if (isset($ip) && is_string($ip)) {
        $where[] = $poststable . '.xar_phostname LIKE ?';
        $bindvars[] = $ip;
    }

    if (isset($powner) && is_numeric($powner)) {
        $where[] = $poststable . '.xar_powner = ?';
        $bindvars[] = $powner;
    }

    if (empty($numitems) || !is_numeric($numitems)) $numitems = 20;
    if (empty($startnum) || !is_numeric($startnum)) $startnum = 1;

    $query = 'SELECT ' . join(', ', $select);
    $query .= ' FROM ' . $from;
    if (!empty($where)) {
        $query .= ' WHERE ' . join(' AND ', $where);
    }
    if (!empty($groupby)) {
        $query .= ' GROUP BY '.join(',', $groupby);
    }
    if (!empty($orderby)) {
        $query .= ' ORDER BY '.join(',', $orderby);
    } else {
        $query .= ' ORDER BY ' . $rolesdef['table'] . '.xar_name ASC';
    }
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    if (!$result) return;
    $posters = array();
    for (; !$result->EOF; $result->MoveNext()) {
        $data = $result->fields;
        $poster = array();
        foreach ($fields as $key => $field) {
            $value = array_shift($data);
            $poster[$field] = $value;
        }
        $posters[] = $poster;
    }
    return $posters;

}
?>