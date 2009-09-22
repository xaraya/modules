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

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('EditDynExample')) return;

    $data['items_per_page'] = xarModVars::get('dyn_example','items_per_page');

/* start APPROACH # 1 and # 2 : retrieve the items directly in the template */
    // Note: we don't retrieve any items here ourselves - we'll let the
    //       <xar:data-view ... /> tag do that in the template itself
/* end APPROACH # 1 and # 2 : retrieve the items directly in the template */

/* start APPROACH # 3 : getting the object list via API */
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
    
    // Get the items 
    $items = $mylist->getItems($filters);
    
    // pass along the whole object list to the template
    $data['mylist'] = & $mylist;
    
    // or pass along the properties and values instead of the object list (cfr. below)
    //$data['properties'] =& $mylist->getProperties();
    //$data['values'] = $items;
    // In this example we wont pass these vars to the template but rather get them on the template itslef
    // using the getitems tag
/* end APPROACH # 3 : getting the object list via API */

/* start APPROACH # 4 : getting only the raw item values via API */
    
    // Do the same as above, but then work on the item values
    $data['labels'] = array();
    $data['items'] = array();
    foreach ($items as $itemid => $fields) {
        $data['items'][$itemid] = array();
        foreach ($fields as $name => $value) {
            $data['items'][$itemid][$name] = xarVarPrepForDisplay($value);
            // do some other processing here...
        }
        // define in some labels
        if (count($data['labels']) == 0) {
            foreach (array_keys($fields) as $name) {
                $data['labels'][$name] = xarML(ucfirst($name));
            }
            $data['labels']['options'] = xarML('Options');
        }
    }
/* end APPROACH # 4 : getting only the raw item values via API */

    // Return the template variables defined in this function
    return $data;
}

?>
