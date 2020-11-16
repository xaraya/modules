<?php
    function calendar_admin_viewevents($args)
    {
        if (!xarSecurity::check('EditCalendar')) {
            return;
        }
        $data['object'] = xarMod::apiFunc('dynamicdata', 'user', 'getobjectlist', array('name' => 'calendar_event'));
        $data['object']->getItems();
        return xarTpl::module('calendar', 'admin', 'view', $data);
    }
