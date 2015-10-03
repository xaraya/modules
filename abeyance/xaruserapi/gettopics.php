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

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $topicstable = $xartable['crispbb_topics'];
    $trequired = array('id', 'fid', 'tstatus');
    if (empty($topicfields) || !is_array($topicfields)) {
        $topicfields = array('id', 'fid', 'tstatus', 'towner', 'ttype', 'ttitle', 'lastpid', 'tsettings', 'topicstype', 'firstpid', 'numreplies', 'numdels', 'numsubs');
    }
    $forumfields = array('fname','fdesc','fstatus','ftype','fsettings','fprivileges');
    $postsfields = array('powner','ptime','pstatus','psettings','poststype', 'pdesc','ptext');

    foreach ($trequired as $reqfield) {
        if (!in_array($reqfield, $topicfields)) $topicfields[] = $reqfield;
    }
    $fields = array();
    $select = array();
    $where = array();
    $orderby = array();
    $groupby = array();
    $bindvars = array();
    foreach ($topicfields as $k => $fieldname) {
        $select[] = $topicstable . '.' . $fieldname;
        $fieldname = $fieldname == 'id' ? 'tid' : $fieldname;
        $fields[] = $fieldname;
    }
    $from = $topicstable;

    $poststable = $xartable['crispbb_posts'];
    foreach ($postsfields as $k => $fieldname) {
        $select[] = $poststable . '.' . $fieldname;
        $fields[] = $fieldname;
    }
    if (!empty($submitted)) {
        // only get topics with submitted replies
        $from .= ' LEFT JOIN ' . $poststable;
        $from .= ' ON ' . $poststable . '.tid' . ' = ' . $topicstable . '.id';
        $from .= ' AND ' . $poststable . '.pstatus = 2';
        $addme = 1;
        $where[] = $poststable . '.pstatus = 2';
    } elseif (!empty($deleted)) {
        // only get topics with deleted replies
        $from .= ' LEFT JOIN ' . $poststable;
        $from .= ' ON ' . $poststable . '.tid' . ' = ' . $topicstable . '.id';
        $from .= ' AND ' . $poststable . '.pstatus = 5';
        $addme = 1;
        $where[] = $poststable . '.pstatus = 5';
    } else {
        $from .= ' LEFT JOIN ' . $poststable;
        $from .= ' ON ' . $poststable . '.id' . ' = ' . $topicstable . '.lastpid';
        $addme = 1;
    }
    $hookstable = $xartable['crispbb_hooks'];
    $select[] = $hookstable . '.moduleid AS hookmodid';
    $fields[] = 'hookmodid';
    $select[] = $hookstable . '.itemtype AS hooktype';
    $fields[] = 'hooktype';
    $select[] = $hookstable . '.itemid AS objectid';
    $fields[] = 'objectid';
    if ($addme && ($dbconn->databaseType != 'sqlite')) {
        $from = '(' . $from . ')';
    }
    if (!empty($hookmodid)) {
        $where[] = $hookstable . '.moduleid = ' . $hookmodid;
        if (!empty($hooktype)) {
            $where[] = $hookstable . '.itemtype = ' . $hooktype;
        }
    }
    // Add the LEFT JOIN ... ON ... posts for the reply count
    $from .= ' LEFT JOIN ' . $hookstable;
    $from .= ' ON ' . $hookstable . '.tid = ' . $topicstable . '.id';
    $addme = 1;

    if ($addme && ($dbconn->databaseType != 'sqlite')) {
        $from = '(' . $from . ')';
    }
    // Add the LEFT JOIN ... ON ... posts for the first post data
    $from .= ' LEFT JOIN ' . $poststable . ' AS firstpost';
    $from .= ' ON firstpost.id = ' . $topicstable . '.firstpid';
    $select[] = 'firstpost.pdesc AS tdesc';
    $select[] = 'firstpost.ptime AS ttime';
    $select[] = 'firstpost.ptext AS ttext';
    $fields[] = 'tdesc';
    $fields[] = 'ttime';
    $fields[] = 'ttext';

    if (isset($starttime) && is_numeric($starttime)) {
        $where[] = $poststable.".ptime >= ?";
        $bindvars[] = $starttime;
    }
    if (isset($endtime) && is_numeric($endtime)) {
        $where[] = $poststable.".ptime <= ?";
        $bindvars[] = $endtime;
    }

    if (isset($noreplies)) {
        $where[] = $topicstable . ".firstpid = " . $topicstable . ".lastpid";
    }

    $forumstable = $xartable['crispbb_forums'];
    foreach ($forumfields as $k => $fieldname) {
        $select[] = $forumstable . '.' . $fieldname;
        $fields[] = $fieldname;
    }

    if ($addme && ($dbconn->databaseType != 'sqlite')) {
        $from = '(' . $from . ')';
    }
    $from .= ' LEFT JOIN ' . $forumstable;
    $from .= ' ON ' . $forumstable . '.id' . ' = ' . $topicstable . '.fid';
    if (!empty($fid)) {
        if (is_numeric($fid)) {
            $where[] = $forumstable . '.id = ' . $fid;
        } elseif (is_array($fid) && count($fid) > 0) {
            $seenfid = array();
            foreach ($fid as $id) {
                if (empty($id) || !is_numeric($id)) continue;
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
            $seenfstatus = array();
            foreach ($fstatus as $id) {
                if (empty($id) || !is_numeric($id)) continue;
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
            $seenftype = array();
            foreach ($ftype as $id) {
                if (empty($id) || !is_numeric($id)) continue;
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
    $addme = 1;
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
         'categories','user','leftjoin',
         array('cids' => $cids, 'modid' => xarMod::getRegID('crispbb'))
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
    if (!empty($categoriesdef['where'])) $where[] = $categoriesdef['where'];
    if (empty($nohitcount)) {
        // Get the LEFT JOIN ... ON ...  and WHERE (!) parts from hitcount
        $hitcountdef = xarMod::apiFunc('hitcount', 'user', 'leftjoin',
            array(
                'modid' => xarMod::getRegID('crispbb'),
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
        $from .= ' ON ' . $hitcountdef['field'] . ' = ' . $topicstable.'.id';
        $from .= ' AND ' . $hitcountdef['itemtype'] . ' = ' . $topicstable.'.topicstype';
        $addme = 1;
    }

    if (empty($noratings) && xarModIsAvailable('ratings')) {
        // Get the LEFT JOIN ... ON ...  and WHERE (!) parts from hitcount
        $ratingsdef = xarMod::apiFunc('ratings', 'user', 'leftjoin',
            array(
                'modid' => xarMod::getRegID('crispbb'),
            ));
        if (!empty($ratingsdef)) {
            $select[] = $ratingsdef['rating'];
            $select[] = $ratingsdef['numratings'];
            $fields[] = 'rating';
            $fields[] = 'numratings';
            if ($addme && ($dbconn->databaseType != 'sqlite')) {
                $from = '(' . $from . ')';
            }
            $from .= ' LEFT JOIN ' . $ratingsdef['table'];
            $from .= ' ON ' . $ratingsdef['field'] . ' = ' . $topicstable.'.id';
            $from .= ' AND ' . $ratingsdef['itemtype'] . ' = ' . $topicstable . '.topicstype';
            $addme = 1;
        }
    }

    $rolesdef = xarMod::apiFunc('roles', 'user', 'leftjoin');
    $rolesfields = array('name','uname','id');
    foreach ($rolesfields as $rfield) {
        $select[] = $rolesdef['table'] . '.' . $rfield;
        $fields[] = 'towner'.$rfield;
    }
    if (($dbconn->databaseType != 'sqlite')) {
        $from = '(' . $from . ')';
    }
    // Add the LEFT JOIN ... ON ... roles for the towner info
    $from .= ' LEFT JOIN ' . $rolesdef['table'];
    $from .= ' ON ' . $rolesdef['table'] . '.id' . ' = ' . $topicstable . '.towner';

    if (!empty($tid)) {
        if (is_numeric($tid)) {
            $where[] = $topicstable . '.id = ' . $tid;
        } elseif (is_array($tid) && count($tid) > 0) {
            $seentid = array();
            foreach ($tid as $id) {
                if (empty($id) || !is_numeric($id)) continue;
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
            $seentstatus = array();
            foreach ($tstatus as $id) {
                if (!is_numeric($id)) continue;
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
            $seenttype = array();
            foreach ($ttype as $id) {
                if (!is_numeric($id)) continue;
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

    if (!empty($sort)) {
        if (in_array($sort, $topicfields)) {
            if ($sort == 'towner') {
                $myorder = $rolesdef['table'] . '.name';
            } else {
                $myorder = $topicstable . '.' . $sort;
            }
        } elseif (in_array($sort, $postsfields)) {
            $myorder = $poststable . '.' . $sort;
        } elseif (in_array($sort, $forumfields)) {
            $myorder = $forumstable . '.' . $sort;
        } elseif ($sort == 'numhits') {
            $myorder = $hitcountdef['hits'];
        } elseif ($sort == 'numreplies') {
            $myorder = $topicstable .'numreplies';//'COUNT(replies.pid)';
        } elseif ($sort == 'ttime') {
            $myorder = 'firstpost.ptime';
        } elseif ($sort == 'numratings') {
            if (isset($ratingsdef['rating'])) {
                $myorder = $ratingsdef['rating'];
            }
        }
        if (!empty($order)) {
            $myorder .= ' ' . strtoupper($order) . ' ';
        }
        if (!empty($myorder)) {
            $orderby[] = $myorder;
        }
    }

    if (isset($topicstart) && is_numeric($topicstart)) {
        $where[] = "firstpost.ptime >= ?";
        $bindvars[] = $starttime;
    }

    if (isset($topicend) && is_numeric($topicend)) {
        $where[] = "firstpost.ptime <= ?";
        $bindvars[] = $endtime;
    }

    if (!empty($towner) && is_numeric($towner)) {
        $where[] = $topicstable . '.towner = ?';
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
            $fulltext = xarModVars::get('articles', 'fulltextsearch');
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
                    $searchfield = $topicstable . '.ttitle';
                } elseif ($field == 'pdesc') {
                    $searchfield = $poststable . '.pdesc';
                } elseif ($field == 'ptext') {
                    $searchfield = $poststable . '.ptext';
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
    if (!empty($groupby)) {
        $query .= ' GROUP BY ' . join(',', $groupby);
    } else {
        $query .= ' GROUP BY ' . $topicstable . '.id';
    }
    if (!empty($orderby)) {
        $query .= ' ORDER BY ' . join(',', $orderby);
    }else {
        $query .= ' ORDER BY ' . $poststable . '.ptime DESC';
    }
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    if (!$result) return;
    $topics = array();
    // module defaults
    $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
        array('preset' => 'fsettings,fprivileges,ftransfields,ttransfields,ptransfields'));
    $loggedin = xarUserIsLoggedIn();
    $uid = xarUserGetVar('id');
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
        if (!$secLevel = xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $topic, 'priv' => 'viewforum'))) {
            $checkfailed = true;
            continue;
        }
        if ($topic['tstatus'] == 3) { //moved topic
            $moves = $topic['tsettings']['moved'];
            $move = !empty($moves) ? array_pop($moves) : array();
            if (!empty($move['tid'])) { // get the actual topic that was moved
                $moved = xarMod::apiFunc('crispbb', 'user', 'gettopic', array('tid' => $move['tid'], 'privcheck' => true));
                // user might not have privs for the forum the topic was moved to
                if (empty($moved) || !empty($moved['error'])) {
                    unset($topic); unset($moved); $checkfailed = true; continue;
                }
            }
        } elseif ($topic['tstatus'] == 2) {
            if (!xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                array('check' => $topic, 'priv' => 'approvetopics'))) {
                    $checkfailed = true;
                    continue;
            }
        }
        $topic['forumLevel'] = $secLevel;
        // add privs for current user level in this forum
        $topic['privs'] = $topic['fprivileges'][$secLevel];
        if (empty($nolinks)) {
            // forum viewers
            $topic['viewforumurl'] = xarModURL('crispbb', 'user', 'view',
                array('fid' => !empty($moved['fid']) ? $moved['fid'] : $topic['fid']));
            if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                array('check' => $topic, 'priv' => 'readforum'))) {
                // topic readers
                $topic['viewtopicurl'] = xarModURL('crispbb', 'user', 'display',
                    array('tid' => !empty($moved['tid']) ? $moved['tid'] : $topic['tid']));
                if (empty($moved)) {
                    $topic['lastreplyurl'] = xarModURL('crispbb', 'user', 'display',
                        array('tid' => $topic['tid'], 'action' => 'lastreply'));
                }
                // hooked module item
                if (!empty($topic['hookmodid']) && !empty($topic['objectid'])) {
                    $modname = xarModGetNameFromID($topic['hookmodid']);
                    $itemlinks = xarMod::apiFunc($modname, 'user', 'getitemlinks',
                        array('itemids' => array($topic['objectid'])), 0);
                    if (!empty($itemlinks[$topic['objectid']])) {
                        $topic['hookitem'] = $itemlinks[$topic['objectid']];
                    } else {
                        $hookitem = array();
                        $modinfo = xarMod::getInfo($topic['hookmodid']);
                        $ttitle = $modinfo['displayname'];
                        if (!empty($topic['hooktype'])) {
                            $ttitle .= ' ';
                            $mytypes = xarMod::apiFunc($modname, 'user', 'getitemtypes', array(), 0);
                            $ttitle .= !empty($mytypes[$topic['hooktype']]['label']) ? $mytypes[$topic['hooktype']]['label'] : $topic['hooktype'];
                            unset($mytypes);
                        }
                        $ttitle .= ' ' . $topic['objectid'];
                        $linkurl = xarModURL($modname, 'user', 'display', array('itemtype' => $topic['hooktype'], 'itemid' => $topic['objectid']));
                        $topic['hookitem'] = array('title' => xarVarPrepForDisplay($ttitle), 'label' => xarVarPrepForDisplay($ttitle), 'url' => $linkurl);
                        unset($ttitle);
                        unset($modinfo);
                        unset($linkurl);
                    }
                    if ($topic['forumLevel'] == 800) {
                        $topic['unlinkhookurl'] = xarModURL('crispbb', 'admin', 'unlinkhooks',
                            array('modid' => $topic['hookmodid'], 'itemtype' => $topic['hooktype'], 'itemid' => $topic['objectid']));
                    }
                    unset($modname);
                    unset($itemlinks);
                }

                if ($loggedin) {
                    // topic starters
                    if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                        array('check' => $topic, 'priv' => 'newtopic'))) {
                        $topic['newtopicurl'] = xarModURL('crispbb', 'user', 'newtopic',
                            array('fid' => $topic['fid']));
                    }
                    $tids = array();
                    $tids[$topic['tid']] = 1;
                    // only provide these links if the topic wasn't moved
                    if (empty($moved)) {
                        // topic repliers
                        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                            array('check' => $topic, 'priv' => 'newreply'))) {
                            $topic['newreplyurl'] = xarModURL('crispbb', 'user', 'newreply',
                                array('tid' => $topic['tid']));
                        }
                        // topic editors
                        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                            array('check' => $topic, 'priv' => 'edittopics'))) {
                            $topic['edittopicurl'] = xarModURL('crispbb', 'user', 'modifytopic',
                                array('tid' => $topic['tid']));
                        }
                        // topic approvers
                        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                            array('check' => $topic, 'priv' => 'approvetopics'))) {
                            if ($topic['tstatus'] == 2) {
                                $topic['approvetopicurl'] = xarModURL('crispbb', 'user', 'moderate',
                                    array(
                                        'component' => 'topics',
                                        'fid' => $topic['fid'],
                                        'tstatus' => $topic['tstatus'],
                                        'modaction' => 'approve',
                                        'phase' => 'update',
                                        'tids' => $tids,
                                ));
                            }
                        }
                        if (!empty($topic['numsubs']) && xarMod::apiFunc('crispbb', 'user','checkseclevel',
                            array('check' => $topic, 'priv' => 'approvereplies'))) {
                                $topic['modrepliesurl'] = xarModURL('crispbb', 'user', 'moderate',
                                    array(
                                        'component' => 'posts',
                                        'tid' => $topic['tid'],
                                        'pstatus' => 2
                                ));
                        }
                        if (!empty($topic['numdels']) && xarMod::apiFunc('crispbb', 'user','checkseclevel',
                            array('check' => $topic, 'priv' => 'deletereplies'))) {
                                $topic['modtrashcanurl'] = xarModURL('crispbb', 'user', 'moderate',
                                    array(
                                        'component' => 'posts',
                                        'tid' => $topic['tid'],
                                        'pstatus' => 5
                                ));
                        }
                        // topic closers
                        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
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
                        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
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
                        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
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
                        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
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
                        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
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
                        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
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
                        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
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
                        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
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
                        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
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
        $ftransformed = xarMod::apiFunc('crispbb', 'user', 'dotransforms', $transargs);
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
        $ttransformed = xarMod::apiFunc('crispbb', 'user', 'dotransforms', $transargs);
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
        $ptransformed = xarMod::apiFunc('crispbb', 'user', 'dotransforms', $transargs);
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