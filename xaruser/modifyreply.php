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
function crispbb_user_modifyreply($args)
{
    extract($args);
    if (!xarVar::fetch('pid', 'id', $pid)) return;
    if (!xarVar::fetch('phase', 'enum:form:update', $phase, 'form', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('preview', 'checkbox', $preview, false, xarVar::NOT_REQUIRED)) return;

    $data = xarMod::apiFunc('crispbb', 'user', 'getpost', array('pid' => $pid, 'privcheck' => true));

    if ($data == 'NO_PRIVILEGES' || empty($data['editreplyurl'])) {
        return xarTpl::module('privileges','user','errors',array('layout' => 'no_privileges'));
    }
    /* TODO: eval this in here
    if (!empty($data['floodcontrol']) && empty($errorMsg)) {
        $lastpost = xarMod::apiFunc('crispbb', 'user', 'getpost',
            array(
                'fid' => $data['fid'],
                'powner' => $uid,
                'sort' => 'ptime',
                'order' => 'DESC',
                'numitems' => 1
            ));
        if ($lastpost['ptime'] > $now-$data['floodcontrol']) {
            $errorMsg = $data;
            $errorMsg['message'] = xarML('This forum requires that you wait at least #(1) seconds between posts.');
            $errorMsg['return_url'] = xarController::URL('crispbb', 'user', 'display', array('tid' => $tid));
            $errorMsg['pageTitle'] = xarML('Flood control');
            $errorMsg['type'] = 'FLOOD_CONTROL';
            xarTpl::setPageTitle(xarVar::prepForDisplay($errorMsg['pageTitle']));
            return xarTpl::module('crispbb', 'user', 'error', $errorMsg);
        }
    }
    */
    $forumLevel = $data['forumLevel'];
    $privs = $data['privs'];
    $uid = xarUser::getVar('id');
    $errorMsg = array();
    $invalid = array();
    $now = time();

    $categories[$data['catid']] = xarMod::apiFunc('categories', 'user', 'getcatinfo',
            array('cid' => $data['catid']));

    $data['categories'] = $categories;

    $tracker = unserialize(xarModUserVars::get('crispbb', 'tracker_object'));
    $data['userpanel'] = $tracker->getUserPanelInfo();

    $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
        array('preset' => 'privactionlabels,privleveloptions,tstatusoptions,ttypeoptions'));
    $poststype = $data['poststype'];
    // transforms

    $hasbbcode = xarModHooks::isHooked('bbcode', 'crispbb', $poststype);
    $hassmilies = xarModHooks::isHooked('smilies', 'crispbb', $poststype);
    if (!$hasbbcode) $privs['bbcode'] = 0;
    if (!$hassmilies) $privs['smilies'] = 0;

    if (!empty($data['iconfolder'])) {
        $iconlist = xarMod::apiFunc('crispbb', 'user', 'gettopicicons',
            array('iconfolder' => $data['iconfolder'], 'shownone' => true));
        $data['iconlist'] = $iconlist;
    }


    if ($phase == 'update' || $preview) {
        if (!xarVar::fetch('pdesc', 'str', $pdesc, '', xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('ptext', 'str', $ptext, '', xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('topicicon', 'str', $topicicon, 'none',xarVar::NOT_REQUIRED)) return;

        if (!xarVar::fetch('htmldeny', 'checkbox', $htmldeny, false, xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('bbcodedeny', 'checkbox', $bbcodedeny, false, xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('smiliesdeny', 'checkbox', $smiliesdeny, false, xarVar::NOT_REQUIRED)) return;
        if (empty($iconlist[$topicicon])) $topicicon = 'none';
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

        if (!empty($data['topicdescmin']) || !empty($data['topicdescmax'])) {
            $tdlen = strlen(strip_tags($transformed['pdesc']));
            if ($tdlen < $data['topicdescmin']) {
                $invalid['pdesc'] = xarML('Description must be at least #(1) characters', $data['topicdescmin']);
            } elseif ($tdlen > $data['topicdescmax']) {
                $invalid['pdesc'] = xarML('Description can not be more than #(1) characters', $data['topicdescmax']);
            }
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
            if (!xarMod::apiFunc('crispbb', 'user', 'updatepost',
                array(
                    'pid' => $pid,
                    'pdesc' => $pdesc,
                    'ptext' => $ptext,
                    'psettings' => $psettings,
                    'poststype' => $data['poststype'],
                    'fid' => $data['fid']
                ))) return;
            if (!xarMod::apiFunc('crispbb', 'user', 'updateposter',
                array('uid' => $data['powner']))) return;
            if (empty($return_url)) {
                $return_url = xarController::URL('crispbb', 'user', 'display',
                    array('tid' => $data['tid'], 'pid' => $pid));
            }
            if (!empty($data['postbuffer'])) {
                $return_url = xarController::URL('crispbb', 'user', 'display',
                    array('tid' => $data['tid'], 'pid' => $pid));
                xarVar::setCached('Meta.refresh','url', $return_url);
                xarVar::setCached('Meta.refresh','time', $data['postbuffer']);
                $pageTitle = xarML('Reply Updated');
                xarTpl::setPageTitle(xarVar::prepForDisplay($pageTitle));
                $data['pageTitle'] = $pageTitle;
                $data['pid'] = $pid;
                $data['message'] = xarML('Reply to #(1) was updated successfully', $data['ttitle']);
                return xarTpl::module('crispbb', 'user', 'return', $data);
            }

            return xarController::redirect($return_url);
        }
        $data['preview'] = $transformed;
        $data['pdesc'] = $pdesc;
        $data['ptext'] = $ptext;
        $data['topicicon'] = $topicicon;
        $data['htmldeny'] = $htmldeny;
        $data['bbcodedeny'] = $bbcodedeny;
        $data['smiliesdeny'] = $smiliesdeny;
    }

    $pageTitle = xarML('Edit Reply');

    $data['pageTitle'] = $pageTitle;
    $data['actions'] = $presets['privactionlabels'];
    $data['levels'] = $presets['privleveloptions'];
    $data['invalid'] = $invalid;
    $data['powner'] = $uid;
    xarTpl::setPageTitle(xarVar::prepForDisplay($pageTitle));
    // call hooks
    $item = array();
    $item['module'] = 'crispbb';
    $item['itemtype'] = $poststype;
    $item['itemid'] = $pid;
    $item['ptext'] = $data['ptext'];
    $item['pdesc'] = $data['pdesc'];
    $hooks = xarModHooks::call('item', 'modify', $pid, $item);

    $data['hookoutput'] = !empty($hooks) ? $hooks : array();

    $formaction =  xarModHooks::call('item', 'formaction', '', array(), 'crispbb', $poststype);
    $formdisplay = xarModHooks::call('item', 'formdisplay','', array(), 'crispbb', $poststype);
    $data['formaction'] = !empty($formaction) && is_array($formaction) ? join('',$formaction) : '';
    $data['formdisplay'] = !empty($formdisplay) && is_array($formdisplay) ? join('',$formdisplay) : '';

    if (xarVar::isCached('Hooks.dynamicdata','withupload') || xarModHooks::isHooked('uploads', 'crispbb', $data['topicstype'])) {
        $data['withupload'] = 1;
    } else {
        $data['withupload'] = 0;
    }
    return $data;
}
?>