<?php
/**
 * View items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
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
    $data = xarModAPIFunc('dyn_example','admin','menu');

    if(!xarVarFetch('startnum', 'isset', $data['startnum'], NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('catid',    'isset', $data['catid'],    NULL, XARVAR_DONT_SET)) {return;}

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('EditDynExample')) return;

    $data['itemsperpage'] = xarModGetVar('dyn_example','itemsperpage');

/* start APPROACH # 1 and # 2 : retrieve the items directly in the template */
    // Note: we don't retrieve any items here ourselves - we'll let the
    //       <xar:data-list ... /> tag do that in the template itself
/* end APPROACH # 1 and # 2 : retrieve the items directly in the template */

/* start APPROACH # 3 : getting the object list via API */
    $mylist = xarModAPIFunc('dynamicdata','user','getitems',
                             array('module'    => 'dyn_example',
                                   'itemtype'  => 0,
                                   'catid'     => $data['catid'],
                                   'numitems'  => $data['itemsperpage'],
                                   'startnum'  => $data['startnum'],
                                   'status'    => 1,      // only get the properties with status 1 = active
                                   'getobject' => 1));    // get back the object list
/* here we use a different variation than in xaruser.php */
    // pass along the whole object list to the template
    $data['mylist'] = & $mylist;
    // or pass along the properties and values instead of the object list (cfr. xaruser.php)
    //$data['properties'] =& $mylist->getProperties();
    //$data['values'] =& $mylist->items;
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
            $data['labels']['options'] = xarML('Options');
        }
    }
/* end APPROACH # 4 : getting only the raw item values via API */

    // Return the template variables defined in this function
    return $data;
}

?>