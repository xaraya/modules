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

    $topic = xarModAPIFunc('crispbb', 'user', 'gettopic', array('tid' => $tid, 'privcheck' => true));

    if ($topic == 'NO_PRIVILEGES') {
        $errorMsg['message'] = xarML('You do not have the privileges required for this action');
        $errorMsg['return_url'] = xarModURL('crispbb', 'user', 'main');
        $errorMsg['type'] = 'NO_PRIVILEGES';
        $errorMsg['pageTitle'] = xarML('No Privileges');
        xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
        return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
    }

    $data = $topic;

    $forumLevel = $data['forumLevel'];
    $privs = $data['privs'];
    $uid = xarUserGetVar('uid');
    $errorMsg = array();
    $invalid = array();
    $now = time();

    if (!empty($action) || !empty($actionpid)) {
        $totalposts = $data['numreplies'] + 1;
        $postsperpage = $data['postsperpage'];
        $order = $data['postsortorder'];
        $lastpid = $data['lastpid'];
        if ($action == 'unread') {
            $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));
            $lastvisit = $tracking[0]['lastvisit'];
            $lastreadforum = !empty($tracking[$data['fid']][0]['lastread']) ? $tracking[$data['fid']][0]['lastread'] : $lastvisit;
            $lastreadtopic = !empty($tracking[$data['fid']][$tid]) ? $tracking[$data['fid']][$tid] : $lastreadforum;
            if ($data['ptime'] > $lastreadtopic) {
                $postssince = xarModAPIFunc('crispbb', 'user', 'getposts',
                    array(
                        'tid' => $tid,
                        'starttime' => $lastreadtopic,
                        'sort' => 'ptime',
                        'order' => $order,
                        'pstatus' => array(0,1)
                    ));
                $totalunread = count($postssince);
                if (!empty($postssince)) {
                    if (strtoupper($order) == 'ASC') { // last post on last page (default)
                        $totalposts = $totalposts - $totalunread;
                    } else { // last post is on first page
                        $totalposts = $totalunread;
                    }
                    foreach ($postssince as $pid => $post) {
                        $lastpid = $pid;
                        break;
                    }
                }
            }
        } elseif (!empty($actionpid)) {
            $allposts = xarModAPIFunc('crispbb', 'user', 'getposts',
                array('tid' => $tid, 'order' => $order));
            if (!empty($allposts)) {
                $i = 0;
                foreach ($allposts as $foundpid => $post) {
                    $i++;
                    if ($foundpid == $actionpid) break;
                }
                if (!empty($i)) {
                    $lastpid = $foundpid;
                    if (strtoupper($order) == 'ASC') { // last post on last page (default)
                        $totalposts = $i;
                    } else { // last post is on first page
                        $totalposts = $totalposts - $i;
                    }
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
        return xarResponseRedirect($return_url);
    }

    $pageTitle = $data['ttitle'];
    $categories[$data['catid']] = xarModAPIFunc('categories', 'user', 'getcatinfo',
            array('cid' => $data['catid']));
    $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));
    // Start Tracking
    if (!empty($tracking)) {
        $tracking[$data['fid']][$tid] = $now; // mark topic read
        $lastreadforum = !empty($tracking[$data['fid']][0]['lastread']) ? $tracking[$data['fid']][0]['lastread'] : 1;
        $lastupdate = !empty($tracking[$data['fid']][0]['lastupdate']) ? $tracking[$data['fid']][0]['lastupdate'] : $now;
        $unread = false;
        $thiststatus = array(0,1,2);
        if (!empty($privs['locktopics'])) $thiststatus[] = 4;
        if ($lastupdate > $lastreadforum) {
            $topicssince = xarModAPIFunc('crispbb', 'user', 'gettopics',
                array('fid' => $data['fid'], 'starttime' => $lastreadforum, 'sort' => 'ptime', 'order' => 'DESC', 'tstatus' => $thiststatus));
            if (!empty($topicssince)) {
                $tids = array_keys($topicssince);
                $readtids = array_keys($tracking[$data['fid']]);
                foreach ($tids as $seentid) {
                    if (in_array($seentid, $readtids)) continue;
                    $unread = true;
                    break;
                }
            }
        }
        if (!$unread) { // user has read all other topics in the forum
            $tracking[$data['fid']] = array();
            $tracking[$data['fid']][0] = array();
            $tracking[$data['fid']][0]['lastread'] = $now;
        }
        $tracking[$data['fid']][0]['lastview'] = $now;
        $data['lastvisit'] = $tracking[0]['lastvisit'];
        $data['visitstart'] = $tracking[0]['visitstart'];
        $data['totalvisit'] = $tracking[0]['totalvisit'];
        xarVarSetCached('Blocks.crispbb', 'tracking', $tracking);
        xarModSetUserVar('crispbb', 'tracking', serialize($tracking));
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
    $item['return_url'] = xarModURL('crispbb', 'user', 'display', array('tid' => $tid, 'startnum' => $startnum));
    xarVarSetCached('Hooks.hitcount','save', true);
    $hooks = xarModCallHooks('item', 'display', $tid, $item);

    $data['hookoutput'] = !empty($hooks) && is_array($hooks) ? $hooks : array();

    //$sort = $data['ttype'] == 3 ? 'pdesc' : 'ptime';
    //$order = $data['ttype'] == 3 ? 'ASC' : $data['postsortorder'];
    $sort = 'ptime';
    $order = $data['postsortorder'];
    $posts = xarModAPIFunc('crispbb', 'user', 'getposts',
        array(
            'tid' => $tid,
            'sort' => $sort,
            'order' => $order,
            'startnum' => $startnum,
            'numitems' => $data['postsperpage']
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
            $hookitem['return_url'] = xarModURL('crispbb', 'user', 'display', array('tid' => $tid, 'startnum' => $startnum));
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
    $posterlist = xarModAPIFunc('roles', 'user', 'getall', array('uidlist' => $uidlist));

    $data['posts'] = $posts;
    $data['categories'] = $categories;
    $data['pageTitle'] = $pageTitle;
    $data['posterlist'] = $posterlist;
    $data['uidlist'] = $uidlist;
    $data['forumLevel'] = $forumLevel;
    $presets = xarModAPIFunc('crispbb', 'user', 'getpresets',
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
    $data['unanswered'] = xarModAPIFunc('crispbb', 'user', 'counttopics',
        array(
            'fid' => $data['fid'],
            'tstatus' => $tstatus,
            'noreplies' => true
        ));
    $data['totalunanswered'] = xarModAPIFunc('crispbb', 'user', 'counttopics', array('tstatus' => $tstatus, 'noreplies' => true));
    $data['pager'] = xarTplGetPager($startnum,
        $data['totalposts'],
        xarModURL('crispbb', 'user', 'display', array('tid' => $tid, 'startnum' => '%%')),
        $data['postsperpage']);
    if ($data['totalposts'] > $data['postsperpage']) {
        $pageNumber = empty($startnum) || $startnum < 2 ? 1 : round($startnum/$data['postsperpage'])+1;
        $pageTitle .= xarML(' - Page #(1)',$pageNumber);
    }
    $data['forumoptions'] = xarModAPIFunc('crispbb', 'user', 'getitemlinks');
    xarTplSetPageTitle(xarVarPrepForDisplay($pageTitle));

    $data['viewstatsurl'] = xarModURL('crispbb', 'user', 'stats');

    if (!empty($data['modtopicurl'])) {
        $modactions = array();
        $check = $data;
        $tstatusoptions = $presets['tstatusoptions'];
        // topic approvers
        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'approvetopics'))) {
                $modactions[] = array('id' => 'approve', 'name' => xarML('Approve'));
                $modactions[] = array('id' => 'disapprove', 'name' => xarML('Disapprove'));
        } else {
            unset($tstatusoptions[2]);
        }
        // topic splitters
        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'splittopics'))) {
                $modactions[] = array('id' => 'split', 'name' => xarML('Split'));
        } else {
            unset($tstatusoptions[3]);
        }

        // topic deleters
        if (xarModAPIFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'deletereplies'))) {
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
    if (!xarVarFetch('theme', 'enum:rss:atom:xml:json', $theme, '', XARVAR_NOT_REQUIRED)) return;
    if (!empty($theme)) {
        return xarTPLModule('crispbb', 'user', 'display-' . $theme, $data);
    }
    return $data;
}
?>