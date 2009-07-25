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
function crispbb_user_view($args)
{

    extract($args);
    if (!xarVarFetch('fid', 'id', $fid)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('action', 'enum:read:unread', $action, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url', 'str:1', $return_url, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('sortfield', 'str:1', $sortfield, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortorder', 'enum:ASC:DESC:asc:desc', $sortorder, '', XARVAR_NOT_REQUIRED)) return;


    // select status of topics to include in topic count
    $tstatus = array(0,1,2,3,4); // open, closed, reported, moved and locked topics

    $forums = xarModAPIFunc('crispbb', 'user', 'getforums', array('tstatus' => $tstatus));

    if (empty($forums[$fid]['privs'])) {
        $msg = xarML('You do not have the privileges required for this action');
        $errorMsg['message'] = $msg;
        $errorMsg['return_url'] = xarModURL('crispbb', 'user', 'main');
        $errorMsg['type'] = $data['error'];
        $errorMsg['pageTitle'] = xarML('No Privileges');
        xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
        return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
    }

    $data = $forums[$fid];

    $data['tstatus'] = NULL;
    $privs = $data['privs'];
    $uid = xarUserGetVar('uid');
    $errorMsg = array();
    $invalid = array();
    $now = time();

    // user links
    if (xarUserIsLoggedIn()) {
        // Start Tracking
        $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));
        if ($action == 'read') {
            $tracking[$fid] = array(); // clear topics
            $tracking[$fid][0] = array(); // 0 = view forum
            $tracking[$fid][0]['lastread'] = $now; // set forum read now
        }
        $lastreadforum = !empty($tracking[$fid][0]['lastread']) ? $tracking[$fid][0]['lastread'] : 1;
        $tracking[$fid][0]['lastview'] = $now; // last view is now
    } else {
        $lastreadforum = $now;
    }

    if (!empty($data['iconfolder'])) {
        $iconlist = array();
        //$iconlist['none'] = array('id' => 'none', 'name' => xarML('None'));
        $topicicons = xarModAPIFunc('crispbb', 'user', 'browse_files', array('module' => 'crispbb', 'basedir' => 'xarimages/'.$data['iconfolder'], 'match_re' => '/(gif|png|jpg)$/'));
        if (!empty($topicicons)) {
            foreach ($topicicons as $ticon) {
                $tname =  preg_replace( "/\.\w+$/U", "", $ticon );
                $imagepath = $data['iconfolder'] . '/' . $ticon;
                $iconlist[$ticon] = array('id' => $ticon, 'name' => $tname, 'imagepath' => $imagepath);
            }
        }
        $data['iconlist'] = $iconlist;
    } else {
        $data['iconlist'] = array();
    }

    $seenposters = array();
    $categories[$data['catid']] = xarModAPIFunc('categories', 'user', 'getcatinfo',
            array('cid' => $data['catid']));
    $sort = !empty($sortfield) ? $sortfield : $data['topicsortfield'];
    $order = !empty($sortorder) ? $sortorder : $data['topicsortorder'];
    $tstatus = array(0,1,2,3); // default open, closed, moved
    if (!empty($privs['locktopics'])) {
        $tstatus[] = 4; // if you can lock topics, you can see them too
        // adjust numreplies for forumLevel less than 600
        if ($data['forumLevel'] < 600) {
            $data['numreplies'] = xarModAPIFunc('crispbb', 'user', 'countposts',
                array(
                    'fid' => $fid,
                    'tstatus' => $tstatus
                ));
        }
    }

    $todo = array();

    $topics = xarModAPIFunc('crispbb', 'user', 'gettopics',
        array(
            'fid' => $fid,
            'tstatus' => $tstatus,
            'startnum' => $startnum,
            'numitems' => $data['topicsperpage'],
            'ttype' => 0,
            'sort' => $sort,
            'order' => $order,
        ));

    $todo['topics'] = $topics;

    if ( (empty($startnum) || $startnum == 1) || $data['showstickies'] == 1 ) {
        $stickies = xarModAPIFunc('crispbb', 'user', 'gettopics',
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
        $numstickies = xarModAPIFunc('crispbb', 'user', 'counttopics',
            array(
                'fid' => $fid,
                'ttype' => 1,
                'tstatus' => $tstatus,
            ));
    }

    if ( (empty($startnum) || $startnum == 1) || $data['showannouncements'] == 1 ) {
        $announcements = xarModAPIFunc('crispbb', 'user', 'gettopics',
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
        $numannouncements = xarModAPIFunc('crispbb', 'user', 'counttopics',
            array(
                'fid' => $fid,
                'ttype' => 2,
                'tstatus' => $tstatus,
            ));
    }

    if ( (empty($startnum) || $startnum == 1) || $data['showfaqs'] == 1 ) {
        $faqs = xarModAPIFunc('crispbb', 'user', 'gettopics',
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
        $numfaqs = xarModAPIFunc('crispbb', 'user', 'counttopics',
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
            if (!empty($tracking)) { // only do this for relevent users
                $item['unreadurl'] = !empty($privs['readforum']) ? xarModURL('crispbb', 'user', 'display', array('tid' => $item['tid'], 'action' => 'unread')) : '';
                // has topic been updated since this forum was marked read?
                if ($lastreadforum < $item['ptime']) {
                    // has user read this topic since forum was marked read?
                    if (empty($tracking[$item['fid']][$key]) || ($tracking[$item['fid']][$key] < $item['ptime'])) {
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
    $posterlist = xarModAPIFunc('roles', 'user', 'getall', array('uidlist' => $posteruids));

    $data['posterlist'] = $posterlist;
    $pageTitle = $data['fname'];
    $data['categories'] = $categories;
    $data['pageTitle'] = $pageTitle;
    $presets = xarModAPIFunc('crispbb', 'user', 'getpresets',
        array('preset' => 'privactionlabels,privleveloptions,tstatusoptions'));
    $data['actions'] = $presets['privactionlabels'];
    $data['levels'] = $presets['privleveloptions'];

    $item = array();
    $item['module'] = 'crispbb';
    $item['itemtype'] = $data['itemtype'];
    $item['itemid'] = $fid;
    $item['return_url'] = xarModURL('crispbb', 'user', 'view', array('fid' => $fid, 'startnum' => $startnum));
    $hooks = xarModCallHooks('item', 'display', $fid, $item);
    $data['hookoutput'] = !empty($hooks) ? $hooks : array();

    $data['unanswered'] = xarModAPIFunc('crispbb', 'user', 'counttopics',
        array(
            'fid' => $fid,
            'tstatus' => $tstatus,
            'noreplies' => true
        ));
    $data['totalunanswered'] = xarModAPIFunc('crispbb', 'user', 'counttopics', array('tstatus' => $tstatus, 'noreplies' => true));
    // End Tracking
    if (!empty($tracking)) {
        $data['lastvisit'] = $tracking[0]['lastvisit'];
        $data['visitstart'] = $tracking[0]['visitstart'];
        $data['totalvisit'] = $tracking[0]['totalvisit'];
        xarVarSetCached('Blocks.crispbb', 'tracking', $tracking);
        xarModSetUserVar('crispbb', 'tracking', serialize($tracking));
    }

    $data['pager'] = xarTplGetPager($startnum,
        $data['numtopics'] - $numstickies - $numannouncements - $numfaqs,
        xarModURL('crispbb', 'user', 'view', array('fid' => $fid, 'startnum' => '%%')),
        $data['topicsperpage']);

    if ($data['numtopics'] > $data['topicsperpage']) {
        $pageNumber = empty($startnum) || $startnum < 2 ? 1 : round($startnum/$data['topicsperpage'])+1;
        $pageTitle .= ' - Page '.$pageNumber;
    }
    $data['forumoptions'] = xarModAPIFunc('crispbb', 'user', 'getitemlinks');
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
        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'closetopics'))) {
            $modactions[] = array('id' => 'open', 'name' => xarML('Open'));
            $modactions[] = array('id' => 'close', 'name' => xarML('Close'));
        } else {
            unset($tstatusoptions[1]);
        }
        // topic approvers
        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'approvetopics'))) {
                $modactions[] = array('id' => 'approve', 'name' => xarML('Approve'));
                $modactions[] = array('id' => 'disapprove', 'name' => xarML('Disapprove'));
        } else {
            unset($tstatusoptions[2]);
        }
        // topic movers
        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'movetopics'))) {
                $modactions[] = array('id' => 'move', 'name' => xarML('Move'));
        } else {
            unset($tstatusoptions[3]);
        }
        // topic lockers
        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'locktopics'))) {
                $modactions[] = array('id' => 'unlock', 'name' => xarML('Unlock'));
                $modactions[] = array('id' => 'lock', 'name' => xarML('Lock'));
        } else {
            unset($tstatusoptions[4]);
        }
        // topic deleters
        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'deletetopics'))) {
                $modactions[] = array('id' => 'delete', 'name' => xarML('Delete'));
        } else {
            unset($tstatusoptions[5]);
        }
        // forum editors
        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'editforum'))) {
                $modactions[] = array('id' => 'purge', 'name' => xarML('Purge'));
        }
        $data['modactions'] = $modactions;
        xarSessionSetVar('crispbb_return_url', xarServerGetCurrentURL());
    }

    xarTPLSetPageTitle(xarVarPrepForDisplay(xarML($pageTitle)));
    if (!xarVarFetch('theme', 'enum:rss:atom:xml:json', $theme, '', XARVAR_NOT_REQUIRED)) return;
    if (!empty($theme)) {
        return xarTPLModule('crispbb', 'user', 'view-' . $theme, $data);
    }
    return $data;

}
?>