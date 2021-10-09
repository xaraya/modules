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
 * Standard function to do something
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_userapi_getposts($args)
{
    extract($args);
    $startnum ??= 1;
    $numitems ??= -1;
    if (empty($cids) && !empty($catid)) {
        $cids = [$catid];
    }
    if (empty($cids)) {
        $cids = [];
    }

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $forumstable = $xartable['crispbb_forums'];
    $topicstable = $xartable['crispbb_topics'];
    $poststable = $xartable['crispbb_posts'];

    $postsfields = ['id', 'tid', 'powner','ptime','pstatus','poststype','psettings', 'pdesc', 'ptext', 'phostname'];
    $topicfields = ['id', 'tstatus', 'towner', 'ttype', 'topicstype','ttitle', 'tsettings', 'firstpid'];
    $forumfields = ['id','fname', 'fdesc', 'fstatus', 'fsettings', 'fprivileges'];

    $select = [];
    $where = [];
    $orderby = [];
    $bindvars = [];
    $fields = [];
    foreach ($postsfields as $k => $fieldname) {
        $select[] = $poststable . '.' . $fieldname;
        $fieldname = $fieldname == 'id' ? 'pid' : $fieldname;
        $fields[] = $fieldname;
    }
    $from = $poststable;

    foreach ($topicfields as $k => $fieldname) {
        $select[] = $topicstable . '.' . $fieldname;
        $fieldname = $fieldname == 'id' ? 'tid' : $fieldname;
        $fields[] = $fieldname;
    }

    $from .= ' LEFT JOIN ' . $topicstable;
    $from .= ' ON ' . $topicstable . '.id' . ' = ' . $poststable . '.tid';
    $addme = 1;

    foreach ($forumfields as $k => $fieldname) {
        $select[] = $forumstable . '.' . $fieldname;
        $fieldname = $fieldname == 'id' ? 'fid' : $fieldname;
        $fields[] = $fieldname;
    }
    if ($addme && ($dbconn->databaseType != 'sqlite')) {
        $from = '(' . $from . ')';
    }
    $from .= ' LEFT JOIN ' . $forumstable;
    $from .= ' ON ' . $forumstable . '.id' . ' = ' . $topicstable . '.fid';

    $itemtypestable = $xartable['crispbb_itemtypes'];
    $select[] = $itemtypestable . '.id';
    $fields[] = 'forumtype';
    if ($addme && ($dbconn->databaseType != 'sqlite')) {
        $from = '(' . $from . ')';
    }
    $from .= ' LEFT JOIN ' . $itemtypestable;
    $from .= ' ON ' . $itemtypestable . '.fid' . ' = ' . $forumstable . '.id';
    $from .= ' AND ' . $itemtypestable . '.component = "forum"';

    $categoriesdef = xarMod::apiFunc(
        'categories',
        'user',
        'leftjoin',
        ['cids' => $cids, 'modid' => xarMod::getRegID('crispbb')]
    );
    $addme = 0;
    if (!empty($categoriesdef)) {
        $select[] = $categoriesdef['category_id'];
        $fields[] = 'catid';
        $from .= ' LEFT JOIN ' . $categoriesdef['table'];
        $from .= ' ON ' . $categoriesdef['field'] . ' = ' . $forumstable . '.id';
        $addme = 1;
        if (!empty($categoriesdef['more']) && ($dbconn->databaseType != 'sqlite')) {
            $from = '(' . $from . ')';
            $from .= $categoriesdef['more'];
        }
    }
    if (!empty($categoriesdef['where'])) {
        $where[] = $categoriesdef['where'];
    }

    if (!empty($pid)) {
        if (is_numeric($pid)) {
            $where[] = $poststable . '.id = ' . $pid;
        } elseif (is_array($pid) && count($pid) > 0) {
            $seenpid = [];
            foreach ($pid as $id) {
                if (empty($id) || !is_numeric($id)) {
                    continue;
                }
                $seenpid[$id] = 1;
            }
            if (count($seenpid) == 1) {
                $pids = array_keys($seenpid);
                $where[] = $poststable . '.id = ' . $pids[0];
            } elseif (count($seenpid) > 1) {
                $pids = join(', ', array_keys($seenpid));
                $where[] = $poststable . '.id IN (' . $pids . ')';
            }
        }
    }

    if (isset($pstatus)) {
        if (is_numeric($pstatus)) {
            $where[] = $poststable . '.pstatus = ' . $pstatus;
        } elseif (is_array($pstatus) && count($pstatus) > 0) {
            $seenpstatus = [];
            foreach ($pstatus as $id) {
                if (!is_numeric($id)) {
                    continue;
                }
                $seenpstatus[$id] = 1;
            }
            if (count($seenpstatus) == 1) {
                $pstatuses = array_keys($seenpstatus);
                $where[] = $poststable . '.pstatus = ' . $pstatuses[0];
            } elseif (count($seenpstatus) > 1) {
                $pstatuses = join(', ', array_keys($seenpstatus));
                $where[] = $poststable . '.pstatus IN (' . $pstatuses . ')';
            }
        }
    }

    if (!empty($tid)) {
        if (is_numeric($tid)) {
            $where[] = $topicstable . '.id = ' . $tid;
        } elseif (is_array($tid) && count($tid) > 0) {
            $seentid = [];
            foreach ($tid as $id) {
                if (empty($id) || !is_numeric($id)) {
                    continue;
                }
                $seentid[$id] = 1;
            }
            if (count($seentid) == 1) {
                $tids = array_keys($seentid);
                $where[] = $topicstable . '.id = ' . $tids[0];
            } elseif (count($seentid) > 1) {
                $tids = join(', ', array_keys($seentid));
                $where[] = $topicstable . '.id IN (' . $tids . ')';
            }
        }
    }

    if (isset($tstatus)) {
        if (is_numeric($tstatus)) {
            $where[] = $topicstable . '.tstatus = ' . $tstatus;
        } elseif (is_array($tstatus) && count($tstatus) > 0) {
            $seentstatus = [];
            foreach ($tstatus as $id) {
                if (!is_numeric($id)) {
                    continue;
                }
                $seentstatus[$id] = 1;
            }
            if (count($seentstatus) == 1) {
                $tstatuses = array_keys($seentstatus);
                $where[] = $topicstable . '.tstatus = ' . $tstatuses[0];
            } elseif (count($seentstatus) > 1) {
                $tstatuses = join(', ', array_keys($seentstatus));
                $where[] = $topicstable . '.tstatus IN (' . $tstatuses . ')';
            }
        }
    }
    if (isset($ttype)) {
        if (is_numeric($ttype)) {
            $where[] = $topicstable . '.ttype = ' . $ttype;
        } elseif (is_array($ttype) && count($ttype) > 0) {
            $seenttype = [];
            foreach ($ttype as $id) {
                if (!is_numeric($id)) {
                    continue;
                }
                $seenttype[$id] = 1;
            }
            if (count($seenttype) == 1) {
                $ttypes = array_keys($seenttype);
                $where[] = $topicstable . '.ttype = ' . $ttypes[0];
            } elseif (count($seenttype) > 1) {
                $ttypes = join(', ', array_keys($seenttype));
                $where[] = $topicstable . '.ttype = IN (' . $ttypes . ')';
            }
        }
    }

    if (!empty($fid)) {
        if (is_numeric($fid)) {
            $where[] = $forumstable . '.id = ' . $fid;
        } elseif (is_array($fid) && count($fid) > 0) {
            $seenfid = [];
            foreach ($fid as $id) {
                if (empty($id) || !is_numeric($id)) {
                    continue;
                }
                $seenfid[$id] = 1;
            }
            if (count($seenfid) == 1) {
                $fids = array_keys($seenfid);
                $where[] = $forumstable . '.id = ' . $fids[0];
            } elseif (count($seenfid) > 1) {
                $fids = join(', ', array_keys($seenfid));
                $where[] = $forumstable . '.id IN (' . $fids . ')';
            }
        }
    }
    if (isset($fstatus)) {
        if (is_numeric($fstatus)) {
            $where[] = $forumstable . '.fstatus = ' . $fstatus;
        } elseif (is_array($fstatus) && count($fstatus) > 0) {
            $seenfstatus = [];
            foreach ($fstatus as $id) {
                if (empty($id) || !is_numeric($id)) {
                    continue;
                }
                $seenfstatus[$id] = 1;
            }
            if (count($seenfstatus) == 1) {
                $fstatuses = array_keys($seenfstatus);
                $where[] = $forumstable . '.fstatus = ' . $fstatuses[0];
            } elseif (count($seenfstatus) > 1) {
                $fstatuses = join(', ', array_keys($seenfstatus));
                $where[] = $forumstable . '.fstatus IN (' . $fstatuses . ')';
            }
        }
    }
    if (isset($ftype)) {
        if (is_numeric($ftype)) {
            $where[] = $forumstable . '.ftype = ' . $ftype;
        } elseif (is_array($ftype) && count($ftype) > 0) {
            $seenftype = [];
            foreach ($ftype as $id) {
                if (empty($id) || !is_numeric($id)) {
                    continue;
                }
                $seenftype[$id] = 1;
            }
            if (count($seenftype) == 1) {
                $ftypes = array_keys($seenftype);
                $where[] = $forumstable . '.ftype = ' . $ftypes[0];
            } elseif (count($seenftype) > 1) {
                $ftypes = join(', ', array_keys($seenftype));
                $where[] = $forumstable . '.ftype IN (' . $ftypes . ')';
            }
        }
    }
    $rolesdef = xarMod::apiFunc('roles', 'user', 'leftjoin');
    $rolesfields = ['name','uname','id'];
    foreach ($rolesfields as $rfield) {
        $select[] = $rolesdef['table'] . '.' . $rfield;
        $rfield = $rfield == 'id' ? 'uid' : $rfield;
        $fields[] = 'powner'.$rfield;
    }
    if (($dbconn->databaseType != 'sqlite')) {
        $from = '(' . $from . ')';
    }
    // Add the LEFT JOIN ... ON ... roles for the towner info
    $from .= ' LEFT JOIN ' . $rolesdef['table'];
    $from .= ' ON ' . $rolesdef['table'] . '.id' . ' = ' . $poststable . '.powner';

    if (!empty($powner) && is_numeric($powner)) {
        $where[] = $poststable . '.powner = ?';
        $bindvars[] = $powner;
    }

    if (!empty($towner) && is_numeric($towner)) {
        $where[] = $topicstable . '.towner = ?';
        $bindvars[] = $towner;
    }

    if (!empty($author) && is_numeric($author)) {
        $where[] = '(' . $topicstable . '.towner = ? OR ' . $poststable . '.powner = ?)';
        $bindvars[] = $author;
        $bindvars[] = $author;
    }

    if (isset($starttime) && is_numeric($starttime)) {
        $where[] = $poststable.".ptime >= ?";
        $bindvars[] = $starttime;
    }
    if (isset($endtime) && is_numeric($endtime)) {
        $where[] = $poststable.".ptime <= ?";
        $bindvars[] = $endtime;
    }

    if (!empty($sort)) {
        if (in_array($sort, $topicfields)) {
            $myorder = $topicstable . '.' . $sort;
        } elseif (in_array($sort, $postsfields)) {
            if ($sort == 'powner') {
                $myorder = $rolesdef['table'] . '.name';
            } else {
                $myorder = $poststable . '.' . $sort;
            }
        } elseif (in_array($sort, $forumfields)) {
            $myorder = $forumstable . '.' . $sort;
        }
        if (!empty($order)) {
            $myorder .= ' ' . strtoupper($order) . ' ';
        }
        if (!empty($myorder)) {
            $orderby[] = $myorder;
        }
    }

    if (!empty($q)) {
        $search = $q;
        // TODO : improve + make use of full-text indexing for recent MySQL versions ?
        if (empty($searchfields)) {
            $searchfields = [];
        }

        $normal = [];
        $find = [];

        // 0. Check for "'equal whole string' searchType"
        if (!empty($searchtype) && $searchtype == 'equal whole string') {
            $normal[] = $search;
            $search   = "";
            $searchtype = 'eq';
        }

        // 0. Check for fulltext or fulltext boolean searchtypes (MySQL only)
        // CHECKME: switch to other search type if $search is less than min. length ?
        if (!empty($searchtype) && substr($searchtype, 0, 8) == 'fulltext') {
            $fulltext = xarModVars::get('articles', 'fulltextsearch');
            if (!empty($fulltext)) {
                $fulltextfields = explode(',', $fulltext);
            } else {
                $fulltextfields = [];
            }
            $matchfields = [];
            foreach ($fulltextfields as $field) {
                if (empty($leftjoin[$field])) {
                    continue;
                }
                $matchfields[] = $leftjoin[$field];
            }
            // TODO: switch mode automatically if + - etc. are detected ?
            $matchmode = '';
            if ($searchtype == 'fulltext boolean') {
                $matchmode = ' IN BOOLEAN MODE';
            }
            $find[] = 'MATCH (' . join(', ', $matchfields) . ') AGAINST (' . $dbconn->qstr($search) . $matchmode . ')';
            // Add this to field list too when sorting by relevance in boolean mode (cfr. getall() sort)
            $leftjoin['relevance'] = 'MATCH (' . join(', ', $matchfields) . ') AGAINST (' . $dbconn->qstr($search) . $matchmode . ') AS relevance';

            // check if we have any other fields to search in
            $morefields = array_diff($searchfields, $fulltextfields);
            if (!empty($morefields)) {
                // FIXME: sort order may not be by relevance if we mix fulltext with other searches
                $searchfields = $morefields;
                $searchtype = '';
            } else {
                // we're done here
                $searchfields = [];
                $search = '';
            }
        }

        // 1. find quoted text
        if (preg_match_all('#"(.*?)"#', $search, $matches)) {
            foreach ($matches[1] as $match) {
                $normal[] = $match;
                $match = preg_quote($match);
                $search = trim(preg_replace("#\"$match\"#", '', $search));
            }
        }
        if (preg_match_all("/'(.*?)'/", $search, $matches)) {
            foreach ($matches[1] as $match) {
                $normal[] = $match;
                $match = preg_quote($match);
                $search = trim(preg_replace("#'$match'#", '', $search));
            }
        }

        // 2. find mandatory +text to include
        // 3. find mandatory -text to exclude
        // 4. find normal text
        $more = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
        $normal = array_merge($normal, $more);
        foreach ($normal as $text) {
            // TODO: use XARADODB to escape wildcards (and use portable ones) ??
            $text = str_replace('%', '\%', $text);
            $text = str_replace('_', '\_', $text);
            foreach ($searchfields as $field) {
                if ($field == 'ttitle') {
                    $searchfield = $topicstable . '.ttitle';
                } elseif ($field == 'pdesc') {
                    $searchfield = $poststable . '.pdesc';
                } elseif ($field == 'ptext') {
                    $searchfield = $poststable . '.ptext';
                }
                if (empty($searchfield)) {
                    continue;
                }
                if (empty($searchtype) || $searchtype == 'like') {
                    $find[] = $searchfield . " LIKE " . $dbconn->qstr('%' . $text . '%');
                } elseif ($searchtype == 'start') {
                    $find[] = $searchfield . " LIKE " . $dbconn->qstr($text . '%');
                } elseif ($searchtype == 'end') {
                    $find[] = $searchfield . " LIKE " . $dbconn->qstr('%' . $text);
                } elseif ($searchtype == 'eq') {
                    $find[] = $searchfield . " = " . $dbconn->qstr($text);
                } else {
                    // TODO: other search types ?
                    $find[] = $searchfield . " LIKE " . $dbconn->qstr('%' . $text . '%');
                }
                unset($searchfield);
            }
        }
        if (!empty($find)) {
            $where[] = '(' . join(' OR ', $find) . ')';
        }
    }


    $query = 'SELECT ' . join(', ', $select);
    $query .= ' FROM ' . $from;
    if (!empty($where)) {
        $query .= ' WHERE ' . join(' AND ', $where);
    }
    if (empty($groupby) || $groupby == 'replies') {
        $query .= ' GROUP BY ' . $poststable . '.id';
    } else {
        $query .= ' GROUP BY ' . $poststable . '.tid';
    }
    if (!empty($orderby)) {
        $query .= ' ORDER BY ' . join(',', $orderby);
    } else {
        $query .= ' ORDER BY ' . $poststable . '.ptime ASC';
    }

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    if (!$result) {
        return;
    }
    $posts = [];
    $uid = xarUser::getVar('id');
    $loggedin = xarUser::isLoggedIn();
    $checkfailed = false;
    // module defaults
    $presets = xarMod::apiFunc(
        'crispbb',
        'user',
        'getpresets',
        ['preset' => 'fsettings,fprivileges,ftransfields,ttransfields,ptransfields']
    );
    for (; !$result->EOF; $result->MoveNext()) {
        $data = $result->fields;
        $post = [];
        foreach ($fields as $key => $field) {
            $value = array_shift($data);
            if ($field == 'fsettings') {
                $fsettings = unserialize($value);
                // add in any new presets from defaults
                foreach ($presets['fsettings'] as $p => $pv) {
                    if (!isset($fsettings[$p])) {
                        $fsettings[$p] = $pv;
                    }
                }
                foreach ($fsettings as $k => $v) {
                    // remove any settings not in defaults
                    if (!isset($presets['fsettings'][$k])) {
                        continue;
                    }
                    $post[$k] = $v;
                }
                continue;
            } elseif ($field == 'tsettings') {
                $value = unserialize($value);
            } elseif ($field == 'psettings') {
                $value = unserialize($value);
                foreach ($value as $k => $v) {
                    $post[$k] = $v;
                }
            } elseif ($field == 'fprivileges') {
                $fprivileges = unserialize($value);
                // add in any new presets from defaults
                foreach ($presets['fprivileges'] as $level => $actions) {
                    foreach ($actions as $action => $value) {
                        if (!isset($fprivileges[$level][$action])) {
                            $fprivileges[$level][$action] = $value;
                        }
                    }
                }
                // remove any settings not in defaults
                foreach ($fprivileges as $level => $actions) {
                    foreach ($actions as $action => $value) {
                        if (!isset($presets['fprivileges'][$level][$action])) {
                            unset($fprivileges[$level][$action]);
                        }
                    }
                }

                $value = $fprivileges;
            }
            $post[$field] = $value;
        }
        $post['catid'] =2;
        if (!$secLevel = xarMod::apiFunc(
            'crispbb',
            'user',
            'checkseclevel',
            ['check' => $post, 'priv' => 'readforum']
        )) {
            $checkfailed = true;
            continue;
        }
        if (!xarMod::apiFunc(
            'crispbb',
            'user',
            'checkseclevel',
            ['check' => $post, 'priv' => 'adminforum']
        )) {
            $post['phostname'] = '';
        }
        if ($post['pid'] == $post['firstpid']) { // topic
            $post['itemtype'] = $post['topicstype'];
            $post['itemid'] = $post['tid'];
        } else {
            $post['itemtype'] = $post['poststype'];
            $post['itemid'] = $post['pid'];
        }
        $post['forumLevel'] = $secLevel;
        // add privs for current user level in this forum
        $post['privs'] = $post['fprivileges'][$secLevel];
        if (empty($nolinks)) { // skip links (when called by shorturl encode this is necessary)
            // topic readers
            $post['viewtopicurl'] = xarController::URL(
                'crispbb',
                'user',
                'display',
                ['tid' => $post['tid']]
            );
            $post['viewreplyurl'] = xarController::URL(
                'crispbb',
                'user',
                'display',
                ['tid' => $post['tid'], 'pid' => $post['pid']]
            );
            $post['displayreplyurl'] = xarController::URL(
                'crispbb',
                'user',
                'displayreply',
                ['pid' => $post['pid']]
            );
            // logged in users
            if ($loggedin) {
                // topic (first post)
                if ($post['pid'] == $post['firstpid']) {
                    $tids = [];
                    $tids[$post['tid']] = 1;
                    // topic editors
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $post, 'priv' => 'edittopics']
                    )) {
                        $post['edittopicurl'] = xarController::URL(
                            'crispbb',
                            'user',
                            'modifytopic',
                            ['tid' => $post['tid']]
                        );
                    }
                    // topic closers
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $post, 'priv' => 'closetopics']
                    )) {
                        if ($post['tstatus'] == 1) {
                            $post['opentopicurl'] = xarController::URL(
                                'crispbb',
                                'user',
                                'moderate',
                                [
                                    'component' => 'topics',
                                    'fid' => $post['fid'],
                                    'tstatus' => $post['tstatus'],
                                    'modaction' => 'open',
                                    'phase' => 'update',
                                    'tids' => implode(',', array_keys($tids)),
                            ]
                            );
                        } else {
                            $post['closetopicurl'] = xarController::URL(
                                'crispbb',
                                'user',
                                'moderate',
                                [
                                    'component' => 'topics',
                                    'fid' => $post['fid'],
                                    'tstatus' => $post['tstatus'],
                                    'modaction' => 'close',
                                    'phase' => 'update',
                                    'tids' => implode(',', array_keys($tids)),
                            ]
                            );
                        }
                    }
                    // topic approvers
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $post, 'priv' => 'approvetopics']
                    )) {
                        if ($post['tstatus'] == 2) {
                            $post['approvetopicurl'] = xarController::URL(
                                'crispbb',
                                'user',
                                'moderate',
                                [
                                    'component' => 'topics',
                                    'fid' => $post['fid'],
                                    'tstatus' => $post['tstatus'],
                                    'modaction' => 'approve',
                                    'phase' => 'update',
                                    'tids' => implode(',', array_keys($tids)),
                            ]
                            );
                        }
                    }
                    // topic movers
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $post, 'priv' => 'movetopics']
                    )) {
                        $post['movetopicurl'] = xarController::URL(
                            'crispbb',
                            'user',
                            'moderate',
                            [
                                        'component' => 'topics',
                                        'fid' => $post['fid'],
                                        'modaction' => 'move',
                                        'tids' => implode(',', array_keys($tids)),
                                ]
                        );
                    }
                    // topic splitters
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $post, 'priv' => 'splittopics']
                    )) {
                        $post['splittopicurl'] = xarController::URL(
                            'crispbb',
                            'user',
                            'moderate',
                            [
                                        'component' => 'posts',
                                        'tid' => $post['tid'],
                                        //'pstatus' => $topic['pstatus'],
                                        //'modaction' => 'split',
                                        //'phase' => 'update',
                                ]
                        );
                    }
                    // topic lockers
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $post, 'priv' => 'locktopics']
                    )) {
                        if ($post['tstatus'] == 4) {
                            $post['unlocktopicurl'] = xarController::URL(
                                'crispbb',
                                'user',
                                'moderate',
                                [
                                    'component' => 'topics',
                                    'fid' => $post['fid'],
                                    'tstatus' => $post['tstatus'],
                                    'modaction' => 'unlock',
                                    'phase' => 'update',
                                    'tids' => implode(',', array_keys($tids)),
                            ]
                            );
                        } else {
                            $post['locktopicurl'] = xarController::URL(
                                'crispbb',
                                'user',
                                'moderate',
                                [
                                    'component' => 'topics',
                                    'fid' => $post['fid'],
                                    'tstatus' => $post['tstatus'],
                                    'modaction' => 'lock',
                                    'phase' => 'update',
                                    'tids' => implode(',', array_keys($tids)),
                            ]
                            );
                        }
                    }
                    // topic deleters
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $post, 'priv' => 'deletetopics']
                    )) {
                        if ($post['tstatus'] == 5) {
                            $post['undeletetopicurl'] = xarController::URL(
                                'crispbb',
                                'user',
                                'moderate',
                                [
                                    'component' => 'topics',
                                    'fid' => $post['fid'],
                                    'tstatus' => $post['tstatus'],
                                    'modaction' => 'undelete',
                                    'phase' => 'update',
                                    'tids' => implode(',', array_keys($tids)),
                            ]
                            );
                        } else {
                            $post['deletetopicurl'] = xarController::URL(
                                'crispbb',
                                'user',
                                'moderate',
                                [
                                    'component' => 'topics',
                                    'fid' => $post['fid'],
                                    'tstatus' => $post['tstatus'],
                                    'modaction' => 'delete',
                                    'phase' => 'update',
                                    'tids' => implode(',', array_keys($tids)),
                            ]
                            );
                        }
                    }
                    // forum moderators
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $post, 'priv' => 'ismoderator']
                    )) {
                        $post['modforumurl'] = xarController::URL(
                            'crispbb',
                            'user',
                            'moderate',
                            ['component' => 'topics', 'fid' => $post['fid']]
                        );
                        $post['modtopicurl'] = xarController::URL(
                            'crispbb',
                            'user',
                            'moderate',
                            ['component' => 'posts', 'tid' => $post['tid']]
                        );
                    }
                    // forum editors
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $post, 'priv' => 'editforum']
                    )) {
                        $post['purgetopicurl'] = xarController::URL(
                            'crispbb',
                            'user',
                            'moderate',
                            [
                                    'component' => 'topics',
                                    'fid' => $post['fid'],
                                    'tstatus' => $post['tstatus'],
                                    'modaction' => 'purge',
                                    'phase' => 'update',
                                    'tids' => implode(',', array_keys($tids)),
                            ]
                        );
                    }
                } else {
                    // post editors
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $post, 'priv' => 'editreplies']
                    )) {
                        $post['editreplyurl'] = xarController::URL(
                            'crispbb',
                            'user',
                            'modifyreply',
                            ['pid' => $post['pid']]
                        );
                    }
                    // post approvers
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $post, 'priv' => 'approvereplies']
                    ) && $post['pstatus'] == 2) {
                        $post['approvereplyurl'] = xarController::URL(
                            'crispbb',
                            'user',
                            'moderate',
                            [
                                    'component' => 'posts',
                                    'tid' => $post['tid'],
                                    'pstatus' => $post['pstatus'],
                                    'modaction' => 'approve',
                                    'phase' => 'update',
                                    'pids' => $post['pid'],
                                ]
                        );
                    }
                    // topic splitters
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $post, 'priv' => 'splittopics']
                    )) {
                        $post['splitreplyurl'] = xarController::URL(
                            'crispbb',
                            'user',
                            'moderate',
                            [
                                        'component' => 'posts',
                                        'tid' => $post['tid'],
                                        'pstatus' => $post['pstatus'],
                                        'modaction' => 'split',
                                        'phase' => 'update',
                                        'pids' => $post['pid'],
                                ]
                        );
                    }
                    // post deleters
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $post, 'priv' => 'deletereplies']
                    )) {
                        if ($post['pstatus'] == 5) {
                            $post['undeletereplyurl'] = xarController::URL(
                                'crispbb',
                                'user',
                                'moderate',
                                [
                                        'component' => 'posts',
                                        'tid' => $post['tid'],
                                        'pstatus' => $post['pstatus'],
                                        'modaction' => 'undelete',
                                        'phase' => 'update',
                                        'pids' => $post['pid'],
                                ]
                            );
                        } else {
                            $post['deletereplyurl'] = xarController::URL(
                                'crispbb',
                                'user',
                                'moderate',
                                [
                                        'component' => 'posts',
                                        'tid' => $post['tid'],
                                        'pstatus' => $post['pstatus'],
                                        'modaction' => 'delete',
                                        'phase' => 'update',
                                        'pids' => $post['pid'],
                                ]
                            );
                        }
                    }
                    // forum moderators
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $post, 'priv' => 'ismoderator']
                    )) {
                        $post['modtopicurl'] = xarController::URL(
                            'crispbb',
                            'user',
                            'moderate',
                            ['component' => 'posts', 'tid' => $post['tid']]
                        );
                    }
                    // forum editors
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $post, 'priv' => 'editforum']
                    )) {
                        $post['purgereplyurl'] = xarController::URL(
                            'crispbb',
                            'user',
                            'moderate',
                            [
                                        'component' => 'posts',
                                        'tid' => $post['tid'],
                                        'pstatus' => $post['pstatus'],
                                        'modaction' => 'purge',
                                        'phase' => 'update',
                                        'pids' => $post['pid'],
                                ]
                        );
                    }
                }
            }
        }
        foreach ($presets['ftransfields'] as $field => $option) {
            if (!isset($post['ftransforms'][$field])) {
                $post['ftransforms'][$field] = [];
            }
        }
        foreach ($presets['ttransfields'] as $field => $option) {
            if (!isset($post['ttransforms'][$field])) {
                $post['ttransforms'][$field] = [];
            }
        }
        foreach ($presets['ptransfields'] as $field => $option) {
            if (!isset($post['ptransforms'][$field])) {
                $post['ptransforms'][$field] = [];
            }
        }
        $transargs = [];
        $transargs['itemtype'] = $post['forumtype'];
        $transargs['transforms'] = $post['ftransforms'];
        $transargs['fname'] = $post['fname'];
        $transargs['fdesc'] = $post['fdesc'];
        $ftransformed = xarMod::apiFunc('crispbb', 'user', 'dotransforms', $transargs);
        $post['transformed_fname'] = $ftransformed['fname'];
        $post['transformed_fdesc'] = $ftransformed['fdesc'];
        $transargs = [];
        $transargs['itemtype'] = $post['topicstype'];
        $transargs['transforms'] = $post['ttransforms'];
        $transargs['ttitle'] = $post['ttitle'];
        //$transargs['tdesc'] = $post['tdesc'];
        $ignore = [];
        if (!empty($post['tsettings']['htmldeny'])) {
            $ignore['html'] = 1;
        }
        if (!empty($post['tsettings']['bbcodedeny'])) {
            $ignore['bbcode'] = 1;
        }
        if (!empty($post['tsettings']['smiliesdeny'])) {
            $ignore['smilies'] = 1;
        }
        $transargs['ignore'] = $ignore;
        $ttransformed = xarMod::apiFunc('crispbb', 'user', 'dotransforms', $transargs);
        $post['transformed_ttitle'] = $ttransformed['ttitle'];
        //$post['transformed_tdesc'] = $ttransformed['tdesc'];
        $transargs = [];
        $transargs['itemtype'] = $post['poststype'];
        $transargs['transforms'] = $post['ptransforms'];
        $transargs['pdesc'] = $post['pdesc'];
        $transargs['ptext'] = $post['ptext'];
        $ignore = [];
        if (!empty($post['psettings']['htmldeny'])) {
            $ignore['html'] = 1;
        }
        if (!empty($post['psettings']['bbcodedeny'])) {
            $ignore['bbcode'] = 1;
        }
        if (!empty($post['psettings']['smiliesdeny'])) {
            $ignore['smilies'] = 1;
        }
        $transargs['ignore'] = $ignore;
        $ptransformed = xarMod::apiFunc('crispbb', 'user', 'dotransforms', $transargs);
        $post['transformed_pdesc'] = $ptransformed['pdesc'];
        $post['transformed_ptext'] = $ptransformed['ptext'];
        $posts[$post['pid']] = $post;
    }
    $result->Close();

    if (empty($posts) && !empty($privcheck) && $checkfailed) {
        $posts['error'] = 'NO_PRIVILEGES';
    }

    return $posts;
}
