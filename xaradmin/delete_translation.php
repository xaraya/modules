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

function publications_admin_delete_translation()
{
    if (!xarSecurity::check('ManagePublications')) {
        return;
    }

    if (!xarVar::fetch('confirmed', 'int', $confirmed, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'str', $data['itemid'], null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('returnurl', 'str', $returnurl, null, xarVar::DONT_SET)) {
        return;
    }

    if (empty($data['itemid'])) {
        if (isset($returnurl)) {
            xarController::redirect($returnurl);
        } else {
            xarController::redirect(xarController::URL('publications', 'admin', 'view'));
        }
    }

    $data['message'] = '';

    /*------------- Ask for Confirmation.  If yes, action ----------------------------*/

    sys::import('modules.dynamicdata.class.objects.master');
    $publication = DataObjectMaster::getObject(['name' => 'publications_publications']);
    if (!isset($confirmed)) {
        $data['title'] = xarML("Delete Translation");
        $data['authid'] = xarSec::genAuthKey();
        $publication->getItem(['itemid' => $data['itemid']]);
        $data['item'] = $publication->getFieldValues();
        $data['yes_action'] = xarController::URL('publications', 'admin', 'delete', ['itemid' => $data['itemid']]);
        return xarTpl::module('publications', 'admin', 'delete_translation', $data);
    } else {
        if (!xarSec::confirmAuthKey()) {
            return;
        }
        $itemid = $publication->deleteItem(['itemid' => $data['itemid']]);
        $data['message'] = "Translation deleted [ID $itemid]";
        if (isset($returnurl)) {
            xarController::redirect($returnurl);
        } else {
            xarController::redirect(xarController::URL('publications', 'admin', 'view', $data));
        }
        return true;
    }
    return true;
}
