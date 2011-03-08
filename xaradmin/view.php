<?php
/**
 * View items
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Contact Form Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * view items
 */
function contactform_admin_view()
{	
	// Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminContactForm')) return;

	$data['saveitems'] = xarModVars::get('contactform','save_to_db');

	// Get this value from the URL query string
    if(!xarVarFetch('startnum', 'isset', $data['startnum'], NULL, XARVAR_DONT_SET)) {return;}
	if(!xarVarFetch('name', 'str', $name, 'contactform', XARVAR_NOT_REQUIRED)) {return;}

	// Get the setting for 'items_per_page'
    $data['items_per_page'] = xarModVars::get('contactform','items_per_page');

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

    // Get the object we'll be working with. Note this is a so called object list
    $mylist = DataObjectMaster::getObjectList(array('name' => $name));
    
    // Load the DD master property class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.properties.master');

	$data['sort'] = xarMod::ApiFunc('contactform','admin','sort', array(
		//how to sort if the URL doesn't say otherwise...
		'sortfield_fallback' => 'id', 
		'ascdesc_fallback' => 'ASC'
	));

    // We have some filters for the items
    $filters = array(
                     'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
					'sort' => $data['sort']
                    );
    
    // Count the items first if you want a full pager - otherwise you'll get simple previous/next links
    $mylist->countItems($filters);

    // Get the items 
    $items = $mylist->getItems($filters);
    
    // pass along the whole object list to the template
    $data['mylist'] = & $mylist;

	$data['name'] = $name;

    // Return the template variables defined in this function
    return $data;
}

?>
