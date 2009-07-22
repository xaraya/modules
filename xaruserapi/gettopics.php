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
function crispbb_userapi_gettopics($args)
{
    extract($args);

    $startnum = isset($startnum) ? $startnum : 1;
    $numitems = isset($numitems) ? $numitems : -1;
    if (empty($cids) && !empty($catid)) {
        $cids = array($catid);
    }
    if (empty($cids)) $cids = array();
    // get hitcount unless asked not to
    $nohitcount = isset($nohitcount) ? 1 : 0;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $topicstable = $xartable['crispbb_topics'];
    $trequired = array('tid', 'fid', 'tstatus');
    if (empty($topicfields) || !is_array($topicfields)) {
        $topicfields = array('tid', 'fid', 'tstatus', 'towner', 'ttype', 'ttitle', 'lastpid', 'tsettings', 'topicstype', 'firstpid');
    }
    $forumfields = array('fname','fdesc','fstatus','fsettings','fprivileges');
    $postsfields = array('powner','ptime','pstatus','psettings','poststype', 'pdesc','ptext');

    foreach ($trequired as $reqfield) {
        if (!in_array($reqfield, $topicfields)) $topicfields[] = $reqfield;
    }
    $fields = $topicfields;
    $select = array();
    $where = array();
    $orderby = array();
    $groupby = array();
    $bindvars = array();
    foreach ($fields as $k => $fieldname) {
        $select[] = $topicstable . '.xar_' . $fieldname;
    }
    $from = $topicstable;

    $poststable = $xartable['crispbb_posts'];
    foreach ($postsfields as $k => $fieldname) {
        $select[] = $poststable . '.xar_' . $fieldname;
        $fields[] = $fieldname;
    }
    $from .= ' LEFT JOIN ' . $poststable;
    $from .= ' ON ' . $poststable . '.xar_pid' . ' = ' . $topicstable . '.xar_lastpid';
    $addme = 1;
    if (empty($numreplies)) {
        if ($addme && ($dbconn->databaseType != 'sqlite')) {
            $from = '(' . $from . ')';
        }
        // Add the LEFT JOIN ... ON ... posts for the reply count
        $from .= ' LEFT JOIN ' . $poststable . ' AS replies';
        $from .= ' ON replies.xar_tid = ' . $topicstable . '.xar_tid';
        $from .= ' AND replies.xar_pstatus IN (0,1)';
        $from .= ' AND ' . $topicstable . ".xar_firstpid != replies.xar_pid";
        $from .= ' AND ' . $topicstable . ".xar_firstpid != " . $topicstable . ".xar_lastpid";
        if (!empty($fid)) {
            if (is_numeric($fid)) {
                $from .= ' AND ' . $topicstable . '.xar_fid = ' . $fid;
            } elseif (is_array($fid) && count($fid) > 0) {
                $seenfid = array();
                foreach ($fid as $id) {
                    if (empty($id) || !is_numeric($id)) continue;
                    $seenfid[$id] = 1;
                }
                if (count($seenfid) == 1) {
                    $fids = array_keys($seenfid);
                    $from .= ' AND ' . $topicstable . '.xar_fid = ' . $fids[0];
                } elseif (count($seenfid) > 1) {
                    $fids = join(', ', array_keys($seenfid));
                    $from .= ' AND ' . $topicstable . '.xar_fid IN (' . $fids . ')';
                }
            }
        }
        $select[] = 'COUNT(replies.xar_pid) AS numreplies';
        $fields[] = 'numreplies';
        $addme = 1;
    }
    if ($addme && ($dbconn->databaseType != 'sqlite')) {
        $from = '(' . $from . ')';
    }
    // Add the LEFT JOIN ... ON ... posts for the reply count
    $from .= ' LEFT JOIN ' . $poststable . ' AS firstpost';
    $from .= ' ON firstpost.xar_pid = ' . $topicstable . '.xar_firstpid';
    $select[] = 'firstpost.xar_pdesc AS tdesc';
    $select[] = 'firstpost.xar_ptime AS ttime';
    $select[] = 'firstpost.xar_ptext AS ttext';
    $fields[] = 'tdesc';
    $fields[] = 'ttime';
    $fields[] = 'ttext';

    if (isset($starttime) && is_numeric($starttime)) {
        $where[] = $poststable.".xar_ptime >= ?";
        $bindvars[] = $starttime;
    }
    if (isset($endtime) && is_numeric($endtime)) {
        $where[] = $poststable.".xar_ptime <= ?";
        $bindvars[] = $endtime;
    }

    if (isset($noreplies)) {
        $where[] = $topicstable . ".xar_firstpid = " . $topicstable . ".xar_lastpid";
    }

    $forumstable = $xartable['crispbb_forums'];
    foreach ($forumfields as $k => $fieldname) {
        $select[] = $forumstable . '.xar_' . $fieldname;
        $fields[] = $fieldname;
    }

    if ($addme && ($dbconn->databaseType != 'sqlite')) {
        $from = '(' . $from . ')';
    }
    $from .= ' LEFT JOIN ' . $forumstable;
    $from .= ' ON ' . $forumstable . '.xar_fid' . ' = ' . $topicstable . '.xar_fid';
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
    $addme = 1;
    $itemtypestable = $xartable['crispbb_itemtypes'];
    $select[] = $itemtypestable . '.xar_itemtype';
    $fields[] = 'forumtype';
    if ($addme && ($dbconn->databaseType != 'sqlite')) {
        $from = '(' . $from . ')';
    }
    $from .= ' LEFT JOIN ' . $itemtypestable;
    $from .= ' ON ' . $itemtypestable . '.xar_fid' . ' = ' . $forumstable . '.xar_fid';
    $from .= ' AND ' . $itemtypestable . '.xar_component = "forum"';

    $categoriesdef = xarModAPIFunc(
         'categories','user','leftjoin',
         array('cids' => $cids, 'modid' => xarModGetIDFromName('crispbb'))
    );
    $addme = 0;
    if (!empty($categoriesdef)) {
        $select[] = $categoriesdef['cid'];
        $fields[] = 'catid';
        $from .= ' LEFT JOIN ' . $categoriesdef['table'];
        $from .= ' ON ' . $categoriesdef['field'] . ' = ' . $forumstable . '.xar_fid';
        $addme = 1;
        if (!empty($categoriesdef['more']) && ($dbconn->databaseType != 'sqlite')) {
            $from = '(' . $from . ')';
            $from .= $categoriesdef['more'];
        }
    }
    if (!empty($categoriesdef['where'])) $where[] = $categoriesdef['where'];

    if (empty($nohitcount)) {
        // Get the LEFT JOIN ... ON ...  and WHERE (!) parts from hitcount
        $hitcountdef = xarModAPIFunc('hitcount', 'user', 'leftjoin',
            array(
                'modid' => xarModGetIDFromName('crispbb'),
                // 'itemtype' => (isset($topicstype) ? $topicstype : null)
            ));
        if (empty($hitcountdef['hits'])) {
            if(!xarSecurityCheck('ReadHitcountItems',1,'Item',"crispbb:All:All")) return;
        }
        $select[] = $hitcountdef['hits'];
        $fields[] = 'numviews';
        if ($addme && ($dbconn->databaseType != 'sqlite')) {
            $from = '(' . $from . ')';
        }
        // Add the LEFT JOIN ... ON ... hitcount for the topic counter
        $from .= ' LEFT JOIN ' . $hitcountdef['table'];
        $from .= ' ON ' . $hitcountdef['field'] . ' = ' . $topicstable.'.xar_tid';
        $from .= ' AND ' . $hitcountdef['table'] . '.xar_itemtype' . ' = ' . $topicstable.'.xar_topicstype';
        $addme = 1;
    }

    $rolesdef = xarModAPIFunc('roles', 'user', 'leftjoin');
    $rolesfields = array('name','uname','uid');
    foreach ($rolesfields as $rfield) {
        $select[] = $rolesdef['table'] . '.xar_' . $rfield;
        $fields[] = 'towner'.$rfield;
    }
    if (($dbconn->databaseType != 'sqlite')) {
        $from = '(' . $from . ')';
    }
    // Add the LEFT JOIN ... ON ... roles for the towner info
    $from .= ' LEFT JOIN ' . $rolesdef['table'];
    $from .= ' ON ' . $rolesdef['table'] . '.xar_uid' . ' = ' . $topicstable . '.xar_towner';

    if (!empty($tid)) {
        if (is_numeric($tid)) {
            $where[] = $topicstable . '.xar_tid = ' . $tid;
        } elseif (is_array($tid) && count($tid) > 0) {
            $seentid = array();
            foreach ($tid as $id) {
                if (empty($id) || !is_numeric($id)) continue;
                $seentid[$id] = 1;
            }
            if (count($seentid) == 1) {
                $tids = array_keys($seentid);
                $where[] = $topicstable . '.xar_tid = ' . $tids[0];
            } elseif (count($seentid) > 1) {
                $tids = join(', ', array_keys($seentid));
                $where[] = $topicstable . '.xar_tid IN (' . $tids . ')';
            }
        }
    }

    if (isset($tstatus)) {
        if (is_numeric($tstatus)) {
            $where[] = $topicstable . '.xar_tstatus = ' . $tstatus;
        } elseif (is_array($tstatus) && count($tstatus) > 0) {
            $seentstatus = array();
            foreach ($tstatus as $id) {
                if (!is_numeric($id)) continue;
                $seentstatus[$id] = 1;
            }
            if (count($seentstatus) == 1) {
                $tstatuses = array_keys($seentstatus);
                $where[] = $topicstable . '.xar_tstatus = ' . $tstatuses[0];
            } elseif (count($seentstatus) > 1) {
                $tstatuses = join(', ', array_keys($seentstatus));
                $where[] = $topicstable . '.xar_tstatus IN (' . $tstatuses . ')';
            }
        }
    }

    if (isset($ttype)) {
        if (is_numeric($ttype)) {
            $where[] = $topicstable . '.xar_ttype = ' . $ttype;
        } elseif (is_array($ttype) && count($ttype) > 0) {
            $seenttype = array();
            foreach ($ttype as $id) {
                if (!is_numeric($id)) continue;
                $seenttype[$id] = 1;
            }
            if (count($seenttype) == 1) {
                $ttypes = array_keys($seenttype);
                $where[] = $topicstable . '.xar_ttype = ' . $ttypes[0];
            } elseif (count($seenttype) > 1) {
                $ttypes = join(', ', array_keys($seenttype));
                $where[] = $topicstable . '.xar_ttype = IN (' . $ttypes . ')';
            }
        }
    }

    if (!empty($sort)) {
        if (in_array($sort, $topicfields)) {
            if ($sort == 'towner') {
                $myorder = $rolesdef['table'] . '.xar_name';
            } else {
                $myorder = $topicstable . '.xar_' . $sort;
            }
        } elseif (in_array($sort, $postsfields)) {
            $myorder = $poststable . '.xar_' . $sort;
        } elseif (in_array($sort, $forumfields)) {
            $myorder = $forumstable . '.xar_' . $sort;
        } elseif ($sort == 'numhits') {
            $myorder = $hitcountdef['hits'];
        } elseif ($sort == 'numreplies') {
            $myorder = 'numreplies';//'COUNT(replies.xar_pid)';
        } elseif ($sort == 'ttime') {
            $myorder = 'firstpost.xar_ptime';
        }
        if (!empty($order)) {
            $myorder .= ' ' . strtoupper($order) . ' ';
        }
        if (!empty($myorder)) {
            $orderby[] = $myorder;
        }
    }

    if (!empty($towner) && is_numeric($towner)) {
        $where[] = $topicstable . '.xar_towner = ?';
        $bindvars[] = $towner;
    }

    if (!empty($q))
    {
        if (empty($searchfields)) $searchfields = array();
        $search = $q;
        // TODO : improve + make use of full-text indexing for recent MySQL versions ?

        $normal = array();
        $find = array();

        // 0. Check for "'equal whole string' searchType"
        if (!empty($searchtype) && $searchtype == 'equal whole string')
        {
            $normal[] = $search;
            $search   = "";
            $searchtype = 'eq';
        }

        // 0. Check for fulltext or fulltext boolean searchtypes (MySQL only)
        // CHECKME: switch to other search type if $search is less than min. length ?
        if (!empty($searchtype) && substr($searchtype,0,8) == 'fulltext') {
            $fulltext = xarModGetVar('articles', 'fulltextsearch');
            if (!empty($fulltext)) {
                $fulltextfields = explode(',',$fulltext);
            } else {
                $fulltextfields = array();
            }
            $matchfields = array();
            foreach ($fulltextfields as $field) {
                if (empty($leftjoin[$field])) continue;
                $matchfields[] = $leftjoin[$field];
            }
        // TODO: switch mode automatically if + - etc. are detected ?
            $matchmode = '';
            if ($searchtype == 'fulltext boolean') {
                $matchmode = ' IN BOOLEAN MODE';
            }
            $find[] = 'MATCH (' . join(', ',$matchfields) . ') AGAINST (' . $dbconn->qstr($search) . $matchmode . ')';
            // Add this to field list too when sorting by relevance in boolean mode (cfr. getall() sort)
            $leftjoin['relevance'] = 'MATCH (' . join(', ',$matchfields) . ') AGAINST (' . $dbconn->qstr($search) . $matchmode . ') AS relevance';

            // check if we have any other fields to search in
            $morefields = array_diff($searchfields, $fulltextfields);
            if (!empty($morefields)) {
            // FIXME: sort order may not be by relevance if we mix fulltext with other searches
                $searchfields = $morefields;
                $searchtype = '';
            } else {
                // we're done here
                $searchfields = array();
                $search = '';
            }
        }

        // 1. find quoted text
        if (preg_match_all('#"(.*?)"#',$search,$matches)) {
            foreach ($matches[1] as $match) {
                $normal[] = $match;
                $match = preg_quote($match);
                $search = trim(preg_replace("#\"$match\"#",'',$search));
            }
        }
        if (preg_match_all("/'(.*?)'/",$search,$matches)) {
            foreach ($matches[1] as $match) {
                $normal[] = $match;
                $match = preg_quote($match);
                $search = trim(preg_replace("#'$match'#",'',$search));
            }
        }

        // 2. find mandatory +text to include
        // 3. find mandatory -text to exclude
        // 4. find normal text
        $more = preg_split('/\s+/',$search,-1,PREG_SPLIT_NO_EMPTY);
        $normal = array_merge($normal,$more);
        foreach ($normal as $text) {
            // TODO: use XARADODB to escape wildcards (and use portable ones) ??
            $text = str_replace('%','\%',$text);
            $text = str_replace('_','\_',$text);
            foreach ($searchfields as $field) {
                if ($field == 'ttitle') {
                    $searchfield = $topicstable . '.xar_ttitle';
                } elseif ($field == 'pdesc') {
                    $searchfield = $poststable . '.xar_pdesc';
                } elseif ($field == 'ptext') {
                    $searchfield = $poststable . '.xar_ptext';
                }
                if (empty($searchfield)) continue;
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
        $where[] = '(' . join(' OR ',$find) . ')';
        }
    }


    $query = 'SELECT ' . join(', ', $select);
    $query .= ' FROM ' . $from;
    if (!empty($where)) {
        $query .= ' WHERE ' . join(' AND ', $where);
    }
    $query .= ' GROUP BY ' . $topicstable . '.xar_tid';
    if (!empty($orderby)) {
        $query .= ' ORDER BY ' . join(',', $orderby);
    }else {
        $query .= ' ORDER BY ' . $poststable . '.xar_ptime DESC';
    }
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    if (!$result) return;
    $topics = array();
    // module defaults
    $presets = xarModAPIFunc('crispbb', 'user', 'getpresets',
        array('preset' => 'fsettings,fprivileges,ftransfields,ttransfields,ptransfields'));
    $loggedin = xarUserIsLoggedIn();
    $uid = xarUserGetVar('uid');
    $checkfailed = false;
    for (; !$result->EOF; $result->MoveNext()) {
        $data = $result->fields;
        $topic = array();
        foreach ($fields as $key => $field) {
            $value = array_shift($data);
            if ($field == 'tsettings') {
                $value = unserialize($value);
                foreach ($value as $k => $v) {
                    $topic[$k] = $v;
                }
            } elseif ($field == 'fsettings') {
                $fsettings = unserialize($value);
                // add in any new presets from defaults
                foreach ($presets['fsettings'] as $p => $pv) {
                    if (!isset($fsettings[$p])) {
                        $fsettings[$p] = $pv;
                    }
                }
                foreach ($fsettings as $k => $v) {
                    // remove any settings not in defaults
                    if (!isset($presets['fsettings'][$k])) continue;
                    $topic[$k] = $v;
                }
                continue;
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
            } elseif ($field == 'psettings') {
                $value = unserialize($value);
            }
            $topic[$field] = $value;
        }
        if (!$secLevel = xarModAPIFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $topic, 'priv' => 'viewforum'))) {
            $checkfailed = true;
            continue;
        }
        if ($topic['tstatus'] == 3) { //moved topic
            $moves = $topic['tsettings']['moved'];
            $move = !empty($moves) ? array_pop($moves) : array();
            if (!empty($move['tid'])) { // get the actual topic that was moved
                $moved = xarModAPIFunc('crispbb', 'user', 'gettopic', array('tid' => $move['tid'], 'privcheck' => true));
                // user might not have privs for the forum the topic was moved to
                if (empty($moved) || !empty($moved['error'])) {
                    unset($topic); unset($moved); $checkfailed = true; continue;
                }
            }
        }
        $topic['forumLevel'] = $secLevel;
        // add privs for current user level in this forum
        $topic['privs'] = $topic['fprivileges'][$secLevel];
        if (empty($nolinks)) {
            // forum viewers
            $topic['viewforumurl'] = xarModURL('crispbb', 'user', 'view',
                array('fid' => !empty($moved['fid']) ? $moved['fid'] : $topic['fid']));
            if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
                array('check' => $topic, 'priv' => 'readforum'))) {
                // topic readers
                $topic['viewtopicurl'] = xarModURL('crispbb', 'user', 'display',
                    array('tid' => !empty($moved['tid']) ? $moved['tid'] : $topic['tid']));
                if (empty($moved)) {
                    $topic['lastreplyurl'] = xarModURL('crispbb', 'user', 'display',
                        array('tid' => $topic['tid'], 'action' => 'lastreply'));
                }
                if ($loggedin) {
                    // topic starters
                    if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
                        array('check' => $topic, 'priv' => 'newtopic'))) {
                        $topic['newtopicurl'] = xarModURL('crispbb', 'user', 'newtopic',
                            array('fid' => $topic['fid']));
                    }
                    $tids = array();
                    $tids[$topic['tid']] = 1;
                    // only provide these links if the topic wasn't moved
                    if (empty($moved)) {
                        // topic repliers
                        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
                            array('check' => $topic, 'priv' => 'newreply'))) {
                            $topic['newreplyurl'] = xarModURL('crispbb', 'user', 'newreply',
                                array('tid' => $topic['tid']));
                        }
                        // topic editors
                        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
                            array('check' => $topic, 'priv' => 'edittopics'))) {
                            $topic['edittopicurl'] = xarModURL('crispbb', 'user', 'modifytopic',
                                array('tid' => $topic['tid']));
                        }
                        // topic closers
                        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
                            array('check' => $topic, 'priv' => 'closetopics'))) {
                            if ($topic['tstatus'] == 1) {
                                $topic['opentopicurl'] = xarModURL('crispbb', 'user', 'moderate',
                                    array(
                                        'component' => 'topics',
                                        'fid' => $topic['fid'],
                                        'tstatus' => $topic['tstatus'],
                                        'modaction' => 'open',
                                        'phase' => 'update',
                                        'tids' => $tids,
                                ));
                            } else {
                                $topic['closetopicurl'] = xarModURL('crispbb', 'user', 'moderate',
                                    array(
                                        'component' => 'topics',
                                        'fid' => $topic['fid'],
                                        'tstatus' => $topic['tstatus'],
                                        'modaction' => 'close',
                                        'phase' => 'update',
                                        'tids' => $tids,
                                ));
                            }
                        }
                        // topic movers
                        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
                            array('check' => $topic, 'priv' => 'movetopics'))) {
                            $topic['movetopicurl'] = xarModURL('crispbb', 'user', 'moderate',
                                    array(
                                        'component' => 'topics',
                                        'fid' => $topic['fid'],
                                        'modaction' => 'move',
                                        'tids' => $tids,
                                ));
                        }
                        // topic splitters
                        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
                            array('check' => $topic, 'priv' => 'splittopics'))) {
                            $topic['splittopicurl'] = xarModURL('crispbb', 'user', 'moderate',
                                    array(
                                        'component' => 'posts',
                                        'tid' => $topic['tid'],
                                        //'pstatus' => $topic['pstatus'],
                                        //'modaction' => 'split',
                                        //'phase' => 'update',
                                ));
                        }
                        // topic lockers
                        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
                            array('check' => $topic, 'priv' => 'locktopics'))) {
                            if ($topic['tstatus'] == 4) {
                                $topic['unlocktopicurl'] = xarModURL('crispbb', 'user', 'moderate',
                                    array(
                                        'component' => 'topics',
                                        'fid' => $topic['fid'],
                                        'tstatus' => $topic['tstatus'],
                                        'modaction' => 'unlock',
                                        'phase' => 'update',
                                        'tids' => $tids,
                                ));
                            } else {
                                $topic['locktopicurl'] = xarModURL('crispbb', 'user', 'moderate',
                                    array(
                                        'component' => 'topics',
                                        'fid' => $topic['fid'],
                                        'tstatus' => $topic['tstatus'],
                                        'modaction' => 'lock',
                                        'phase' => 'update',
                                        'tids' => $tids,
                                ));
                            }
                        }
                        // topic deleters
                        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
                            array('check' => $topic, 'priv' => 'deletetopics'))) {
                            if ($topic['tstatus'] == 5) {
                                $topic['undeletetopicurl'] = xarModURL('crispbb', 'user', 'moderate',
                                    array(
                                        'component' => 'topics',
                                        'fid' => $topic['fid'],
                                        'tstatus' => $topic['tstatus'],
                                        'modaction' => 'undelete',
                                        'phase' => 'update',
                                        'tids' => $tids,
                                ));
                            } else {
                                $topic['deletetopicurl'] = xarModURL('crispbb', 'user', 'moderate',
                                    array(
                                        'component' => 'topics',
                                        'fid' => $topic['fid'],
                                        'tstatus' => $topic['tstatus'],
                                        'modaction' => 'delete',
                                        'phase' => 'update',
                                        'tids' => $tids,
                                ));
                            }
                        }
                        // forum moderators
                        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
                            array('check' => $topic, 'priv' => 'ismoderator'))) {
                            $topic['modforumurl'] = xarModURL('crispbb', 'user', 'moderate',
                                array('component' => 'topics', 'fid' => $topic['fid']));
                            $topic['modtopicurl'] = xarModURL('crispbb', 'user', 'moderate',
                                array('component' => 'posts', 'tid' => $topic['tid']));
                            // TODO: deprecate this, use moderateurl instead
                            $topic['admintopicsurl'] = xarModURL('crispbb', 'admin', 'topics',
                                array('fid' => $topic['fid']));
                        }
                        // forum editors
                        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
                            array('check' => $topic, 'priv' => 'editforum'))) {
                            $topic['purgetopicurl'] = xarModURL('crispbb', 'user', 'moderate',
                                    array(
                                        'component' => 'topics',
                                        'fid' => $topic['fid'],
                                        'tstatus' => $topic['tstatus'],
                                        'modaction' => 'purge',
                                        'phase' => 'update',
                                        'tids' => $tids,
                                ));
                        }
                    } else {
                        // topic deleters
                        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
                            array('check' => $topic, 'priv' => 'deletetopics'))) {
                            if ($topic['tstatus'] == 5) {
                                $topic['undeletetopicurl'] = xarModURL('crispbb', 'user', 'moderate',
                                    array(
                                        'component' => 'topics',
                                        'fid' => $topic['fid'],
                                        'tstatus' => $topic['tstatus'],
                                        'modaction' => 'undelete',
                                        'phase' => 'update',
                                        'tids' => $tids,
                                ));
                            } else {
                                $topic['deletetopicurl'] = xarModURL('crispbb', 'user', 'moderate',
                                    array(
                                        'component' => 'topics',
                                        'fid' => $topic['fid'],
                                        'tstatus' => $topic['tstatus'],
                                        'modaction' => 'delete',
                                        'phase' => 'update',
                                        'tids' => $tids,
                                ));
                            }
                        }
                        // forum editors
                        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
                            array('check' => $topic, 'priv' => 'editforum'))) {
                            $topic['purgetopicurl'] = xarModURL('crispbb', 'user', 'moderate',
                                    array(
                                        'component' => 'topics',
                                        'fid' => $topic['fid'],
                                        'tstatus' => $topic['tstatus'],
                                        'modaction' => 'purge',
                                        'phase' => 'update',
                                        'tids' => $tids,
                                ));
                        }
                    }
                }
            }
        }
        foreach ($presets['ftransfields'] as $field => $option) {
            if (!isset($topic['ftransforms'][$field]))
                $topic['ftransforms'][$field] = array();
        }
        foreach ($presets['ttransfields'] as $field => $option) {
            if (!isset($topic['ttransforms'][$field]))
                $topic['ttransforms'][$field] = array();
        }
        foreach ($presets['ptransfields'] as $field => $option) {
            if (!isset($topic['ptransforms'][$field]))
                $topic['ptransforms'][$field] = array();
        }
        $transargs = array();
        $transargs['itemtype'] = $topic['forumtype'];
        $transargs['transforms'] = $topic['ftransforms'];
        $transargs['fname'] = $topic['fname'];
        $transargs['fdesc'] = $topic['fdesc'];
        $ftransformed = xarModAPIFunc('crispbb', 'user', 'dotransforms', $transargs);
        $topic['transformed_fname'] = $ftransformed['fname'];
        $topic['transformed_fdesc'] = $ftransformed['fdesc'];
        $transargs = array();
        $transargs['itemtype'] = $topic['topicstype'];
        $transargs['transforms'] = $topic['ttransforms'];
        $transargs['ttitle'] = $topic['ttitle'];
        $transargs['tdesc'] = $topic['tdesc'];
        $transargs['ttext'] = $topic['ttext'];
        $ignore = array();
        if (!empty($topic['tsettings']['htmldeny'])) $ignore['html'] = 1;
        if (!empty($topic['tsettings']['bbcodedeny'])) $ignore['bbcode'] = 1;
        if (!empty($topic['tsettings']['smiliesdeny'])) $ignore['smilies'] = 1;
        $transargs['ignore'] = $ignore;
        $ttransformed = xarModAPIFunc('crispbb', 'user', 'dotransforms', $transargs);
        $topic['transformed_ttitle'] = $ttransformed['ttitle'];
        $topic['transformed_tdesc'] = $ttransformed['tdesc'];
        $topic['transformed_ttext'] = $ttransformed['ttext'];
        $transargs = array();
        $transargs['itemtype'] = $topic['poststype'];
        $transargs['transforms'] = $topic['ptransforms'];
        $transargs['pdesc'] = $topic['pdesc'];
        $ignore = array();
        if (!empty($topic['psettings']['htmldeny'])) $ignore['html'] = 1;
        if (!empty($topic['psettings']['bbcodedeny'])) $ignore['bbcode'] = 1;
        if (!empty($topic['psettings']['smiliesdeny'])) $ignore['smilies'] = 1;
        $transargs['ignore'] = $ignore;
        $ptransformed = xarModAPIFunc('crispbb', 'user', 'dotransforms', $transargs);
        $topic['transformed_pdesc'] = $ptransformed['pdesc'];

        $topics[$topic['tid']] = $topic;
    }
    $result->Close();

    if (empty($topics) && !empty($privcheck) && $checkfailed) {
        $topics['error'] = xarML('NO_PRIVILEGES');
    }

    return $topics;

}
?>