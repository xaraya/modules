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
 *  Wrapper for the dayIs method of the calendar class
 *  @author Roger Raymond <roger@asphyxia.com>
 *  @version $Id: dayis.php,v 1.2 2003/06/24 21:23:06 roger Exp $
 *  @param int $day 0 - 6 [Sun - Sat]
 *  @param int $date valid date YYYYMMDD
 *  @return bool true/false depending on day looking for and the date
 */
function calendar_userapi_dayIs($args)
{
    extract($args);
    unset($args);
    // make sure we have a valid day value
    if (!xarVar::validate('int:0:7', $day)) {
        return;
    }
    // TODO: Revisit this later and make a new validator for it
    // make sure we have a valid date
    if (!xarVar::validate('int::', $date)) {
        return;
    }
    $c = xarMod::apiFunc('calendar', 'user', 'factory', 'calendar');
    return $c->dayIs($day, $date);
}
