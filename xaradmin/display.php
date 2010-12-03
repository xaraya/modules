<?php
 /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage amazonfps
 * @link http://xaraya.com/index.php/release/eid/1169
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Display a payment 
 *
 *  
 */
function amazonfps_admin_display()
{
    if(!xarVarFetch('itemid',       'id',    $itemid,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return; 

	print xarModURL('amazonfps','user','pay'); exit;

    // Check if we still have no id of the item to modify.
    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'modify', 'amazonfps');
        throw new Exception($msg);
    }

	$data['itemid'] = $itemid;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
 
	if (!xarSecurityCheck('DeleteAmazonFPS',0)) {
		return;
	}
	
	$data['name'] = 'amazonfps_payments'; 

    // Get the object we'll be working with
    $data['object'] = DataObjectMaster::getObject(array('name' => 'amazonfps_payments')); 
	$data['label'] = $data['object']->label;
   
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;
 
    $data['object']->getItem(array('itemid' => $itemid));

	$data['itemid'] = $itemid;

    return $data;

}
 

?>