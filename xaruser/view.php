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
    $data = xarModAPIFunc('dyn_example','user','menu');

    if(!xarVarFetch('startnum', 'isset', $data['startnum'], NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('catid',    'isset', $data['catid'],    NULL, XARVAR_DONT_SET)) {return;}

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    // We check here to see that the current user has the privilege to view items in the module
    if (!xarSecurityCheck('ViewDynExample')) return;

    // get user settings for 'itemsperpage'
    $data['itemsperpage'] = xarModGetUserVar('dyn_example','itemsperpage');

/* start APPROACH # 1 and # 2 : retrieve the items directly in the template */
    // Note: we don't retrieve any items here ourselves - we'll let the
    //       <xar:data-view ... /> tag do that in the template itself
/* end APPROACH # 1 and # 2 : retrieve the items directly in the template */

/* start APPROACH # 3 : getting the object list via API
 *
 * Here we retreive all items via an API call to dynamic data itself
 * We need to pass all variables we need to get a correct listing
 */
    $mylist = xarModAPIFunc('dynamicdata','user','getitems',
                             array('module'    => 'dyn_example',
                                   'itemtype'  => 0,
                                   'catid'     => $data['catid'],
                                   'numitems'  => $data['itemsperpage'],
                                   'startnum'  => $data['startnum'],
                                   'status'    => 1,      // only get the properties with status 1 = active
                                   'getobject' => 1));    // get back the object list
    // pass along the whole object list to the template (cfr. xaradmin.php)
    $data['mylist'] = & $mylist;
/* here we use a different variation than in xaradmin.php */
    // or pass along the properties and values instead of the object list
    $data['properties'] =& $mylist->getProperties();
    $data['values'] =& $mylist->items;
    // TODO: add a pager here (needed for this approach)
/* end APPROACH # 3 : getting the object list via API */

/* start APPROACH # 4 : getting only the raw item values via API */
    $values = xarModAPIFunc('dynamicdata','user','getitems',
                             array('module'   => 'dyn_example',
                                   'itemtype' => 0,
                                   'catid'    => $data['catid'],
                                   'numitems' => $data['itemsperpage'],
                                   'startnum' => $data['startnum'],
                                   'status'   => 1));
    $data['labels'] = array();
    $data['items'] = array();
    foreach ($values as $itemid => $fields) {
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
        }
    }
    // TODO: add a pager here (needed for this approach)
/* end APPROACH # 4 : getting only the raw item values via API */

    // We are changing the name of the page to raise
    // better search engine compatibility.
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('View Dynamic Examples')));

    // Return the template variables defined in this function
    return $data;
}

?>