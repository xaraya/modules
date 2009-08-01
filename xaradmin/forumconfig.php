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

    if (!xarVarFetch('sublink', 'str:1:', $sublink, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'enum:form:update', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    // allow return url to be over-ridden
    if (!xarVarFetch('returnurl', 'str:1:', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    //if (!xarVarFetch('', '', $, , XARVAR_NOT_REQUIRED)) return;
    //if (!xarVarFetch('', '', $settings[''], $defaults[''], XARVAR_NOT_REQUIRED)) return;
    $invalid = array();
    $now = time();
    $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));
    // End Tracking
    if (!empty($tracking)) {
        xarVarSetCached('Blocks.crispbb', 'tracking', $tracking);
        xarModSetUserVar('crispbb', 'tracking', serialize($tracking));
    }
    $pageTitle = '';
    switch (strtolower($sublink)) {
        case 'forum':
        default:
            $presets = xarModAPIFunc('crispbb', 'user', 'getpresets',
                array('preset' => 'topicsortoptions,sortorderoptions,pagedisplayoptions,fsettings,ftransfields,ttransfields,ptransfields'));
            if ($phase == 'update') {
                // get factory defaults
                $defaults = $presets['fsettings'];
                // check for factory reset
                if (!xarVarFetch('resetdefaults', 'checkbox', $resetdefaults, false, XARVAR_NOT_REQUIRED));
                // perform factory reset
                if ($resetdefaults) {
                    $settings = $defaults;
                // fetch settings from input, falling back to factory defaults if invalid
                } else {
                    $settings = array();
                    if (!xarVarFetch('topicsperpage', 'int:1:100', $settings['topicsperpage'], $defaults['topicsperpage'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('topicsortorder', 'enum:ASC:DESC', $settings['topicsortorder'], $defaults['topicsortorder'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('topicsortfield', 'enum:ptime', $settings['topicsortfield'], $defaults['topicsortfield'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('postsperpage', 'int:1:100', $settings['postsperpage'], $defaults['postsperpage'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('postsortorder', 'enum:ASC:DESC', $settings['postsortorder'], $defaults['postsortorder'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('hottopicposts', 'int:1:100', $settings['hottopicposts'], $defaults['hottopicposts'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('hottopichits', 'int:1:100', $settings['hottopichits'], $defaults['hottopichits'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('hottopicratings', 'int:1', $settings['hottopicratings'], $defaults['hottopicratings'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('topictitlemin', 'int:0:254', $settings['topictitlemin'], $defaults['topictitlemin'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('topictitlemax', 'int:0:254', $settings['topictitlemax'], $defaults['topictitlemax'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('topicdescmin', 'int:0:100', $settings['topicdescmin'], $defaults['topicdescmin'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('topicdescmax', 'int:0:100', $settings['topicdescmax'], $defaults['topicdescmax'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('topicpostmin', 'int:0:65535', $settings['topicpostmin'], $defaults['topicpostmin'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('topicpostmax', 'int:0:65535', $settings['topicpostmax'], $defaults['topicpostmax'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('showstickies', 'int:0:1', $settings['showstickies'], $defaults['showstickies'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('showannouncements', 'int:0:1', $settings['showannouncements'], $defaults['showannouncements'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('iconfolder', 'str:0', $settings['iconfolder'], $defaults['iconfolder'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('floodcontrol', 'int:0:3600', $settings['floodcontrol'], $defaults['floodcontrol'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('postbuffer', 'int:0:60', $settings['postbuffer'], $defaults['postbuffer'], XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('topicapproval', 'checkbox', $settings['topicapproval'], false, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('replyapproval', 'checkbox', $settings['replyapproval'], false, XARVAR_NOT_REQUIRED)) return;
                    // perform any validations here
                    // TODO: check icon folder
                    // TODO: check available hooks (hits, ratings)
                }
                /*
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
                */
                if (empty($invalid)) {
                    if (!xarSecConfirmAuthKey()) return;
                    xarModSetVar('crispbb', 'forumsettings', serialize($settings));
                    // check for apply to all forums
                    if (!xarVarFetch('applydefaults', 'checkbox', $applydefaults, false, XARVAR_NOT_REQUIRED)) return;
                    if ($applydefaults) {
                        $forums = xarModAPIFunc('crispbb', 'user', 'getforums');
                        if (!empty($forums)) {
                            foreach ($forums as $fid => $forum) {
                                $thissettings = $forum['fsettings'];
                                foreach ($settings as $k => $v) {
                                    $thissettings[$k] = $v;
                                }
                                if (!xarModAPIFunc('crispbb', 'admin', 'update',
                                    array(
                                        'fid' => $fid,
                                        'fsettings' => $thissettings,
                                        'nohooks' => true
                                    ))) return;
                                unset($thissettings);
                            }
                        }
                    }
                    // update the status message
                    xarSessionSetVar('crispbb_statusmsg', xarML('Default forum configuration updated'));
                    // if no returnurl specified, return to forumconfig
                    if (empty($returnurl)) {
                        $returnurl = xarModURL('crispbb', 'admin', 'forumconfig');
                    }
                    xarResponseRedirect($returnurl);
                    return true;
                }
            }
            // get current default settings
            $data = xarModAPIFunc('crispbb', 'user', 'getsettings', array('setting' => 'fsettings'));

            $data['topicfields'] = $presets['topicsortoptions'];
            $data['orderoptions'] = $presets['sortorderoptions'];
            $data['pageoptions'] = $presets['pagedisplayoptions'];
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
            xarModAPIFunc('crispbb', 'user', 'getitemtypes');
            $itemtype = xarModAPIFunc('crispbb', 'user', 'getitemtype',
                array('fid' => 0, 'component' => $component));
            // get all the hooks available
            $hooklist = xarModAPIFunc('modules', 'admin', 'gethooklist');
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
                    if ($component == 'forum') {
                        $ishooked = true;
                        $hookStatus = 2;
                        $hookMessage = xarML('Categories is always hooked to all forums in crispBB');
                    } else {
                        $ishooked = false;
                        $hookStatus = 2;
                        $hookMessage = xarML('Categories hooks are disabled for #(1) in crispBB', $label);
                    }
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
                $hookModinfo = xarModGetInfo($hookModid);
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
                $changedcids = false;
                if ($component == 'forum') {
                    // handle categories
                    $oldcids = xarModGetVar('crispbb', 'mastercids.'.$itemtype);
                    $oldparent = array_shift(explode(';', $oldcids));
                    if (!xarVarFetch('config_cids', 'list:int:1:', $cids, NULL, XARVAR_NOT_REQUIRED)) return;
                    $mastercids = array();
                    foreach ($cids as $cid) {
                        if (empty($cid) || !is_numeric($cid)) {
                            continue;
                        }
                        // we only ever have 1 base category, so we take the first cat we find
                        $mastercids[] = $cid;
                        $changedcids = $cid != $oldparent ? true : false;
                        break;
                    }
                    // number of categories is always 1
                    $hookargs['number_of_categories'] = 1;
                    $hookargs['cids'] = $mastercids;
                }
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
                                xarModAPIFunc('modules','admin','enablehooks',
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
                                xarModAPIFunc('modules','admin','disablehooks',
                                    array(
                                        'callerModName' => 'crispbb',
                                        'callerItemType' => $itemtype,
                                        'hookModName' => $checkmod
                                    ));
                                $isupdated = true;
                            }
                        }
                        xarModAPIFunc('crispbb', 'user', 'getitemtypes');
                    }
                    // synch hooks
                    if ($isupdated || $changedcids) {
                        $itemtypes = xarModAPIFunc('crispbb', 'user', 'getitemtypes');
                        if ($changedcids) {
                            foreach ($itemtypes as $c => $cv) {
                                if ($cv['fid'] == 0) continue;
                                if ($cv['component'] == 'forum') {
                                    xarModSetVar('crispbb', 'number_of_categories.'.$cv['itemtype'], 1);
                                    xarModSetVar('crispbb', 'mastercids.'.$cv['itemtype'], $mastercids);
                                }
                            }
                        }
                    }
                    // call updateconfig hooks
                    $hookargs['module'] = 'crispbb';
                    $hookargs['itemtype'] = $itemtype;
                    xarModCallHooks('module','updateconfig','crispbb', $hookargs);
                    // update the status message
                    xarSessionSetVar('crispbb_statusmsg', xarML('Default #(1) hooks configuration updated', $component));
                    // if no returnurl specified, return to forumconfig, this sublink
                    if (empty($returnurl)) {
                        $returnurl = xarModURL('crispbb', 'admin', 'forumconfig', array('sublink' => $sublink));
                    }
                    xarResponseRedirect($returnurl);
                    return true;
                }
            }
            // get config hooks for this itemtype
            $hooks = xarModCallHooks('module', 'modifyconfig', 'crispbb',
                            array('module' => 'crispbb', 'itemtype' => $itemtype));
            // change categories display to a dropdown list
            if (isset($hooks['categories']) && !empty($hooks['categories'])) {
                $mastercids = xarModGetVar('crispbb', 'mastercids.'.$itemtype);
                $parentcat = array_shift(explode(';', $mastercids));
                // select current category
                $seencid[$parentcat] = 1;
                $items = array();
                $item = array();
                $item['num'] = 1;
                $item['select'] = xarModAPIFunc('categories', 'visual', 'makeselect',
                                               array('cid' => 0,
                                                     'multiple' => 0,
                                                     'name_prefix' => 'config_',
                                                     'return_itself' => false,
                                                     'select_itself' => false,
                                                     'values' => &$seencid));

                $items[] = $item;
                unset($item);
                $labels = array();
                $labels['categories'] = xarML('Category');
                $hookdata = array();
                $hookdata['newcat'] = '';
                $hookdata['numcats'] = 1;
                $hookdata['items'] = $items;
                $hookdata['modname'] = 'crispbb';
                // use our own template for modifyconfig hook GUI
                $hooks['categories'] = xarTplModule('crispbb','categories','modifyconfighook', $hookdata);
            }
            $pageTitle = xarML('Default #(1) Hooks Configuration', ucfirst($component));

            foreach ($hooks as $hookmodname => $hookvals) {
                $hooksettings[$hookmodname]['output'] = $hookvals;
            }
        break;

        case 'privileges':
            if (!xarVarFetch('privs', 'list', $privs, array(), XARVAR_NOT_REQUIRED)) return;
            $presets = xarModAPIFunc('crispbb', 'user', 'getpresets',
                array('preset' => 'privactionlabels,fprivileges,privleveloptions'));
            $defaults = $presets['fprivileges'];
            $actionlabels = $presets['privactionlabels'];
            if (empty($privs)) {
                $privs = xarModAPIFunc('crispbb', 'user', 'getsettings',
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
                    xarModSetVar('crispbb', 'privilegesettings', serialize($privs));
                    // check for apply to all forums
                    if (!xarVarFetch('applyprivs', 'checkbox', $applyprivs, false, XARVAR_NOT_REQUIRED)) return;
                    if ($applyprivs) {
                        $forums = xarModAPIFunc('crispbb', 'user', 'getforums');
                        if (!empty($forums)) {
                            foreach ($forums as $fid => $forum) {
                                if (!xarModAPIFunc('crispbb', 'admin', 'update',
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
                    if (empty($returnurl)) {
                        $returnurl = xarModURL('crispbb', 'admin', 'forumconfig', array('sublink' => 'privileges'));
                    }
                    xarResponseRedirect($returnurl);
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

    $data['menulinks'] = xarModAPIFunc('crispbb', 'admin', 'getmenulinks',
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