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

function calendar_user_submit()
{
    xarVar::fetch('cal_sdow', 'int:0:6', $cal_sdow, 0);
    xarVar::fetch('cal_date', 'int::', $cal_date, 0);

    $c = xarMod::apiFunc('calendar', 'user', 'factory', 'calendar');
    $c->setStartDayOfWeek($cal_sdow);

    $data = xarMod::apiFunc('calendar', 'user', 'getUserDateTimeInfo');
    $data['cal_sdow'] =& $c->getStartDayOfWeek();
    $data['shortDayNames'] =& $c->getShortDayNames($c->getStartDayOfWeek());
    $data['mediumDayNames'] =& $c->getMediumDayNames($c->getStartDayOfWeek());
    $data['longDayNames'] =& $c->getLongDayNames($c->getStartDayOfWeek());
    $data['calendar'] =& $c;

    // return the event data
    xarVar::fetch('event_id', 'int::', $event_id, 0);
    $e = xarMod::apiFunc('calendar', 'user', 'factory', 'event');
    $e->buildEvent($event_id);
    // remember to pass in the existing array so it can be appended too
    $e->getEventDataForBL($data);

    // echo the content to the screen
    return $data;
}
