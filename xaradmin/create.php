<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.dynamicdata.class.objects.master');

function publications_admin_create()
{
    if (!xarSecurity::check('AddPublications')) {
        return;
    }

    if (!xarVar::fetch('ptid', 'id', $data['ptid'])) {
        return;
    }
    if (!xarVar::fetch('new_cids', 'array', $cids, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('preview', 'str', $data['preview'], null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('save', 'str', $save, null, xarVar::NOT_REQUIRED)) {
        return;
    }

    // Confirm authorisation code
    // This has been disabled for now
    // if (!xarSec::confirmAuthKey()) return;

    $data['items'] = [];
    $pubtypeobject = DataObjectMaster::getObject(['name' => 'publications_types']);
    $pubtypeobject->getItem(['itemid' => $data['ptid']]);
    $data['object'] = DataObjectMaster::getObject(['name' => $pubtypeobject->properties['name']->value]);

    $isvalid = $data['object']->checkInput();

    $data['settings'] = xarMod::apiFunc('publications', 'user', 'getsettings', ['ptid' => $data['ptid']]);

    if ($data['preview'] || !$isvalid) {
        // Show debug info if called for
        if (!$isvalid &&
            xarModVars::get('publications', 'debugmode') &&
            in_array(xarUser::getVar('id'), xarConfigVars::get(null, 'Site.User.DebugAdmins'))) {
            var_dump($data['object']->getInvalids());
        }
        // Preview or bad data: redisplay the form
        $data['properties'] = $data['object']->getProperties();
        if ($data['preview']) {
            $data['tab'] = 'preview';
        }
        return xarTpl::module('publications', 'admin', 'new', $data);
    }

    // Create the object
    $itemid = $data['object']->createItem();

    // Inform the world via hooks
    $item = ['module' => 'publications', 'itemid' => $itemid, 'itemtype' => $data['object']->properties['itemtype']->value];
    xarHooks::notify('ItemCreate', $item);

    // Redirect if we came from somewhere else
    $current_listview = xarSession::getVar('publications_current_listview');
    if (!empty($cuurent_listview)) {
        xarController::redirect($current_listview);
    }

    xarController::redirect(xarController::URL(
        'publications',
        'admin',
        'view',
        ['ptid' => $data['ptid']]
    ));
    return true;
}
