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
        if (!xarVarFetch('objectname', 'str', $objectname, null, XARVAR_DONT_SET)) return;
        if (!empty($objectname)) xarModUserVars::set($modulename,'defaultmastertable', $objectname);

        // Set a return url
        xarSession::setVar('ddcontext.' . $modulename, array('return_url' => xarServerGetCurrentURL()));

        // Get the available dropdown options
        $object = DataObjectMaster::getObjectList(array('objectid' => 1));
        $data['objectname'] = xarModUserVars::get($modulename,'defaultmastertable');
        $items = $object->getItems();
        $options = array();
        foreach ($items as $item)
            if (strpos($item['name'],$modulename) !== false)
                $options[] = array('id' => $item['name'], 'name' => $item['name']);
        $data['options'] = $options;
        return $data;
    }
?>