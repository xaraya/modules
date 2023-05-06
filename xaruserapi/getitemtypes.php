<?php
/**
 * Return the itemtypes of the xarayatesting module
 *
 */
    function xarayatesting_userapi_getitemtypes($args)
    {
        $itemtypes = array();

        $itemtypes[1] = array('label' => xarML('Native Xarayatesting'),
                              'title' => xarML('View Xarayatesting'),
                              'url'   => xarController::URL('xarayatesting','user','view')
                             );

        $extensionitemtypes = xarMod::apiFunc('dynamicdata','user','getmoduleitemtypes',array('moduleid' => 30073, 'native' => false));

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
