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
 * Standard function to view forum index
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_user_forum_index()
{
    if (!xarVar::fetch('catid', 'id', $catid, NULL, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('action', 'enum:read', $action, '', xarVar::NOT_REQUIRED)) return;
    if ($action == 'read') {
        if (!xarVar::fetch('fid', 'id', $readfid, NULL, xarVar::DONT_SET)) return;
    }

    $data = array();
    $now = time();
    $uid = xarUser::getVar('id');
    $tstatus = array(0,1,3,4); // open, closed, submitted, moved, locked topics

    // Get the forums
    $forums = xarMod::apiFunc('crispbb', 'user', 'getforums',
        array(
            'catid' => $catid,
            'bycat' => true,
            'tstatus' => $tstatus,
            'privcheck' => true
            ));
    // if the error was no privs, we should have an error message
    if (!empty($forums['error']) && $forums['error'] == 'NO_PRIVILEGES') {
        return xarTpl::module('privileges','user','errors',array('layout' => 'no_privileges'));
    }

    // Logged in user
    if (xarUser::isLoggedIn()) {
        // Start Tracking
        $tracker = unserialize(xarModUserVars::get('crispbb', 'tracker_object'));
        $data['readurl'] = xarController::URL('crispbb', 'user', 'forum_index', array('action' => 'read'));
    } else {
        $data['readurl'] = '';
    }

    // Get forum categories
    $mastertype = xarMod::apiFunc('crispbb', 'user', 'getitemtype',
        array('fid' => 0, 'component' => 'forum'));
    $basecats = xarMod::apiFunc('crispbb','user','getcatbases');
    $parentcat = count($basecats) > 0 ? $basecats[0] : 0;
    if (!empty($catid)) {
        $categories[$catid] = xarMod::apiFunc('categories', 'user', 'getcatinfo',
            array('cid' => $catid));
    } else {
        $categories = xarMod::apiFunc('categories', 'user', 'getchildren',
            array('cid' => $parentcat));
    }

    $minLevel = xarMod::apiFunc('crispbb', 'user', 'getseclevel',
        array('catid' => $catid));
    $seenposters = array();
    $seenLevels = array();
    $totaltopics = 0;
    $totalreplies = 0;
    if (!empty($categories)) {
        foreach ($categories as $cid => $category) {
            $catLevel = xarMod::apiFunc('crispbb', 'user', 'getseclevel',
                array('catid' => $cid));
            if (empty($catLevel)) { // No privs
                unset($categories[$cid]);
                if (isset($forums[$cid])) {
                    unset($forums[$cid]);
                }
                continue;
            }
            $catinfo = $category;
            $numforums = isset($forums[$cid]) ? count($forums[$cid]) : 0;
            $catinfo['numforums'] = $numforums;
            $catinfo['viewurl'] = xarController::URL('crispbb', 'user', 'forum_index', array('catid' => $cid));
            $categories[$cid] = $catinfo;
            if (!empty($numforums)) {
                foreach ($forums[$cid] as $fid => $forum) {
                    $finfo = $forum;
                    $seenLevel = $forum['forumLevel'];
                    $minLevel = isset($minLevel) && $seenLevel >= $minLevel ? $minLevel : $seenLevel;
                    if (!empty($seenLevel)) $seenLevels[$seenLevel] = $forum['fprivileges'][$seenLevel];
                    if (!empty($tracker)) {
                        $lastupdate = $tracker->lastUpdate($fid);
                        if ($action == 'read' && (empty($readfid) || $fid == $readfid)) {
                            $tracker->markRead($fid);
                        }
                        $lastread = $tracker->lastRead($fid);
                    } else {
                        $lastupdate = $lastread = time();
                    }
                    $unread = $lastread < $lastupdate ? true : false;

                    switch ($forum['fstatus']) {
                        case '0': // open
                        default:
                            $timeimage = 0; // read
                            if ($unread) { // unread
                                $timeimage = 1;
                            }
                            break;
                        case '1': // closed forum
                            $timeimage = 2; // read
                            if ($unread) { // unread
                                $timeimage = 3;
                            }
                            break;
                    }
                    $finfo['timeimage'] = $timeimage;
                    if (!empty($finfo['lasttid'])) {
                        $seenposters[$finfo['towner']] = 1;
                        $seenposters[$finfo['powner']] = 1;
                    }
                    if (!empty($finfo['privs']['approvetopics'])) {
                        $unnapproved = xarMod::apiFunc('crispbb', 'user', 'counttopics', array('tstatus' => 2, 'fid' => $fid));
                        if (!empty($unnapproved)) {
                            $finfo['modtopicsurl'] = xarController::URL('crispbb', 'user', 'moderate',
                                array(
                                    'component' => 'topics',
                                    'fid' => $finfo['fid'],
                                    'tstatus' => 2
                                ));
                        }
                    }
                    $forums[$cid][$fid] = $finfo;
                    $totaltopics = $totaltopics + $finfo['numtopics'];
                    $totalreplies = $totalreplies + $finfo['numreplies'];
                }
            } else {
                // hide cats with no forums?
                unset($categories[$cid]);
                continue;
            }
        }
    }

    $posteruids = !empty($seenposters) ? array_keys($seenposters) : array();
    // TODO: use crispbb getposters api function for this
    $posterlist = xarMod::apiFunc('crispbb', 'user', 'getposters', array('uidlist' => $posteruids, 'showstatus' => true));
    $data['posterlist'] = $posterlist;
    $data['categories'] = $categories;
    $data['forums'] = $forums;
    $data['catid'] = $catid;
    $data['totaltopics'] = $totaltopics;
    $data['totalreplies'] = $totalreplies;
    if (empty($minLevel) || empty($seenLevels[$minLevel]['locktopics'])) $tstatus = array(0,1,3);
    $data['totalunanswered'] = xarMod::apiFunc('crispbb', 'user', 'counttopics', array('tstatus' => $tstatus, 'noreplies' => true));

    $data['forumoptions'] = xarMod::apiFunc('crispbb', 'user', 'getitemlinks');
    $data['fids'] = implode(',', array_keys($data['forumoptions']));

    $pageTitle = empty($catid) ? xarML('Forum Index') : $categories[$catid]['name'];
    $data['pageTitle'] = $pageTitle;

    if (!empty($tracker)) {
        $data['userpanel'] = $tracker->getUserPanelInfo();
    }

    $data['viewstatsurl'] = !empty($seenLevels[$minLevel]['readforum']) ? xarController::URL('crispbb', 'user', 'stats') : '';
    xarTpl::setPageTitle(xarVar::prepForDisplay($pageTitle));
    if (!xarVar::fetch('theme', 'enum:rss:atom:xml:json', $theme, '', xarVar::NOT_REQUIRED)) return;
    if (!empty($theme)) {
        return xarTpl::module('crispbb', 'user', 'forum_index-' . $theme, $data);
    }
    return $data;
}
?>