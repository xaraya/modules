<?php
/**
 * View items of the shop objects
 *
 */
    function shop_admin_view($args)
    {
        if (!xarSecurityCheck('ManageShop')) return;

        $modulename = 'shop';

        // Define which object will be shown
        if (!xarVarFetch('objectname', 'str', $objectname, null, XARVAR_DONT_SET)) return;
        if (!empty($objectname)) xarModUserVars::set($modulename,'defaultmastertable', $objectname);

        // Get the available dropdown options
        $object = DataObjectMaster::getObjectList(array('objectid' => 1));
        $data['objectname'] = xarModUserVars::get($modulename,'defaultmastertable');
        $items = $object->getItems();
        $options = array();
        foreach ($items as $item)
            if (strpos($item['name'],$modulename) !== false)
                $options[$item['label']] = array('id' => $item['name'], 'name' => $item['label']);
                ksort($options);
        $data['options'] = $options;
        return $data;
    }
?>