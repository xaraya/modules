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
function crispbb_userapi_getipsbyposter($args)
{
    extract($args);

    $sort = !empty($sort) ? $sort : '';
    $showstatus = !empty($showstatus) ? true : false;

    if (empty($numitems) || !is_numeric($numitems)) $numitems = 20;
    if (empty($startnum) || !is_numeric($startnum)) $startnum = 1;

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $forumstable = $xartable['crispbb_forums'];
    $topicstable = $xartable['crispbb_topics'];
    $poststable = $xartable['crispbb_posts'];
    $posterstable = $xartable['crispbb_posters'];
    $rolesdef = xarMod::apiFunc('roles', 'user', 'leftjoin');

    $select = array();
    $from = $poststable;
    $where = array();
    $groupby = array();
    $orderby = array();
    $bindvars = array();

    $fields = array('phostname','powner');
    $select[] = 'DISTINCT ' . $poststable . '.phostname';
    $select[] = $poststable . '.powner';
    if (!empty($uid) && is_numeric($uid)) {
        $where[] = $poststable . '.powner = ?';
        $bindvars[] = $uid;
    }
    // get user info from roles
    $rolesfields = array('name','uname','id','date_reg');
    foreach ($rolesfields as $rfield) {
        $select[] = $rolesdef['table'] . '.' . $rfield;
        $fields[] = $rfield;
    }
    // Add the LEFT JOIN ... ON ... roles for the poster info
    $from .= ' LEFT JOIN ' . $rolesdef['table'];
    $from .= ' ON ' . $rolesdef['table'] . '.id' . ' = ' . $poststable . '.powner';

    if ($dbconn->databaseType != 'sqlite') {
        $from = '(' . $from . ')';
    }
    // Add the LEFT JOIN ... ON ... roles for the session info
    $from .= ' LEFT JOIN ' . $posterstable . ' AS pcounts';
    $from .= ' ON pcounts.id = ' . $rolesdef['table'] . '.id';
    $select[] = 'pcounts.numtopics';
    $fields[] = 'numtopics';
    $select[] = 'pcounts.numreplies';
    $fields[] = 'numreplies';

    // get current status for this user (online|offline)
    if ($showstatus) {
        $now = time();
        if (empty($filter)){
            $filter = $now - (xarConfigVars::get(null,'Site.Session.InactivityTimeout') * 60);
        }
        $sessioninfoTable = $xartable['session_info'];
        if ($dbconn->databaseType != 'sqlite') {
            $from = '(' . $from . ')';
        }
        // Add the LEFT JOIN ... ON ... roles for the session info
        $from .= ' LEFT JOIN ' . $sessioninfoTable . ' AS session';
        $from .= ' ON session.id = ' . $rolesdef['table'] . '.id';
        $select[] = 'session.last_use';
        $from .= ' AND session.last_use > ' . $filter;
        $fields[] = 'lastseen';
    }

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
    }

    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    if (!$result) return;
    $posters = array();
    for (; !$result->EOF; $result->MoveNext()) {
        $data = $result->fields;
        $poster = array();
        foreach ($fields as $key => $field) {
            $value = array_shift($data);
            if ($field == 'lastseen') {
                if (!empty($value)) {
                    // TODO: make expire time configurable
                    $expired = $now - (15 * 60);
                    if ($value < $expired) {
                        $poster['online'] = 0;
                    } else {
                        $poster['online'] = 1;
                    }
                } else {
                    $poster['online'] = 0;
                }
            } elseif ($field == 'id') {
                $field = 'uid';
                $poster['powner'] = $value;
                $poster['towner'] = $value;
            }
            $poster[$field] = $value;
        }
        $posters[] = $poster;
    }
    return $posters;

}
?>