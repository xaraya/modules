<?php
/**
 * Create a new item of the event object
 *
 */
    function calendar_user_new()
    {
        if (!xarSecurityCheck('AddCalendar')) return;

        if (!xarVarFetch('page',  'str:1',  $data['page'], 'week', XARVAR_NOT_REQUIRED)) return;
        $data['object'] = DataobjectMaster::getObject(array('name' => 'calendar_event'));
        $data['tplmodule'] = 'calendar';
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
?>