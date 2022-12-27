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

sys::import("modules.calendar.class.Calendar.Decorator.event");
sys::import("modules.calendar.class.Calendar.Decorator.dayevent");

function calendar_user_view()
{
    $data = xarMod::apiFunc('calendar', 'user', 'getUserDateTimeInfo');
    $DayEvents = new Calendar_Day($data['cal_year'], $data['cal_month'], $data['cal_day'], CALENDAR_FIRST_DAY_OF_WEEK);
    $args = [
        'day' => &$Day,
    ];
    $events = xarMod::apiFunc('calendar', 'user', 'getevents', $args);
    return $data;
}
