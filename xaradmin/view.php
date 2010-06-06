<?php
/**
 * View items
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Path Module
 * @link http://www.xaraya.com/index.php/release/eid/1150
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * view items
 */
function path_admin_view()
{
    if(!xarVarFetch('startnum', 'isset', $data['startnum'], NULL, XARVAR_DONT_SET)) {return;}

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('EditPath')) return;

    $data['items_per_page'] = xarModVars::get('path','items_per_page');

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

	// Get the object label for the template
	$object = DataObjectMaster::getObject(array('name' => 'path'));
	$data['label'] = $object->label;

	// Get the fields to display in the admin interface
	$config = $object->configuration;
	if (!empty($config['adminfields'])) {
		$data['adminfields'] = $config['adminfields'];
	} else {
		$data['adminfields'] = array_keys($object->getProperties());
	}

    // Get the object we'll be working with. Note this is a so called object list
    $mylist = DataObjectMaster::getObjectList(array('name' =>  'path'));
    
    // Load the DD master property class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.properties.master');

	$data['sort'] = xarMod::ApiFunc('path','admin','sort', array(
		//how to sort if the URL doesn't say otherwise...
		'sortfield_fallback' => 'id', 
		'ascdesc_fallback' => 'ASC'
	));

    // We have some filters for the items
    $filters = array(
						'startnum' => $data['startnum'],
                     'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
					'sort' => $data['sort']
                    );
    
    // Get the items 
    $items = $mylist->getItems($filters);
    
    // pass along the whole object list to the template
    $data['mylist'] = & $mylist;

    // Return the template variables defined in this function
    return $data;
}

?>
