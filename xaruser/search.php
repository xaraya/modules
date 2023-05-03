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
sys::import('modules.base.class.pager');
function crispbb_user_search()
{
    // search args from search hook
    if (!xarVar::fetch('q',         'isset',  $q,        NULL, xarVar::DONT_SET)) return;
    if (!xarVar::fetch('bool',      'isset',  $bool,     NULL, xarVar::DONT_SET)) return;
    if (!xarVar::fetch('sort',      'isset',  $sort,     NULL, xarVar::DONT_SET)) return;
    if (!xarVar::fetch('author',    'isset',  $author,   NULL, xarVar::DONT_SET)) return;
    if(!xarVar::fetch('startnum', 'int:0', $startnum,  NULL, xarVar::NOT_REQUIRED)) {return;}

    // crispbb specific args from search form or redirect
    if (!xarVar::fetch('crispbb_component', 'enum:replies:topics', $component, 'topics', xarVar::DONT_SET)) return;
    if (!xarVar::fetch('crispbb_fields',    'str', $searchfields, '', xarVar::DONT_SET)) return;
    if (!xarVar::fetch('crispbb_fids',      'str', $fids, '', xarVar::DONT_SET)) return;
    if (!xarVar::fetch('crispbb_start',     'int', $starttime, NULL, xarVar::DONT_SET)) return;
    if (!xarVar::fetch('crispbb_end',       'int', $endtime, NULL, xarVar::DONT_SET)) return;

    // search args from redirect
    if (!xarVar::fetch('towner', 'id', $towner, NULL, xarVar::DONT_SET)) return;
    if (!xarVar::fetch('powner', 'id', $powner, NULL, xarVar::DONT_SET)) return;
    if (!xarVar::fetch('noreplies', 'checkbox', $noreplies, false, xarVar::DONT_SET)) return;
    if (!xarVar::fetch('unread', 'checkbox', $unread, false, xarVar::DONT_SET)) return;
    if (!xarVar::fetch('latest', 'checkbox', $latest, false, xarVar::DONT_SET)) return;

    if (!xarVar::fetch('start', 'int', $start, NULL, xarVar::DONT_SET)) return;
    if (!xarVar::fetch('end', 'int', $end, NULL, xarVar::DONT_SET)) return;
    if (!xarVar::fetch('fid', 'id', $fid, NULL, xarVar::DONT_SET)) return;
    if (!xarVar::fetch('catid', 'id', $catid, NULL, xarVar::DONT_SET)) return;

    $now = time();
    $data = array();
    $search = array();
    $results = array();
    $condition = '';
    $reqfields = array('ttitle', 'pdesc', 'ptext');
    list($current_module) = xarController::$request->getInfo();
    $data['searchactive'] = $current_module == 'search' ? true : false;

    sys::import('modules.crispbb.class.tracker');
    $tracker = unserialize(xarModUserVars::get('crispbb', 'tracker_object'));
    $data['userpanel'] = $tracker->getUserPanelInfo();
    $forums = xarMod::apiFunc('crispbb', 'user', 'getforums');

    if (!isset($q) && !empty($forums)) { // no search performed, pre-select some options
        if (empty($fids)) {
            $fids = implode(',', array_keys($forums));
        }
        if (empty($searchfields)) {
            $searchfields = implode(',', $reqfields);
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
        $user = xarMod::apiFunc('roles', 'user', 'get', array('name' => $author));
        if (!empty($user)) {
            if ($component == 'topics') {
                $towner = $user['id'];
            } else {
                $powner = $user['id'];
            }
            //$authorid = $user['uid'];
            $data['author'] = $author;
        } else {
            unset($author);
        }
        unset($user);
    }

    if (isset($start) && is_numeric($start)) {
        $starttime = xarLocale::formatDate("%Y-%m-%d %H:%M:%S",$start);
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
        $starttime -= xarMLS::userOffset() * 3600;
        if ($starttime > $now) {
            $starttime = $now;
        }
        $data['crispbb_start'] = $starttime;
    }

    if (isset($end) && is_numeric($end)) {
        $endtime = xarLocale::formatDate("%Y-%m-%d %H:%M:%S",$end);
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
        $endtime -= xarMLS::userOffset() * 3600;
        if ($endtime > $now) {
            $endtime = $now;
        }
        $data['crispbb_end'] = $endtime;
    }

    if (!empty($towner) && is_numeric($towner)) {
        $user = xarMod::apiFunc('roles','user','get',
                             array('id' => $towner));
        if (empty($user['id'])) {
            unset($towner);
        }
        unset($user);
    }

    if (!empty($powner) && is_numeric($powner)) {
        $user = xarMod::apiFunc('roles','user','get',
                             array('id' => $powner));
        if (empty($user['id'])) {
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
            $fidkeys = explode(',', $fids);
            foreach ($fidkeys as $reqfid) {
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
            $search['searchfields'] = explode(',', $searchfields);
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
                $results = xarMod::apiFunc('crispbb', 'user', 'gettopics', $search);
                $totalitems = xarMod::apiFunc('crispbb', 'user', 'counttopics', $search);
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
                $results = xarMod::apiFunc('crispbb', 'user', 'getposts', $search);
                $totalitems = xarMod::apiFunc('crispbb', 'user', 'countposts', $search);;
            break;
        }
        if (!empty($starttime) && (empty($endtime) || $endtime == $now)) {
            $condition = 'since ' . xarLocale::formatDate("%Y-%m-%d %H:%M:%S",$starttime);
        } elseif (!empty($endtime) && $endtime < $now && (empty($starttime))) {
            $condition = 'before ' . xarLocale::formatDate("%Y-%m-%d %H:%M:%S",$endtime);
        } elseif (!empty($starttime) && !empty($endtime)) {
            $condition = 'between ' . xarLocale::formatDate("%Y-%m-%d %H:%M:%S",$starttime) . ' and ' . xarLocale::formatDate("%Y-%m-%d %H:%M:%S",$endtime);
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
            $author = xarUser::getVar('name', $towner);
        } elseif (!empty($powner)) {
            $author = xarUser::getVar('name', $powner);
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

                    if (!empty($tracker)) {
                        $lastreadforum = $tracker->lastRead($item['fid']);

                        $item['unreadurl'] = !empty($item['privs']['readforum']) ? xarController::URL('crispbb', 'user', 'display', array('tid' => $item['tid'], 'action' => 'unread')) : '';
                        // has topic been updated since this forum was marked read?
                        if ($lastreadforum < $item['ptime']) {
                            // has user read this topic since forum was marked read?

                            if ($tracker->lastRead($item['fid'], $item['tid']) < $item['ptime']) {
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
                            $iconlist = xarMod::apiFunc('crispbb', 'user', 'gettopicicons',
                                array('iconfolder' => $topic['iconfolder']));
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
                $data['posterlist'] = xarMod::apiFunc('crispbb', 'user', 'getposters', array('uidlist' => $posteruids, 'showstatus' => true));
                $data['showforum'] = true;
            } else {
                $seenposters = array();
                xarVar::setCached('Hooks.hitcount','save', true);
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
                        $hookitem['returnurl'] = xarController::URL('crispbb', 'user', 'display', array('tid' => $item['tid'], 'startnum' => $startnum));
                        $item['hookoutput'] = xarModHooks::call('item', 'display', $post['tid'], $hookitem);
                    }   else {
                        $hookitem = array();
                        $hookitem['module'] = 'crispbb';
                        $hookitem['itemtype'] = $post['poststype'];
                        $hookitem['itemid'] = $post['pid'];
                        $hookitem['pid'] = $post['pid'];
                        $hookitem['returnurl'] = xarController::URL('crispbb', 'user', 'display', array('tid' => $item['tid'], 'startnum' => $startnum));
                        $posthooks = xarModHooks::call('item', 'display', $post['pid'], $hookitem);
                        $item['hookoutput'] = !empty($posthooks) && is_array($posthooks) ? $posthooks : array();
                        unset($posthooks);
                    }
                    if (!empty($post['topicicon']) && (!empty($post['iconfolder']))) {
                        $iconlist = array();
                        if (isset($iconlists[$item['fid']])) {
                            $iconlist = $iconlists[$item['fid']];
                        }
//                        if (empty($iconlist)) {
//                            $iconlist = xarMod::apiFunc('crispbb', 'user', 'gettopicicons',
//                                array('iconfolder' => $data['iconfolder']));
//                            $iconlists[$item['fid']] = $iconlist;
//                        }
                        if (!empty($iconlist[$item['topicicon']])) {
                            $item['topicicon'] = $iconlist[$item['topicicon']]['imagepath'];
                        } else {
                            $item['topicicon'] = '';
                        }
                    }
                    $results[$pid] = $item;
                }

                $uidlist = !empty($seenposters) ? array_keys($seenposters) : array();
                $data['uidlist'] = $uidlist;
                $data['posterlist'] = xarMod::apiFunc('crispbb', 'user', 'getposters', array('uidlist' => $uidlist, 'showstatus' => true));
            }
        }
    }

    $data['fids'] = $fids;
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
        $data['pager'] = xarTplPager::getPager($startnum,
            $totalitems,
            xarController::URL('crispbb', 'user', 'search', $pageargs),
            $search['numitems']);
    }

    if (empty($forums)) {
        $data['status'] = xarML('No forums found to search');
    } elseif (!empty($search) && empty($results)) {
        $data['status'] = xarML('No #(1) found matching your criteria', $component);
    }

    if ($data['searchactive']) {
        return xarTpl::module('crispbb', 'user', 'searchhook', $data);
    } else {
        xarTpl::setPageTitle(xarVar::prepForDisplay(xarML('Search Forums')));
        $data['totalunanswered'] = xarMod::apiFunc('crispbb', 'user', 'counttopics', array('noreplies' => true, 'tstatus' => array(0,1,2,4)));
        $data['forumoptions'] = xarMod::apiFunc('crispbb', 'user', 'getmenulinks');
        $data['condition'] = xarVar::prepForDisplay(xarML($condition));
        if (!xarVar::fetch('theme', 'enum:rss:atom:xml:json', $theme, '', xarVar::NOT_REQUIRED)) return;
        if (!empty($theme)) {
            return xarTpl::module('crispbb', 'user', 'search-' . $theme, $data);
        }
        return $data;
    }
}
?>
