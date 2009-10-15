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
 * Modify forum configuration
 *
 * This is a standard function that is called whenever a user
 * wishes to modify the default forum configuration settings.
 * The user needs Admin privileges.
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @param array  $args An array containing all the arguments to this function.
 * @param int    $exid The id of the item to be modified
 * @param int    $objectid The id of the unified object, for use with other modules
 * @param array  $invalid This array is initialised in the beginning of the function
                          to hold all the errors caught in admin-update
 * @param int    $number A number for the item, used as an example
 * @param string $name A name for the item, used as an example
 * @return array $item containing all elements and variables for the template
 */
function crispbb_admin_forumconfig($args)
{
    extract($args);

    // Admin only function
    if (!xarSecurityCheck('AdminCrispBB')) return;

    if (!xarVarFetch('sublink', 'pre:trim:lower:str:1:', $sublink, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'pre:trim:lower:enum:form:update', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    // allow return url to be over-ridden
    if (!xarVarFetch('return_url', 'pre:trim:str:1:', $data['return_url'], '', XARVAR_NOT_REQUIRED)) return;

    $invalid = array();
    $now = time();

    $pageTitle = '';
    switch (strtolower($sublink)) {
        case 'forum':
            default:
            sys::import('modules.dynamicdata.class.objects.master');
            $data['settings'] = DataObjectMaster::getObject(array('name' => 'crispbb_forum_settings'));
            $fieldlist = array('topicsperpage', 'topicsortorder', 'topicsortfield', 'postsperpage', 'postsortorder', 'hottopicposts', 'hottopichits', 'showstickies', 'showannouncements', 'showfaqs', 'topictitlemin', 'topictitlemax', 'topicdescmin', 'topicdescmax', 'topicpostmin', 'topicpostmax', 'floodcontrol', 'postbuffer', 'topicapproval', 'replyapproval');
            $data['settings']->setFieldlist($fieldlist);
            $data['settings']->tplmodule = 'crispbb';
            $data['settings']->layout = 'normal';
            if ($phase == 'update') {
                // validate input
                $isvalid = $data['settings']->checkInput();
                // get the property values back from the object
                $settings = array();
                foreach ($data['settings']->properties as $name => $value) {
                    if (!in_array($name, $fieldlist)) continue;
                    $settings[$name] = $data['settings']->properties[$name]->value;
                }
                // passed validation, call static updateSettings method to store new settings
                if ($isvalid) {
                    if (!xarSecConfirmAuthKey()) {
                        return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
                    }
                    xarModVars::set('crispbb', 'forumsettings', serialize($settings));
                    if (empty($data['return_url'])) {
                        $data['return_url'] = xarModURL('crispbb', 'admin', 'forumconfig');
                    }
                    xarResponse::Redirect($data['return_url']);
                    return true;
                }
                // failed validation, pass input back to form
                $data['values'] = $settings;
            } else {
                $data['values'] = xarMod::apiFunc('crispbb', 'user', 'getsettings', array('setting' => 'fsettings'));
            }
            // propagate any new property values
            // CHANGEME: this is a convenience function, any property updates in new releases
            // should really be dealt with in the upgrade function of xarinit();
            // Leaving it for now, 'cos it sure is 'convenient' :D
            foreach ($data['settings']->properties as $name => $value) {
                if (!isset($data['values'][$name]) && in_array($name, $fieldlist)) // only add missing property values
                    $data['values'][$name] = $data['settings']->properties[$name]->value;
            }
            $pageTitle = xarML('Default Forum Configuration');
        break;

        case 'forumhooks':
        case 'topichooks':
        case 'posthooks':
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
            xarMod::apiFunc('crispbb', 'user', 'getitemtypes');
            $itemtype = xarMod::apiFunc('crispbb', 'user', 'getitemtype',
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
                        } else {
                            $ishooked = xarModIsHooked($hookMod, 'crispbb', $itemtype);
                            $hookStatus = 1;
                            $hookMessage = xarML('Hook this module to all #(1) in crispBB', $label);
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
                    } else {
                        $ishooked = xarModIsHooked($hookMod, 'crispbb', $itemtype);
                        $hookStatus = 1;
                        $hookMessage = xarML('Hook this module to all #(1) in crispBB', $label);
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
                $hookargs = array();
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
                    // synch hooks
                    if ($isupdated) {
                        $itemtypes = xarMod::apiFunc('crispbb', 'user', 'getitemtypes');
                    }
                    // call updateconfig hooks
                    $hookargs['module'] = 'crispbb';
                    $hookargs['itemtype'] = $itemtype;
                    xarModCallHooks('module','updateconfig','crispbb', $hookargs);
                    // update the status message
                    xarSessionSetVar('crispbb_statusmsg', xarML('Default #(1) hooks configuration updated', $component));
                    // if no returnurl specified, return to forumconfig, this sublink
                    if (empty($data['return_url'])) {
                        $data['return_url'] = xarModURL('crispbb', 'admin', 'forumconfig', array('sublink' => $sublink));
                    }
                    xarResponse::Redirect($data['return_url']);
                    return true;
                }
            }
            // get config hooks for this itemtype
            $hooks = xarModCallHooks('module', 'modifyconfig', 'crispbb',
                            array('module' => 'crispbb', 'itemtype' => $itemtype));
            // change categories display to empty
            if (isset($hooks['categories'])) $hooks['categories'] = '';

            $pageTitle = xarML('Default #(1) Hooks Configuration', ucfirst($component));

            foreach ($hooks as $hookmodname => $hookvals) {
                $hooksettings[$hookmodname]['output'] = $hookvals;
            }
        break;

        case 'privileges':
            if (!xarVarFetch('privs', 'list', $privs, array(), XARVAR_NOT_REQUIRED)) return;
            $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
                array('preset' => 'privactionlabels,fprivileges,privleveloptions'));
            $defaults = $presets['fprivileges'];
            $actionlabels = $presets['privactionlabels'];
            if (empty($privs)) {
                $privs = xarMod::apiFunc('crispbb', 'user', 'getsettings',
                    array('setting' => 'fprivileges'));
            }
            // format privs for storage
            foreach ($defaults as $level => $actions) {
                foreach ($actions as $key => $value) {
                    if (!isset($privs[$level][$key])) {
                        $privs[$level][$key] = 0;
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
                if (empty($invalid)) {
                    if (!xarSecConfirmAuthKey()) return;
                    xarModVars::set('crispbb', 'privilegesettings', serialize($privs));
                    // check for apply to all forums
                    if (!xarVarFetch('applyprivs', 'checkbox', $applyprivs, false, XARVAR_NOT_REQUIRED)) return;
                    if ($applyprivs) {
                        $forums = xarMod::apiFunc('crispbb', 'user', 'getforums');
                        if (!empty($forums)) {
                            foreach ($forums as $fid => $forum) {
                                if (!xarMod::apiFunc('crispbb', 'admin', 'update',
                                    array(
                                        'fid' => $fid,
                                        'fprivileges' => $privs,
                                        'nohooks' => true
                                    ))) return;
                            }
                        }
                    }
                    // update the status message
                    xarSessionSetVar('crispbb_statusmsg', xarML('Default privileges configuration updated'));
                    // if no returnurl specified, return to forumconfig
                    if (empty($data['return_url'])) {
                        $data['return_url'] = xarModURL('crispbb', 'admin', 'forumconfig', array('sublink' => 'privileges'));
                    }
                    xarResponse::Redirect($data['return_url']);
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
            $data['actions'] = $actionlabels;
            $data['levels'] = $presets['privleveloptions'];
            $data['privs'] = $privs;
        break;
    }

    $data['invalid'] = $invalid;
    $data['sublink'] = $sublink;
    $data['hooks'] = !empty($hooks) ? $hooks : '';
    $data['hookoutput'] = !empty($hooks) ? $hooks : '';
    $data['hooksettings'] = !empty($hooksettings) ? $hooksettings : '';
    $data['pageTitle'] = $pageTitle;

    $data['menulinks'] = xarMod::apiFunc('crispbb', 'admin', 'getmenulinks',
        array(
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'forumconfig',
            'current_sublink' => $sublink
        ));

    xarTPLSetPageTitle(xarVarPrepForDisplay($pageTitle));

    return $data;
}
?>