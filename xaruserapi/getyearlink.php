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
 *  calendar_userapi_getYearLink
 *  Create a valid link to a particluar year
 *  @version $Id: getyearlink.php,v 1.1 2003/06/24 20:01:14 roger Exp $
 *  @author Roger Raymond
 *  @access public
 *  @param string $date YYYYMMDD date to provide link to
 *  @return string a valid link based on xarController::URL()
 *  @todo add necessary get vars to the resulting URL
 */
function calendar_userapi_getYearLink($date=null)
{
    if (!isset($date)) {
        $date = date('Ymd');
    }
    $year = substr($date, 0, 4);
    $month = substr($date, 4, 2);
    $day = substr($date, 6, 2);

    $link = xarController::URL('calendar', 'user', 'year', ['cal_date'=>$date]);
    return $link;
}
