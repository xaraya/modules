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
    if (!xarVar::fetch('sublink', 'str:1:', $sublink, '', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('phase', 'enum:form:update', $phase, 'form', xarVar::NOT_REQUIRED)) return;
    // allow return url to be over-ridden
    if (!xarVar::fetch('returnurl', 'str:1:', $returnurl, '', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('catid', 'id', $catid, NULL, xarVar::NOT_REQUIRED)) return;
    // @CHECKME: is this still needed?
    if (!xarVar::fetch('confirm', 'checkbox', $confirm, false, xarVar::NOT_REQUIRED)) return;

    if (!xarSecurity::check('AddCrispBB', 0)) {
        return xarTpl::module('privileges','user','errors',array('layout' => 'no_privileges'));
    }

    // get the forum object
    sys::import('modules.dynamicdata.class.objects.master');
    $data['forum'] = DataObjectMaster::getObject(array('name' => 'crispbb_forums'));
    $forumfields = array('fname','fdesc','fstatus','ftype','category');
    $data['forum']->setFieldlist($forumfields);

    // Get the base categories ofthis module
    $basecats = xarMod::apiFunc('crispbb','user','getcatbases');
    $basecid = count($basecats) > 0 ? $basecats[0] : 0;
    
    if (!empty($basecid)) {
        $categories = xarMod::apiFunc('categories', 'user', 'getchildren',
            array('cid' => $basecid));
        if (!empty($catid) && isset($categories[$catid])) {
            // pre-select the category if we found one
            $data['forum']->properties['category']->categories = array($catid);
        }
    }
    $data['basecid'] = $basecid;
    $data['basecatinfo'] = !empty($basecid) ? $basecats[0] : array();
    $data['categories'] = !empty($categories) ? $categories : array();

    // present different settings fields and layout depending on the type of forum :)
    $ftype = $data['forum']->properties['ftype']->value;
    if ($ftype == 1) {
        $settingsfields = array('redirected');
        $layout = 'redirected';
    } else {
        $settingsfields = array('topicsperpage', 'topicsortorder', 'topicsortfield', 'postsperpage', 'postsortorder', 'hottopicposts', 'hottopichits', 'showstickies', 'showannouncements', 'showfaqs', 'topictitlemin', 'topictitlemax', 'topicdescmin', 'topicdescmax', 'topicpostmin', 'topicpostmax', 'floodcontrol', 'postbuffer', 'topicapproval', 'replyapproval', 'iconfolder', 'icondefault');
        $layout = 'normal';
    }
    $data['settings'] = DataObjectMaster::getObject(array('name' => 'crispbb_forum_settings'));
    $data['settings']->setFieldlist($settingsfields);
    $data['settings']->tplmodule = 'crispbb';
    $data['settings']->layout = $layout;

    // get the master forum itemtype
    // @TODO: use getObject instead of getObjectList
    $itemtypes = DataObjectMaster::getObjectList(array('name' => 'crispbb_itemtypes'));
    $filter = array('where' => 'fid eq 0 and component eq "forum"');
    $forumtypes = $itemtypes->getItems($filter);
    $itemtype = count($forumtypes) == 1 ? key($forumtypes) : NULL;

    // fetch default settings for a new forum
    $fprivileges = xarMod::apiFunc('crispbb', 'user', 'getsettings', array('setting' => 'fprivileges'));
    $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
        array('preset' => 'ftransfields,ttransfields,ptransfields'));

    $invalid = array();
    $now = time();

    if (!$confirm) { // @CHECKME: still necessary?
        $phase = 'form';
    }

    if ($phase == 'update') {
        $isvalid = $data['forum']->checkInput();
        $cids = $data['forum']->properties['category']->categories;
        if (is_array($cids) && in_array($basecid, $cids)) {
            $isvalid = false;
            $data['forum']->properties['category']->categories = array();
            $data['forum']->properties['category']->invalid = xarML("Forums cannot be added to the base forum category");
        }

        $andvalid = $data['settings']->checkInput(array(), true);
        // see if user switched forum types
        if ($data['forum']->properties['ftype']->value == $ftype) {
            if (!$andvalid) {
                $invalids = $data['settings']->getInvalids();
                if (count($invalids) == 1 && key($invalids) == 'icondefault') {
                    $iconfolder = $data['settings']->properties['iconfolder']->value;
                    $iconlist = xarMod::apiFunc('crispbb', 'user', 'gettopicicons',
                        array('iconfolder' => $iconfolder, 'shownone' => true));
                    $data['settings']->properties['icondefault']->options = $iconlist;
                    $andvalid = $data['settings']->checkInput();
                }
            }
        } else {
            $andvalid = false;
            if ($data['forum']->properties['ftype']->value == 1) {
                $settingsfields = array('redirected');
                $layout = 'redirected';
            } else {
                $settingsfields = array('topicsperpage', 'topicsortorder', 'topicsortfield', 'postsperpage', 'postsortorder', 'hottopicposts', 'hottopichits', 'showstickies', 'showannouncements', 'showfaqs', 'topictitlemin', 'topictitlemax', 'topicdescmin', 'topicdescmax', 'topicpostmin', 'topicpostmax', 'floodcontrol', 'postbuffer', 'topicapproval', 'replyapproval','iconfolder','icondefault');
                $layout = 'normal';
            }
            $data['settings']->setFieldList($settingsfields);
            $data['settings']->layout = $layout;
            $andvalid = $data['settings']->checkInput(array(), true);
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
        // only update if both the forum and settings objects are valid
        if ($isvalid && $andvalid) {
            if (!xarSec::confirmAuthKey()) {
                return xarTpl::module('privileges','user','errors',array('layout' => 'bad_author'));
            }
            $extra = array();
            $extra['fsettings'] = serialize($settings);
            $extra['fprivileges'] = serialize($fprivileges);
            if (empty($data['forum']->properties['fowner']->value)) {
                $extra['fowner'] = xarModVars::get('roles', 'admin');
                $forumfields[] = 'fowner';
            }
            $forumfields[] = 'fsettings';
            $forumfields[] = 'fprivileges';
            $data['forum']->setFieldlist($forumfields);
            $fid = $data['forum']->createItem($extra);
            // no fid, throw back
            if (empty($fid)) return;
            // update the status message
            xarSession::setVar('crispbb_statusmsg', xarML('New forum: fid #(1) created', $fid));
            // if no returnurl specified, return to the modify function for the newly created forum
            if (empty($returnurl)) {
                $returnurl = xarController::URL('crispbb', 'admin', 'modify',
                    array('fid' => $fid, 'sublink' => 'edit'));
            }
            xarController::redirect($returnurl);
            return true;
        }
    }
    if (empty($settings)) {
        $settings = array();
        foreach (array_keys($data['settings']->properties) as $name) {
            $settings[$name] = $data['settings']->properties[$name]->value;
        }
    }
    $data['values'] = $settings;
    $pageTitle = xarML('Add New Forum');

    if (!empty($data['values']['iconfolder'])) {
        $iconlist = xarMod::apiFunc('crispbb', 'user', 'gettopicicons',
            array('iconfolder' => $data['values']['iconfolder'], 'shownone' => true));
        $data['settings']->properties['icondefault']->options = $iconlist;
        $data['iconlist'] = $iconlist;
    } else {
        $data['iconlist'] = array();
    }

    $secLevels = empty($secLevels) ? xarMod::apiFunc('crispbb', 'user', 'getsettings', array('setting' => 'fprivileges')) : $secLevels;
    // populate the menulinks for this function
    $data['menulinks'] = xarMod::apiFunc('crispbb', 'admin', 'getmenulinks',
        array(
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'new',
            'current_sublink' => $sublink,
            'catid' => !empty($cids) ? reset($cids) : NULL,
            'secLevels' => $secLevels
        ));

    $item = array();
    $item['module'] = 'crispbb';
    $item['itemtype'] = $itemtype; // All itemtypes
    $hooks = xarModHooks::call('item', 'new', '', $item);

    // unset category hook (if set)
    if (isset($hooks['categories'])) unset($hooks['categories']);

    $data['hookoutput'] = !empty($hooks) ? $hooks : '';
    // @CHECKME: what's the correct way to do this?
    if (xarVar::isCached('Hooks.dynamicdata','withupload') || xarModHooks::isHooked('uploads', 'crispbb', $itemtype)) {
        $data['withupload'] = 1;
    } else {
        $data['withupload'] = 0;
    }
    // set page title
    xarTpl::setPageTitle(xarVar::prepForDisplay($pageTitle));

    return $data;

}
?>