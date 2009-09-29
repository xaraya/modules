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
 * This is a standard function to display an item
 *
 * @param $args an array of arguments (if called by other modules)
 * @param $args['objectid'] a generic object id (if called by other modules)
 * @param $args['itemid'] the itemid used for this dyn_example module
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

// Make sure user has read privileges for the item
    if (!xarSecurityCheck('ReadDynExample',1,'Item',$itemid)) return;

  // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
    // Get the object definition we'll be working with
    $object = DataObjectMaster::getObject(array('name' => 'dyn_example'));

    //We'll need $object in the template for both Approach #1 and #2, so let's go ahead and add it to the $tdata array.  At the end of this function, we'll return $tdata to the template.
    $tdata['object'] = $object;

    //We don't really have the item until we call getItem()
    $some_id = $object->getItem(array('itemid' => $itemid));

    //Make sure we got something
    if (!isset($some_id) || $some_id != $itemid) return;

/* start APPROACH #1: data-display */
    // All we really need for this approach is an $itemid and $object 
    $tdata['itemid'] = $itemid;
/* end APPROACH #1  */

/* start APPROACH #2  make the values available in template variables  */

    //Get the property names and values for the item with the getFieldValues() method
    $values = $object->getFieldValues();

    //$values is an associative array of property names and values, so...
    foreach ($values as $name => $value) {
        $tdata[$name] = xarVarPrepForDisplay($value);
    }

    //We're dealing with people's names in this example, so we have a property named "name."  But "$name" might be confusing to use as a variable in the template, so let's make a slight adjustment here...
    $tdata['person_name'] = $tdata['name'];

    //Now we have our item values to return to the template, like so... $tdata['person_name'], $tdata['age'] and $tdata['picture'].  In the template these become #$person_name#, #$age#, #$picture#.  
    //At the end of this function, we're going to return $tdata to the template.  There is no real significance to calling the variable $tdata, as long as we're consistent.  The template won't know and won't care if we've returned the array as $tdata, $apples, $frogs, etc.

/* end APPROACH # 2 */

    
/* start APPROACH #3:  data-getitem  */
  /* This approach doesn't require that we do anything further here.  We've already loaded $object into the $tdata array and called getItem(), so we're all set */
/* end APPROACH #3 */

    xarVarSetCached('Blocks.dyn_example', 'itemid', $itemid);

    // Let's use the person's name as the page title.  We can use this in the title tag of our HTML document
    $title = $tdata['name'];
    xarTplSetPageTitle(xarVarPrepForDisplay($title));

    // Return the template variables defined in this function
    return $tdata;

}

?>