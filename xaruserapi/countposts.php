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
function crispbb_userapi_countposts($args)
{
    extract($args);
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $forumstable = $xartable['crispbb_forums'];
    $topicstable = $xartable['crispbb_topics'];
    $poststable = $xartable['crispbb_posts'];
    $hookstable = $xartable['crispbb_hooks'];

    $where = array();
    $bindvars = array();


    $from = $poststable;

    $from .= ' LEFT JOIN ' . $topicstable;
    $from .= ' ON ' . $topicstable . '.xar_tid' . ' = ' . $poststable . '.xar_tid';
    if ($dbconn->databaseType != 'sqlite') {
        $from = '(' . $from . ')';
    }
    $from .= ' LEFT JOIN ' . $forumstable;
    $from .= ' ON ' . $forumstable . '.xar_fid' . ' = ' . $topicstable . '.xar_fid';
    if ($dbconn->databaseType != 'sqlite') {
        $from = '(' . $from . ')';
    }

    if (isset($pstatus)) {
        if (is_numeric($pstatus)) {
            $where[] = $poststable . '.xar_pstatus = ' . $pstatus;
        } elseif (is_array($pstatus) && count($pstatus) > 0) {
            $seenpstatus = array();
            foreach ($pstatus as $id) {
                if (!is_numeric($id)) continue;
                $seenpstatus[$id] = 1;
            }
            if (count($seenpstatus) == 1) {
                $pstatuses = array_keys($seenpstatus);
                $where[] = $poststable . '.xar_pstatus = ' . $pstatuses[0];
            } elseif (count($seenpstatus) > 1) {
                $pstatuses = join(', ', array_keys($seenpstatus));
                $where[] = $poststable . '.xar_pstatus IN (' . $pstatuses . ')';
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

    if (!empty($pid)) {
        if (is_numeric($pid)) {
            $where[] = $poststable . '.xar_pid = ' . $pid;
        } elseif (is_array($pid) && count($pid) > 0) {
            $seenpid = array();
            foreach ($pid as $id) {
                if (empty($id) || !is_numeric($id)) continue;
                $seenpid[$id] = 1;
            }
            if (count($seenpid) == 1) {
                $pids = array_keys($seenpid);
                $where[] = $poststable . '.xar_pid = ' . $pids[0];
            } elseif (count($seenpid) > 1) {
                $pids = join(', ', array_keys($seenpid));
                $where[] = $poststable . '.xar_pid IN (' . $pids . ')';
            }
        }
    }

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

    if (!empty($powner) && is_numeric($powner)) {
        $where[] = $poststable . '.xar_powner = ?';
        $bindvars[] = $powner;
    }

    if (!empty($towner) && is_numeric($towner)) {
        $where[] = $topicstable . '.xar_towner = ?';
        $bindvars[] = $towner;
    }

    if (!empty($author) && is_numeric($author)) {
        $where[] = '(' . $topicstable . '.xar_towner = ? OR ' . $poststable . '.xar_powner = ?)';
        $bindvars[] = $author;
        $bindvars[] = $author;
    }

    if (isset($starttime) && is_numeric($starttime)) {
        $where[] = $poststable.".xar_ptime >= ?";
        $bindvars[] = $starttime;
    }
    if (isset($endtime) && is_numeric($endtime)) {
        $where[] = $poststable.".xar_ptime <= ?";
        $bindvars[] = $endtime;
    }

    if (!empty($q))
    {
        $search = $q;
        // TODO : improve + make use of full-text indexing for recent MySQL versions ?
        if (empty($searchfields)) $searchfields = array();

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

    $query = "SELECT COUNT(1)";
    $query .= ' FROM ' . $from;
    if (!empty($where)) {
        $query .= ' WHERE ' . join(' AND ', $where);
    }

    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($numitems) = $result->fields;
    $result->Close();

    return $numitems;

}
?>