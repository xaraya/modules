<?php
/**
 * View items of the xarayatesting object
 *
 */
    function xarayatesting_admin_view($args)
    {
        if (!xarSecurity::check('ManageXarayatesting')) {
            return;
        }

        $modulename = 'xarayatesting';

        // Define which object will be shown
        if (!xarVar::fetch('objectname', 'str', $objectname, null, xarVar::DONT_SET)) {
            return;
        }
        if (!empty($objectname)) {
            xarModVars::set($modulename, 'defaultmastertable', $objectname);
        }

        // Set a return url
        xarSession::setVar('ddcontext.' . $modulename, array('return_url' => xarServer::getCurrentURL()));

        // Get the available dropdown options
        $object = DataObjectMaster::getObjectList(array('objectid' => 1));
        $data['objectname'] = xarModVars::get($modulename, 'defaultmastertable');
        $items = $object->getItems();
        $options = array();
        foreach ($items as $item) {
            if (strpos($item['name'], $modulename) !== false) {
                $options[] = array('id' => $item['name'], 'name' => $item['name']);
            }
        }
        $data['options'] = $options;
        return $data;
    }
