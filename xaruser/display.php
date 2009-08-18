<?php
/**
 * Display an Item
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
 * display an item
 * This is a standard function to provide detailed informtion on a single item
 * available from the module.
 *
 * @param $args an array of arguments (if called by other modules)
 * @param $args['objectid'] a generic object id (if called by other modules)
 * @param $args['itemid'] the item id used for this dyn_example module
 * @return array $data
 */
function dyn_example_user_display($args)
{
    // TODO: add reason for DONT_SET
    if(!xarVarFetch('itemid',   'id', $itemid,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('objectid', 'id', $objectid, NULL, XARVAR_DONT_SET)) {return;}

    extract($args);

    if (!empty($objectid)) {
        $itemid = $objectid;
    }

    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'user', 'display', 'dyn_example');
        throw new Exception($msg);
    }
    // Add the user menu to the data array

/* start APPROACH # 1 and # 2 : retrieve the item directly in the template */
    // Note: we don't retrieve any item here ourselves - we'll let the
    //       <xar:data-display ... /> tag do that in the template itself
    $data['itemid'] = $itemid;
/* end APPROACH # 1 and # 2 : retrieve the item directly in the template */

    if (!xarSecurityCheck('ReadDynExample',1,'Item',$itemid)) return;

/* start APPROACH # 3 : getting the object via API */
    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
    // Get the object we'll be working with
    $object = DataObjectMaster::getObject(array('name' => 'dyn_example', 'itemid' => $itemid));

    // get the values for this item
    $newid = $object->getItem();
    if (!isset($newid) || $newid != $itemid) return;

    // pick whatever properties are relevant to be transformed (e.g. the 'name' property here)
    $name = $object->properties['name']->getValue();
    // tranform those values
    list($name) = xarModCallHooks('item',
                                  'transform',
                                  $itemid,
                                  array($name));
    // update the properties with the transformed values
    $object->properties['name']->setValue($name);

    // use some property for the page title below too
    $title = $name;

    // pass along the whole object to the template, for use in <xar:data-display ... />
    $data['object'] =& $object;
    // or pass along only the properties instead of the object, and do the layout ourselves
    //$data['properties'] =& $object->getProperties();
/* end APPROACH # 3 : getting the object via API */


/* start APPROACH # 4 : getting only the raw item values via API */
    $values = $object->getFieldValues();
    $data['labels'] = array();
    $data['values'] = array();
    foreach ($values as $name => $value) {
        $data['values'][$name] = xarVarPrepForDisplay($value);
        // do some other processing here...

        // define in some labels
        $data['labels'][$name] = xarML(ucfirst($name));
    }
/* end APPROACH # 4 : getting only the raw item values via API */

    // get user settings for 'bold'
    $data['is_bold'] = xarModUserVars::get('dyn_example', 'bold');

    xarVarSetCached('Blocks.dyn_example', 'itemid', $itemid);

    // Once again, we are changing the name of the title for better
    // Search engine capability.
    xarTplSetPageTitle(xarVarPrepForDisplay($title));

    // Return the template variables defined in this function
    return $data;

}

?>