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
 */
/**
 * Modify an item
 *
 * This is a standard function that is called whenever an user
 * wishes to modify an existing module item
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @param array  $args An array containing all the arguments to this function.
 */
function crispbb_admin_modify($args)
{
    extract($args);
    if (!xarVarFetch('sublink', 'str:1:', $sublink, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'enum:form:update', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    // allow return url to be over-ridden
    if (!xarVarFetch('return_url', 'str:1:', $return_url, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fid', 'id', $fid, NULL, XARVAR_NOT_REQUIRED)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    $data['forum'] = DataObjectMaster::getObject(array('name' => 'crispbb_forums'));
    //$data['forum']->joinCategories();
    $fieldlist = array('fname','fdesc','fstatus','ftype','category');
    $data['forum']->setFieldlist($fieldlist);
    $data['forum']->userAction = 'editforum';
    $itemid = $data['forum']->getItem(array('itemid' => $fid));
    $basecats = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'crispbb'));
    $basecid = count($basecats) > 0 ? $basecats[0]['category_id'] : null;
    if ($itemid != $fid)
        return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));

    if (empty($data['forum']->userLevel))
        return xarTplModule('privileges','user','errors',array('layout' => 'no_privileges'));

    $data['forum']->getItemLinks();

    $userLevel = $data['forum']->userLevel;
    $secLevels = $data['forum']->fprivileges;

    $invalid = array();
    $pageTitle = $data['forum']->properties['fname']->value;
    $now = time();

    switch($sublink) {
        case 'edit':
            $ftype = $data['forum']->properties['ftype']->value;
            if ($ftype == 1) {
                $settingsfields = array('redirected');
                $layout = 'redirected';
            } else {
                $settingsfields = array('topicsperpage', 'topicsortorder', 'topicsortfield', 'postsperpage', 'postsortorder', 'hottopicposts', 'hottopichits', 'showstickies', 'showannouncements', 'showfaqs', 'topictitlemin', 'topictitlemax', 'topicdescmin', 'topicdescmax', 'topicpostmin', 'topicpostmax', 'floodcontrol', 'postbuffer', 'topicapproval', 'replyapproval','iconfolder','icondefault');
                $layout = 'normal';
            }
            $data['settings'] = DataObjectMaster::getObject(array('name' => 'crispbb_forum_settings'));
            $data['settings']->setFieldlist($settingsfields);
            $data['settings']->tplmodule = 'crispbb';
            $data['settings']->layout = $layout;
            $data['values'] = $data['forum']->fsettings;

            $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
                array('preset' => 'forumstatusoptions,topicsortoptions,sortorderoptions,pagedisplayoptions,ftransfields,ttransfields,ptransfields,ftypeoptions'));

            // handle update
            if ($phase == 'update') {
                $isvalid = $data['forum']->checkInput();
                $cids = $data['forum']->properties['category']->categories;
                if (is_array($cids) && in_array($basecid, $cids)) {
                    $isvalid = false;
                    $data['forum']->properties['category']->categories = array();
                    $data['forum']->properties['category']->invalid = xarML("Forums cannot be added to the base forum category");
                }
                $andvalid = false;
                // see if user switched forum types
                if ($data['forum']->properties['ftype']->value == $ftype) {
                    if (!empty($settingsfields)) {
                        $data['settings']->setFieldList($settingsfields);
                    }
                    $andvalid = $data['settings']->checkInput();
                    if (in_array('icondefault', $settingsfields)) {
                        $iconfolder = $data['settings']->properties['iconfolder']->value;
                        $iconlist = xarMod::apiFunc('crispbb', 'user', 'gettopicicons',
                            array('iconfolder' => $iconfolder, 'shownone' => true));
                        $data['settings']->properties['icondefault']->options = $iconlist;
                        $andvalid = $data['settings']->checkInput();
                    }
                    $settings = array();
                    foreach ($data['settings']->properties as $name => $value) {
                        $settings[$name] = $data['settings']->properties[$name]->value;
                    }
                    // @TODO: make these properties somehow
                    foreach ($presets['ftransfields'] as $field => $option) {
                        if (!isset($settings['ftransforms'][$field]))
                            $settings['ftransforms'][$field] = array();
                    }
                    foreach ($presets['ttransfields'] as $field => $option) {
                        if (!isset($settings['ttransforms'][$field]))
                            $settings['ttransforms'][$field] = array();
                    }
                    foreach ($presets['ptransfields'] as $field => $option) {
                        if (!isset($settings['ptransforms'][$field]))
                            $settings['ptransforms'][$field] = array();
                    }
                }
                if (empty($invalid) && $isvalid) {
                    if (!xarSecConfirmAuthKey()) return;
                    $extra = array('fsettings' => serialize($settings));
                    $data['forum']->updateHooks(true);
                    $data['forum']->updateItem($extra);
                    xarSessionSetVar('crispbb_statusmsg', xarML('#(1) settings updated', $pageTitle));
                    if (empty($return_url)) {
                        $return_url = xarModURL('crispbb', 'admin', 'modify',
                            array('fid' => $fid, 'sublink' => 'edit'));
                    }
                    xarMod::apiFunc('crispbb', 'user', 'getitemtypes');
                    xarResponse::Redirect($return_url);
                }
                $data['values'] = $settings;
            }
            // handle form
            $item = array();
            $item['module'] = 'crispbb';
            $item['itemtype'] = $data['forum']->itemtype;
            $item['itemid'] = $fid;
            $hooks = xarModCallHooks('item', 'modify', $fid, $item);
            if (xarVarIsCached('Hooks.dynamicdata','withupload') || xarModIsHooked('uploads', 'crispbb', $item['itemtype'])) {
                $data['withupload'] = 1;
            } else {
                $data['withupload'] = 0;
            }
            // change categories display to a dropdown list
            if (isset($hooks['categories'])) $hooks['categories'] = '';
            // propagate any new property values
            // CHANGEME: this is a convenience function, any property updates in new releases
            // should really be dealt with in the upgrade function of xarinit();
            // Leaving it for now, 'cos it sure is 'convenient' :D
            foreach ($data['settings']->properties as $name => $value) {
                if (!isset($data['values'][$name]) && in_array($name, $settingsfields)) // only add missing property values
                    $data['values'][$name] = $data['settings']->properties[$name]->value;
            }
            if (!empty($data['values']['iconfolder'])) {
                $iconlist = xarMod::apiFunc('crispbb', 'user', 'gettopicicons',
                    array('iconfolder' => $data['values']['iconfolder'], 'shownone' => true));
                $data['settings']->properties['icondefault']->options = $iconlist;
                $data['iconlist'] = $iconlist;
            } else {
                $data['iconlist'] = array();
            }


            $pageTitle = 'Edit ' . $pageTitle;
            xarMod::apiFunc('crispbb', 'user', 'getitemtypes');
        break;

        case 'forumhooks':
        case 'topichooks':
        case 'posthooks':
            if ( ($sublink == 'forumhooks' && empty($data['forum']->itemlinks['forumhooks'])) ||
                ($sublink == 'topichooks' && empty($data['forum']->itemlinks['topichooks'])) ||
                ($sublink == 'posthooks' && empty($data['forum']->itemlinks['posthooks'])) ) {
                $errorMsg['message'] = xarML('You do not have the privileges required for this action');
                $errorMsg['return_url'] = xarModURL('crispbb', 'user', 'main');
                $errorMsg['type'] = 'NO_PRIVILEGES';
                $errorMsg['pageTitle'] = xarML('No Privileges');
                xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
                return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
            }
            if ($sublink == 'forumhooks') {
                $component = 'forum';
                $label = 'forums';
                // make sure cats are available and hooked to forums
            } elseif ($sublink == 'topichooks') {
                $component = 'topics';
                $label = 'topics';
                // make sure hitcount is available and hooked to topics
            } elseif ($sublink == 'posthooks') {
                $component = 'posts';
                $label = 'posts';
            }
            $itemtype = xarMod::apiFunc('crispbb', 'user', 'getitemtype',
                array('fid' => $fid, 'component' => $component));
            $mastertype = xarMod::apiFunc('crispbb', 'user', 'getitemtype',
                array('fid' => 0, 'component' => $component));
            // get all the hooks available
            $hooklist = xarMod::apiFunc('modules', 'admin', 'gethooklist');
            // hook modules must have at least one of these hook functions
            $hooktypes = array('new','create','modify','update','display','delete','transform');
            $hooksettings = array();
            foreach ($hooklist as $hookMod => $hookData) {
                // make sure we only get modules with useful hook functions
                $hashooktypes = false;
                foreach ($hookData as $hooktype => $hookedto) {
                    foreach ($hooktypes as $comparetype) {
                        if (strstr($hooktype, $comparetype) !== false) {
                            // only need to find one for this to be true
                            $hashooktypes = true;
                            break;
                        }
                    }
                }
                if (!$hashooktypes) continue;
                if ($hookMod == 'categories') {
                    $ishooked = false;
                    $hookStatus = 2;
                    $hookMessage = xarML('Categories hooks are disabled for all items in crispBB', $label);
                } elseif ($hookMod == 'hitcount') {
                    if ($component == 'topics') {
                        $ishooked = true;
                        $hookStatus = 2;
                        $hookMessage = xarML('Hitcount is always hooked to all topics in crispBB');
                    } else {
                        $ishooked = false;
                        $hookStatus = 2;
                        $hookMessage = xarML('Hitcount hooks are disabled for #(1) in crispBB', $label);
                    }
                } elseif ($hookMod == 'crispsubs') {
                    if ($component == 'topics') {
                        if (xarModIsHooked($hookMod, 'crispbb', 0)) {
                            $ishooked = true;
                            $hookStatus = 0;
                            $hookMessage = xarML('This module is hooked to all itemtypes in crispBB');
                        } elseif (xarModIsHooked($hookMod, 'crispbb', $mastertype)) {
                            $ishooked = true;
                            $hookStatus = 0;
                            $hookMessage = xarML('This module is hooked to all #(1) in crispBB', $label);
                        } else {
                            $ishooked = xarModIsHooked($hookMod, 'crispbb', $itemtype);
                            $hookStatus = 1;
                            $hookMessage = xarML('Hook this module to #(1) #(2)', $data['forum']->properties['fname']->value, $component != 'forum' ? $label : '');
                        }
                    } else {
                        $ishooked = false;
                        $hookStatus = 2;
                        $hookMessage = xarML('crispSubs hooks are disabled for #(1) in crispBB', $label);
                    }
                } else {
                    if (xarModIsHooked($hookMod, 'crispbb', 0)) {
                        $ishooked = true;
                        $hookStatus = 0;
                        $hookMessage = xarML('This module is hooked to all itemtypes in crispBB');
                    } elseif (xarModIsHooked($hookMod, 'crispbb', $mastertype)) {
                        $ishooked = true;
                        $hookStatus = 0;
                        $hookMessage = xarML('This module is hooked to all #(1) in crispBB', $label);
                    } else {
                        $ishooked = xarModIsHooked($hookMod, 'crispbb', $itemtype);
                        $hookStatus = 1;
                        $hookMessage = xarML('Hook this module to #(1) #(2)', $data['forum']->properties['fname']->value, $component != 'forum' ? $label : '');
                    }
                }
                $hookModid = xarModGetIdFromName($hookMod);
                $hookModinfo = xarMod::getInfo($hookModid);
                $hooksettings[$hookMod] = array(
                    'status' => $hookStatus,
                    'output' => '',
                    'message' => $hookMessage,
                    'ishooked' => $ishooked,
                    'displayname' => $hookModinfo['displayname']
                );
            }
            if ($phase == 'update') {
                if (empty($invalid)) {
                    if (!xarSecConfirmAuthKey()) return;
                    $isupdated = false;
                    foreach ($hooksettings as $checkmod => $checkvals) {
                        // skip hooks that can't be changed from here
                        if ($checkvals['status'] <> 1) continue;
                        xarVarFetch("hooked_" . $checkmod,'isset',$ishooked,'',XARVAR_DONT_REUSE);
                        // Explicit setting to hook module to all items in this component
                        if (!empty($ishooked) && isset($ishooked[1]) && !empty($ishooked[1])) {
                            // only hook if not already hooked
                            if (!$checkvals['ishooked']) {
                                xarMod::apiFunc('modules','admin','enablehooks',
                                    array(
                                        'callerModName' => 'crispbb',
                                        'callerItemType' => $itemtype,
                                        'hookModName' => $checkmod
                                    ));
                                $isupdated = true;
                            }
                        // No setting
                        } else {
                            // unhook if currently hooked
                            if ($checkvals['ishooked']) {
                                xarMod::apiFunc('modules','admin','disablehooks',
                                    array(
                                        'callerModName' => 'crispbb',
                                        'callerItemType' => $itemtype,
                                        'hookModName' => $checkmod
                                    ));
                                $isupdated = true;
                            }
                        }
                        xarMod::apiFunc('crispbb', 'user', 'getitemtypes');
                    }
                    xarMod::apiFunc('crispbb', 'user', 'getitemtypes');
                    // call updateconfig hooks
                    $hookargs['module'] = 'crispbb';
                    $hookargs['itemtype'] = $itemtype;
                    xarModCallHooks('module','updateconfig','crispbb', $hookargs);
                    // update the status message
                    xarSessionSetVar('crispbb_statusmsg', xarML('#(1) hooks for #(2) updated', ucfirst($component), $data['forum']->properties['fname']->value));
                    // if no returnurl specified, return to forumconfig, this sublink
                    if (empty($return_url)) {
                        $return_url = xarModURL('crispbb', 'admin', 'modify',
                            array('fid' => $fid, 'sublink' => $sublink));
                    }
                    xarResponse::Redirect($return_url);
                    return true;
                }
            }
            $hooks = xarModCallHooks('module', 'modifyconfig', 'crispbb',
                            array('module' => 'crispbb', 'itemtype' => $itemtype));
            if (isset($hooks['categories'])) unset($hooks['categories']);
            foreach ($hooks as $hookmodname => $hookvals) {
                $hooksettings[$hookmodname]['output'] = $hookvals;
            }
            $data['hooksettings'] = $hooksettings;
            $pageTitle .= $component == 'forum' ? ' Hooks' : ' ' . ucfirst($component) . ' Hooks';
            xarMod::apiFunc('crispbb', 'user', 'getitemtypes');
        break;

        case 'privileges':
            if (empty($data['forum']->itemlinks['privileges'])) {
                $errorMsg['message'] = xarML('You do not have the privileges required for this action');
                $errorMsg['return_url'] = xarModURL('crispbb', 'user', 'main');
                $errorMsg['type'] = 'NO_PRIVILEGES';
                $errorMsg['pageTitle'] = xarML('No Privileges');
                xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
                return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
            }
            if (!xarVarFetch('privs', 'list', $privs, array(), XARVAR_NOT_REQUIRED)) return;
            $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
                array('preset' => 'privactionlabels,fprivileges,privleveloptions'));
            $defaults = $presets['fprivileges'];
            if (empty($privs)) {
                $privs = $data['forum']->fprivileges;
            } else {
                // format privs for storage
                foreach ($defaults as $level => $actions) {
                    foreach ($actions as $key => $value) {
                        if (!isset($privs[$level][$key])) {
                            $privs[$level][$key] = 0;
                        }
                    }
                }
            }
            if ($phase == 'update') {
                // check for factory reset
                if (!xarVarFetch('resetprivs', 'checkbox', $resetprivs, false, XARVAR_NOT_REQUIRED)) return;
                // perform factory reset
                if ($resetprivs) {
                    $privs = $defaults;
                }
                $fieldlist = array('fprivileges');
                $data['forum']->setFieldList($fieldlist);
                if (empty($invalid)) {
                    if (!xarSecConfirmAuthKey()) return;
                    $extra = array('fprivileges' => serialize($privs));
                    $data['forum']->updateHooks(false);
                    $data['forum']->updateItem($extra);
                    // update the status message
                    xarSessionSetVar('crispbb_statusmsg', xarML('Forum privileges updated'));
                    // if no returnurl specified, return to forumconfig
                    if (empty($return_url)) {
                        $return_url = xarModURL('crispbb', 'admin', 'modify', array('fid' => $fid, 'sublink' => 'privileges'));
                    }
                    xarResponse::Redirect($return_url);
                    return true;
                }
            }
            // format privs for form display
            foreach ($privs as $level => $actions) {
                foreach ($actions as $key => $value) {
                    if ($level < 300) {
                        $privs[$level][$key] = 2;
                    } elseif (!empty($privs[$level-100][$key])) {
                        $privs[$level][$key] = 2;
                    } elseif ($level == 600 && $key != 'editforum' && $key != 'addforum') {
                        $privs[$level][$key] = 2;
                    } elseif ($level == 700 && $key != 'deleteforum' && $key != 'editforum') {
                        $privs[$level][$key] = 2;
                    } elseif ($level == 800) {
                        $privs[$level][$key] = 2;
                    } else {
                        $privs[$level][$key] = $value;
                    }
                }
            }
            $data['actions'] = $presets['privactionlabels'];
            $data['levels'] = $presets['privleveloptions'];
            $data['privs'] = $privs;
            $pageTitle .= ' Privileges';
        break;
        default:
            $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
                array('preset' => 'privactionlabels,privleveloptions'));
            $data['actions'] = $presets['privactionlabels'];
            $data['levels'] = $presets['privleveloptions'];
        break;
    }

    $data['fid'] = $fid;
    $data['invalid'] = $invalid;
    $data['sublink'] = $sublink;
    $data['menulinks'] = xarMod::apiFunc('crispbb', 'admin', 'getmenulinks',
        array('current_mod' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'modify',
            'current_sublink' => $sublink,
            'fid' => $fid,
            //'catid' => $data['catid'],
            'secLevels' => $secLevels
    ));
    $data['pageTitle'] = $pageTitle;
    $data['hookoutput'] = !empty($hooks) ? $hooks : '';

    xarTPLSetPageTitle(xarVarPrepForDisplay(xarML($pageTitle)));

    return $data;
}
?>