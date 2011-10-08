<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * delete item
 */
function publications_user_delete()
{
    $return = xarModURL('publications', 'user','view',array('ptid' => xarModVars::get('publications', 'defaultpubtype')));
    if(!xarVarFetch('confirmed',  'int', $confirmed,  NULL,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('itemid',     'str', $itemid,     NULL,  XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('idlist',     'str', $idlist,     NULL,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('returnurl',  'str', $data['returnurl'],  $return,  XARVAR_NOT_REQUIRED)) {return;}

    if (!empty($itemid)) $idlist = $itemid;
    $ids = explode(',',trim($idlist,','));
    
    if (empty($idlist)) {
        if (isset($returnurl)) {
            xarController::redirect($returnurl);
        } else {
            xarController::redirect(xarModURL('publications', 'ser','view'));
        }
    }

    $data['message'] = '';
    $data['itemid']  = $itemid;

/*------------- Ask for Confirmation.  If yes, action ----------------------------*/

    sys::import('modules.dynamicdata.class.objects.master');
    $publication = DataObjectMaster::getObject(array('name' => 'publications_documents'));
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
        $data['yes_action'] = xarModURL('publications','user','delete',array('idlist' => $idlist));
        return xarTplModule('publications','user', 'delete',$data);        
    } else {
        if (!xarSecConfirmAuthKey()) return;
        foreach ($ids as $id) {
            $itemid = $publication->deleteItem(array('itemid' => $id));
            $data['message'] = "Publication deleted [ID $id]";
        }
        if (isset($returnurl)) {
            xarController::redirect($returnurl);
        } else {
            xarController::redirect(xarModURL('publications', 'user', 'view', $data));
        }
        return true;
    }
}

?>
