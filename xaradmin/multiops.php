<?php
/**
 * Payments Module
 *
 * @package modules
 * @subpackage payments
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2016 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Action for bulk operations
 */
sys::import('modules.dynamicdata.class.objects.master');
function payments_admin_multiops()
{
    // Get parameters
    if(!xarVar::fetch('idlist',   'isset', $idlist,    NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('operation',   'isset', $operation,    NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('redirecttarget',   'isset', $redirecttarget,    NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('returnurl',   'str', $returnurl,  NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('object',   'str', $object,  'listings_listing', xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('localmodule',   'str', $module,  'listings', xarVar::DONT_SET)) {return;}

    // Confirm authorisation code
    //if (!xarSec::confirmAuthKey()) return;

    if (!isset($idlist) || count($idlist) == 0) {
        $msg = xarML('No items selected');
        throw new DataNotFoundException(null, $msg);
    }
    if (!isset($operation) || count($operation) == 0) {
        $msg = xarML('No operation provided');
         throw new BadParameterException(null,$msg);
    }
    $ids = explode(',',$idlist);
    $totalids = count($ids);

    if (($totalids <= 0)) xarController::redirect($returnurl);


    // doin stuff with items
    $listing = DataObjectMaster::getObject(array('name' => $object));
    if (!empty($listing->filepath)) include_once($listing->filepath);
    switch ($operation) {
    case 0: /* virtually delete item */ 
        foreach ($ids as $id => $val) {
            if (empty($val)) {
              continue;
            }
            //get the listing
             $item = $listing->getItem(array('itemid' => $val));
             $thenumber = $listing->properties['number']->value;
             $listing->properties['number']->initialization_transform = true;
             if (!$listing->updateItem(array('state' => $operation, 'number' => $thenumber))) return;
        }
        break;
    case 1: /* reject item */
    case 2: /* processed */
    case 3: /* item is active, ready */
        foreach ($ids as $id => $val) {
            if (empty($val)) {
              continue;
            }
            //get the listing
             $item = $listing->getItem(array('itemid' => $val));
             if (!$listing->updateItem(array('state' => $operation))) return;
        }
        break;
    case 10: /* physically delete each item */
        foreach ($ids as $id => $val) {
            if (empty($val)) {
              continue;
            }
            //get the listing
             $item = $listing->getItem(array('itemid' => $val));
            if (!$listing->deleteItem()) return;
        }
        break;
    } // end switch

    xarController::redirect($returnurl);

    return true;
}

?>
