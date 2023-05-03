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

function calendar_userapi_getmonthstructure($args=array()) 
{
    extract($args); unset($args);
    if(!isset($month)) return;
    if(!isset($year)) return;
    
    xarVar::validate('int:1:12', $month);
    xarVar::validate('int::', $year);
    xarVar::fetch('cal_sdow','int:0:6',$cal_sdow,0);
    
    $c = xarMod::apiFunc('calendar','user','factory','calendar');
    $c->setStartDayOfWeek($cal_sdow);
    // echo the content to the screen
    return $c->getCalendarMonth($year.$month);
}

?>
