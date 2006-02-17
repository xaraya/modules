<?php
/**
 * Create a valid link to a particular week
 *
 * @package modules
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian development Team
 */
/**
 * Short description of the function: ??
 *
 * A somewhat longer description of the function: No idea ;)
 *
 * @author  Author Name <author@email>
 * @deprec  date since deprecated <insert this if function is deprecated>
 * @access  public / private / protected
 * @param   type param1 Description of parameter 1
 * @param   type param2 Description of parameter 2
 * @return  type to return description
 * @throws  list of exception identifiers which can be thrown
 * @todo    MichelV. <#> What is this?
 */

function julian_userapi_init()
{
    //=================================================================
    //  define constants used to make the code more readable
    //=================================================================
    define('CAL_SUNDAY',    0);
    define('CAL_MONDAY',    1);
    define('CAL_TUESDAY',   2);
    define('CAL_WEDNESDAY', 3);
    define('CAL_THURSDAY',  4);
    define('CAL_FRIDAY',    5);
    define('CAL_SATURDAY',  6);

    define('CAL_TYPE_VEVENT',    0);
    define('CAL_TYPE_VTODO',     1);
    define('CAL_TYPE_VJOURNAL',  2);
    define('CAL_TYPE_VFREEBUSY', 3);
    define('CAL_TYPE_VALARM',    4);

    define('CAL_CUTYPE_INDIVIDUAL',0);
    define('CAL_CUTYPE_GROUP',     1);
    define('CAL_CUTYPE_RESOURCE',  2);
    define('CAL_CUTYPE_ROOM',      3);
    define('CAL_CUTYPE_UNKNOWN',   4);

    define('CAL_ROLE_CHAIR',            0);
    define('CAL_ROLE_REQ_PARTICIPANT',  1);
    define('CAL_ROLE_OPT_PARTICIPANT',  2);
    define('CAL_ROLE_NON_PARTICIPANT',  3);

    define('CAL_PARTSTAT_NEEDS_ACTION', 0);
    define('CAL_PARTSTAT_ACCEPTED',     1);
    define('CAL_PARTSTAT_DECLINED',     2);
    define('CAL_PARTSTAT_TENTATIVE',    3);
    define('CAL_PARTSTAT_DELEGATED',    4);
    define('CAL_PARTSTAT_COMPLETED',    5);
    define('CAL_PARTSTAT_IN_PROCESS',   6);

    define('CAL_CLASS_PUBLIC',       0);
    define('CAL_CLASS_PRIVATE',      1);
    define('CAL_CLASS_CONFIDENTIAL', 2);

    define('CAL_RELTYPE_PARENT',    0);
    define('CAL_RELTYPE_CHILD',     1);
    define('CAL_RELTYPE_SIBLING',   2);

    define('CAL_ALARM_ACTION_AUDIO',      0);
    define('CAL_ALARM_ACTION_DISPLAY',    1);
    define('CAL_ALARM_ACTION_EMAIL',      2);
    define('CAL_ALARM_ACTION_PROCEDURE',  3);

    define('CAL_STATUS_TENTATIVE',    0); // vevent
    define('CAL_STATUS_CONFIRMED',    1); // vevent
    define('CAL_STATUS_CANCELLED',    2); // vevent, vtodo, vjournal
    define('CAL_STATUS_NEEDS_ACTION', 3); // vtodo
    define('CAL_STATUS_COMPLETED',    4); // vtodo
    define('CAL_STATUS_IN-PROCESS',   5); // vtodo
    define('CAL_STATUS_DRAFT',        7); // vjournal
    define('CAL_STATUS_FINAL',        8); // vjournal

    define('CAL_TRANSP_OPAQUE',      0);
    define('CAL_TRANSP_TRANSPARENT', 1);



    //define('_AM_VAL',              1);
    //define('_PM_VAL',              2);
    //define('_ACTION_DELETE',       4);
    //define('_ACTION_EDIT',         2);
    //define('_EVENT_APPROVED',      1);
    //define('_EVENT_QUEUED',        0);
    //define('_EVENT_HIDDEN',       -1);
    // $event_repeat

    define('CAL_NO_REPEAT',            0);
    define('CAL_REPEAT',               1);
    define('CAL_REPEAT_ON',            2);

    // $event_repeat_freq
    define('CAL_REPEAT_EVERY',         1);
    define('CAL_REPEAT_EVERY_OTHER',   2);
    define('CAL_REPEAT_EVERY_THIRD',   3);
    define('CAL_REPEAT_EVERY_FOURTH',  4);

    // $event_repeat_freq_type
    define('CAL_REPEAT_EVERY_DAY',     0);
    define('CAL_REPEAT_EVERY_WEEK',    1);
    define('CAL_REPEAT_EVERY_MONTH',   2);
    define('CAL_REPEAT_EVERY_YEAR',    3);

    // $event_repeat_on_num
    define('CAL_REPEAT_ON_1ST',        1);
    define('CAL_REPEAT_ON_2ND',        2);
    define('CAL_REPEAT_ON_3RD',        3);
    define('CAL_REPEAT_ON_4TH',        4);
    define('CAL_REPEAT_ON_LAST',       5);

    // $event_repeat_on_day
    define('CAL_REPEAT_ON_SUN',        0);
    define('CAL_REPEAT_ON_MON',        1);
    define('CAL_REPEAT_ON_TUE',        2);
    define('CAL_REPEAT_ON_WED',        3);
    define('CAL_REPEAT_ON_THU',        4);
    define('CAL_REPEAT_ON_FRI',        5);
    define('CAL_REPEAT_ON_SAT',        6);

    // $event_repeat_on_freq
    //define('CAL_REPEAT_ON_MONTH',      1);
    //define('CAL_REPEAT_ON_2MONTH',     2);
    //define('CAL_REPEAT_ON_3MONTH',     3);
    //define('CAL_REPEAT_ON_4MONTH',     4);
    //define('CAL_REPEAT_ON_6MONTH',     6);
    //define('CAL_REPEAT_ON_YEAR',       12);

    // event sharing values
    define('CAL_SHARING_PRIVATE',      0);
    define('CAL_SHARING_PUBLIC',       1);
    define('CAL_SHARING_BUSY',         2);
    define('CAL_SHARING_GLOBAL',       3);
}
?>
