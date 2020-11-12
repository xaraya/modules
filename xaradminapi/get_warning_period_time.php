<?php
/**
 * Reminders Module
 *
 * @package modules
 * @subpackage reminders
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */


function reminders_adminapi_get_warning_period_time($args)
{
    $days = array(
            A0          => 0,
            A1_day      => 1,
            A2_days     => 2,
            A3_days     => 3,
            A4_days     => 4,
            A5_days     => 5,
            A6_days     => 6,
            A1_week     => 7,
            A2_weeks    => 14,
            A3_weeks    => 21,
            A1_month    => 30,
            A2_months   => 60,
            A3_months   => 90,
            A6_months   => 180,
            A1_year     => 365,
            );
    if ($args['timeframe'] = 'seconds') {
        foreach ($days as $k => $v) $days[$k] = $v * 86400;
    }
    return $days;
}
?>