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

function publications_admin_delete_pubtype()
{
    if (!xarSecurity::check('AdminPublications')) {
        return;
    }

    if (!xarVar::fetch('confirmed', 'int', $confirmed, null, XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'str', $itemid, null, XARVAR_DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('idlist', 'str', $idlist, null, XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('returnurl', 'str', $returnurl, null, XARVAR_DONT_SET)) {
        return;
    }

    if (!empty($itemid)) {
        $idlist = $itemid;
    }
    $ids = explode(',', trim($idlist, ','));
    
    if (empty($idlist)) {
        if (isset($returnurl)) {
            xarController::redirect($returnurl);
        } else {
            xarController::redirect(xarModURL('publications', 'admin', 'view_pubtypes'));
        }
    }

    $data['message'] = '';
    $data['itemid']  = $itemid;

    /*------------- Ask for Confirmation.  If yes, action ----------------------------*/

    sys::import('modules.dynamicdata.class.objects.master');
    $pubtype = DataObjectMaster::getObject(array('name' => 'publications_types'));
    if (!isset($confirmed)) {
        $data['idlist'] = $idlist;
        if (count($ids) > 1) {
            $data['title'] = xarML("Delete Publication Types");
        } else {
            $data['title'] = xarML("Delete Publication Type");
        }
        $data['authid'] = xarSecGenAuthKey();
        $items = array();
        foreach ($ids as $i) {
            $pubtype->getItem(array('itemid' => $i));
            $item = $pubtype->getFieldValues();
            $items[] = $item;
        }
        $data['items'] = $items;
        $data['yes_action'] = xarModURL('publications', 'admin', 'delete_pubtype', array('idlist' => $idlist));
        return xarTpl::module('publications', 'admin', 'delete_pubtype', $data);
    } else {
        if (!xarSecConfirmAuthKey()) {
            return;
        }
        foreach ($ids as $id) {
            $itemid = $pubtype->deleteItem(array('itemid' => $id));
            $data['message'] = "Publication Type deleted [ID $id]";
        }
        if (isset($returnurl)) {
            xarController::redirect($returnurl);
        } else {
            xarController::redirect(xarModURL('publications', 'admin', 'view_pubtypes', $data));
        }
        return true;
    }

    return true;
}
