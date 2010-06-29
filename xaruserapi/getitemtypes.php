<?php
/**
 * Return the itemtypes of the ckeditor module
 *
 */
    function ckeditor_userapi_getitemtypes($args)
    {
        $itemtypes = array();

        $itemtypes[1] = array('label' => xarML('Native CKEditor'),
                              'title' => xarML('View CKEditor'),
                              'url'   => xarModURL('ckeditor','user','view')
                             );

        $extensionitemtypes = xarModAPIFunc('dynamicdata','user','getmoduleitemtypes',array('moduleid' => 30066, 'native' => false));

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