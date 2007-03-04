<?php
    function calendar_admin_view($args)
    {
        if (!xarSecurityCheck('EditCalendar')) return;
        $data['object'] = xarModApiFunc('dynamicdata','user','getobjectlist', array('name' => 'calendar_calendar'));
        $data['object']->getItems();
        return $data;
    }
?>