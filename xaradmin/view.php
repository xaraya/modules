<?php
/**
 * View items of the foo object
 *
 */
    function foo_admin_view($args)
    {
        if (!xarSecurityCheck('ManageFoo')) return;

        $modulename = 'foo';

        // Define which object will be shown
        if (!xarVarFetch('objectname', 'str', $objectname, 'foo_foo', XARVAR_DONT_SET)) return;
        if (!empty($objectname)) xarModVars::set($modulename,'mastertable', $objectname);

        // Set a return url
        xarSession::setVar('ddcontext.' . $modulename, array('return_url' => xarServerGetCurrentURL()));

        // Get the available dropdown options
        $object = DataObjectMaster::getObjectList(array('objectid' => 1));
        $data['objectname'] = xarModVars::get($modulename,'mastertable');
        $items = $object->getItems();
        $options = array();
        foreach ($items as $item)
            if (strpos($item['name'],$modulename) !== false)
                $options[] = array('id' => $item['name'], 'name' => $item['name']);
        $data['options'] = $options;
        return $data;
    }
?>