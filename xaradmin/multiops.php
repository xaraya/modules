<?php
/**
 * Action for bulk operations
 */
sys::import('modules.dynamicdata.class.objects.master');
function publications_admin_multiops()
{
    // Get parameters
    if (!xarVar::fetch('idlist', 'isset', $idlist, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('operation', 'isset', $operation, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('redirecttarget', 'isset', $redirecttarget, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('returnurl', 'str', $returnurl, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('objectname', 'str', $objectname, 'listings_listing', xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('localmodule', 'str', $module, 'listings', xarVar::DONT_SET)) {
        return;
    }

    // Confirm authorisation code
    //if (!xarSec::confirmAuthKey()) return;

    // Catch missing params here, rather than below
    if (empty($idlist)) {
        return xarTpl::module('publications', 'user', 'errors', array('layout' => 'no_items'));
    }
    if ($operation === '') {
        return xarTpl::module('publications', 'user', 'errors', array('layout' => 'no_operation'));
    }

    $ids = explode(',', $idlist);

    switch ($operation) {
        case 0:
        foreach ($ids as $id => $val) {
            if (empty($val)) {
                continue;
            }

            // Get the item
            $item = $object->getItem(array('itemid' => $val));
            
            // Update it
            if (!$object->deleteItem(array('state' => $operation))) {
                return;
            }
        }
        break;

    }
    return true;
}
