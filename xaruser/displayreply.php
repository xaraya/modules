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
function crispbb_user_displayreply($args)
{
    extract($args);
    if (!xarVar::fetch('startnum', 'int:1:', $startnum, 1, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('action', 'enum:lastreply:unread', $action, false, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('return_url', 'str:1', $return_url, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('pid', 'id', $pid, null, xarVar::DONT_SET)) {
        return;
    }

    $post = xarMod::apiFunc('crispbb', 'user', 'getpost', ['pid' => $pid, 'privcheck' => true]);

    if ($post == 'NO_PRIVILEGES') {
        $errorMsg['message'] = xarML('You do not have the privileges required for this action');
        $errorMsg['return_url'] = xarController::URL('crispbb', 'user', 'forum_index');
        $errorMsg['type'] = 'NO_PRIVILEGES';
        $errorMsg['pageTitle'] = xarML('No Privileges');
        xarTpl::setPageTitle(xarVar::prepForDisplay($errorMsg['pageTitle']));
        return xarTpl::module('crispbb', 'user', 'error', $errorMsg);
    }

    $topic = xarMod::apiFunc('crispbb', 'user', 'gettopic', ['tid' => $post['tid'], 'privcheck' => true]);

    $data = $topic;
    $tid = $data['tid'];
    // Logged in user
    if (xarUser::isLoggedIn()) {
        // Start Tracking
        $tracker = unserialize(xarModUserVars::get('crispbb', 'tracker_object'));
        $data['userpanel'] = $tracker->getUserPanelInfo();
    }
    $forumLevel = $data['forumLevel'];
    $privs = $data['privs'];
    $uid = xarUser::getVar('id');
    $errorMsg = [];
    $invalid = [];
    $now = time();
    $pstatuses = [0];
    if (!empty($privs['approvereplies'])) {
        $pstatuses[] = 2;
    }
    if (!empty($privs['editforum'])) {
        $pstatuses[] = 5;
    }

    if (count($pstatuses) > 1) {
        $data['numreplies'] = xarMod::apiFunc(
            'crispbb',
            'user',
            'countposts',
            ['tid' => $tid, 'pstatus' => $pstatuses]
        )-1;
    }

    $pageTitle = $data['ttitle'] . ' - Post ' . $pid;
    $categories[$data['catid']] = xarMod::apiFunc(
        'categories',
        'user',
        'getcatinfo',
        ['cid' => $data['catid']]
    );

    if (!empty($tracker)) {
        $tracker->markRead($data['fid'], $tid);
        $lastreadforum = $tracker->lastRead($data['fid']);
        $lastupdate = $tracker->lastUpdate($data['fid']);
        $unread = false;
        $thiststatus = [0,1,2];
        if (!empty($privs['locktopics'])) {
            $thiststatus[] = 4;
        }
        if ($lastupdate > $lastreadforum) {
            $topicssince = xarMod::apiFunc(
                'crispbb',
                'user',
                'gettopics',
                ['fid' => $data['fid'], 'starttime' => $lastreadforum, 'sort' => 'ptime', 'order' => 'DESC', 'tstatus' => $thiststatus]
            );
            if (!empty($topicssince)) {
                $tids = array_keys($topicssince);
                $readtids = $tracker->seenTids($data['fid']);
                foreach ($tids as $seentid) {
                    if (in_array($seentid, $readtids)) {
                        continue;
                    }
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
        $iconlist = xarMod::apiFunc(
            'crispbb',
            'user',
            'gettopicicons',
            ['iconfolder' => $data['iconfolder']]
        );
        $data['iconlist'] = $iconlist;
    } else {
        $data['iconlist'] = [];
    }
    if (!empty($data['topicicon']) && isset($iconlist[$data['topicicon']])) {
        $data['topicicon'] = $iconlist[$data['topicicon']]['imagepath'];
    } else {
        $data['topicicon'] = '';
    }

    xarVar::setCached('Blocks.crispbb', 'current_tid', $tid);
    $item = [];
    $item['module'] = 'crispbb';
    $item['itemtype'] = $data['topicstype'];
    $item['itemid'] = $tid;
    $item['tid'] = $tid;
    $item['returnurl'] = xarServer::getCurrentURL();
    xarVar::setCached('Hooks.hitcount', 'save', true);
    $hooks = xarModHooks::call('item', 'display', $tid, $item);

    $data['hookoutput'] = !empty($hooks) && is_array($hooks) ? $hooks : [];

    //$sort = $data['ttype'] == 3 ? 'pdesc' : 'ptime';
    //$order = $data['ttype'] == 3 ? 'ASC' : $data['postsortorder'];
    $sort = 'ptime';
    $order = $data['postsortorder'];
    $posts = xarMod::apiFunc(
        'crispbb',
        'user',
        'getposts',
        [
            'tid' => $tid,
            'pid' => $pid,
            'sort' => $sort,
            'order' => $order,
            'startnum' => $startnum,
            'numitems' => $data['postsperpage'],
            'pstatus' => $pstatuses,
        ]
    );
    $seenposters = [];
    foreach ($posts as $pid => $post) {
        $item = $post;
        if (!empty($post['towner'])) {
            $seenposters[$post['towner']] = 1;
        }
        if (!empty($post['powner'])) {
            $seenposters[$post['powner']] = 1;
        }
        if ($post['firstpid'] == $pid) {
            if (!empty($data['topicicon'])) {
                $item['topicicon'] = $data['topicicon'];
            } else {
                $item['topicicon'] = '';
            }
            $item['hookoutput'] = $data['hookoutput'];
            if (xarModHooks::isHooked('bbcode', 'crispbb', $item['topicstype']) && !empty($data['newreplyurl'])) {
                $item['quotereplyurl'] = xarController::URL('crispbb', 'user', 'newreply', ['tid' => $tid, 'pids' => [$pid => 1]]);
            }
        } else {
            if (!empty($post['topicicon']) && isset($iconlist[$post['topicicon']])) {
                $item['topicicon'] = $iconlist[$post['topicicon']]['imagepath'];
            } else {
                $item['topicicon'] = '';
            }
            $hookitem = [];
            $hookitem['module'] = 'crispbb';
            $hookitem['itemtype'] = $post['poststype'];
            $hookitem['itemid'] = $post['pid'];
            $hookitem['pid'] = $post['pid'];
            $hookitem['returnurl'] = xarController::URL('crispbb', 'user', 'display', ['tid' => $tid, 'startnum' => $startnum]);
            $posthooks = xarModHooks::call('item', 'display', $post['pid'], $hookitem);
            $item['hookoutput'] = !empty($posthooks) && is_array($posthooks) ? $posthooks : [];
            unset($posthooks);
            if (xarModHooks::isHooked('bbcode', 'crispbb', $item['poststype']) && !empty($data['newreplyurl'])) {
                $item['quotereplyurl'] = xarController::URL('crispbb', 'user', 'newreply', ['tid' => $tid, 'pids' => [$pid => 1]]);
            }
        }
        if ($data['fstatus'] == 0) { // open forum
            //$item['reporturl'] = xarController::URL('crispbb', 'user', 'reportpost', array('pid' => $post['pid']));
        }
        $posts[$pid] = $item;
    }

    $uidlist = !empty($seenposters) ? array_keys($seenposters) : [];
    $posterlist = xarMod::apiFunc('crispbb', 'user', 'getposters', ['uidlist' => $uidlist, 'showstatus' => true]);

    $data['posts'] = $posts;
    $data['categories'] = $categories;
    $data['pageTitle'] = $pageTitle;
    $data['posterlist'] = $posterlist;
    $data['uidlist'] = $uidlist;
    $data['forumLevel'] = $forumLevel;
    $presets = xarMod::apiFunc(
        'crispbb',
        'user',
        'getpresets',
        ['preset' => 'privactionlabels,privleveloptions,tstatusoptions']
    );
    $data['actions'] = $presets['privactionlabels'];
    $data['levels'] = $presets['privleveloptions'];
    $data['privs'] = $privs;
    // adjust hitcount to account for current view
    $data['numviews'] = $data['numviews'] + 1;
    $data['startnum'] = $startnum;
    $data['totalposts'] = $data['numreplies'] + 1;
    $tstatus = [0,1,2,3];
    if (!empty($privs['locktopics'])) {
        $tstatus[] = 4;
    }
    $data['unanswered'] = xarMod::apiFunc(
        'crispbb',
        'user',
        'counttopics',
        [
            'fid' => $data['fid'],
            'tstatus' => $tstatus,
            'noreplies' => true,
        ]
    );
    $data['totalunanswered'] = xarMod::apiFunc('crispbb', 'user', 'counttopics', ['tstatus' => $tstatus, 'noreplies' => true]);
    /*
    sys::import('modules.base.class.pager');
    $data['pager'] = xarTplPager::getPager($startnum,
        $data['totalposts'],
        xarController::URL('crispbb', 'user', 'display', array('tid' => $tid, 'startnum' => '%%')),
        $data['postsperpage']);
    */
    if ($data['totalposts'] > $data['postsperpage']) {
        $pageNumber = empty($startnum) || $startnum < 2 ? 1 : round($startnum/$data['postsperpage'])+1;
        $pageTitle .= xarML(' - Page #(1)', $pageNumber);
    }
    $data['forumoptions'] = xarMod::apiFunc('crispbb', 'user', 'getitemlinks');
    xarTpl::setPageTitle(xarVar::prepForDisplay($pageTitle));

    $data['viewstatsurl'] = xarController::URL('crispbb', 'user', 'stats');

    if (!empty($data['modtopicurl'])) {
        $modactions = [];
        $check = $data;
        $tstatusoptions = $presets['tstatusoptions'];
        // reply approvers
        if (xarMod::apiFunc(
            'crispbb',
            'user',
            'checkseclevel',
            ['check' => $check, 'priv' => 'approvereplies']
        )) {
            $modactions[] = ['id' => 'approve', 'name' => xarML('Approve')];
        //$modactions[] = array('id' => 'disapprove', 'name' => xarML('Disapprove'));
        } else {
            unset($tstatusoptions[2]);
        }
        // topic splitters
        if (xarMod::apiFunc(
            'crispbb',
            'user',
            'checkseclevel',
            ['check' => $check, 'priv' => 'splittopics']
        )) {
            $modactions[] = ['id' => 'split', 'name' => xarML('Split')];
        } else {
            unset($tstatusoptions[3]);
        }

        // topic deleters
        if (xarMod::apiFunc(
            'crispbb',
            'user',
            'checkseclevel',
            ['check' => $check, 'priv' => 'deletereplies']
        )) {
            $modactions[] = ['id' => 'delete', 'name' => xarML('Delete')];
        } else {
            unset($tstatusoptions[5]);
        }
        // forum editors
        if (xarMod::apiFunc(
            'crispbb',
            'user',
            'checkseclevel',
            ['check' => $check, 'priv' => 'editforum']
        )) {
            $modactions[] = ['id' => 'purge', 'name' => xarML('Purge')];
        }
        $data['modactions'] = $modactions;
        xarSession::setVar('crispbb_return_url', xarServer::getCurrentURL());
    }
    if (!xarVar::fetch('theme', 'enum:rss:atom:xml:json', $theme, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!empty($theme)) {
        return xarTpl::module('crispbb', 'user', 'display-' . $theme, $data);
    }
    return xarTpl::module('crispbb', 'user', 'display', $data);
}
