<?php
/**
 * Utility function to retrieve the list of item types of this module (if any)
 *
 * @subpackage Foo module
 */
/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 * @returns array
 * @return array containing the item types and their description
 */
function foo_userapi_getitemtypes($args)
{
    $itemtypes = array();

    $itemtypes[1] = array('label' => xarML('Native Foo'),
                          'title' => xarML('View Foo'),
                          'url'   => xarModURL('foo','user','view')
                         );

    $extensionitemtypes = xarModAPIFunc('dynamicdata','user','getmoduleitemtypes',array('moduleid' => 30000, 'native' => false));

    /* TODO: activate this code when we move to php5
    $keys = array_merge(array_keys($itemtypes),array_keys($extensionitemtypes));
    $values = array_merge(array_values($itemtypes),array_values($extensionitemtypes));
    return array_combine($keys,$values);
    */

    $types = array();
    foreach ($itemtypes as $key => $value) $types[$key] = $value;
    foreach ($extensionitemtypes as $key => $value) $types[$key] = $value;
    return $types;
}

?>
