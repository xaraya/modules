<?php
    function calendar_userapi_getitemtypes($args)
    {
        $itemtypes = array();

        $itemtypes[1] = array('label' => xarML('Event'),
                              'title' => xarML('View Event'),
                              'url'   => xarModURL('calendar','user','view')
                             );
        $itemtypes[2] = array('label' => xarML('ToDo'),
                              'title' => xarML('View ToDo'),
                              'url'   => xarModURL('calendar','user','view')
                             );
        $itemtypes[3] = array('label' => xarML('alarm'),
                              'title' => xarML('View Alarm'),
                              'url'   => xarModURL('calendar','user','view')
                             );
        $itemtypes[4] = array('label' => xarML('freebusy'),
                              'title' => xarML('View FreeBusy'),
                              'url'   => xarModURL('calendar','user','view')
                             );
        // @todo let's use DataObjectMaster::getModuleItemType here, but not until roles brings in dd automatically
        $extensionitemtypes = xarModAPIFunc('dynamicdata','user','getmoduleitemtypes',array('moduleid' => 7, 'native' =>false));

        $keys = array_merge(array_keys($itemtypes),array_keys($extensionitemtypes));
        $values = array_merge(array_values($itemtypes),array_values($extensionitemtypes));
        return array_combine($keys,$values);
    }
?>
