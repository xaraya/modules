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
 * Standard function to view topics in a forum
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
 sys::import('xaraya.pager');
function crispbb_user_view($args)
{

    extract($args);
    if (!xarVarFetch('fid', 'id', $fid)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('action', 'enum:read:unread', $action, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url', 'str:1', $return_url, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('sort', 'str:1', $sortfield, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('order', 'enum:ASC:DESC:asc:desc', $sortorder, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('start', 'int:1', $starttime, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('end', 'int:1', $endtime, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('period', 'enum:day:week:month:year:beginning', $period, NULL, XARVAR_NOT_REQUIRED)) return;

    // select status of topics to include in topic count
    $tstatus = array(0,1,2,3,4); // open, closed, submitted, moved and locked topics

    $forums = xarMod::apiFunc('crispbb', 'user', 'getforums', array('tstatus' => $tstatus));

    if (empty($forums[$fid]['privs'])) {
        return xarTplModule('privileges','user','errors',array('layout' => 'no_privileges'));
    }

    if ($forums[$fid]['ftype'] == 1) {
        $redirecturl = $forums[$fid]['redirected']['redirecturl'];
        xarResponse::Redirect($redirecturl);
    }

    $data = $forums[$fid];

    $data['tstatus'] = NULL;
    $privs = $data['privs'];
    $uid = xarUserGetVar('id');
    $errorMsg = array();
    $invalid = array();
    $now = time();
    $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
        array('preset' => 'privactionlabels,privleveloptions,tstatusoptions,topicsortoptions,sortorderoptions'));
    // user links
    if (xarUserIsLoggedIn()) {
        $tracker = unserialize(xarModUserVars::get('crispbb', 'tracker_object'));
        $data['userpanel'] = $tracker->getUserPanelInfo();
        if ($action == 'read') {
            $tracker->markRead($fid);
        }
        $lastreadforum = $tracker->lastRead($fid);
    } else {
        $lastreadforum = $now;
    }

    if (!empty($data['iconfolder'])) {
        $iconlist = xarMod::apiFunc('crispbb', 'user', 'gettopicicons',
            array('iconfolder' => $data['iconfolder']));
        $data['iconlist'] = $iconlist;
    } else {
        $data['iconlist'] = array();
    }

    $seenposters = array();
    $categories[$data['catid']] = xarMod::apiFunc('categories', 'user', 'getcatinfo',
            array('cid' => $data['catid']));
    $tsortoptions = $presets['topicsortoptions'];
    $topicstype = xarMod::apiFunc('crispbb', 'user', 'getitemtype', array('fid' => $fid, 'component' => 'topics'));
    if (!xarModIsAvailable('ratings') || !xarModIsHooked('ratings', 'crispbb', $topicstype)) {
        unset($tsortoptions['numratings']);
    }
    if (empty($sort) && !empty($sortfield)) {
        $sort = $sortfield;
    }
    if (empty($sort) || empty($tsortoptions[$sort])) {
        $sort = $data['topicsortfield'];
    }
    $order = !empty($sortorder) ? $sortorder : $data['topicsortorder'];

    $data['tsortoptions'] = $tsortoptions;
    $data['orderoptions'] = $presets['sortorderoptions'];
    $data['sortfield'] = $sort;
    $data['sortorder'] = $order;
    if (!empty($period)) {
        switch($period) {
            case 'day':
                $starttime = $now-(24*60*60);
            break;
            case 'week':
                $starttime = $now-(7*24*60*60);
            break;
            case 'month':
                $starttime = $now-(30*24*60*60);
            break;
            case 'year':
                $starttime = $now-(365*24*60*60);
            break;
            case 'beginning':
                default:
                $starttime = 1;
            break;
        }
    }
    $timeoptions = array();
    $timeoptions[] = array('id' => 'day', 'name' => xarML('Last 24 Hours'));
    $timeoptions[] = array('id' => 'week', 'name' => xarML('Last 7 Days'));
    $timeoptions[] = array('id' => 'month', 'name' => xarML('Last Month'));
    $timeoptions[] = array('id' => 'year', 'name' => xarML('Last Year'));
    $timeoptions[] = array('id' => 'beginning', 'name' => xarML('Beginning'));
    $data['timeoptions'] = $timeoptions;
    $data['period'] = empty($period) ? 'year' : $period;

    $tstatus = array(0,1,3); // default open, closed, moved
    if (!empty($privs['locktopics'])) {
        $tstatus[] = 4; // if you can lock topics, you can see them too
        // adjust numreplies for forumLevel less than 600
        if ($data['forumLevel'] < 600) {
            $data['numreplies'] = xarMod::apiFunc('crispbb', 'user', 'countposts',
                array(
                    'fid' => $fid,
                    'tstatus' => $tstatus
                ));
        }
    }
    if (!empty($privs['approvetopics'])) {
        $tstatus[] = 2; // if you can approve topics, you can see submitted topics
        // adjust numreplies for forumLevel less than 600
        if ($data['forumLevel'] < 600) {
            $data['numreplies'] = xarMod::apiFunc('crispbb', 'user', 'countposts',
                array(
                    'fid' => $fid,
                    'tstatus' => $tstatus
                ));
        }
    }
    $todo = array();

    $topics = xarMod::apiFunc('crispbb', 'user', 'gettopics',
        array(
            'fid' => $fid,
            'tstatus' => $tstatus,
            'startnum' => $startnum,
            'numitems' => $data['topicsperpage'],
            'ttype' => 0,
            'sort' => $sort,
            'order' => $order,
            'starttime' => $starttime,
            'numsubs' => !empty($privs['approvereplies']) ? true : false,
            'numdels' => !empty($privs['deletereplies']) ? true : false,
        ));

    if (!empty($starttime)) {
        $data['numtopics'] = xarMod::apiFunc('crispbb', 'user', 'counttopics',
            array('fid' => $fid, 'tstatus' => $tstatus, 'ttype' => 0, 'starttime' => $starttime));
    }

    $todo['topics'] = $topics;

    if ( (empty($startnum) || $startnum == 1) || $data['showstickies'] == 1 ) {
        $stickies = xarMod::apiFunc('crispbb', 'user', 'gettopics',
            array(
                'fid' => $fid,
                'tstatus' => $tstatus,
                'startnum' => 1,
                'ttype' => 1,
                'sort' => $sort,
                'order' => $order,
            ));
        $numstickies = count($stickies);
        $todo['stickies'] = $stickies;
    } else {
        $numstickies = xarMod::apiFunc('crispbb', 'user', 'counttopics',
            array(
                'fid' => $fid,
                'ttype' => 1,
                'tstatus' => $tstatus,
            ));
    }

    if ( (empty($startnum) || $startnum == 1) || $data['showannouncements'] == 1 ) {
        $announcements = xarMod::apiFunc('crispbb', 'user', 'gettopics',
            array(
                'fid' => $fid,
                'tstatus' => $tstatus,
                'startnum' => 1,
                'ttype' => 2,
                'sort' => $sort,
                'order' => $order,
            ));
        $todo['announcements'] = $announcements;
        $numannouncements = count($announcements);
    } else {
        $numannouncements = xarMod::apiFunc('crispbb', 'user', 'counttopics',
            array(
                'fid' => $fid,
                'ttype' => 2,
                'tstatus' => $tstatus,
            ));
    }

    if ( (empty($startnum) || $startnum == 1) || $data['showfaqs'] == 1 ) {
        $faqs = xarMod::apiFunc('crispbb', 'user', 'gettopics',
            array(
                'fid' => $fid,
                'tstatus' => $tstatus,
                'startnum' => 1,
                'ttype' => 3,
                'sort' => $sort,
                'order' => $order,
            ));
        $todo['faqs'] = $faqs;
        $numfaqs = count($faqs);
    } else {
        $numfaqs = xarMod::apiFunc('crispbb', 'user', 'counttopics',
            array(
                'fid' => $fid,
                'ttype' => 3,
                'tstatus' => $tstatus,
            ));
    }

    foreach ($todo as $topictype => $topics) {
        if (empty($topics)) continue;
        foreach ($topics as $key => $topic) {
            $item = $topic;
            if (!empty($item['towner'])) $seenposters[$item['towner']] = 1;
            if (!empty($item['powner'])) $seenposters[$item['powner']] = 1;
            // tracking
            $unread = false;

            if (!empty($tracker)) {
                $item['unreadurl'] = !empty($privs['readforum']) ? xarModURL('crispbb', 'user', 'display', array('tid' => $item['tid'], 'action' => 'unread')) : '';
                // has topic been updated since this forum was marked read?
                if ($lastreadforum < $item['ptime']) {
                    // has user read this topic since forum was marked read?
                    if ($tracker->lastRead($item['fid'], $item['tid']) < $item['ptime']) {
                        $unread = true;
                    }
                }
            }
            $ishot = $item['numreplies'] >= $data['hottopicposts'] && $item['numviews'] >= $data['hottopichits'] && 0 >= $data['hottopicratings'] ? true : false;
            $thisstatus = $data['fstatus'] != 1 || $item['tstatus'] == 4 ? $item['tstatus'] : 1;
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
                case '2': // requires approval
                    $timeimage = 10;
                break;
                case '3':
                    $timeimage = 9; // moved topic
                break;
                case '4':
                    $timeimage = 8; // locked topic
                break;
            }
            $item['timeimage'] = $timeimage;
            if (!empty($topic['topicicon']) && isset($iconlist[$item['topicicon']])) {
                $item['topicicon'] = $iconlist[$item['topicicon']]['imagepath'];
            } else {
                $item['topicicon'] = '';
            }
            $data[$topictype][$item['tid']] = $item;
        }
    }

    $posteruids = !empty($seenposters) ? array_keys($seenposters) : array();
    $posterlist = xarMod::apiFunc('crispbb', 'user', 'getposters', array('uidlist' => $posteruids, 'showstatus' => true));

    $data['posterlist'] = $posterlist;
    $pageTitle = $data['fname'];
    $data['categories'] = $categories;
    $data['pageTitle'] = $pageTitle;

    $data['actions'] = $presets['privactionlabels'];
    $data['levels'] = $presets['privleveloptions'];

    $item = array();
    $item['module'] = 'crispbb';
    $item['itemtype'] = $data['itemtype'];
    $item['itemid'] = $fid;
    $item['return_url'] = xarModURL('crispbb', 'user', 'view', array('fid' => $fid, 'startnum' => $startnum));
    $hooks = xarModCallHooks('item', 'display', $fid, $item);
    $data['hookoutput'] = !empty($hooks) ? $hooks : array();

    $data['unanswered'] = xarMod::apiFunc('crispbb', 'user', 'counttopics',
        array(
            'fid' => $fid,
            'tstatus' => $tstatus,
            'noreplies' => true
        ));
    $data['totalunanswered'] = xarMod::apiFunc('crispbb', 'user', 'counttopics', array('tstatus' => $tstatus, 'noreplies' => true));

    $pagerTpl = $data['numtopics'] > (10*$data['topicsperpage']) ? 'multipage' : 'default';
    $data['pager'] = xarTplGetPager($startnum,
        $data['numtopics'] - $numstickies - $numannouncements - $numfaqs,
        xarModURL('crispbb', 'user', 'view', array('fid' => $fid, 'startnum' => '%%', 'sort' => $sort, 'order' => $order, 'period' => $period)),
        $data['topicsperpage'],
        array(),
        $pagerTpl);

    if ($data['numtopics'] > $data['topicsperpage']) {
        $pageNumber = empty($startnum) || $startnum < 2 ? 1 : round($startnum/$data['topicsperpage'])+1;
        $pageTitle .= ' - Page '.$pageNumber;
    }
    $data['forumoptions'] = xarMod::apiFunc('crispbb', 'user', 'getitemlinks');
    $data['viewstatsurl'] = !empty($privs['readforum']) ? xarModURL('crispbb', 'user', 'stats') : '';

    if (!empty($data['modforumurl'])) {
        $modactions = array();
        $check = array();
        $check['fid'] = $data['fid'];
        $check['catid'] = $data['catid'];
        $check['fstatus'] = $data['fstatus'];
        $check['fprivileges'] = $data['fprivileges'];
        $check['tstatus'] = 0;
        $check['towner'] = NULL;
        $tstatusoptions = $presets['tstatusoptions'];
        // topic closers
        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'closetopics'))) {
            $modactions[] = array('id' => 'open', 'name' => xarML('Open'));
            $modactions[] = array('id' => 'close', 'name' => xarML('Close'));
        } else {
            unset($tstatusoptions[1]);
        }
        // topic approvers
        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'approvetopics'))) {
                $modactions[] = array('id' => 'approve', 'name' => xarML('Approve'));
                $unnapproved = xarMod::apiFunc('crispbb', 'user', 'counttopics', array('tstatus' => 2, 'fid' => $fid));
                if (!empty($unnapproved)) {
                    $data['modtopicsurl'] = xarModURL('crispbb', 'user', 'moderate',
                        array(
                            'component' => 'topics',
                            'fid' => $fid,
                            'tstatus' => 2
                        ));
                }
        } else {
            unset($tstatusoptions[2]);
        }
        // topic movers
        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'movetopics'))) {
                $modactions[] = array('id' => 'move', 'name' => xarML('Move'));
        } else {
            unset($tstatusoptions[3]);
        }
        // topic lockers
        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'locktopics'))) {
                $modactions[] = array('id' => 'unlock', 'name' => xarML('Unlock'));
                $modactions[] = array('id' => 'lock', 'name' => xarML('Lock'));
        } else {
            unset($tstatusoptions[4]);
        }
        // topic deleters
        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'deletetopics'))) {
                $modactions[] = array('id' => 'delete', 'name' => xarML('Delete'));
                $deleted = xarMod::apiFunc('crispbb', 'user', 'counttopics', array('tstatus' => 5, 'fid' => $fid));
                if (!empty($deleted)) {
                    $data['modtrashcanurl'] = xarModURL('crispbb', 'user', 'moderate',
                        array(
                            'component' => 'topics',
                            'fid' => $fid,
                            'tstatus' => 5
                        ));
                }
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

    xarTPLSetPageTitle(xarVarPrepForDisplay(xarML($pageTitle)));
    if (!xarVarFetch('theme', 'enum:rss:atom:xml:json', $theme, '', XARVAR_NOT_REQUIRED)) return;
    if (!empty($theme)) {
        return xarTPLModule('crispbb', 'user', 'view-' . $theme, $data);
    }
    return $data;

}
?>