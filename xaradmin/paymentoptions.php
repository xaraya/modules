<?php
 /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage shop Module
 * @link http://www.xaraya.com/index.php/release/eid/1031
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  List payment options
 */
function shop_admin_paymentoptions()
{
    if(!xarVarFetch('startnum', 'isset', $data['startnum'], NULL, XARVAR_DONT_SET)) {return;}
	if(!xarVarFetch('user_id', 'isset', $user_id, NULL, XARVAR_DONT_SET)) {return;}

	$objectname = 'shop_paymentoptions';
	$data['objectname'] = $objectname;

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminShop')) return;

    $data['items_per_page'] = xarModVars::get('shop','items_per_page');

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

	// Get the object label for the template
	$object = DataObjectMaster::getObject(array('name' => $objectname));
	$data['label'] = $object->label;

	// Get the fields to display in the admin interface
	$config = $object->configuration;
	if (!empty($config['adminfields'])) {
		$data['adminfields'] = $config['adminfields'];
	} else {
		$data['adminfields'] = array_keys($object->getProperties());
	}

    // Get the object we'll be working with. Note this is a so called object list
    $mylist = DataObjectMaster::getObjectList(array('name' =>  $objectname));
    
    // Load the DD master property class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.properties.master');

	$data['sort'] = xarMod::ApiFunc('shop','admin','sort', array(
		//how to sort if the URL doesn't say otherwise...
		'sortfield_fallback' => 'ID', 
		'ascdesc_fallback' => 'ASC'
	));

    // We have some filters for the items
    $filters = array(
						'startnum' => $data['startnum'],
                     'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
					'sort' => $data['sort']
                    );

	if (isset($user_id)) {
		$filters['where'] = 'user_id eq '.$user_id;
	}
    
    // Get the items 
    $items = $mylist->getItems($filters);

	if (isset($user_id)) {

		// Get the object we'll be working with. Note this is a so called object list
		$mylist2 = DataObjectMaster::getObjectList(array('name' =>  'shop_customers'));
		
		$filters = array();

		if (isset($user_id)) {
			$filters['where'] = 'id eq '.$user_id;
		}

		$items2 = $mylist2->getItems($filters);

		$data['fname'] = $items2[$user_id]['FirstName'];
		$data['lname'] = $items2[$user_id]['LastName'];

	}

	$data['mylist'] = $mylist;

    // Return the template variables defined in this function
    return $data;
}

?>
