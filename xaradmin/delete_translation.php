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
    if (!xarSecurityCheck('ManagePublications')) return;

    if(!xarVarFetch('confirmed',  'int', $confirmed,  NULL,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('itemid',     'str', $data['itemid'],     NULL,  XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('returnurl',  'str', $returnurl,  NULL,  XARVAR_DONT_SET)) {return;}

    if (empty($data['itemid'])) {
        if (isset($returnurl)) {
            xarController::redirect($returnurl);
        } else {
            xarController::redirect(xarModURL('publications', 'admin','view'));
        }
    }

    $data['message'] = '';

/*------------- Ask for Confirmation.  If yes, action ----------------------------*/

    sys::import('modules.dynamicdata.class.objects.master');
    $publication = DataObjectMaster::getObject(array('name' => 'publications_publications'));
    if (!isset($confirmed)) {
        $data['title'] = xarML("Delete Translation");
        $data['authid'] = xarSecGenAuthKey();
        $publication->getItem(array('itemid' => $data['itemid']));
        $data['item'] = $publication->getFieldValues();
        $data['yes_action'] = xarModURL('publications','admin','delete',array('itemid' => $data['itemid']));
        return xarTplModule('publications','admin', 'delete_translation',$data);        
    } else {
        if (!xarSecConfirmAuthKey()) return;
        $itemid = $publication->deleteItem(array('itemid' => $data['itemid']));
        $data['message'] = "Translation deleted [ID $itemid]";
        if (isset($returnurl)) {
            xarController::redirect($returnurl);
        } else {
            xarController::redirect(xarModURL('publications', 'admin', 'view', $data));
        }
        return true;
    }
    return true;
}

?>