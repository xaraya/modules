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
 * Add new forum
 *
 * This is a standard function that is called whenever a user
 * wishes to create a new forum.
 * The user needs at least Add privileges.
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_admin_new($args)
{
    extract($args);
    if (!xarVarFetch('sublink', 'str:1:', $sublink, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'enum:form:update', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    // allow return url to be over-ridden
    if (!xarVarFetch('returnurl', 'str:1:', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    // fetch any input from form
    if (!xarVarFetch('fname', 'str:1:', $fname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fdesc', 'str:1:', $fdesc, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fstatus', 'int:0:', $fstatus, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fowner', 'id', $fowner, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('catid', 'id', $catid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cids', 'array', $cids, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('new_cids', 'array', $cids, NULL, XARVAR_DONT_SET)) return;

    // fetch default settings for a new forum
    $defaults = xarModAPIFunc('crispbb', 'user', 'getsettings', array('setting' => 'fsettings'));
    $itemtype = xarModAPIFunc('crispbb', 'user', 'getitemtype',
        array('fid' => 0, 'component' => 'forum'));
    if (!empty($cids) && count($cids) > 0) {
        $cids = array_values(preg_grep('/\d+/',$cids));
    } elseif (!empty($catid) && is_numeric($catid)) {
        $cids = array($catid);
    } else {
        $cids = array();
    }

    if (!xarSecurityCheck('AddCrispBB', 0)) {
        $errorMsg['message'] = xarML('You do not have the privileges required for this action');
        $errorMsg['return_url'] = xarModURL('crispbb', 'user', 'main');
        $errorMsg['type'] = 'NO_PRIVILEGES';
        $errorMsg['pageTitle'] = xarML('No Privileges');
        xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
        return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
    }
    $presets = xarModAPIFunc('crispbb', 'user', 'getpresets',
        array('preset' => 'forumstatusoptions,topicsortoptions,sortorderoptions,pagedisplayoptions,ftransfields,ttransfields,ptransfields'));

    $invalid = array();
    $now = time();
    $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));
    // End Tracking
    if (!empty($tracking)) {
        xarModSetUserVar('crispbb', 'tracking', serialize($tracking));
    }
    if ($phase == 'update') {


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

        // perform any extra validations
        // TODO: check icon folder
        // TODO: check available hooks (hits, ratings)
        if (strlen($fname) < 1 || strlen($fname) > 100) {
            $invalid['fname'] = xarML('Name must be between 1 and 100 characters');
        }
        if (strlen($fdesc) > 255) {
            $invalid['fdesc'] = xarML('Description must be 255 characters or less');
        }
        if (empty($fowner)) $fowner = xarModGetVar('roles', 'admin');

        // form validated ok, go ahead and create the forum
        if (empty($invalid)) {
            if (!xarSecConfirmAuthKey()) return;
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
            $fprivileges = xarModAPIFunc('crispbb', 'user', 'getsettings', array('setting' => 'fprivileges'));
            $fid = xarModAPIFunc('crispbb', 'admin', 'create',
                array(
                    'fname' => $fname,
                    'fdesc' => $fdesc,
                    'fstatus' => $fstatus,
                    'fowner' => $fowner,
                    'fsettings' => $settings,
                    'fprivileges' => $fprivileges,
                    'cids' => $cids
                ));
            // no fid, throw back
            if (empty($fid)) return;
            // update the status message
            xarSessionSetVar('crispbb_statusmsg', xarML('New forum: fid #(1) created', $fid));
            // if no returnurl specified, return to the modify function for the newly created forum
            if (empty($returnurl)) {
                $returnurl = xarModURL('crispbb', 'admin', 'modify',
                    array('fid' => $fid, 'sublink' => 'edit'));
            }
            xarResponseRedirect($returnurl);
            return true;
        }
        // failed validation, pass back the settings fetched from input
        $defaults = $settings;
    }
    $pageTitle = xarML('Add New Forum');
    // if we got here, either the phase is form, or input failed validation
    // either way, we just want to pass the data back to the form
    $data = $defaults; // populate data array with forum settings
    $data['fname'] = $fname;
    $data['fdesc'] = $fdesc;
    $data['fstatus'] = $fstatus;
    $data['fowner'] = $fowner;
    $data['invalid'] = $invalid;


    $data['statusoptions'] = $presets['forumstatusoptions'];
    $data['topicfields'] = $presets['topicsortoptions'];
    $data['orderoptions'] = $presets['sortorderoptions'];
    $data['pageoptions'] = $presets['pagedisplayoptions'];
    $secLevels = empty($secLevels) ? xarModAPIFunc('crispbb', 'user', 'getsettings', array('setting' => 'fprivileges')) : $secLevels;
    // populate the menulinks for this function
    $data['menulinks'] = xarModAPIFunc('crispbb', 'admin', 'getmenulinks',
        array(
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'new',
            'current_sublink' => $sublink,
            'catid' => !empty($cids) ? $cids[0] : NULL,
            'secLevels' => $secLevels
        ));

    $item = array();
    $item['module'] = 'crispbb';
    $item['itemtype'] = 1; // All itemtypes
    $item['cids'] = $cids;
    $hooks = xarModCallHooks('item', 'new', '', $item);

    // change categories display to a dropdown list
    if (isset($hooks['categories']) && !empty($hooks['categories'])) {
        $mastercids = xarModGetVar('crispbb', 'mastercids.'.$itemtype);
        $parentcat = array_shift(explode(';', $mastercids));
        $seencid = array();
        if (!empty($cids) && is_array($cids)) {
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
        }

        $items = array();
        $item = array();
        $item['num'] = 1;
        $item['select'] = xarModAPIFunc('categories', 'visual', 'makeselect',
                                     array('cid' => $parentcat,
                                           'multiple' => 0,
                                           'name_prefix' => 'new_',
                                           'return_itself' => false,
                                           'select_itself' => false,
                                           'values' => &$seencid));

        $items[] = $item;
        unset($item);
        $labels = array();
        $labels['categories'] = xarML('Category');
        $hooks['categories'] = xarTplModule('crispbb','categories','newhook',
                           array('labels' => $labels,
                                 'numcats' => 1,
                                 'items' => $items));
    }

    $data['hookoutput'] = !empty($hooks) ? $hooks : '';
    if (xarVarIsCached('Hooks.dynamicdata','withupload') || xarModIsHooked('uploads', 'crispbb', $itemtype)) {
        $data['withupload'] = 1;
    } else {
        $data['withupload'] = 0;
    }
    // set page title
    xarTPLSetPageTitle(xarVarPrepForDisplay($pageTitle));

    return $data;

}
?>