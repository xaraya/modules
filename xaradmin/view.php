<?php
    function calendar_admin_view($args)
    {
        if (!xarVarFetch('name', 'str:1:', $name, 'calendar_calendar')) return;
        if (!xarSecurityCheck('EditCalendar')) return;
        $data['object'] = xarModApiFunc('dynamicdata','user','getobjectlist', array('name' => $name));
        $data['object']->getItems();
        return $data;
    }
?>