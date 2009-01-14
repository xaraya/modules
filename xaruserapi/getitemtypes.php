<?php
/**
 * Return the itemtypes of the foo module
 *
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
