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
 * @param int    $exid The id of the item to be modified
 * @param int    $objectid The id of the unified object, for use with other modules
 * @param array  $invalid This array is initialised in the beginning of the function
                          to hold all the errors caught in admin-update
 * @param int    $number A number for the item, used as an example
 * @param string $name A name for the item, used as an example
 * @return array $item containing all elements and variables for the template
 */
function crispbb_admin_modify($args)
{
    extract($args);
    if (!xarVarFetch('sublink', 'str:1:', $sublink, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'enum:form:update', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    // allow return url to be over-ridden
    if (!xarVarFetch('returnurl', 'str:1:', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('fid', 'id', $fid, NULL, XARVAR_NOT_REQUIRED)) return;
    $data = xarModAPIFunc('crispbb', 'user', 'getforum', array('fid' => $fid, 'privcheck' => true));
    if ($data == 'NO_PRIVILEGES' || empty($data['addforumurl'])) {
        $errorMsg['message'] = xarML('You do not have the privileges required for this action');
        $errorMsg['return_url'] = xarModURL('crispbb', 'user', 'main');
        $errorMsg['type'] = 'NO_PRIVILEGES';
        $errorMsg['pageTitle'] = xarML('No Privileges');
        xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
        return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
    }

    $userLevel = $data['forumLevel'];
    $secLevels = $data['fprivileges'];

    $invalid = array();
    $pageTitle = $data['fname'];
    $now = time();
    $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));
    // End Tracking
    if (!empty($tracking)) {
        xarVarSetCached('Blocks.crispbb', 'tracking', $tracking);
        xarModSetUserVar('crispbb', 'tracking', serialize($tracking));
    }
    switch($sublink) {
        case 'edit':
            if (empty($data['editforumurl'])) {
                $errorMsg['message'] = xarML('You do not have the privileges required for this action');
                $errorMsg['return_url'] = xarModURL('crispbb', 'user', 'main');
                $errorMsg['type'] = 'NO_PRIVILEGES';
                $errorMsg['pageTitle'] = xarML('No Privileges');
                xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
                return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
            }
            $presets = xarModAPIFunc('crispbb', 'user', 'getpresets',
                array('preset' => 'forumstatusoptions,topicsortoptions,sortorderoptions,pagedisplayoptions,ftransfields,ttransfields,ptransfields'));
            // handle update
            if ($phase == 'update') {
                if (!xarVarFetch('fname', 'str:1:', $fname, '', XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('fdesc', 'str:1:', $fdesc, '', XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('fstatus', 'int:0:', $fstatus, 0, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('fowner', 'id', $fowner, NULL, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('catid', 'id', $catid, NULL, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('cids', 'list:id', $cids, NULL, XARVAR_DONT_SET)) return;
                if (!xarVarFetch('modify_cids', 'list:id', $cids, NULL, XARVAR_DONT_SET)) return;
                if (!empty($cids) && count($cids) > 0) {
                    $cids = array_values(preg_grep('/\d+/',$cids));
                } elseif (!empty($catid) && is_numeric($catid)) {
                    $cids = array($catid);
                } else {
                    $cids = array();
                }
                $settings = array();
                if (!xarVarFetch('topicsperpage', 'int:1:100', $settings['topicsperpage'], $data['topicsperpage'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('topicsortorder', 'enum:ASC:DESC', $settings['topicsortorder'], $data['topicsortorder'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('topicsortfield', 'enum:ptime', $settings['topicsortfield'], $data['topicsortfield'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('postsperpage', 'int:1:100', $settings['postsperpage'], $data['postsperpage'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('postsortorder', 'enum:ASC:DESC', $settings['postsortorder'], $data['postsortorder'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('hottopicposts', 'int:0:100', $settings['hottopicposts'], $data['hottopicposts'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('hottopichits', 'int:0:100', $settings['hottopichits'], $data['hottopichits'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('hottopicratings', 'int:0', $settings['hottopicratings'], $data['hottopicratings'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('topictitlemin', 'int:0:254', $settings['topictitlemin'], $data['topictitlemin'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('topictitlemax', 'int:0:254', $settings['topictitlemax'], $data['topictitlemax'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('topicdescmin', 'int:0:100', $settings['topicdescmin'], $data['topicdescmin'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('topicdescmax', 'int:0:100', $settings['topicdescmax'], $data['topicdescmax'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('topicpostmin', 'int:0:65535', $settings['topicpostmin'], $data['topicpostmin'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('topicpostmax', 'int:0:65535', $settings['topicpostmax'], $data['topicpostmax'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('showstickies', 'int:0:1', $settings['showstickies'], $data['showstickies'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('showannouncements', 'int:0:1', $settings['showannouncements'], $data['showannouncements'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('showfaqs', 'int:0:1', $settings['showfaqs'], $data['showfaqs'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('iconfolder', 'str:0', $settings['iconfolder'], $data['iconfolder'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('floodcontrol', 'int:0:3600', $settings['floodcontrol'], $data['floodcontrol'], XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('postbuffer', 'int:0:60', $settings['postbuffer'], $data['postbuffer'], XARVAR_NOT_REQUIRED)) return;

                if (!xarVarFetch('ftransforms', 'list', $settings['ftransforms'], array(), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('ttransforms', 'list', $settings['ttransforms'], array(), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('ptransforms', 'list', $settings['ptransforms'], array(), XARVAR_NOT_REQUIRED)) return;
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
                // perform any extra validations
                // TODO: check icon folder
                // TODO: check available hooks (hits, ratings)
                if (strlen($fname) < 1 || strlen($fname) > 100) {
                    $invalid['fname'] = xarML('Name must be between 1 and 100 characters');
                }
                if (strlen($fdesc) > 255) {
                    $invalid['fdesc'] = xarML('Description must be 255 characters or less');
                }
                if (empty($fowner)) $fowner = $data['fowner']; //xarModGetVar('roles', 'admin');

                if (empty($invalid)) {
                    if (!xarSecConfirmAuthKey()) return;
                    if (!xarModAPIFunc('crispbb', 'admin', 'update',
                        array(
                            'fid' => $fid,
                            'fname' => $fname,
                            'fdesc' => $fdesc,
                            'fowner' => $fowner,
                            'fstatus' => $fstatus,
                            'fsettings' => $settings,
                            'cids' => $cids
                        ))) return;
                    xarSessionSetVar('crispbb_statusmsg', xarML('#(1) settings updated', $fname));
                    if (empty($returnurl)) {
                        $returnurl = xarModURL('crispbb', 'admin', 'modify',
                            array('fid' => $fid, 'sublink' => 'edit'));
                    }
                    xarModAPIFunc('crispbb', 'user', 'getitemtypes');
                    xarResponseRedirect($returnurl);
                }
                // failed validation, pass back the input
                $data['fname'] = $fname;
                $data['fdesc'] = $fdesc;
                $data['fstatus'] = $fstatus;
                $data['fowner'] = $fowner;
                foreach ($settings as $k => $v) {
                    if (!isset($data[$k])) continue;
                    $data[$k] = $v;
                }
            }
            // handle form
            $item = array();
            $item['module'] = 'crispbb';
            $item['itemtype'] = $data['itemtype'];
            $item['itemid'] = $fid;
            $hooks = xarModCallHooks('item', 'modify', $fid, $item);
            if (xarVarIsCached('Hooks.dynamicdata','withupload') || xarModIsHooked('uploads', 'crispbb', $data['itemtype'])) {
                $data['withupload'] = 1;
            } else {
                $data['withupload'] = 0;
            }
            // change categories display to a dropdown list
            if (isset($hooks['categories']) && !empty($hooks['categories'])) {
                $mastertype = xarModAPIFunc('crispbb', 'user','getitemtype', array('fid' => 0, 'component' => 'forum'));
                $mastercids = xarModGetVar('crispbb', 'mastercids.'.$mastertype);
                $parentcat = array_shift(explode(';', $mastercids));
                // get all valid cids
                $seencid = array();
                if (!empty($cids)) {
                    foreach ($cids as $cid) {
                        if (empty($cid) || !is_numeric($cid)) {
                            continue;
                        }
                        if (empty($seencid[$cid])) {
                            $seencid[$cid] = 1;
                        } else {
                            $seencid[$cid]++;
                        }
                    }
                } else {
                    $seencid[$data['catid']] = 1;
                }
                $items = array();
                $item = array();
                $item['num'] = 1;
                $item['select'] = xarModAPIFunc('categories', 'visual', 'makeselect',
                                             array('cid' => $parentcat,
                                                   'multiple' => 0,
                                                   'name_prefix' => 'modify_',
                                                   'return_itself' => false,
                                                   'select_itself' => false,
                                                   'values' => &$seencid));

                $items[] = $item;
                unset($item);
                $labels = array();
                $labels['categories'] = xarML('Category');
                // replace hook output
                $hooks['categories'] = xarTplModule('categories','admin','modifyhook',
                                   array('labels' => $labels,
                                         'numcats' => 1,
                                         'items' => $items));
            }

            $forumtype = $data['itemtype'];
            $topicstype = xarModAPIFunc('crispbb', 'user', 'getitemtype',
                array('fid' => $fid, 'component' => 'topics'));
            $poststype = xarModAPIFunc('crispbb', 'user', 'getitemtype',
                array('fid' => $fid, 'component' => 'posts'));
            $data['ftranshooks'] = xarModGetHookList('crispbb', 'item', 'transform', $forumtype);
            $data['ttranshooks'] = xarModGetHookList('crispbb', 'item', 'transform', $topicstype);
            $data['ptranshooks'] = xarModGetHookList('crispbb', 'item', 'transform', $poststype);
            $data['statusoptions'] = $presets['forumstatusoptions'];
            $data['topicfields'] = $presets['topicsortoptions'];
            $data['orderoptions'] = $presets['sortorderoptions'];
            $data['pageoptions'] = $presets['pagedisplayoptions'];
            $pageTitle = 'Edit ' . $data['fname'];
            xarModAPIFunc('crispbb', 'user', 'getitemtypes');
        break;

        case 'forumhooks':
        case 'topichooks':
        case 'posthooks':
            if ( ($sublink == 'forumhooks' && empty($data['hooksforumurl'])) ||
                ($sublink == 'topichooks' && empty($data['hookstopicsurl'])) ||
                ($sublink == 'posthooks' && empty($data['hookspostsurl'])) ) {
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
            $itemtype = xarModAPIFunc('crispbb', 'user', 'getitemtype',
                array('fid' => $fid, 'component' => $component));
            $mastertype = xarModAPIFunc('crispbb', 'user', 'getitemtype',
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
                    } elseif (xarModIsHooked($hookMod, 'crispbb', $mastertype)) {
                        $ishooked = true;
                        $hookStatus = 0;
                        $hookMessage = xarML('This module is hooked to all #(1) in crispBB', $label);
                    } else {
                        $ishooked = xarModIsHooked($hookMod, 'crispbb', $itemtype);
                        $hookStatus = 1;
                        $hookMessage = xarML('Hook this module to #(1) #(2)', $data['fname'], $component != 'forum' ? $label : '');
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
                if ($component == 'forum') {
                    // handle categories
                    $mastercids = xarModGetVar('crispbb', 'mastercids.'.$mastertype);
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
                    xarModAPIFunc('crispbb', 'user', 'getitemtypes');
                    // call updateconfig hooks
                    $hookargs['module'] = 'crispbb';
                    $hookargs['itemtype'] = $itemtype;
                    xarModCallHooks('module','updateconfig','crispbb', $hookargs);
                    // update the status message
                    xarSessionSetVar('crispbb_statusmsg', xarML('#(1) hooks for #(2) updated', ucfirst($component), $data['fname']));
                    // if no returnurl specified, return to forumconfig, this sublink
                    if (empty($returnurl)) {
                        $returnurl = xarModURL('crispbb', 'admin', 'modify',
                            array('fid' => $fid, 'sublink' => $sublink));
                    }
                    xarResponseRedirect($returnurl);
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
            xarModAPIFunc('crispbb', 'user', 'getitemtypes');
        break;

        case 'privileges':
            if (empty($data['privsforumurl'])) {
                $errorMsg['message'] = xarML('You do not have the privileges required for this action');
                $errorMsg['return_url'] = xarModURL('crispbb', 'user', 'main');
                $errorMsg['type'] = 'NO_PRIVILEGES';
                $errorMsg['pageTitle'] = xarML('No Privileges');
                xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
                return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
            }
            if (!xarVarFetch('privs', 'list', $privs, array(), XARVAR_NOT_REQUIRED)) return;
            $presets = xarModAPIFunc('crispbb', 'user', 'getpresets',
                array('preset' => 'privactionlabels,fprivileges,privleveloptions'));
            $defaults = $presets['fprivileges'];
            if (empty($privs)) {
                $privs = $data['fprivileges'];
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
                if (empty($invalid)) {
                    if (!xarSecConfirmAuthKey()) return;
                    if (!xarModAPIFunc('crispbb', 'admin', 'update',
                        array(
                            'fid' => $fid,
                            'fprivileges' => $privs,
                            'nohooks' => true
                        ))) return;
                    // update the status message
                    xarSessionSetVar('crispbb_statusmsg', xarML('Forum privileges updated'));
                    // if no returnurl specified, return to forumconfig
                    if (empty($returnurl)) {
                        $returnurl = xarModURL('crispbb', 'admin', 'modify', array('fid' => $fid, 'sublink' => 'privileges'));
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
            $data['actions'] = $presets['privactionlabels'];
            $data['levels'] = $presets['privleveloptions'];
            $data['privs'] = $privs;
            $pageTitle .= ' Privileges';
        break;
        default:
            $presets = xarModAPIFunc('crispbb', 'user', 'getpresets',
                array('preset' => 'privactionlabels,privleveloptions'));
            $data['actions'] = $presets['privactionlabels'];
            $data['levels'] = $presets['privleveloptions'];
        break;
    }

    $data['invalid'] = $invalid;
    $data['sublink'] = $sublink;
    $data['menulinks'] = xarModAPIFunc('crispbb', 'admin', 'getmenulinks',
        array('current_mod' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'modify',
            'current_sublink' => $sublink,
            'fid' => $fid,
            'catid' => $data['catid'],
            'secLevels' => $secLevels
    ));
    $data['pageTitle'] = $pageTitle;
    $data['hookoutput'] = !empty($hooks) ? $hooks : '';

    xarTPLSetPageTitle(xarVarPrepForDisplay(xarML($pageTitle)));

    return $data;
}
?>