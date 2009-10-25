<?php
/**
 * View a list of items
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
 * view a list of items
 * This is a standard function to provide an overview of all of the items
 * available from the module.
 *
 * @param startnum
 * @param catid
 * @return array
 */
function dyn_example_user_view()
{

	// Check here to see that the current user has the privilege to view items in the module
    if (!xarSecurityCheck('ViewDynExample')) return;

	// Get this value from the URL query string
	// TODO: why XARVAR_DONT_SET ?
    if(!xarVarFetch('startnum', 'isset', $tdata['startnum'], NULL, XARVAR_DONT_SET)) {return;}

    // Get user setting for 'items_per_page'
    $tdata['items_per_page'] = xarModUserVars::get('dyn_example','items_per_page');

/* APPROACH #1 and #3 */

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

    // Get the object we'll be working with
    $mylist = DataObjectMaster::getObjectList(array('name' => 'dyn_example'));

    // We have some filters for the items
    $filters = array('numitems'  => $tdata['items_per_page'],
                     'startnum'  => $tdata['startnum'],
                     'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE
                    );
    
    // Count the items first if you want a full pager - otherwise you'll get simple previous/next links
    $mylist->countItems($filters);

    // Get the items 
    $items = $mylist->getItems($filters);

    // Pass along the whole object list to the template.  Only needed in Approach #1
    $tdata['mylist'] = & $mylist;

/* end APPROACH #1 and #3 */

/* start APPROACH #2 : retrieve the items directly in the template */

    // Note: we don't retrieve any items here ourselves - we'll let the
    //       <xar:data-view ... /> tag do that in the template itself

/* end APPROACH #2 : retrieve the items directly in the template */

/* start APPROACH #3 */

    $tdata['properties'] =& $mylist->getProperties();
    $tdata['values'] =& $mylist->items;

/* end APPROACH #3  */

/* start APPROACH #4 : getting only the raw item values via API */
    $tdata['labels'] = array();
    $tdata['items'] = array();
    foreach ($items as $itemid => $fields) {
        $tdata['items'][$itemid] = array();
        foreach ($fields as $name => $value) {
            $tdata['items'][$itemid][$name] = xarVarPrepForDisplay($value);
            // do some other processing here...
        }
        // define in some labels
        if (count($tdata['labels']) == 0) {
            foreach (array_keys($fields) as $name) {
                $tdata['labels'][$name] = xarML(ucfirst($name));
            }
        }
    }
    // TODO: add a pager here (needed for this approach)
/* end APPROACH #4 : getting only the raw item values via API */

    // We are changing the name of the page to raise
    // better search engine compatibility.
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('View Dynamic Examples')));

    // Return the template variables defined in this function
    return $tdata;
}

?>
