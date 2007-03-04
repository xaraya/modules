<?php
//sys::import('xaraya.structures.descriptor');
//sys::import('modules.calendar.class.month');

function calendar_monthblock_init()
{
    return array(
        'nocache' => 1, // don't cache by default
        'pageshared' => 1, // share across pages
        'usershared' => 0, // don't share across users
        'cacheexpire' => null);
        $descriptor = new ObjectDescriptor(array(
                                        'nocache' => 1,
                                        'usershared' => 0,
                                        'text_type' => 'Month',
                                        'text_type_long' => 'Month selection',
                                        'module' => 'calendar',
                                        ));
        $block = new MonthBlock($descriptor);
        return $block->getArgs();
}

/**
 * get information on block
 */
function calendar_monthblock_info()
{
    return array(
        'text_type' => 'Month',
        'module' => 'calendar',
        'text_type_long' => 'Month selection'
    );
}

/**
 * Display func.
 * @param $blockinfo array containing title,content
 */
function calendar_monthblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ReadCalendar', 0, 'Block', "All:" . $blockinfo['title'] . ":" . $blockinfo['bid'])) {return;}

    if (!defined('CALENDAR_ROOT')) {
        define('CALENDAR_ROOT', xarModGetVar('calendar','pearcalendar_root'));
    }
    include_once(CALENDAR_ROOT.'Month/Weekdays.php');
    include_once(CALENDAR_ROOT.'Decorator/Textual.php');
    sys::import("modules.calendar.class.Calendar.Decorator.Xaraya");

    // Build the month
    $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
    $data['MonthCal'] = new Calendar_Month_Weekdays(
        $data['cal_year'],
        $data['cal_month'],
        CALENDAR_FIRST_DAY_OF_WEEK);
    $data['MonthCal']->build();

    $blockinfo['content'] = $data;

    return $blockinfo;
}

?>
