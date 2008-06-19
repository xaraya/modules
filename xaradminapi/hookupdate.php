<?php

    sys::import('modules.dynamicdata.class.objects.master');

    function calendar_adminapi_hookupdate($data)
    {
        if (!isset($data['extrainfo']) || !is_array($data['extrainfo'])) $data['extrainfo'] = array();

        // When called via hooks, modname will be empty, but we get it from the
        // extrainfo or the current module
        if (empty($data['module'])) {
            if (!empty($data['extrainfo']['module'])) $data['module'] = $data['extrainfo']['module'];
            else $data['module'] = xarModGetName();
        }
        $data['module_id'] = xarMod::getID(($data['module']));
        if (empty($data['module_id']))  throw new IDNotFoundException("module id for " . $data['modname']);

        if (!isset($data['itemtype']) || !is_numeric($data['itemtype'])) {
             if (isset($data['extrainfo']['itemtype']) && is_numeric($data['extrainfo']['itemtype'])) $data['itemtype'] = $data['extrainfo']['itemtype'];
             else $data['itemtype'] = 0;
        }
        if (!isset($data['itemid']) || !is_numeric($data['itemid'])) {
             if (isset($data['extrainfo']['item_id']) && is_numeric($data['extrainfo']['item_id'])) $data['itemid'] = $data['extrainfo']['item_id'];
             else $data['itemid'] = 0;
        }

        $data['extrainfo']['module_id'] = $data['module_id'];
        $data['extrainfo']['itemtype'] = $data['itemtype'];
        $data['extrainfo']['item_id'] = $data['itemid'];

        $data['extrainfo']['name'] = isset($data['extrainfo']['name']) ? $data['extrainfo']['name'] : xarML('Unknown Event');
        $data['extrainfo']['start_time'] = isset($data['extrainfo']['start_time']) ? $data['extrainfo']['start_time'] : time();
        $data['extrainfo']['duration'] = isset($data['extrainfo']['duration']) ? $data['extrainfo']['duration'] : 60;
        $data['extrainfo']['end_time'] = isset($data['extrainfo']['end_time']) ? $data['extrainfo']['end_time'] : $data['extrainfo']['start_time'] + $data['extrainfo']['duration'];
        $data['extrainfo']['recurring_code'] = isset($data['extrainfo']['recurring_code']) ? $data['extrainfo']['recurring_code'] : 0;
        $data['extrainfo']['recurring_span'] = isset($data['extrainfo']['recurring_span']) ? $data['extrainfo']['recurring_span'] : 0;
                        
        $data['extrainfo']['start_location'] = isset($data['extrainfo']['start_location']) ? $data['extrainfo']['start_location'] : null;
        $data['extrainfo']['end_location'] = isset($data['extrainfo']['end_location']) ? $data['extrainfo']['end_location'] : null;
        $data['extrainfo']['object_id'] = isset($data['extrainfo']['object_id']) ? $data['extrainfo']['object_id'] : 0;
        $data['extrainfo']['role_id'] = isset($data['extrainfo']['role_id']) ? $data['extrainfo']['role_id'] : xarSession::getVar('role_id');
        $data['extrainfo']['return_link'] = isset($data['extrainfo']['return_link']) ? $data['extrainfo']['return_link'] : '';
        $data['extrainfo']['state'] = isset($data['extrainfo']['state']) ? $data['extrainfo']['state'] : 3;
        $data['extrainfo']['timestamp'] = isset($data['extrainfo']['timestamp']) ? $data['extrainfo']['timestamp'] : time();

        $object = DataObjectMaster::getObject(array('name' => 'calendar_event'));
        $item = $object->updateItem($data['extrainfo']);
        
        return $data['extrainfo'];
    }
?>
