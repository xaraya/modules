<?php
/**
 * Action for bulk operations
 */
sys::import('modules.dynamicdata.class.objects.master');
function publications_admin_multiops()
{
    // Get parameters
    if(!xarVarFetch('idlist',      'str',   $idlist,     NULL,  XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('operation',   'isset', $operation,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('returnurl',   'str',   $returnurl,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('object',      'str',   $object,     'listings_listing', XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('localmodule', 'str',   $module,     'listings', XARVAR_DONT_SET)) {return;}

    if(!xarVarFetch('redirecttarget', 'str',  $redirecttarget, 'list',    XARVAR_NOT_REQUIRED)) {return;}

    $args = array();
    if (isset($idlist)) $args['idlist'] = $idlist;
//    if (isset($returnurl)) $args['returnurl'] = $returnurl;

    switch ($operation) {
        case 0:
        xarResponseRedirect(xarModURL('publications','user','delete',$args));
        break;

        case 'delete_customer':
        xarResponseRedirect(xarModURL('ledgerar','user','delete_customer',array('idlist' => $idlist)));
        break;

        case 'customerlist':
        xarResponseRedirect(xarModURL('ledgerar','user','view_customers'));
        break;
    }
    return true;

    // Confirm authorisation code
    //if (!xarSecConfirmAuthKey()) return;

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
    if (($totalids <=0) or ($operation == 0)) xarResponseRedirect($returnurl);


    // doin stuff with items
    $listing = DataObjectMaster::getObject(array('name' => $object));
    if (!empty($listing->filepath) && $listing->filepath != 'auto') include_once($listing->filepath);

    switch ($operation) {
    case 0: /* virtually delete item */
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

    xarResponseRedirect($returnurl);

    return true;
}

?>