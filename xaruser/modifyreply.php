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
    if (!xarVarFetch('pid', 'id', $pid)) return;
    if (!xarVarFetch('phase', 'enum:form:update', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('preview', 'checkbox', $preview, false, XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('crispbb', 'user', 'getpost', array('pid' => $pid, 'privcheck' => true));

    if ($data == 'NO_PRIVILEGES' || empty($data['editreplyurl'])) {
        $errorMsg = array();
        $errorMsg['message'] = xarML('You do not have the privileges required for this action');
        $errorMsg['return_url'] = xarModURL('crispbb', 'user', 'main');
        $errorMsg['type'] = 'NO_PRIVILEGES';
        $errorMsg['pageTitle'] = xarML('No Privileges');
        xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
        return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
    }
    /* TODO: eval this in here
    if (!empty($data['floodcontrol']) && empty($errorMsg)) {
        $lastpost = xarModAPIFunc('crispbb', 'user', 'getpost',
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
            $errorMsg['return_url'] = xarModURL('crispbb', 'user', 'display', array('tid' => $tid));
            $errorMsg['pageTitle'] = xarML('Flood control');
            $errorMsg['type'] = 'FLOOD_CONTROL';
            xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
            return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
        }
    }
    */
    $forumLevel = $data['forumLevel'];
    $privs = $data['privs'];
    $uid = xarUserGetVar('uid');
    $errorMsg = array();
    $invalid = array();
    $now = time();

    $categories[$data['catid']] = xarModAPIFunc('categories', 'user', 'getcatinfo',
            array('cid' => $data['catid']));

    $data['categories'] = $categories;

    $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));
    // End Tracking
    if (!empty($tracking)) {
        $tracking[$data['fid']][0]['lastview'] = $now;
        $data['lastvisit'] = $tracking[0]['lastvisit'];
        $data['visitstart'] = $tracking[0]['visitstart'];
        $data['totalvisit'] = $tracking[0]['totalvisit'];
        xarModSetUserVar('crispbb', 'tracking', serialize($tracking));
    }
    $presets = xarModAPIFunc('crispbb', 'user', 'getpresets',
        array('preset' => 'privactionlabels,privleveloptions,tstatusoptions,ttypeoptions'));
    $poststype = $data['poststype'];
    // transforms

    $hasbbcode = xarModIsHooked('bbcode', 'crispbb', $poststype);
    $hassmilies = xarModIsHooked('smilies', 'crispbb', $poststype);
    if (!$hasbbcode) $privs['bbcode'] = 0;
    if (!$hassmilies) $privs['smilies'] = 0;

    if (!empty($data['iconfolder'])) {
        $iconlist = array();
        $iconlist['none'] = array('id' => 'none', 'name' => xarML('None'));
        $topicicons = xarModAPIFunc('crispbb', 'user', 'browse_files', array('module' => 'crispbb', 'basedir' => 'xarimages/'.$data['iconfolder'], 'match_re' => '/(gif|png|jpg)$/'));
        if (!empty($topicicons)) {
            foreach ($topicicons as $ticon) {
                $tname =  preg_replace( "/\.\w+$/U", "", $ticon );
                $imagepath = $data['iconfolder'] . '/' . $ticon;
                $iconlist[$ticon] = array('id' => $ticon, 'name' => $tname, 'imagepath' => $imagepath);
            }
        }
        $data['iconlist'] = $iconlist;
    }


    if ($phase == 'update' || $preview) {
        if (!xarVarFetch('pdesc', 'str', $pdesc, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('ptext', 'str', $ptext, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('topicicon', 'str', $topicicon, 'none',XARVAR_NOT_REQUIRED)) return;

        if (!xarVarFetch('htmldeny', 'checkbox', $htmldeny, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('bbcodedeny', 'checkbox', $bbcodedeny, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('smiliesdeny', 'checkbox', $smiliesdeny, false, XARVAR_NOT_REQUIRED)) return;
        if (empty($iconlist[$topicicon])) $topicicon = 'none';
        // transforms
        $hasbbcode = xarModIsHooked('bbcode', 'crispbb', $poststype);
        $hassmilies = xarModIsHooked('smilies', 'crispbb', $poststype);

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

        $transformed = xarModAPIFunc('crispbb', 'user', 'dotransforms', $transargs);

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
            if (!xarSecConfirmAuthKey()) return;
            if (!xarModAPIFunc('crispbb', 'user', 'updatepost',
                array(
                    'pid' => $pid,
                    'pdesc' => $pdesc,
                    'ptext' => $ptext,
                    'psettings' => $psettings,
                    'poststype' => $data['poststype'],
                    'fid' => $data['fid']
                ))) return;
            if (empty($return_url)) {
                $return_url = xarModURL('crispbb', 'user', 'display',
                    array('tid' => $data['tid'], 'pid' => $pid));
            }
            if (!empty($data['postbuffer'])) {
                $return_url = xarModURL('crispbb', 'user', 'display',
                    array('tid' => $data['tid'], 'pid' => $pid));
                xarVarSetCached('Meta.refresh','url', $return_url);
                xarVarSetCached('Meta.refresh','time', $data['postbuffer']);
                $pageTitle = xarML('Reply Updated');
                xarTPLSetPageTitle(xarVarPrepForDisplay($pageTitle));
                $data['pageTitle'] = $pageTitle;
                $data['pid'] = $pid;
                $data['message'] = xarML('Reply to #(1) was updated successfully', $data['ttitle']);
                return xarTPLModule('crispbb', 'user', 'return', $data);
            }

            return xarResponseRedirect($return_url);
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
    xarTplSetPageTitle(xarVarPrepForDisplay($pageTitle));
    // call hooks
    $item = array();
    $item['module'] = 'crispbb';
    $item['itemtype'] = $poststype;
    $item['itemid'] = $pid;
    $item['ptext'] = $data['ptext'];
    $item['pdesc'] = $data['pdesc'];
    $hooks = xarModCallHooks('item', 'modify', $pid, $item);

    $data['hookoutput'] = !empty($hooks) ? $hooks : array();

    $formaction =  xarModCallHooks('item', 'formaction', '', array(), 'crispbb', $poststype);
    $formdisplay = xarModCallHooks('item', 'formdisplay','', array(), 'crispbb', $poststype);
    $data['formaction'] = !empty($formaction) && is_array($formaction) ? join('',$formaction) : '';
    $data['formdisplay'] = !empty($formdisplay) && is_array($formdisplay) ? join('',$formdisplay) : '';

    if (xarVarIsCached('Hooks.dynamicdata','withupload') || xarModIsHooked('uploads', 'crispbb', $data['topicstype'])) {
        $data['withupload'] = 1;
    } else {
        $data['withupload'] = 0;
    }
    return $data;
}
?>