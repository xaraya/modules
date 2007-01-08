<?php
/**
 * Jump to another date
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 *
 * Forwards from the jump form to the page it's suppose to go to based on the jump to date.
 *
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @link http://www.metrostat.net
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @param string jump_to enum day, week, month, year The function to jump to
 * @param int cal_date The already made calendar date in format DDMMYYYY OPTIONAL
 * @param int jump_month Month
 * @param int jump_day
 * @param int jump_year
 * @return array URL to jump to
 */
function julian_user_jump($args)
{
    //This takes a month,day,year, and location to jump to and forwards it on to the new location.
    extract($args);
    unset($args);
    if (!xarVarFetch('cal_date','int::',$cal_date,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('jump_to','str::',$jump_to)) return;
    // If there is no cal_date already, we need to make one
    if (empty($cal_date) || !is_numeric($cal_date)) {

        xarVarFetch('jump_month','int',$jump_month);
        xarVarFetch('jump_day','int',$jump_day);
        xarVarFetch('jump_year','int',$jump_year);
        // Bug 5358, 5347
        if ($jump_month < 10) {
            $jump_month = '0'.$jump_month;
        }
        if ($jump_day < 10) {
            $jump_day = '0'.$jump_day;
        }
        $cal_date = $jump_year.$jump_month.$jump_day;
    }
    xarResponseRedirect(xarModURL('julian', 'user', $jump_to, array('cal_date' => $cal_date)));
}
?>