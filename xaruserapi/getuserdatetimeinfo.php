<?php
/**
 * Format a date for a user
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian development Team
 */

/**
 * Format a date for users
 *
 * This function should make a nice viewable date. Can be deprecated soon?
 *
 * @author  Julian Development Team, MichelV. <michelv@xarayahosting.nl>
 * @access  public
 * @deprec  date since deprecated <insert this if function is deprecated>
 * @param   the date
 * @return  array $bl_data
 * @todo    MichelV. <#> Deprecate
 */

function julian_userapi_getUserDateTimeInfo()
{
    // dates come in as YYYYMMDD
    xarVarFetch('cal_date', 'str:4:8', $cal_date, xarLocaleFormatDate('%Y%m%d'));

    $bl_data = array();
    $bl_data['cal_date'] = $cal_date;

    if(!preg_match('/([0-9]{4,4})([0-9]{2,2})?([0-9]{2,2})?/',$cal_date,$match)) {
        $year = gmdate('Y');
        $month = gmdate('m');
        $day = gmdate('d');
    } else {
        $year = $match[1];
        if(isset($match[2])) {
            $month=$match[2];
        } else {
            $month=gmdate('m');
        }
        if(isset($match[3])) {
            $day=$match[3];
        } else {
            $day=gmdate('d');
        }
    }

    $bl_data['selected_date']      = $year.$month.$day;
    $bl_data['selected_day']       = $day;
    $bl_data['selected_month']     = $month;
    $bl_data['selected_year']      = $year;
    $bl_data['selected_timestamp'] = gmmktime(0,0,0,$month,$day,$year);

    return $bl_data;
}
?>
