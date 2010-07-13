<?php
/**
 * Display an Item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/eid/1152
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * display an item
 
 * @return array $data
 */
function downloads_admin_display($args)
{

    if(!xarVarFetch('itemid',   'id', $itemid,   NULL, XARVAR_DONT_SET)) {return;}

    extract($args);

    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'user', 'display', 'downloads');
        throw new Exception($msg);
    }

	if (!xarSecurityCheck('ReadDownloads',1,'Item',$itemid)) return;

	sys::import('modules.dynamicdata.class.objects.master');

	// Get the object name
	$downloadsobject = DataObjectMaster::getObject(array('name' => 'downloads'));
	$downloadsobject->getItem(array('itemid' => $itemid));

	//$instance = $itemid.':'.downloads.':'.xarUserGetVar('id');
	if (xarSecurityCheck('EditDownloads',0)) {
		$data['editurl'] = xarModURL('downloads','admin','modify',array('itemid' => $itemid));
	}

	// Get the item's field values
    $object = DataObjectMaster::getObject(array('name' => 'downloads'));
    $object->getItem(array('itemid' => $itemid));
    if (!isset($itemid)) return;
    $values = $object->getFieldValues();

	$data['itemid'] = $itemid;
		
    foreach ($values as $name => $value) {
        $data[$name] = $value;
    }

	// First look for something like user-display-itemid<123>.xt  
	// then look for something like user-display-<downloads_type>.xt
	// then fall back on user-display.xt
	$template = 'itemid' . $itemid;
	$itemtemplate = xarTpl__getSourceFileName('downloads','user-display',$template);
	if (strstr($itemtemplate, $template)) {
		$template = $itemtemplate;
	} else {
		$template = '';
	}

    return xarTplModule('downloads','admin','display', $data, $template);

}

?>