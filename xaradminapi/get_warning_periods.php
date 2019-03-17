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


function reminders_adminapi_get_warning_periods()
{
    $warning_periods = array(
                    array('id' => A0,        'name' => xarML('Choose a period')),
                    array('id' => A1_day,    'name' => xarML('1 day')),
                    array('id' => A2_days,   'name' => xarML('2 days')),
                    array('id' => A3_days,   'name' => xarML('3 days')),
                    array('id' => A4_days,   'name' => xarML('4 days')),
                    array('id' => A5_days,   'name' => xarML('5 days')),
                    array('id' => A6_days,   'name' => xarML('6 days')),
                    array('id' => A1_week,   'name' => xarML('1 week')),
                    array('id' => A2_weeks,  'name' => xarML('2 weeks')),
                    array('id' => A3_weeks,  'name' => xarML('3 weeks')),
                    array('id' => A1_month,  'name' => xarML('1 month')),
                    array('id' => A2_months, 'name' => xarML('2 months')),
                    array('id' => A3_months, 'name' => xarML('3 months')),
                    array('id' => A6_months, 'name' => xarML('6 months')),
                    array('id' => A1_year,   'name' => xarML('1 year')),
                    );
    return $warning_periods;
}
?>