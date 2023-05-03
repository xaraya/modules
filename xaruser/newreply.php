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
function crispbb_user_newreply($args)
{
    extract($args);
    if (!xarVar::fetch('tid', 'id', $tid)) return;

    $data = xarMod::apiFunc('crispbb', 'user', 'gettopic', array('tid' => $tid, 'privcheck' => true));

    if ($data == 'NO_PRIVILEGES' || empty($data['newreplyurl'])) {
        return xarTpl::module('privileges','user','errors',array('layout' => 'no_privileges'));
    }

    $forumLevel = $data['forumLevel'];
    $privs = $data['privs'];
    $uid = xarUser::getVar('id');
    $errorMsg = array();
    $invalid = array();
    $now = time();
    // when coming from the hook function, we need to skip the flood control
    // (since this user will have just created a new topic)
    if (!empty($data['floodcontrol'])) {
        // the newtopic function lets us know if the hook is active
        $hook_active = xarSession::getVar('crispbb_hook_active');
        // if it is, we compare against the floodcontrol threshold
        if (!empty($hook_active)) {
            // we keep the hook active if the threshold hasn't passed,
            // this allows preview to work for this reply
            if ($hook_active < $now - $data['floodcontrol']) {
                // if the threshold has passed, we can deactivate the hook now
                $hook_active = false;
                xarSession::setVar('crispbb_hook_active', false);
            }
        }
    }
    if (!empty($data['floodcontrol']) && empty($errorMsg) && !$hook_active) {
        $lastpost = xarMod::apiFunc('crispbb', 'user', 'getposts',
            array(
                'fid' => $data['fid'],
                'powner' => $uid,
                'sort' => 'ptime',
                'order' => 'DESC',
                'numitems' => 1
            ));
        if (!empty($lastpost)) {
            $lastpost = reset($lastpost);
            if ($lastpost['ptime'] > $now-$data['floodcontrol']) {
                $errorMsg = $data;
                $errorMsg['message'] = xarML('This forum requires that you wait at least #(1) seconds between posts.', $data['floodcontrol']);
                $errorMsg['return_url'] = xarController::URL('crispbb', 'user', 'view', array('fid' => $data['fid']));
                $errorMsg['type'] = 'FLOOD_CONTROL';
                $errorMsg['pageTitle'] = xarML('Flood Control');
                xarTpl::setPageTitle(xarVar::prepForDisplay($errorMsg['pageTitle']));
                return xarTpl::module('crispbb', 'user', 'error', $errorMsg);
            }
        }
    }

    if (!xarVar::fetch('pids', 'list', $pids, array(), xarVar::NOT_REQUIRED)) return;

    if (!xarVar::fetch('pdesc', 'str', $pdesc, '', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('ptext', 'str', $ptext, '', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('pstatus', 'int:0:5', $pstatus, 0, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('topicicon', 'str', $topicicon, 'none',xarVar::NOT_REQUIRED)) return;

    if (!xarVar::fetch('phase', 'enum:form:update:quickreply:quotereply', $phase, 'form', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('preview', 'checkbox', $preview, false, xarVar::NOT_REQUIRED)) return;

    if (!xarVar::fetch('htmldeny', 'checkbox', $htmldeny, false, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('bbcodedeny', 'checkbox', $bbcodedeny, false, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('smiliesdeny', 'checkbox', $smiliesdeny, false, xarVar::NOT_REQUIRED)) return;

    if (!xarVar::fetch('return_url', 'str:1', $return_url, '', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('modname', 'str:1', $modname, '', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('itemtype', 'id', $itemtype, NULL, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('objectid', 'id', $objectid, NULL, xarVar::NOT_REQUIRED)) return;

    $categories[$data['catid']] = xarMod::apiFunc('categories', 'user', 'getcatinfo',
            array('cid' => $data['catid']));

    $data['categories'] = $categories;

    $tracker = unserialize(xarModUserVars::get('crispbb', 'tracker_object'));
    $data['userpanel'] = $tracker->getUserPanelInfo();

    $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
        array('preset' => 'privactionlabels,privleveloptions,tstatusoptions,ttypeoptions,pstatusoptions'));
    $poststype = xarMod::apiFunc('crispbb', 'user', 'getitemtype',
        array('fid' => $data['fid'], 'component' => 'posts'));

    if (!isset($data['approvereplies'])) {
        $data['approvereplies'] = $data['replyapproval'];
    }
    if ($data['approvereplies'] && empty($privs['approvereplies'])) {
        $pstatus = 2;
    }
    // transforms
    $hasbbcode = xarModHooks::isHooked('bbcode', 'crispbb', $poststype);
    $hassmilies = xarModHooks::isHooked('smilies', 'crispbb', $poststype);

    // always $hashtml
    if (!empty($privs['html'])) { // user has privs to use html
        // user can disable html?
        $htmldeny = !empty($privs['htmldeny']) ? $htmldeny : false;
        $hashtml = true;
    } else { // no privs, no html
        $hashtml = false;
    }
    // TODO: present html option for fields in modify forum
    // for now this is always empty, so we'll skip it
    /*
    if ($hashtml) { // check if we're transforming any fields
        if (empty($data['ttransforms']['ttitle']['html']) && empty($data['ttransforms']['tdesc']['html']) && empty($data['ttransforms']['ttext']['html'])) { // no fields, no html
            $hashtml = false;
        }
    }
    */
    if ($hashtml) { // still got html, check if it's been disabled
        if ($htmldeny) {
            $hashtml = false;
        }
    }

    $seenpids = array();
    if (!empty($pids) && is_array($pids)) {
        foreach ($pids as $qpid => $qval) {
            if (empty($qpid) || empty($qval)) continue;
            $seenpids[$qpid] = 1;
        }
    }

    if (!empty($seenpids)) {
        $quotes = xarMod::apiFunc('crispbb', 'user', 'getposts', array('pid' => array_keys($seenpids)));
        if (!empty($quotes)) {
            foreach ($quotes as $quote) {
                if ($hasbbcode) {
                    $ptext .= '[quote=' . xarUser::getVar('name',$quote['powner']) . ']' . $quote['ptext'] . '[/quote]';
                } elseif ($hashtml) {
                    $ptext .= '<blockquote>' . $quote['ptext'] . '</blockquote>';
                }
            }
        }
    }

    if ($hasbbcode) { // BBCode is available
        if (!empty($privs['bbcode'])) { // user has privs to use bbcode
            // user can disable bbcode?
            $bbcodedeny = !empty($privs['bbcodedeny']) ? $bbcodedeny : false;
        } else { // no privs, no bbcode
            $hasbbcode = false;
        }
        if ($hasbbcode) { // check if we're transforming any fields
            if (empty($data['ptransforms']['pdesc']['bbcode']) && empty($data['ptransforms']['ptext']['bbcode'])) { // no fields, no bbcode
                $hasbbcode = false;
            }
        }
        if ($hasbbcode) { // still got bbcode, check if it's been disabled
            if ($bbcodedeny) {
                $hasbbcode = false;
            }
        }
    }


    if ($hassmilies) { // Smilies are available
        if (!empty($privs['smilies'])) { // user has privs to use smilies
            // user can disable smilies?
            $smiliesdeny = !empty($privs['smiliesdeny']) ? $smiliesdeny : false;
        } else { // no privs, no smilies
            $hassmilies = false;
        }
        if ($hassmilies) { // check if we're transforming any fields
            if ( empty($data['ptransforms']['pdesc']['smilies']) && empty($data['ptransforms']['ptext']['smilies'])) { // no fields, no smilies
                $hassmilies = false;
            }
        }
        if ($hassmilies) { // still got smilies, check if it's been disabled
            if ($smiliesdeny) {
                $hassmilies = false;
            }
        }
    }

    $transargs = array();
    $transargs['itemtype'] = $poststype;
    $transargs['transforms'] = $data['ptransforms'];
    $transargs['pdesc'] = $pdesc;
    $transargs['ptext'] = $ptext;
    $ignore = array();
    if (!$hashtml) $ignore['html'] = 1;
    if (!$hasbbcode) $ignore['bbcode'] = 1;
    if (!$hassmilies) $ignore['smilies'] = 1;
    $transargs['ignore'] = $ignore;

    $transformed = xarMod::apiFunc('crispbb', 'user', 'dotransforms', $transargs);

    if (!empty($data['iconfolder'])) {
        $iconlist = xarMod::apiFunc('crispbb', 'user', 'gettopicicons',
            array('iconfolder' => $data['iconfolder'], 'shownone' => true));
        if (empty($iconlist[$topicicon])) $topicicon = 'none';
        $data['iconlist'] = $iconlist;
    }

    // for quick reply
    if ($phase == 'quickreply') {
        $phase = 'update';
        if (empty($pdesc)) { // we want to skip description validation if there isn't one
            $nodesc = 1;
        }
    }

    if ($phase == 'update' || $preview) {
        if ((!empty($data['topicdescmin']) || !empty($data['topicdescmax'])) && empty($nodesc)) {
            $tdlen = strlen(strip_tags($transformed['pdesc']));
            if ($tdlen < $data['topicdescmin']) {
                $invalid['pdesc'] = xarML('Description must be at least #(1) characters', $data['topicdescmin']);
            } elseif ($tdlen > $data['topicdescmax']) {
                $invalid['pdesc'] = xarML('Description can not be more than #(1) characters', $data['topicdescmax']);
            }
        }

        if ($data['approvereplies'] && $pstatus != 2 && empty($privs['approvereplies'])) {
            $pstatus = 2;
        }

        $ptlen = strlen(strip_tags($transformed['ptext']));
        if ($ptlen < $data['topicpostmin']) {
            $invalid['ptext'] = xarML('Post must be at least #(1) characters', $data['topicpostmin']);
        } elseif ($ptlen > $data['topicpostmax']) {
            $invalid['ptext'] = xarML('Post can not be more than #(1) characters', $data['topicpostmax']);
        }
        $psettings = array();
        $psettings['topicicon'] = $topicicon;
        $psettings['htmldeny'] = empty($privs['html']) || $htmldeny ? true : false;
        $psettings['bbcodedeny'] = empty($privs['bbcode']) || $bbcodedeny ? true : false;
        $psettings['smiliesdeny'] = empty($privs['smilies']) || $smiliesdeny ? true : false;

        if (empty($invalid) && !$preview) {
            if (!xarSec::confirmAuthKey())
                return xarTpl::module('privileges','user','errors',array('layout' => 'bad_author'));
            // log ip
            if (!isset($phostname) || empty($phostname)) {
                $forwarded = xarServer::getVar('HTTP_X_FORWARDED_FOR');

                if (!empty($forwarded)) {
                    $phostname = preg_replace('/,.*/', '', $forwarded);
                } else {
                    $phostname = xarServer::getVar('REMOTE_ADDR');
                }
            }
            $powner = $uid;
            $ptime = $now;
            if (!$pid = xarMod::apiFunc('crispbb', 'user', 'createpost',
                array(
                    'tid' => $tid,
                    'powner' => $powner,
                    'pstatus' => $pstatus,
                    'ptime' => $ptime,
                    'poststype' => $poststype,
                    'pdesc' => $pdesc,
                    'ptext' => $ptext,
                    'psettings' => $psettings,
                    'fid' => $data['fid']
                ))) return;
             // End Tracking
            if (!empty($tracker)) {
                $tracker->markRead($data['fid'], $tid);
                $lastreadforum = $tracker->lastRead($data['fid']);

                $unread = false;
                // get any topics since forum was last read
                $topicssince = xarMod::apiFunc('crispbb', 'user', 'gettopics',
                    array('fid' => $data['fid'], 'starttime' => $lastreadforum));
                if (!empty($topicssince)) {
                    $tids = array_keys($topicssince);
                    $readtids = $tracker->seenTids($data['fid']);
                    foreach ($tids as $newtid) { // look for any posts still unread
                        if (in_array($newtid, $readtids)) continue; // read it, skip it
                        $unread = true; // found an unread post
                        break; // only need to find one
                    }
                }
                if (!$unread) { // didn't find any unread posts, mark forum read
                    $tracker->markRead($data['fid']);
                }
            }
            if (!xarMod::apiFunc('crispbb', 'user', 'updateposter',
                array('uid' => $powner))) return;
            // ok to let subscribers know about this reply now
            if (xarMod::isAvailable('crispsubs') && $pstatus != 2) {
                $topicstype = xarMod::apiFunc('crispbb', 'user', 'getitemtype',
                    array('fid' => $data['fid'], 'component' => 'topics'));
                if (xarModHooks::isHooked('crispsubs', 'crispbb', $topicstype)) {
                    xarMod::apiFunc('crispsubs', 'user', 'updatehook',
                        array(
                            'modname' => 'crispbb',
                            'itemtype' => $topicstype,
                            'objectid' => $data['tid'],
                            'objecturl' => xarController::URL('crispbb', 'user', 'display',
                                array('tid' => $data['tid'], 'pid' => $pid))
                        ));
                }
            }
            if (!empty($data['postbuffer']) || $pstatus == 2) {
                if ($pstatus == 2) {
                    if (empty($return_url)) {
                    $return_url = xarController::URL('crispbb', 'user', 'display',
                        array('tid' => $tid, 'action' => 'lastreply'));
                    }
                    $data['postbuffer'] = 5;
                    $pageTitle = xarML('Reply Submitted');
                    $message = xarML('Thank you. Your reply has been submitted, and will be displayed once approved.');
                    $data['pid'] = NULL;
                } else {
                    $message = xarML('Your reply to #(1) was posted successfully', $data['ttitle']);
                    if (empty($return_url)) {
                        $return_url = xarController::URL('crispbb', 'user', 'display',
                            array('tid' => $tid, 'pid' => $pid));
                    }
                    $pageTitle = xarML('Reply Posted');
                    $data['tid'] = $tid;
                    $data['pid'] = $pid;
                }
                xarVar::setCached('Meta.refresh','url', $return_url);
                xarVar::setCached('Meta.refresh','time', $data['postbuffer']);
                xarTpl::setPageTitle(xarVar::prepForDisplay($pageTitle));
                $data['pageTitle'] = $pageTitle;
                $data['message'] = $message;
                return xarTpl::module('crispbb', 'user', 'return', $data);
            }
            if (empty($return_url)) {
                $return_url = xarController::URL('crispbb', 'user', 'display', array('tid' => $tid,  'action' => 'lastreply'));
            }
            return xarController::redirect($return_url);
        }
        $data['preview'] = $transformed;
        $data['htmldeny'] = $htmldeny;
        $data['bbcodedeny'] = $bbcodedeny;
        $data['smiliesdeny'] = $smiliesdeny;
    }

    $pageTitle = xarML('Reply to #(1)', $data['ttitle']);
    $privs['html'] = ($hashtml && !$htmldeny) || (!$hashtml && $htmldeny) ? true : false;
    $privs['bbcode'] = ($hasbbcode && !$bbcodedeny) || (!$hasbbcode && $bbcodedeny) ? true : false;
    $privs['smilies'] = ($hassmilies && !$smiliesdeny) || (!$hassmilies && $smiliesdeny) ? true : false;
    $data['pdesc'] = $pdesc;
    $data['ptext'] = $ptext;
    $data['pstatus'] = $pstatus;
    $data['topicicon'] = $topicicon;
    $data['invalid'] = $invalid;
    $data['pageTitle'] = $pageTitle;
    $data['privs'] = $privs;
    $data['actions'] = $presets['privactionlabels'];
    $data['levels'] = $presets['privleveloptions'];
    $data['powner'] = $uid;
    // call hooks
    $item = array();
    $item['module'] = 'crispbb';
    $item['itemtype'] = $poststype;
    $hooks = xarModHooks::call('item', 'new', '', $item);
    $data['hookoutput'] = !empty($hooks) ? $hooks : array();
    // coming from create hook, get the itemlink
    if (isset($modname) && isset($objectid)) {
         $itemlinks = xarMod::apiFunc($modname, 'user', 'getitemlinks',
             array('itemtype' => $itemtype, 'itemids' => array($objectid), ), 0);
        if (!empty($itemlinks[$objectid])) {
            $return_url = $itemlinks[$objectid]['url'];
        } else {
            $return_url = xarController::URL($modname, 'user', 'display', array('itemtype' => $itemtype, 'itemid' => $objectid));
        }
    }
    $data['return_url'] = $return_url;

    $formaction =  xarModHooks::call('item', 'formaction', '', array(), 'crispbb', $poststype);
    $formdisplay = xarModHooks::call('item', 'formdisplay','', array(), 'crispbb', $poststype);
    $data['formaction'] = !empty($formaction) && is_array($formaction) ? join('',$formaction) : '';
    $data['formdisplay'] = !empty($formdisplay) && is_array($formdisplay) ? join('',$formdisplay) : '';

    $pstatusoptions = $presets['pstatusoptions'];
    if (empty($privs['approvereplies'])) {
        unset($pstatusoptions[2]);
    }
    unset($pstatusoptions[5]);
    $data['pstatusoptions'] = $pstatusoptions;

    if (xarVar::isCached('Hooks.dynamicdata','withupload') || xarModHooks::isHooked('uploads', 'crispbb', $poststype)) {
        $data['withupload'] = 1;
    } else {
        $data['withupload'] = 0;
    }

    xarTpl::setPageTitle(xarVar::prepForDisplay($pageTitle));

    return $data;
}
?>