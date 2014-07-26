<?php
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2014 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

    include_once(CALENDAR_ROOT.'Year.php');
    define('CALENDAR_MONTH_STATE',CALENDAR_USE_MONTH_WEEKDAYS);
    function calendar_user_year()
    {
        $data = xarMod::apiFunc('calendar','user','getUserDateTimeInfo');
        $Year = new Calendar_Year($data['cal_year']);
        $Year->build(); // TODO: find a better way to handle this
        $data['Year'] =& $Year;
        $data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
        return $data ;
    }

?>