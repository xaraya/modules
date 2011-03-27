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
function crispbb_userapi_counttopics($args)
{
    extract($args);
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $forumstable = $xartable['crispbb_forums'];
    $topicstable = $xartable['crispbb_topics'];
    $poststable = $xartable['crispbb_posts'];
    $hookstable = $xartable['crispbb_hooks'];

    $where = array();
    $bindvars = array();

    if (isset($hid) || isset($moduleid) || isset($itemtype) || isset($itemid)) {
        $from = $hookstable;
        $from .= ' LEFT JOIN ' . $topicstable;
        $from .= ' ON ' . $topicstable . '.id' . ' = ' . $hookstable . '.tid';
        // TODO:
    } else {
        $from = $topicstable;
    }
    $from .= ' LEFT JOIN ' . $forumstable;
    $from .= ' ON ' . $forumstable . '.id' . ' = ' . $topicstable . '.fid';
    if ($dbconn->databaseType != 'sqlite') {
        $from = '(' . $from . ')';
    }
    $from .= ' LEFT JOIN ' . $poststable;
    $from .= ' ON ' . $poststable . '.id' . ' = ' . $topicstable . '.lastpid';

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

    if (isset($noreplies)) {
        $where[] = $topicstable . '.firstpid = ' . $topicstable . '.lastpid';
    }

    if (isset($towner)) {
        $where[] = $topicstable . '.towner = ?';
        $bindvars[] = $towner;
    }

    if (isset($starttime) && is_numeric($starttime)) {
        $where[] = $poststable.".ptime >= ?";
        $bindvars[] = $starttime;
    }
    if (isset($endtime) && is_numeric($endtime)) {
        $where[] = $poststable.".ptime <= ?";
        $bindvars[] = $endtime;
    }

    if (isset($pstatus)) {
        if (is_numeric($pstatus)) {
            $where[] = $poststable . '.pstatus = ' . $pstatus;
        } elseif (is_array($pstatus) && count($pstatus) > 0) {
            $seenpstatus = array();
            foreach ($pstatus as $id) {
                if (!is_numeric($id)) continue;
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