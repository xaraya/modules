<?php
/**
 * Modify an item of the event object
 *
 */
    function calendar_user_modify()
    {
        if (!xarSecurityCheck('EditCalendar')) return;

        if (!xarVarFetch('itemid',  'int',  $data['itemid'], 0, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('page',  'str:1',  $data['page'], 'week', XARVAR_NOT_REQUIRED)) return;
        xarSession::setVar('ddcontext.calendar', array('page' => $page,
                                                        ));
        $data['object'] = DataobjectMaster::getObject(array('name' => 'calendar_event'));
        $data['object']->getItem(array('itemid' => $data['itemid']));        
        $data['tplmodule'] = 'calendar';
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
?>