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
/**
 * Get all email dates yet to be sent, sorted by age (oldest first)
 *
 */
function reminders_userapi_get_remaining_dates($args)
{
    // Support both objects and arrays
    if (!empty($args['object'])) {
        $fields = $data['object']->getFieldValues([], 1);
    } else {
        $fields = $args['array'];
    }

    // Get the array of all the reminder dates of this reminder
    $dates = xarMod::apiFunc('reminders', 'user', 'get_date_array', ['array' => $fields]);

    // Get the value for the current date
    $datetime = new XarDateTime();
    $datetime->settoday();
    $today = $datetime->getTimestamp();

    // Go through all the dates, weeding out those that do not apply
    foreach ($dates as $key => $date) {
        // Remove all dates with value 0 (these were not chosen
        if ($date['date'] == 0) {
            unset($dates[$key]);
        }
        // Remove all dates that have the done flag set
        if ($date['done'] == 1) {
            unset($dates[$key]);
        }
        // Remove all dates that are in the past
        // Remove any dates that correspond to today (we didn't set the done flag yet)
        if ($date['date'] <= $today) {
            unset($dates[$key]);
        }
    }

    // What we have left is the dates that still have to send an email
    $remaining = $dates;

    return $remaining;
}
