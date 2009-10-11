<?php
/**
 * View items
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * view items
 */
function dyn_example_admin_view()
{
    if(!xarVarFetch('startnum', 'isset', $data['startnum'], NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('sort',     'isset', $data['sort'],     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('ascdesc', 'isset', $ascdesc, NULL, XARVAR_NOT_REQUIRED)) {return;}

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('EditDynExample')) return;

    $data['items_per_page'] = xarModVars::get('dyn_example','items_per_page');

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
    // Get the object we'll be working with. Note this is a so called object list
    $mylist = DataObjectMaster::getObjectList(array('name' => 'dyn_example'));
    
    // Load the DD master property class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.properties.master');

    // We have some filters for the items
    $filters = array('numitems'  => $data['items_per_page'],
                     'startnum'  => $data['startnum'],
                     'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
                     'sort'      => $data['sort'],
                    );
    
    // Count the items first if you want a full pager - otherwise you'll get simple previous/next links
    $mylist->countItems($filters);

    // Get the items 
    $items = $mylist->getItems($filters);
    
    // pass along the whole object list to the template
    $data['mylist'] = & $mylist;

    if (isset($sortfield)) {
        $data['sortfield'] = $sortfield;
    } else {
        $data['sortfield'] = 'id';
    }
    if (isset($ascdesc)) {
        $data['ascdesc'] = ' ' . $ascdesc;
    } else {
        $data['ascdesc'] = ' ASC';
    }

    // Return the template variables defined in this function
    return $data;
}

?>
