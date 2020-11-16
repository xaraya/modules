<?php
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * Create a new item of the event object
 *
 */
function calendar_user_new()
{
    if (!xarSecurity::check('AddCalendar')) {
        return;
    }

    if (!xarVar::fetch('page', 'str:1', $data['page'], 'week', xarVar::NOT_REQUIRED)) {
        return;
    }
    xarSession::setVar('ddcontext.calendar', array('page' => $data['page'],
                                                    ));
    $data['object'] = DataobjectMaster::getObject(array('name' => 'calendar_event'));
    $data['tplmodule'] = 'calendar';
    $data['authid'] = xarSec::genAuthKey();
    return $data;
}
