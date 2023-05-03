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
function crispbb_user_modifytopic($args)
{
    extract($args);
    if (!xarVar::fetch('tid', 'id', $tid)) return;
    if (!xarVar::fetch('phase', 'enum:form:update', $phase, 'form', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('preview', 'checkbox', $preview, false, xarVar::NOT_REQUIRED)) return;

    $data = xarMod::apiFunc('crispbb', 'user', 'gettopic', array('tid' => $tid, 'privcheck' => true));

    if ($data == 'NO_PRIVILEGES' || empty($data['edittopicurl'])) {
        return xarTpl::module('privileges','user','errors',array('layout' => 'no_privileges'));
    }

    $forumLevel = $data['forumLevel'];
    $privs = $data['privs'];
    $uid = xarUser::getVar('id');
    $invalid = array();
    $now = time();

    $categories[$data['catid']] = xarMod::apiFunc('categories', 'user', 'getcatinfo',
            array('cid' => $data['catid']));
    $tracker = unserialize(xarModUserVars::get('crispbb', 'tracker_object'));
    $data['userpanel'] = $tracker->getUserPanelInfo();

    $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
        array('preset' => 'privactionlabels,privleveloptions,tstatusoptions,ttypeoptions'));
    $ttypeoptions = array();
    $ttypeoptions[] = $presets['ttypeoptions'][0];
    if (!empty($privs['stickies'])) {
        $ttypeoptions[1] = $presets['ttypeoptions'][1];
    }
    if (!empty($privs['announcements'])) {
        $ttypeoptions[2] = $presets['ttypeoptions'][2];
    }
    $data['ttypeoptions'] = $ttypeoptions;
    if (!empty($privs['faqs'])) {
        $ttypeoptions[3] = $presets['ttypeoptions'][3];
    }
    $data['ttypeoptions'] = $ttypeoptions;
    $tstatusoptions = array();
    $tstatusoptions[0] = $presets['tstatusoptions'][0];
    if (!empty($privs['closeowntopic']) || !empty($privs['closetopics'])) {
        $tstatusoptions[1] = $presets['tstatusoptions'][1];
    }
    if (!empty($privs['approvetopics'])) {
        $tstatusoptions[2] = $presets['tstatusoptions'][2];
    }
    if (!empty($privs['locktopics'])) {
        $tstatusoptions[4] = $presets['tstatusoptions'][4];
    }
    $data['tstatusoptions'] = $tstatusoptions;

    if (!isset($data['approvereplies'])) {
        $data['approvereplies'] = $data['replyapproval'];
    }

    if (!empty($data['iconfolder'])) {
        $iconlist = xarMod::apiFunc('crispbb', 'user', 'gettopicicons',
            array('iconfolder' => $data['iconfolder'], 'shownone' => true));
        $data['iconlist'] = $iconlist;
    }
    $data['ptext'] = $data['ttext'];
    $data['pdesc'] = $data['tdesc'];

    if ($phase == 'update' || $preview) {
        if (!xarVar::fetch('ttitle', 'str:1:100', $ttitle, '', xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('pdesc', 'str', $pdesc, '', xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('ptext', 'str', $ptext, '', xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('tstatus', 'int:0:10', $tstatus, 0, xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('ttype', 'int:0:10', $ttype, 0, xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('topicicon', 'str', $topicicon, 'none',xarVar::NOT_REQUIRED)) return;

        if (!xarVar::fetch('htmldeny', 'checkbox', $htmldeny, false, xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('bbcodedeny', 'checkbox', $bbcodedeny, false, xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('smiliesdeny', 'checkbox', $smiliesdeny, false, xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('approvereplies', 'checkbox', $approvereplies, false, xarVar::NOT_REQUIRED)) return;

        if (empty($privs['stickies']) && $ttype == 1) {
            //$invalid['ttype'] = xarML('You can not post sticky topics');
        }
        if (empty($privs['announcements']) && $ttype == 2) {
            //$invalid['ttype'] = xarML('You can not post announcements');
        }
        if (empty($privs['faqs']) && $ttype == 3) {
            //$invalid['ttype'] = xarML('You can not post FAQs');
        }
        if (empty($privs['closeowntopic']) && empty($privs['closetopics']) && $tstatus == 1) {
            $invalid['tstatus'] = xarML('You can not post closed topics');
        }
        if (empty($privs['locktopics']) && $tstatus == 4) {
            $invalid['tstatus'] = xarML('You can not post locked topics');
        }

        // transforms
        $hasbbcode = xarModHooks::isHooked('bbcode', 'crispbb', $data['topicstype']);
        $hassmilies = xarModHooks::isHooked('smilies', 'crispbb', $data['topicstype']);

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
                if (empty($data['ttransforms']['ttitle']['bbcode']) && empty($data['ttransforms']['tdesc']['bbcode']) && empty($data['ttransforms']['ttext']['bbcode'])) { // no fields, no bbcode
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
                if (empty($data['ttransforms']['ttitle']['smilies']) && empty($data['ttransforms']['tdesc']['smilies']) && empty($data['ttransforms']['ttext']['smilies'])) { // no fields, no smilies
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
        $transargs['itemtype'] = $data['topicstype'];
        $transargs['transforms'] = $data['ttransforms'];
        $transargs['ttitle'] = $ttitle;
        $transargs['tdesc'] = $pdesc;
        $transargs['ttext'] = $ptext;
        $ignore = array();
        if (!$hashtml) $ignore['html'] = 1;
        if (!$hasbbcode) $ignore['bbcode'] = 1;
        if (!$hassmilies) $ignore['smilies'] = 1;
        $transargs['ignore'] = $ignore;

        $transformed = xarMod::apiFunc('crispbb', 'user', 'dotransforms', $transargs);
        if (empty($iconlist[$topicicon])) $topicicon = 'none';

        $ttlen = strlen(strip_tags($transformed['ttitle']));
        if ($ttlen < $data['topictitlemin']) {
            $invalid['ttitle'] = xarML('Title must be at least #(1) characters', $data['topictitlemin']);
        } elseif ($ttlen > $data['topictitlemax']) {
            $invalid['ttitle'] = xarML('Title can not be more than #(1) characters', $data['topictitlemax']);
        }

        if (!empty($data['topicdescmin']) || !empty($data['topicdescmax'])) {
            $tdlen = strlen(strip_tags($transformed['tdesc']));
            if ($tdlen < $data['topicdescmin']) {
                $invalid['pdesc'] = xarML('Description must be at least #(1) characters', $data['topicdescmin']);
            } elseif ($tdlen > $data['topicdescmax']) {
                $invalid['pdesc'] = xarML('Description can not be more than #(1) characters', $data['topicdescmax']);
            }
        }

        $ptlen = strlen(strip_tags($transformed['ttext']));
        if ($ptlen < $data['topicpostmin']) {
            $invalid['ptext'] = xarML('Post must be at least #(1) characters', $data['topicpostmin']);
        } elseif ($ptlen > $data['topicpostmax']) {
            $invalid['ptext'] = xarML('Post can not be more than #(1) characters', $data['topicpostmax']);
        }

        $tsettings = array();
        $tsettings['topicicon'] = $topicicon;
        $tsettings['htmldeny'] = empty($privs['html']) || $htmldeny ? true : false;
        $tsettings['bbcodedeny'] = empty($privs['bbcode']) || $bbcodedeny ? true : false;
        $tsettings['smiliesdeny'] = empty($privs['smilies']) || $smiliesdeny ? true : false;
        $tsettings['approvereplies'] = $approvereplies;
        $psettings = array();
        $psettings = $tsettings;
        if (empty($invalid) && !$preview) {
            if (!xarSec::confirmAuthKey())
                return xarTpl::module('privileges','user','errors',array('layout' => 'bad_author'));
            if (!xarMod::apiFunc('crispbb', 'user', 'updatetopic',
                array(
                    'tid' => $tid,
                    'ttitle' => $ttitle,
                    'pdesc' => $pdesc,
                    'ptext' => $ptext,
                    'tstatus' => $tstatus,
                    'ttype' => $ttype,
                    'tsettings' => $tsettings,
                    'psettings' => $psettings,
                ))) return;
            if (!xarMod::apiFunc('crispbb', 'user', 'updatepost',
                array(
                    'pid' => $data['firstpid'],
                    'pdesc' => $pdesc,
                    'ptext' => $ptext,
                    'psettings' => $psettings,
                    'nohooks' => true
                ))) return;
            if (!xarMod::apiFunc('crispbb', 'user', 'updateposter',
                array('uid' => $data['towner']))) return;
            if (empty($return_url)) {
                $return_url = xarController::URL('crispbb', 'user', 'display',
                    array('tid' => $tid));
            }
            if (!empty($data['postbuffer'])) {
                $return_url = xarController::URL('crispbb', 'user', 'display',
                    array('tid' => $tid));
                xarVar::setCached('Meta.refresh','url', $return_url);
                xarVar::setCached('Meta.refresh','time', $data['postbuffer']);
                $pageTitle = xarML('Topic Updated');
                xarTpl::setPageTitle(xarVar::prepForDisplay($pageTitle));
                $data['pageTitle'] = $pageTitle;
                $data['tid'] = $tid;
                $data['ttitle'] = $ttitle;
                $data['pid'] = NULL;
                $data['message'] = xarML('Topic #(1) was updated successfully', $ttitle);
                return xarTpl::module('crispbb', 'user', 'return', $data);
            }

            return xarController::redirect($return_url);
        }

        // failed validation, pass the input back to the form
        $data['preview'] = $transformed;
        $privs['html'] = ($hashtml && !$htmldeny) || (!$hashtml && $htmldeny) ? true : false;
        $privs['bbcode'] = ($hasbbcode && !$bbcodedeny) || (!$hasbbcode && $bbcodedeny) ? true : false;
        $privs['smilies'] = ($hassmilies && !$smiliesdeny) || (!$hassmilies && $smiliesdeny) ? true : false;
        $data['ttitle'] = $ttitle;
        $data['pdesc'] = $pdesc;
        $data['ptext'] = $ptext;
        $data['ttype'] = $ttype;
        $data['tstatus'] = $tstatus;
        $data['htmldeny'] = $htmldeny;
        $data['smiliesdeny'] = $smiliesdeny;
        $data['bbcodedeny'] = $bbcodedeny;
        $data['topicicon'] = $topicicon;
    }


    $pageTitle = xarML('Edit #(1)', $data['ttitle']);
    $data['pageTitle'] = $pageTitle;
    $data['categories'] = $categories;
    $data['forumLevel'] = $forumLevel;
    $data['actions'] = $presets['privactionlabels'];
    $data['levels'] = $presets['privleveloptions'];
    $data['privs'] = $privs;
    $data['invalid'] = $invalid;

    // call hooks
    $item = array();
    $item['module'] = 'crispbb';
    $item['itemtype'] = $data['topicstype'];
    $item['itemid'] = $tid;
    $item['ttitle'] = $data['ttitle'];
    $item['ttext'] = $item['ptext'] = $data['ptext'];
    $item['tdesc'] = $item['pdesc'] = $data['pdesc'];
    $hooks = xarModHooks::call('item', 'modify', $tid, $item);

    $data['hookoutput'] = !empty($hooks) ? $hooks : array();

    $formaction =  xarModHooks::call('item', 'formaction', '', array(), 'crispbb', $data['topicstype']);
    $formdisplay = xarModHooks::call('item', 'formdisplay','', array(), 'crispbb', $data['topicstype']);
    $data['formaction'] = !empty($formaction) && is_array($formaction) ? join('',$formaction) : '';
    $data['formdisplay'] = !empty($formdisplay) && is_array($formdisplay) ? join('',$formdisplay) : '';

    if (xarVar::isCached('Hooks.dynamicdata','withupload') || xarModHooks::isHooked('uploads', 'crispbb', $data['topicstype'])) {
        $data['withupload'] = 1;
    } else {
        $data['withupload'] = 0;
    }

    xarTpl::setPageTitle(xarVar::prepForDisplay($pageTitle));

    return $data;
}
?>