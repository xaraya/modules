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

    if (!xarSecurityCheck('AdminContactForm')) return;

	$data['saveitems'] = xarModVars::get('contactform','save_to_db');

    if(!xarVarFetch('startnum', 'int', $startnum, 1, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('numitems', 'int', $numitems, NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('name', 'str', $name, 'contactform_default', XARVAR_NOT_REQUIRED)) {return;}

    sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObject(array('name' => $name));
	$config = $object->configuration;

	$data['sort'] = xarMod::ApiFunc('contactform','admin','sort', array(
		//how to sort if the URL doesn't say otherwise...
		'sortfield_fallback' => 'id', 
		'ascdesc_fallback' => 'ASC'
	));
	
	if (!$numitems) {
		if (!empty($config['numitems'])) {
			$numitems = $config['numitems'];
		} else {
			$numitems = xarModVars::get('contactform', 'items_per_page');
		}
    }
	
    $data['object'] = DataObjectMaster::getObjectList(array('name' => $name));

    $filters = array(
                    'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
					'sort' => $data['sort'],
					'numitems' => $numitems,
					'startnum' => $startnum
                    ); 

	if (isset($config['adminfields'])) $filters['fieldlist'] = $config['adminfields'];
    
    // Count the items first if you want a full pager - otherwise you'll get simple previous/next links
    $data['total'] = $data['object']->countItems(); 
    $data['object']->getItems($filters);

	$data['name'] = $name;

    // Return the template variables defined in this function
    return $data;
}

?>
