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
 * Standard function to view items
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_admin_view($args)
{
    extract($args);
    if (!xarVarFetch('sublink', 'str:1:', $sublink, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('catid', 'id', $catid, NULL, XARVAR_NOT_REQUIRED)) return;
    $data = array();

    if (!xarSecurityCheck('AddCrispBB', 0)) {
        $errorMsg['message'] = xarML('You do not have the privileges required for this action');
        $errorMsg['return_url'] = xarModURL('crispbb', 'user', 'main');
        $errorMsg['type'] = 'NO_PRIVILEGES';
        $errorMsg['pageTitle'] = xarML('No Privileges');
        xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
        return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
    }
    $now = time();
    $tracking = xarMod::apiFunc('crispbb', 'user', 'tracking', array('now' => $now));
    // End Tracking
    if (!empty($tracking)) {
        xarVarSetCached('Blocks.crispbb', 'tracking', $tracking);
        xarModUserVars::set('crispbb', 'tracking', serialize($tracking));
    }
    // get forum categories
    $mastertype = xarMod::apiFunc('crispbb', 'user', 'getitemtype', array('fid' => 0, 'component' => 'forum'));
    $basecats = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'crispbb'));
    $parentcat = count($basecats) > 0 ? $basecats[0]['category_id'] : null;
    if (!empty($catid)) {
        $categories[$catid] = xarMod::apiFunc('categories', 'user', 'getcatinfo',
            array('cid' => $catid));
    } else {
        $categories = xarMod::apiFunc('categories', 'user', 'getchildren',
            array('cid' => $parentcat));
    }

    //get forum listing using a standard api function
    $forums = xarMod::apiFunc('crispbb', 'user', 'getforums',
        array(
            'catid' => $catid,
            'bycat' => true // returns array[catid][fid]
            ));

    // add links for cats and forums
    $numcats = count($categories);
    $ci = 1;
    $authid = xarSecGenAuthKey();
    $userLevel = xarMod::apiFunc('crispbb', 'user', 'getseclevel');
    $minLevel = 800;
    $secLevels = array();
    if (!empty($categories)) {
    foreach ($categories as $cid => $category) {
        $catinfo = $category;
        $catLevel = xarMod::apiFunc('crispbb', 'user', 'getseclevel',
            array('catid' => $cid));
        if ($catLevel < $minLevel) $minLevel = $catLevel;
        // SECCHECK: add_open_forums $cid:All IGNORE
        if ($catLevel < 400) { // No privs
            unset($categories[$cid]);
            if (isset($forums[$cid])) {
                unset($forums[$cid]);
            }
            continue;
        }
        // add re-order links for categories (admin only)
        if ($userLevel == 800 && empty($catid)) { // admin all forums, showing all forums
            if ($ci == 1) { // 1st cat
                $catinfo['upurl'] = ''; // can't move up
                if ($numcats > 1) { // can only move down if more than 1 cat
                    $catinfo['downurl'] = xarModURL('crispbb', 'admin', 'reordercats',
                        array('catid' => $cid, 'direction' => 'down', 'authid' => $authid));
                } else {
                    $catinfo['downurl'] = '';
                }
            } elseif ($ci == $numcats) { // last cat
                $catinfo['downurl'] = ''; // can't move down
                if ($numcats > 1) { // can only move up if more than 1 cat
                    $catinfo['upurl'] = xarModURL('crispbb', 'admin', 'reordercats',
                        array('catid' => $cid, 'direction' => 'up', 'authid' => $authid));
                } else {
                    $catinfo['upurl'] = '';
                }
            } else { // cats in between can move in either direction
                $catinfo['upurl'] = xarModURL('crispbb', 'admin', 'reordercats',
                        array('catid' => $cid, 'direction' => 'up', 'authid' => $authid));
                $catinfo['downurl'] = xarModURL('crispbb', 'admin', 'reordercats',
                        array('catid' => $cid, 'direction' => 'down', 'authid' => $authid));
            }
        } else {
            $catinfo['downurl'] = $catinfo['upurl'] = '';
        }
        // add forum count
        $numforums = isset($forums[$cid]) ? count($forums[$cid]) : 0;
        $catinfo['numforums'] = $numforums;
        $catinfo['viewurl'] = xarModURL('crispbb', 'admin', 'view', array('catid' => $cid));
        $ci++;
        $categories[$cid] = $catinfo;
        $recount = false;
        if (!empty($numforums)) {
            $fi = 1;
            foreach($forums[$cid] as $fid => $forum) {
                $item = $forum;
                // add re-order links for forums (admin only)
                if ($catLevel == 800) { // admin all forums in this category
                    if ($fi == 1) {
                        $item['upurl'] = '';
                        if ($numforums > 1) {
                            $item['downurl'] = xarModURL('crispbb', 'admin', 'reorder',
                                    array('fid' => $fid, 'catid' => $cid,'direction' => 'down', 'authid' => $authid)
                                );
                        } else {
                            $item['downurl'] = '';
                        }
                    } elseif ($fi == $numforums) {
                        $item['downurl'] = '';
                        if ($numforums > 1) {
                            $item['upurl'] = xarModURL('crispbb', 'admin', 'reorder',
                                array('fid' => $fid, 'catid' => $cid,'direction' => 'up', 'authid' => $authid)
                            );
                        } else {
                            $item['upurl'] = '';
                        }
                    } else {
                        $item['upurl'] = xarModURL('crispbb', 'admin', 'reorder',
                                array('fid' => $fid, 'catid' => $cid,'direction' => 'up', 'authid' => $authid)
                            );
                        $item['downurl'] = xarModURL('crispbb', 'admin', 'reorder',
                                array('fid' => $fid, 'catid' => $cid,'direction' => 'down', 'authid' => $authid)
                            );
                    }
                }
                if (empty($item['addforumurl']) && empty($item['editforumurl'])) {
                    $recount = true;
                    unset($forums[$cid][$fid]);
                    continue;
                }
                if (!empty($item['privs']['approvetopics'])) {
                    $unnapproved = xarMod::apiFunc('crispbb', 'user', 'counttopics', array('tstatus' => 2, 'fid' => $item['fid']));
                    if (!empty($unnapproved)) {
                        $item['modtopicsurl'] = xarModURL('crispbb', 'user', 'moderate',
                            array(
                                'component' => 'topics',
                                'fid' => $item['fid'],
                                'tstatus' => 2
                            ));
                    }
                }
                if (!empty($item['privs']['deletetopics'])) {
                    $deletedtopics = xarMod::apiFunc('crispbb', 'user', 'counttopics', array('tstatus' => 5, 'fid' => $item['fid']));
                    if (!empty($deletedtopics)) {
                        $item['modtrashcanurl'] = xarModURL('crispbb', 'user', 'moderate',
                            array(
                                'component' => 'topics',
                                'fid' => $item['fid'],
                                'tstatus' => 5
                            ));
                    }
                }
                if ($item['forumLevel'] <= $minLevel) {
                    $minLevel = $item['forumLevel'];
                    $secLevels = $item['fprivileges'];
                }
                $forums[$cid][$fid] = $item;
                $fi++;
            } // end forums loop
            // continue cat processing
            if ($recount) $categories[$cid]['numforums'] = count($forums[$cid]);
        }
    } // end categories loop
    }
    $data['categories'] = $categories;
    $data['forums'] = $forums;

    $data['menulinks'] = xarMod::apiFunc('crispbb', 'admin', 'getmenulinks',
        array(
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'view',
            'current_sublink' => $sublink,
            'catid' => $catid,
            'secLevels' => $secLevels
        ));

    $pageTitle = xarML('Manage Forums');
    // store function name for use by admin-main as an entry point
    xarSessionSetVar('crispbb_adminstartpage', 'view');
    xarTPLSetPageTitle(xarVarPrepForDisplay($pageTitle));

    return $data;

}
?>