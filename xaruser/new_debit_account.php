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
 * Create a new item of the paymnts_debit_account object
 *
 */

function payments_user_new_debit_account()
{
    if (!xarSecurityCheck('AddPayments')) return;

    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

# --------------------------------------------------------
#
# Get the payment transactions object
#
    if (!xarVarFetch('name',       'str',    $name,            'payments_debit_account', XARVAR_NOT_REQUIRED)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['tplmodule'] = 'payments';
    $data['authid'] = xarSecGenAuthKey('payments');

# --------------------------------------------------------
#
# Check if we are passing an object item
#
    if (!xarVarFetch('obj',        'str',    $object,            '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid',     'int',    $itemid,            '', XARVAR_NOT_REQUIRED)) return;
    
    $sourceobject = DataObjectMaster::getObject(array('name' => $object));
    $sourceobject->getItem(array('itemid' => $itemid));
    
    // If we have data, transfer it to the new object
    $sourcefields = $sourceobject->getFieldValues(array(), 1);
    if (!empty($sourcefields)) {
        if (isset($sourcefields['name'])) $data['object']->properties['name']->value = $sourcefields['name'];
        if (isset($sourcefields['name'])) $data['object']->properties['account_holder']->value = $sourcefields['name'];
        if (isset($sourcefields['address'])) $data['object']->properties['address']->value = $sourcefields['address'];
        $data['object']->properties['sender_object']->value = $object;
        $data['object']->properties['sender_itemid']->value = $itemid;
    }

# --------------------------------------------------------
#
# The create button was clicked
#
    if ($data['confirm']) {
    
        // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
        if(!xarVarFetch('preview', 'str', $preview,  NULL, XARVAR_DONT_SET)) {return;}

        // Check for a valid confirmation key
        if(!xarSecConfirmAuthKey()) return;
        
        // Get the data from the form
        $isvalid = $data['object']->checkInput();
        
        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTplModule('payments','user','new_debit_account', $data);        
        } else {
            // Good data: create the item
            $itemid = $data['object']->createItem();
            
            // Jump to the next page
            xarController::redirect(xarModURL('payments','user','view_debit_accounts'));
            return true;
        }
    }
    return $data;
}
?>