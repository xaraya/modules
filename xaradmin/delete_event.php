<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * Delete an event
 */
 
function pubsub_admin_delete_event()
{
    // Xaraya security
   if (!xarSecurityCheck('ManagePubSub')) return;
    xarTplSetPageTitle('Delete event');

    if(!xarVarFetch('confirm',  'bool', $data['confirm'],  false, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('itemid',     'str',  $data['itemid'],     NULL,  XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('idlist',     'str',  $idlist,     NULL,  XARVAR_DONT_SET)) {return;}

    //print_r($data['confirm']);
    if (!empty($data['itemid'])) $idlist = $data['itemid'];
    $ids = explode(',',trim($idlist,','));
    
    $data['message'] = '';
    $data['itemid']  = $data['itemid'];
    $data['tplmodule'] = 'pubsub';

/*------------- Ask for Confirmation.  If yes, action ----------------------------*/

    sys::import('modules.dynamicdata.class.objects.master');
    $event = DataObjectMaster::getObject(array('name' => 'pubsub_events'));
    if (!$data['confirm']) {
        $data['idlist'] = $idlist;
        if (is_array($ids)) {
            $data['lang_title'] = xarML("Delete Events");
        } else {
            $ids = array($ids);
            $data['lang_title'] = xarML("Delete Event");
        }
        $data['authid'] = xarSecGenAuthKey();
        if (count($ids) == 1) {
            $event->getItem(array('itemid' => current($ids)));
            $data['object'] = $event;
        } else {
            $items = array();
            foreach ($ids as $i) {
                $event->getItem(array('itemid' => $i));
                $item = $event->getFieldValues();
                $item['name'] = $item['name'];
                $items[] = $item;
            }
            $data['items'] = $items;
        }
        $data['yes_action'] = xarModURL('pubsub','admin','delete_event',array('idlist' => $idlist));

        return $data;        
    } else {
        if (!xarSecConfirmAuthKey()) return;
        $script = implode('_', xarController::$request->getInfo());
        foreach ($ids as $id) {
        	
        	$itemid = $event->getItem(array('itemid' => $id));
//        	$itemid = $event->updateItem(array('itemid' => $id, 'state' => 0));
        	$itemid = $event->deleteItem(array('itemid' => $id, 'state' => 0));
        }

        // Jump to the next page
        xarController::redirect(xarModURL('pubsub', 'admin','view_events'));
        return true;
    }
}
?>
