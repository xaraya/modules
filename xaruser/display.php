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
 * Do something
 *
 * Standard function
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 * @throws none
 */
function crispbb_user_display($args)
{
    extract($args);
    if (!xarVarFetch('tid', 'id', $tid)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('action', 'enum:lastreply:unread', $action, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url', 'str:1', $return_url, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pid', 'id', $actionpid, NULL, XARVAR_DONT_SET)) return;

    $topic = xarMod::apiFunc('crispbb', 'user', 'gettopic', array('tid' => $tid, 'privcheck' => true, 'numdels' => true));

    if ($topic == 'NO_PRIVILEGES') {
        return xarTplModule('privileges','user','errors',array('layout' => 'no_privileges'));
    }

    $data = $topic;
    // Logged in user
    if (xarUserIsLoggedIn()) {
        // Start Tracking
        $tracker = unserialize(xarModUserVars::get('crispbb', 'tracker_object'));
        $data['userpanel'] = $tracker->getUserPanelInfo();
    }
    $forumLevel = $data['forumLevel'];
    $privs = $data['privs'];
    $uid = xarUserGetVar('id');
    $errorMsg = array();
    $invalid = array();
    $now = time();
    $pstatuses = array(0);
    if (!empty($privs['approvereplies'])) {
        $pstatuses[] = 2;
    }
    if (!empty($privs['editforum'])) {
        $pstatuses[] = 5;
    }

    if (count($pstatuses) > 1) {
        $data['numreplies'] = xarMod::apiFunc('crispbb', 'user', 'countposts',
            array('tid' => $tid, 'pstatus' => $pstatuses))-1;
    }

    if (!empty($action) || !empty($actionpid)) {
        $totalposts = $data['numreplies'] + 1;
        $postsperpage = $data['postsperpage'];
        $order = $data['postsortorder'];
        $lastpid = $data['lastpid'];

        if ($action == 'unread') {
            $lastreadtopic = $tracker->lastRead($data['fid'], $tid);
            if ($data['ptime'] > $lastreadtopic) {
                $totalunread = xarMod::apiFunc('crispbb', 'user', 'countposts',
                    array('tid' => $tid, 'starttime' => $lastreadtopic, 'pstatus' => $pstatuses));
                if (!empty($totalunread)) {
                    if (strtoupper($order) == 'ASC') { // last post on last page (default)
                        $totalposts = $totalposts - $totalunread;
                    } else { // last post is on first page
                        $totalposts = $totalunread;
                    }
                    $firstunread = xarMod::apiFunc('crispbb', 'user', 'getposts',
                        array(
                            'tid' => $tid,
                            'starttime' => $lastreadtopic,
                            'sort' => 'ptime',
                            'order' => $order,
                            'numitems' => 1,
                            'pstatus' => $pstatuses
                        ));
                    $firstunread = !empty($firstunread) && is_array($firstunread) ? reset($firstunread) : array();
                    if (!empty($firstunread['pid']) && $firstunread['pid'] != $data['firstpid']) {
                        $lastpid = $firstunread['pid'];
                    }
                }
            }
        } elseif (!empty($actionpid)) {
            $thepost = xarMod::apiFunc('crispbb', 'user', 'getpost',
                array('pid' => $actionpid));
            $previous = xarMod::apiFunc('crispbb', 'user', 'countposts',
                array('tid' => $thepost['tid'], 'endtime' => $thepost['ptime'], 'pstatus' => $pstatuses));
            if (!empty($previous)) {
                $lastpid = $actionpid;
                if (strtoupper($order) == 'ASC') { // last post on last page (default)
                    $totalposts = $previous;
                } else { // last post is on first page
                    $totalposts = $totalposts - $previous;
                }
            }
        } else {
            if (strtoupper($order) == 'DESC') { // last post is on first page
                $totalposts = 1;
            }
        }
        if ($lastpid == $data['firstpid']) {
            $lastpid = NULL;
        } else {
            $lastpid = 'post'.$lastpid;
        }
        if ($totalposts > $postsperpage) {
            $firstItem = 1;
            $lastItem = ($totalposts + $firstItem - 1);
            $lastPage = $lastItem - (($lastItem-$firstItem) % $postsperpage);
            if ($lastPage > 1) {
                $return_url = xarModURL('crispbb', 'user', 'display',
                    array('tid' => $tid, 'startnum' => $lastPage), NULL, $lastpid);
            } else {
                $return_url = xarModURL('crispbb', 'user', 'display',
                    array('tid' => $tid), NULL, $lastpid);
            }
        } else {
            $return_url = xarModURL('crispbb', 'user', 'display', array('tid' => $tid), NULL, $lastpid);
        }
        return xarResponse::Redirect($return_url, 301);
    }

    $pageTitle = $data['ttitle'];
    $categories[$data['catid']] = xarMod::apiFunc('categories', 'user', 'getcatinfo',
            array('cid' => $data['catid']));
    if (!empty($tracker)) {
        $tracker->markRead($data['fid'], $tid);
        $lastreadforum = $tracker->lastRead($data['fid']);
        $lastupdate = $tracker->lastUpdate($data['fid']);
        $unread = false;
        $thiststatus = array(0,1,2);
        if (!empty($privs['locktopics'])) $thiststatus[] = 4;
        if ($lastupdate > $lastreadforum) {
            $topicssince = xarMod::apiFunc('crispbb', 'user', 'gettopics',
                array('fid' => $data['fid'], 'starttime' => $lastreadforum, 'sort' => 'ptime', 'order' => 'DESC', 'tstatus' => $thiststatus));
            if (!empty($topicssince)) {
                $tids = array_keys($topicssince);
                $readtids = $tracker->seenTids($data['fid']);
                foreach ($tids as $seentid) {
                    if (in_array($seentid, $readtids)) continue;
                    $unread = true;
                    break;
                }
            }
        }
        if (!$unread) { // user has read all other topics in the forum
            $tracker->markRead($data['fid']);
        }
    }

    if (!empty($data['iconfolder'])) {
        $iconlist = xarMod::apiFunc('crispbb', 'user', 'gettopicicons',
            array('iconfolder' => $data['iconfolder']));
        $data['iconlist'] = $iconlist;
    } else {
        $data['iconlist'] = array();
    }
    if (!empty($data['topicicon']) && isset($iconlist[$data['topicicon']])) {
        $data['topicicon'] = $iconlist[$data['topicicon']]['imagepath'];
    } else {
        $data['topicicon'] = '';
    }

    xarVarSetCached('Blocks.crispbb', 'current_tid', $tid);
    $item = array();
    $item['module'] = 'crispbb';
    $item['itemtype'] = $data['topicstype'];
    $item['itemid'] = $tid;
    $item['tid'] = $tid;
    $item['returnurl'] = xarModURL('crispbb', 'user', 'display', array('tid' => $tid, 'startnum' => $startnum));
    xarVarSetCached('Hooks.hitcount','save', true);
    $hooks = xarModCallHooks('item', 'display', $tid, $item);

    $data['hookoutput'] = !empty($hooks) && is_array($hooks) ? $hooks : array();

    //$sort = $data['ttype'] == 3 ? 'pdesc' : 'ptime';
    //$order = $data['ttype'] == 3 ? 'ASC' : $data['postsortorder'];
    $sort = 'ptime';
    $order = $data['postsortorder'];
    $posts = xarMod::apiFunc('crispbb', 'user', 'getposts',
        array(
            'tid' => $tid,
            'sort' => $sort,
            'order' => $order,
            'startnum' => $startnum,
            'numitems' => $data['postsperpage'],
            'pstatus' => $pstatuses
        ));
    $seenposters = array();
    foreach ($posts as $pid => $post) {
        $item = $post;
        if (!empty($post['towner'])) $seenposters[$post['towner']] = 1;
        if (!empty($post['powner'])) $seenposters[$post['powner']] = 1;
        if ($post['firstpid'] == $pid) {
            if (!empty($data['topicicon'])) {
                $item['topicicon'] = $data['topicicon'];
            } else {
                $item['topicicon'] = '';
            }
            $item['hookoutput'] = $data['hookoutput'];
            if (xarModIsHooked('bbcode', 'crispbb', $item['topicstype']) && !empty($data['newreplyurl'])) {
                $item['quotereplyurl'] = xarModURL('crispbb', 'user', 'newreply', array('tid' => $tid, 'pids' => array($pid => 1)));
            }
        }   else {
            if (!empty($post['topicicon']) && isset($iconlist[$post['topicicon']])) {
                $item['topicicon'] = $iconlist[$post['topicicon']]['imagepath'];
            } else {
                $item['topicicon'] = '';
            }
            $hookitem = array();
            $hookitem['module'] = 'crispbb';
            $hookitem['itemtype'] = $post['poststype'];
            $hookitem['itemid'] = $post['pid'];
            $hookitem['pid'] = $post['pid'];
            $hookitem['returnurl'] = xarModURL('crispbb', 'user', 'display', array('tid' => $tid, 'startnum' => $startnum));
            $posthooks = xarModCallHooks('item', 'display', $post['pid'], $hookitem);
            $item['hookoutput'] = !empty($posthooks) && is_array($posthooks) ? $posthooks : array();
            unset($posthooks);
             if (xarModIsHooked('bbcode', 'crispbb', $item['poststype']) && !empty($data['newreplyurl'])) {
                $item['quotereplyurl'] = xarModURL('crispbb', 'user', 'newreply', array('tid' => $tid, 'pids' => array($pid => 1)));
            }
        }
        if ($data['fstatus'] == 0) { // open forum
            //$item['reporturl'] = xarModURL('crispbb', 'user', 'reportpost', array('pid' => $post['pid']));
        }
        $posts[$pid] = $item;
    }

    $uidlist = !empty($seenposters) ? array_keys($seenposters) : array();
    $posterlist = xarMod::apiFunc('crispbb', 'user', 'getposters', array('uidlist' => $uidlist, 'showstatus' => true));
    //$posterlist = xarMod::apiFunc('roles', 'user', 'getall', array('uidlist' => $uidlist));
    $data['posts'] = $posts;
    $data['categories'] = $categories;
    $data['pageTitle'] = $pageTitle;
    $data['posterlist'] = $posterlist;
    $data['uidlist'] = $uidlist;
    $data['forumLevel'] = $forumLevel;
    $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
        array('preset' => 'privactionlabels,privleveloptions,tstatusoptions'));
    $data['actions'] = $presets['privactionlabels'];
    $data['levels'] = $presets['privleveloptions'];
    $data['privs'] = $privs;
    // adjust hitcount to account for current view
    $data['numviews'] = $data['numviews'] + 1;
    $data['startnum'] = $startnum;
    $data['totalposts'] = $data['numreplies'] + 1;
    $tstatus = array(0,1,2,3);
    if (!empty($privs['locktopics'])) {
        $tstatus[] = 4;
    }
    $data['unanswered'] = xarMod::apiFunc('crispbb', 'user', 'counttopics',
        array(
            'fid' => $data['fid'],
            'tstatus' => $tstatus,
            'noreplies' => true
        ));
    $data['totalunanswered'] = xarMod::apiFunc('crispbb', 'user', 'counttopics', array('tstatus' => $tstatus, 'noreplies' => true));
    $pagerTpl = ($data['totalposts'] > (10*$data['postsperpage'])) ? 'multipage' : 'default';
    sys::import('xaraya.pager');
    $data['pager'] = xarTplGetPager($startnum,
        $data['totalposts'],
        xarModURL('crispbb', 'user', 'display', array('tid' => $tid, 'startnum' => '%%')),
        $data['postsperpage'],
        array(),
        $pagerTpl);
    if ($data['totalposts'] > $data['postsperpage']) {
        $pageNumber = empty($startnum) || $startnum < 2 ? 1 : round($startnum/$data['postsperpage'])+1;
        $pageTitle .= xarML(' - Page #(1)',$pageNumber);
    }
    $data['forumoptions'] = xarMod::apiFunc('crispbb', 'user', 'getitemlinks');
    xarTplSetPageTitle(xarVarPrepForDisplay($pageTitle));

    $data['viewstatsurl'] = xarModURL('crispbb', 'user', 'stats');

    if (!empty($data['modtopicurl'])) {
        $modactions = array();
        $check = $data;
        $tstatusoptions = $presets['tstatusoptions'];
        // reply approvers
        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'approvereplies'))) {
                $modactions[] = array('id' => 'approve', 'name' => xarML('Approve'));
                //$modactions[] = array('id' => 'disapprove', 'name' => xarML('Disapprove'));
        } else {
            unset($tstatusoptions[2]);
        }
        // topic splitters
        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'splittopics'))) {
                $modactions[] = array('id' => 'split', 'name' => xarML('Split'));
        } else {
            unset($tstatusoptions[3]);
        }

        // topic deleters
        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'deletereplies'))) {
                $modactions[] = array('id' => 'delete', 'name' => xarML('Delete'));
        } else {
            unset($tstatusoptions[5]);
        }
        // forum editors
        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'editforum'))) {
                $modactions[] = array('id' => 'purge', 'name' => xarML('Purge'));
        }
        $data['modactions'] = $modactions;
        xarSessionSetVar('crispbb_return_url', xarServer::getCurrentURL());
    }
    if (!xarVarFetch('theme', 'enum:rss:atom:xml:json', $theme, '', XARVAR_NOT_REQUIRED)) return;
    if (!empty($theme)) {
        return xarTPLModule('crispbb', 'user', 'display-' . $theme, $data);
    }
    return $data;
}
?>