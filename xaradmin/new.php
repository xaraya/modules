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
    if (!xarVarFetch('ftype', 'int:0:', $ftype, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'checkbox', $confirm, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('redirecturl', 'str:1:255', $redirecturl, '', XARVAR_NOT_REQUIRED)) return;

    // fetch default settings for a new forum
    $defaults = xarMod::apiFunc('crispbb', 'user', 'getsettings', array('setting' => 'fsettings'));
    $itemtype = xarMod::apiFunc('crispbb', 'user', 'getitemtype',
        array('fid' => 0, 'component' => 'forum'));
    $fprivileges = xarMod::apiFunc('crispbb', 'user', 'getsettings', array('setting' => 'fprivileges'));


    if (!xarSecurityCheck('AddCrispBB', 0)) {
        $errorMsg['message'] = xarML('You do not have the privileges required for this action');
        $errorMsg['return_url'] = xarModURL('crispbb', 'user', 'main');
        $errorMsg['type'] = 'NO_PRIVILEGES';
        $errorMsg['pageTitle'] = xarML('No Privileges');
        xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
        return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['forum'] = DataObjectMaster::getObject(array('name' => 'crispbb_forums'));
    $fieldlist = array('fname','fdesc','fstatus','ftype','category', 'fsettings', 'fprivileges','fowner');
    $data['forum']->setFieldlist($fieldlist);

    // Load the DD master property class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.properties.master');
    if (!empty($catid)) {
        // pre-select the category if we found one
        $data['forum']->properties['category']->categories = array($catid);
    }
    $data['forum']->properties['fsettings']->value = serialize($defaults);
    $data['forum']->properties['fprivileges']->value = serialize($fprivileges);

    $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
        array('preset' => 'forumstatusoptions,topicsortoptions,sortorderoptions,pagedisplayoptions,ftransfields,ttransfields,ptransfields,ftypeoptions'));

    $invalid = array();
    $now = time();
    $tracking = xarMod::apiFunc('crispbb', 'user', 'tracking', array('now' => $now));
    // End Tracking
    if (!empty($tracking)) {
        xarVarSetCached('Blocks.crispbb', 'tracking', $tracking);
        xarModUserVars::set('crispbb', 'tracking', serialize($tracking));
    }

    if (!$confirm) {
        $phase = 'form';
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
        if (!xarVarFetch('showfaqs', 'int:0:1', $settings['showfaqs'], $defaults['showfaqs'], XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('iconfolder', 'str:0', $settings['iconfolder'], $defaults['iconfolder'], XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('icondefault', 'str:0', $settings['icondefault'], $defaults['icondefault'], XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('floodcontrol', 'int:0:3600', $settings['floodcontrol'], $defaults['floodcontrol'], XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('postbuffer', 'int:0:60', $settings['postbuffer'], $defaults['postbuffer'], XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('topicapproval', 'checkbox', $settings['topicapproval'], $defaults['topicapproval'], XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('replyapproval', 'checkbox', $settings['replyapproval'], $defaults['replyapproval'], XARVAR_NOT_REQUIRED)) return;

        if (empty($fowner)) $fowner = xarModVars::get('roles', 'admin');
        switch ($ftype) {
            case '0': // regular forum type
            default:
            break;
            case '1': // redirected forum
                if (strlen($redirecturl) < 1 || strlen($redirecturl) > 100) {
                    $invalid['redirecturl'] = xarML('URL must be 255 characters or less');
                }
                if (empty($invalid)) {
                    if (strstr($redirecturl,'://')) {
                        if (!ereg("^http://|https://|ftp://", $redirecturl)) {
                            $invalid['redirecturl'] = 'URLs of this type are not allowed';
                        }
                    } elseif (substr($redirecturl,0,1) == '/') {
                        $server = xarServerGetHost();
                        $protocol = xarServerGetProtocol();
                        $redirecturl = $protocol . '://' . $server . $redirecturl;
                    } else {
                        $baseurl = xarServer::getBaseURL();
                        $redirecturl = $baseurl . $redirecturl;
                    }
                }
            break;
        }
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
        $settings['redirected'] = array('redirecturl' => $redirecturl);



        $isvalid = $data['forum']->checkInput();
        // form validated ok, go ahead and create the forum
        if (empty($invalid) && $isvalid) {
            if (!xarSecConfirmAuthKey()) {
                return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
            }
            // create forum
            $fid = $data['forum']->createItem();
            // no fid, throw back
            if (empty($fid)) return;
            // add forder to forum
            $data['forum']->properties['fowner']->setValue($fowner);
            $data['forum']->properties['forder']->setValue($fid);
            $data['forum']->properties['fsettings']->setValue(serialize($settings));
            $data['forum']->properties['fprivileges']->setValue(serialize($fprivileges));
            $data['forum']->updateItem();
            // @TODO: move all this to forum object class createItem method
            // create itemtypes for this forum
            $forumtype = xarMod::apiFunc('crispbb', 'admin', 'createitemtype',
                array('fid' => $fid, 'component' => 'forum'));
            $topicstype = xarMod::apiFunc('crispbb', 'admin', 'createitemtype',
                array('fid' => $fid, 'component' => 'topics'));
            $poststype = xarMod::apiFunc('crispbb', 'admin', 'createitemtype',
                array('fid' => $fid, 'component' => 'posts'));
            // synch hooks
            $itemtypes = xarMod::apiFunc('crispbb', 'user', 'getitemtypes');

            // call hooks for new forum
            $item = $args;
            $item['module'] = 'crispbb';
            $item['itemtype'] = $forumtype;
            $item['itemid'] = $fid;
            xarModCallHooks('item', 'create', $fid, $item);

            // let the tracker know this forum was created
            $fstring = xarModVars::get('crispbb', 'ftracking');
            $ftracking = (!empty($fstring)) ? unserialize($fstring) : array();
            $ftracking[$fid] = time();
            xarModVars::set('crispbb', 'ftracking', serialize($ftracking));

            // update the status message
            xarSessionSetVar('crispbb_statusmsg', xarML('New forum: fid #(1) created', $fid));
            // if no returnurl specified, return to the modify function for the newly created forum
            if (empty($returnurl)) {
                $returnurl = xarModURL('crispbb', 'admin', 'modify',
                    array('fid' => $fid, 'sublink' => 'edit'));
            }
            xarResponse::Redirect($returnurl);
            return true;
        }
        // failed validation, pass back the settings fetched from input
        $defaults = $settings;

    }
    $pageTitle = xarML('Add New Forum');
    // if we got here, either the phase is form, or input failed validation
    // either way, we just want to pass the data back to the form
    $data = array_merge($data,$defaults); // populate data array with forum settings
    $data['fname'] = $fname;
    $data['fdesc'] = $fdesc;
    $data['fstatus'] = $fstatus;
    $data['ftype'] = $ftype;
    $data['fowner'] = $fowner;
    $data['redirecturl'] = $redirecturl;
    $data['invalid'] = $invalid;

    if (!empty($data['iconfolder'])) {
        $iconlist = xarMod::apiFunc('crispbb', 'user', 'gettopicicons',
            array('iconfolder' => $data['iconfolder'], 'shownone' => true));
        $data['iconlist'] = $iconlist;
    } else {
        $data['iconlist'] = array();
    }

    $ftypes = array();
    $ftypes[0] = array('id' => 0, 'name' => xarML('Normal Forum'));
    $ftypes[1] = array('id' => 1, 'name' => xarML('Redirected Forum'));
    $data['ftypeoptions'] = $ftypes; // $presets['ftypeoptions'];
    $data['statusoptions'] = $presets['forumstatusoptions'];
    $tsortoptions = $presets['topicsortoptions'];
    $alltopicstype = xarMod::apiFunc('crispbb', 'user', 'getitemtype', array('fid' => 0, 'component' => 'topics'));
    if (!xarModIsAvailable('ratings') || !xarModIsHooked('ratings', 'crispbb', $alltopicstype)) {
        unset($tsortoptions['numratings']);
    }
    $data['topicfields'] = $tsortoptions;
    $data['orderoptions'] = $presets['sortorderoptions'];
    $data['pageoptions'] = $presets['pagedisplayoptions'];
    $secLevels = empty($secLevels) ? xarMod::apiFunc('crispbb', 'user', 'getsettings', array('setting' => 'fprivileges')) : $secLevels;
    // populate the menulinks for this function
    $data['menulinks'] = xarMod::apiFunc('crispbb', 'admin', 'getmenulinks',
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
    $item['itemtype'] = $itemtype; // All itemtypes
    $hooks = xarModCallHooks('item', 'new', '', $item);

    // unset category hook (if set)
    if (isset($hooks['categories'])) unset($hooks['categories']);

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