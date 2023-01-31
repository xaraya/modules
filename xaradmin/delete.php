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

function publications_admin_delete()
{
    if (!xarSecurity::check('ManagePublications')) return;

    //$return = xarController::URL('publications', 'admin','view',array('ptid' => xarModVars::get('publications', 'defaultpubtype')));
    if(!xarVar::fetch('confirmed',  'int', $confirmed,  NULL,  xarVar::NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('itemid',     'int', $itemid,     NULL,  xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('idlist',     'str', $idlist,     NULL,  xarVar::NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('returnurl',  'str', $returnurl,  NULL,  xarVar::DONT_SET)) {return;}

    if (!empty($itemid)) $idlist = $itemid;
    $ids = explode(',',trim($idlist,','));
    
    if (empty($idlist)) {
        if (isset($returnurl)) {
            xarController::redirect($returnurl);
        } else {
            xarController::redirect(xarController::URL('publications', 'admin','view'));
        }
    }

    $data['message'] = '';
    $data['itemid']  = $itemid;

/*------------- Ask for Confirmation.  If yes, action ----------------------------*/

    sys::import('modules.dynamicdata.class.objects.master');
    $publication = DataObjectMaster::getObject(array('name' => 'publications_publications'));
    if (!isset($confirmed)) {
        $data['idlist'] = $idlist;
        if (count($ids) > 1) {
            $data['title'] = xarML("Delete Publications");
        } else {
            $data['title'] = xarML("Delete Publication");
        }
        $data['authid'] = xarSecGenAuthKey();
        $items = array();
        foreach ($ids as $i) {
            $publication->getItem(array('itemid' => $i));
            $item = $publication->getFieldValues();
            $items[] = $item;
        }
        $data['items'] = $items;
        $data['yes_action'] = xarController::URL('publications','admin','delete',array('idlist' => $idlist));
        return xarTplModule('publications','admin', 'delete',$data);        
    } else {
        if (!xarSecConfirmAuthKey()) return;
        foreach ($ids as $id) {
            $itemid = $publication->deleteItem(array('itemid' => $id));
            $data['message'] = "Publication deleted [ID $id]";

            // Inform the world via hooks
            $item = array('module' => 'publications', 'itemid' => $itemid, 'itemtype' => $publication->properties['itemtype']->value);
            xarHooks::notify('ItemDelete', $item);
        }
        if (isset($returnurl)) {
            xarController::redirect($returnurl);
        } else {
            xarController::redirect(xarController::URL('publications', 'admin', 'view', $data));
        }
        return true;
    }

    return true;
}

?>