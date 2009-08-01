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
 * Do something
 *
 * Standard function
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 * @throws none
 */
function crispbb_user_search()
{
    // search args from search hook
    if (!xarVarFetch('q',         'isset',  $q,        NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('bool',      'isset',  $bool,     NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('sort',      'isset',  $sort,     NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('author',    'isset',  $author,   NULL, XARVAR_DONT_SET)) return;
    if(!xarVarFetch('startnum', 'int:0', $startnum,  NULL, XARVAR_NOT_REQUIRED)) {return;}

    // crispbb specific args from search form or redirect
    if (!xarVarFetch('crispbb_component', 'enum:replies:topics', $component, 'topics', XARVAR_DONT_SET)) return;
    if (!xarVarFetch('crispbb_fields', 'list', $searchfields, array(), XARVAR_DONT_SET)) return;
    if (!xarVarFetch('crispbb_fids', 'list', $fids, array(), XARVAR_DONT_SET)) return;
    if (!xarVarFetch('crispbb_start', 'int', $starttime, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('crispbb_end', 'int', $endtime, NULL, XARVAR_DONT_SET)) return;

    // search args from redirect
    if (!xarVarFetch('towner', 'id', $towner, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('powner', 'id', $powner, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('noreplies', 'checkbox', $noreplies, false, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('unread', 'checkbox', $unread, false, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('latest', 'checkbox', $latest, false, XARVAR_DONT_SET)) return;

    if (!xarVarFetch('start', 'int', $start, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('end', 'int', $end, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('fid', 'id', $fid, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('catid', 'id', $catid, NULL, XARVAR_DONT_SET)) return;

    $now = time();
    $data = array();
    $search = array();
    $results = array();
    $condition = '';
    $reqfields = array('ttitle', 'pdesc', 'ptext');
    list($current_module) = xarRequestGetInfo();
    $data['searchactive'] = $current_module == 'search' ? true : false;
    $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));

    $forums = xarModAPIFunc('crispbb', 'user', 'getforums');

    if (!isset($q) && !empty($forums)) { // no search performed, pre-select some options
        if (empty($fids)) {
        foreach($forums as $fid => $forum) {
            $fids[$fid] = 1;
        }
        }
        if (empty($searchfields)) {
        foreach ($reqfields as $reqfield) {
            $searchfields[$reqfield] = 1;
        }
        }
    }

    if (!empty($noreplies)) {
        $component = 'topics';
        $sort = 'ptime';
        $order = 'DESC';
    }

    if (!empty($unread)) {
        $component = 'topics';
        $sort = 'ptime';
        $order = 'DESC';
    }

    if (!empty($latest)) {
        $component = 'topics';
        $start = 1;
        $sort = 'ptime';
        $order = 'DESC';
    }

    if (!empty($author) && strlen($author) <> 0) {
        $user = xarModAPIFunc('roles', 'user', 'get', array('name' => $author));
        if (!empty($user)) {
            if ($component == 'topics') {
                $towner = $user['uid'];
            } else {
                $powner = $user['uid'];
            }
            //$authorid = $user['uid'];
            $data['author'] = $author;
        } else {
            unset($author);
        }
        unset($user);
    }

    if (isset($start) && is_numeric($start)) {
        $starttime = xarLocaleFormatDate("%Y-%m-%d %H:%M:%S",$start);
    }

    if (empty($starttime)) {
        $starttime = null;
        $data['crispbb_start'] = 'N/A';
    } else {
        if (!preg_match('/[a-zA-Z]+/',$starttime)) {
            $starttime .= ' GMT';
        }
        $starttime = strtotime($starttime);
        // adjust for the user's timezone offset
        $starttime -= xarMLS_userOffset() * 3600;
        if ($starttime > $now) {
            $starttime = $now;
        }
        $data['crispbb_start'] = $starttime;
    }

    if (isset($end) && is_numeric($end)) {
        $endtime = xarLocaleFormatDate("%Y-%m-%d %H:%M:%S",$end);
    }

    if (empty($endtime)) {
        $endtime = $now;
        $data['crispbb_end'] = $endtime;
    } else {
        if (!preg_match('/[a-zA-Z]+/',$endtime)) {
            $endtime .= ' GMT';
        }
        $endtime = strtotime($endtime);
        // adjust for the user's timezone offset
        $endtime -= xarMLS_userOffset() * 3600;
        if ($endtime > $now) {
            $endtime = $now;
        }
        $data['crispbb_end'] = $endtime;
    }

    if (!empty($towner) && is_numeric($towner)) {
        $user = xarModAPIFunc('roles','user','get',
                             array('uid' => $towner));
        if (empty($user['uid'])) {
            unset($towner);
        }
        unset($user);
    }

    if (!empty($powner) && is_numeric($powner)) {
        $user = xarModAPIFunc('roles','user','get',
                             array('uid' => $powner));
        if (empty($user['uid'])) {
            unset($powner);
        }
        unset($user);
    }

    if (!empty($q) || !empty($starttime) || $endtime != $now || !empty($powner) || !empty($towner) || !empty($search) || !empty($authorid) || !empty($noreplies)) {
        if (!empty($q)) {
            $search['q'] = $q;
        }
        $search['startnum'] = $startnum;
        $search['numitems'] = !empty($numitems) ? $numitems : 10;
        if (!empty($starttime)) {
            $search['starttime'] = $starttime;
        }
        if (!empty($endtime)) {
            $search['endtime'] = $endtime;
        }
        if (!empty($fids)) {
            $searchfids = array();
            foreach ($fids as $reqfid => $fidval) {
                if (empty($fidval) || !is_numeric($fidval) || empty($forums[$reqfid])) continue;
                $searchfids[$reqfid] = 1;
            }
            if (!empty($searchfids)) {
                $search['fid'] = array_keys($searchfids);
            } else {
                unset($fids);
            }
            unset($searchfids);
        }
        if (!empty($searchfields)) {
            $search['searchfields'] = array_keys($searchfields);
        }
        $search['tstatus'] = array(0,1);
        $search['pstatus'] = 0;
        switch ($component) {
            case 'topics':
                default:
                if (!empty($towner)) {
                    $search['towner'] = $towner;
                } elseif (!empty($authorid)) {
                    $search['towner'] = $authorid;
                }
                if (!empty($noreplies)) {
                    $search['noreplies'] = 1;
                }
                $search['sort'] = 'ptime';
                $search['order'] = 'DESC';
                $results = xarModAPIFunc('crispbb', 'user', 'gettopics', $search);
                $totalitems = xarModAPIFunc('crispbb', 'user', 'counttopics', $search);
            break;
            case 'replies':
                if (!empty($towner)) {
                    $search['author'] = $towner;
                } elseif (!empty($powner)) {
                    $search['powner'] = $powner;
                } elseif (!empty($authorid)) {
                    $search['author'] = $authorid;
                }
                $search['sort'] = 'ptime';
                $search['order'] = 'DESC';
                $results = xarModAPIFunc('crispbb', 'user', 'getposts', $search);
                $totalitems = xarModAPIFunc('crispbb', 'user', 'countposts', $search);;
            break;
        }
        if (!empty($starttime) && (empty($endtime) || $endtime == $now)) {
            $condition = 'since ' . xarLocaleFormatDate("%Y-%m-%d %H:%M:%S",$starttime);
        } elseif (!empty($endtime) && $endtime < $now && (empty($starttime))) {
            $condition = 'before ' . xarLocaleFormatDate("%Y-%m-%d %H:%M:%S",$endtime);
        } elseif (!empty($starttime) && !empty($endtime)) {
            $condition = 'between ' . xarLocaleFormatDate("%Y-%m-%d %H:%M:%S",$starttime) . ' and ' . xarLocaleFormatDate("%Y-%m-%d %H:%M:%S",$endtime);
        }
        if (!empty($condition)) {
            if ($component == 'topics') {
                $condition = 'topics with replies ' . $condition;
            } else {
                $condition = 'posts ' . $condition;
            }
        } else {
            $condition = $component == 'topics' ? $component : 'posts';
        }
        if (!empty($towner)) {
            $author = xarUserGetVar('name', $towner);
        } elseif (!empty($powner)) {
            $author = xarUserGetVar('name', $powner);
        }
        if (!empty($author)) {
            $condition .= ' by ' . $author;
        }
        if (!empty($noreplies)) {
            $condition .= ' with no replies';
        }
        if (!empty($q)) {
            $condition = $q . ' in ' . $condition;
        }
        if (!empty($search['fid']) && count($search['fid']) == 1) {
            $fid = $search['fid'][0];
            if (!empty($forums[$fid])) {
                $condition .= ' in ' . $forums[$fid]['transformed_fname'];
            }
        }

        $condition = 'Search for ' . $condition;
        if (!$data['searchactive'] && !empty($results)) {
            $seenposters = array();
            $iconlists = array();
            if ($component == 'topics') {
                foreach ($results as $key => $topic) {
                    $item = $topic;
                    $item['modtopicurl'] = '';
                    $item['modforumurl'] = '';
                    if (!empty($item['towner'])) $seenposters[$item['towner']] = 1;
                    if (!empty($item['powner'])) $seenposters[$item['powner']] = 1;
                    // tracking
                    $unread = false;
                    if (!empty($tracking)) { // only do this for relevent users
                        $lastreadforum = !empty($tracking[$item['fid']][0]['lastread']) ? $tracking[$item['fid']][0]['lastread'] : $tracking[0]['lastvisit'];
                        $item['unreadurl'] = !empty($item['privs']['readforum']) ? xarModURL('crispbb', 'user', 'display', array('tid' => $item['tid'], 'action' => 'unread')) : '';
                        // has topic been updated since this forum was marked read?
                        if ($lastreadforum < $item['ptime']) {
                            // has user read this topic since forum was marked read?
                            if (empty($tracking[$item['fid']][$key]) || ($tracking[$item['fid']][$key] < $item['ptime'])) {
                                $unread = true;
                            }
                        }
                    }
                    $ishot = $item['numreplies'] >= $item['hottopicposts'] && $item['numviews'] >= $item['hottopichits'] && 0 >= $item['hottopicratings'] ? true : false;
                    $thisstatus = $item['fstatus'] != 1 || $item['tstatus'] == 4 ? $item['tstatus'] : 1;
                    switch ($thisstatus) {
                        case '0': // open
                        default:
                            if (!$unread && !$ishot) { // read, open
                                $timeimage = 0;
                            } elseif (!$unread && $ishot) { // read, hot
                                $timeimage = 1;
                            } elseif ($unread && !$ishot) { // unread, open
                                $timeimage = 2;
                            } elseif ($unread && $ishot) { // unread, hot
                                $timeimage = 3;
                            }
                        break;
                        case '1': // closed
                            if (!$unread && !$ishot) { // read, closed
                                $timeimage = 4;
                            } elseif (!$unread && $ishot) { // read, hot, closed
                                $timeimage = 5;
                            } elseif ($unread && !$ishot) { // unread, open, closed
                                $timeimage = 6;
                            } elseif ($unread && $ishot) { // unread, hot, closed
                                $timeimage = 7;
                            }
                        break;
                        case '3':
                            $timeimage = 9; // moved topic
                        break;
                        case '4':
                            $timeimage = 8; // locked topic
                        break;
                    }
                    $item['timeimage'] = $timeimage;
                    if (!empty($topic['topicicon']) && (!empty($topic['iconfolder']))) {
                        $iconlist = array();
                        if (isset($iconlists[$item['fid']])) {
                            $iconlist = $iconlists[$item['fid']];
                        }
                        if (empty($iconlist)) {
                            //$iconlist['none'] = array('id' => 'none', 'name' => xarML('None'));
                            $topicicons = xarModAPIFunc('crispbb', 'user', 'browse_files', array('module' => 'crispbb', 'basedir' => 'xarimages/'.$topic['iconfolder'], 'match_re' => '/(gif|png|jpg)$/'));
                            if (!empty($topicicons)) {
                                foreach ($topicicons as $ticon) {
                                    $tname =  preg_replace( "/\.\w+$/U", "", $ticon );
                                    $imagepath = $topic['iconfolder'] . '/' . $ticon;
                                    $iconlist[$ticon] = array('id' => $ticon, 'name' => $tname, 'imagepath' => $imagepath);
                                }
                            }
                            $iconlists[$item['fid']] = $iconlist;
                        }
                        if (!empty($iconlist[$item['topicicon']])) {
                            $item['topicicon'] = $iconlist[$item['topicicon']]['imagepath'];
                        } else {
                            $item['topicicon'] = '';
                        }
                    }
                    $results[$item['tid']] = $item;
                }
                $posteruids = !empty($seenposters) ? array_keys($seenposters) : array();
                $data['uidlist'] = $posteruids;
                $data['posterlist'] = xarModAPIFunc('roles', 'user', 'getall', array('uidlist' => $posteruids));
                $data['showforum'] = true;
            } else {
                $seenposters = array();
                xarVarSetCached('Hooks.hitcount','save', true);
                foreach ($results as $pid => $post) {
                    $item = $post;
                    if (!empty($post['towner'])) $seenposters[$post['towner']] = 1;
                    if (!empty($post['powner'])) $seenposters[$post['powner']] = 1;
                    if ($post['firstpid'] == $pid) {
                        $hookitem = array();
                        $hookitem['module'] = 'crispbb';
                        $hookitem['itemtype'] = $post['topicstype'];
                        $hookitem['itemid'] = $post['tid'];
                        $hookitem['tid'] = $post['tid'];
                        $hookitem['return_url'] = xarModURL('crispbb', 'user', 'display', array('tid' => $item['tid'], 'startnum' => $startnum));
                        $item['hookoutput'] = xarModCallHooks('item', 'display', $post['tid'], $hookitem);
                    }   else {
                        $hookitem = array();
                        $hookitem['module'] = 'crispbb';
                        $hookitem['itemtype'] = $post['poststype'];
                        $hookitem['itemid'] = $post['pid'];
                        $hookitem['pid'] = $post['pid'];
                        $hookitem['return_url'] = xarModURL('crispbb', 'user', 'display', array('tid' => $item['tid'], 'startnum' => $startnum));
                        $posthooks = xarModCallHooks('item', 'display', $post['pid'], $hookitem);
                        $item['hookoutput'] = !empty($posthooks) && is_array($posthooks) ? $posthooks : array();
                        unset($posthooks);
                    }
                    if (!empty($post['topicicon']) && (!empty($post['iconfolder']))) {
                        $iconlist = array();
                        if (isset($iconlists[$item['fid']])) {
                            $iconlist = $iconlists[$item['fid']];
                        }
                        if (empty($iconlist)) {
                            //$iconlist['none'] = array('id' => 'none', 'name' => xarML('None'));
                            $topicicons = xarModAPIFunc('crispbb', 'user', 'browse_files', array('module' => 'crispbb', 'basedir' => 'xarimages/'.$post['iconfolder'], 'match_re' => '/(gif|png|jpg)$/'));
                            if (!empty($topicicons)) {
                                foreach ($topicicons as $ticon) {
                                    $tname =  preg_replace( "/\.\w+$/U", "", $ticon );
                                    $imagepath = $post['iconfolder'] . '/' . $ticon;
                                    $iconlist[$ticon] = array('id' => $ticon, 'name' => $tname, 'imagepath' => $imagepath);
                                }
                            }
                            $iconlists[$item['fid']] = $iconlist;
                        }
                        if (!empty($iconlist[$item['topicicon']])) {
                            $item['topicicon'] = $iconlist[$item['topicicon']]['imagepath'];
                        } else {
                            $item['topicicon'] = '';
                        }
                    }
                    if ($item['fstatus'] == 0) { // open forum
                        //$item['reporturl'] = xarModURL('crispbb', 'user', 'reportpost', array('pid' => $post['pid']));
                    }
                    $results[$pid] = $item;
                }

                $uidlist = !empty($seenposters) ? array_keys($seenposters) : array();
                $data['uidlist'] = $uidlist;
                $data['posterlist'] = xarModAPIFunc('roles', 'user', 'getall', array('uidlist' => $uidlist));
            }
        }
    }


    $data['results'] = $results;
    $data['forums'] = $forums;
    $data['crispbb_fids'] = $fids;
    $data['component'] = $component;
    $data['author'] = !empty($author) ? $author : '';
    $data['crispbb_fields'] = $searchfields;
    $data['q'] = !empty($q) ? $q : '';

    if (!empty($totalitems)) {
        $pageargs = array();
        if (!empty($q)) {
            $pageargs['q'] = $q;
        }
        if (!empty($fids)) {
            $pageargs['crispbb_fids'] = $fids;
        }
        if (!empty($noreplies)) {
            $pageargs['noreplies'] = 1;
        }
        if (!empty($starttime)) {
            $pageargs['start'] = $starttime;
        }
        if (!empty($endtime) && $endtime != $now) {
            $pageargs['end'] = $endtime;
        }
        if (!empty($author)) {
            $pageargs['author'] = $author;
        }
        if (!empty($searchfields)) {
            $pageargs['searchfields'] = $searchfields;
        }
        $pageargs['component'] = $component;
        $pageargs['startnum'] = '%%';
        $data['pager'] = xarTplGetPager($startnum,
            $totalitems,
            xarModURL('crispbb', 'user', 'search', $pageargs),
            $search['numitems']);
    }

    if (empty($forums)) {
        $data['status'] = xarML('No forums found to search');
    } elseif (!empty($search) && empty($results)) {
        $data['status'] = xarML('No #(1) found matching your criteria', $component);
    }
    if (!empty($tracking)) {
        $data['lastvisit'] = $tracking[0]['lastvisit'];
        $data['visitstart'] = $tracking[0]['visitstart'];
        $data['totalvisit'] = $tracking[0]['totalvisit'];
        xarVarSetCached('Blocks.crispbb', 'tracking', $tracking);
        xarModSetUserVar('crispbb', 'tracking', serialize($tracking));
    }
    if ($data['searchactive']) {
        return xarTPLModule('crispbb', 'user', 'searchhook', $data);
    } else {
        xarTPLSetPageTitle(xarVarPrepForDisplay(xarML('Search Forums')));
        $data['totalunanswered'] = xarModAPIFunc('crispbb', 'user', 'counttopics', array('noreplies' => true, 'tstatus' => array(0,1,2,4)));
        $data['forumoptions'] = xarModAPIFunc('crispbb', 'user', 'getmenulinks');
        $data['condition'] = xarVarPrepForDisplay(xarML($condition));
        if (!xarVarFetch('theme', 'enum:rss:atom:xml:json', $theme, '', XARVAR_NOT_REQUIRED)) return;
        if (!empty($theme)) {
            return xarTPLModule('crispbb', 'user', 'search-' . $theme, $data);
        }
        return $data;
    }
}
?>
