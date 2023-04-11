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
            
    sys::import('modules.dynamicdata.class.objects.master');
    sys::import('modules.dynamicdata.class.objects.list');

    function payments_user_export()
    {
        // need export mod
        if (!xarMod::isAvailable('export')) {
            $msg = xarML('The Export module is not available');
            throw new Exception($msg);
        }

        // Data Managers have access
        if (!xarSecurity::check('ProcessPayments') || !xarUser::isLoggedIn()) return;
        
        $oname = 'payments_ccpayments';
        
        $object = DataObjectMaster::getObjectList(array('name' => $oname));
        
        $getarr = array();
        $items = $object->getItems(array('where' => 'state eq 3'));
        
        if (empty($items)) return;
        $items = $object->getViewValues();
                
        // some kind of mod var here to control it in the future
        // mark the downloaded items as processed
        $ids = array_keys($items);
        $operation = 2; // processed
        //processing object
        $pobject = DataObjectMaster::getObject(array('name' => $oname));
        if (!empty($pobject->filepath)) include_once($pobject->filepath);

        $ptime = time();
        foreach ($ids as $id => $val) {
            if (empty($val)) {
              continue;
            }
            //get the listing
             $item = $pobject->getItem(array('itemid' => $val));
             if (!$pobject->updateItem(array('state' => $operation, 'time_processed' => $ptime))) return;
        }

        $refresh = xarSession::getVar('ddcontext.payments');

        xarMod::apiFunc('export','user','export',array(
                'filetype'=>'excel',
                'filename' => 'payments',
                'dir' => true,
                'headers' => true,
                'outputdata' => $items,
                ));
        
//        if (!xarController::redirect($refresh['return_url'])) return;
        
        return true;

    }
?>
