<?php
    function calendar_admin_view($args)
    {
        if (!xarSecurityCheck('ManageCalendar')) return;

        $modulename = 'calendar';

        // Define which object will be shown
        if (!xarVarFetch('objectname', 'str', $objectname, 'calendar_calendar', XARVAR_DONT_SET)) return;
        if (!empty($objectname)) xarModVars::set($modulename,'defaultmastertable', $objectname);

        // Set a return url
        xarSession::setVar('ddcontext.' . $modulename, array('return_url' => xarServer::getCurrentURL()));

        // Get the available dropdown options
        $object = DataObjectMaster::getObjectList(array('objectid' => 1));
        $data['objectname'] = xarModVars::get($modulename,'defaultmastertable');
        $items = $object->getItems();
        $options = array();
        foreach ($items as $item)
            if (strpos($item['name'],$modulename) !== false)
                $options[] = array('id' => $item['name'], 'name' => $item['name']);
        $data['options'] = $options;
        return $data;
    }
?>