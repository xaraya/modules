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
    $warning_periods = [
                    ['id' => A0,        'name' => xarML('Choose a period')],
                    ['id' => A1_day,    'name' => xarML('1 day')],
                    ['id' => A2_days,   'name' => xarML('2 days')],
                    ['id' => A3_days,   'name' => xarML('3 days')],
                    ['id' => A4_days,   'name' => xarML('4 days')],
                    ['id' => A5_days,   'name' => xarML('5 days')],
                    ['id' => A6_days,   'name' => xarML('6 days')],
                    ['id' => A1_week,   'name' => xarML('1 week')],
                    ['id' => A2_weeks,  'name' => xarML('2 weeks')],
                    ['id' => A3_weeks,  'name' => xarML('3 weeks')],
                    ['id' => A1_month,  'name' => xarML('1 month')],
                    ['id' => A2_months, 'name' => xarML('2 months')],
                    ['id' => A3_months, 'name' => xarML('3 months')],
                    ['id' => A6_months, 'name' => xarML('6 months')],
                    ['id' => A1_year,   'name' => xarML('1 year')],
                    ];
    return $warning_periods;
}
