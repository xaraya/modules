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
 * Get all email dates yet to be sent
 *
 */
function reminders_userapi_get_email_dates($args)
{
    if (isset($args['array'])) {
        // We have an array of item values ("an item") from a dataobject
        $dates = [];
        for ($i=1;$i<=10;$i++) {
            // Ignore dates with values 0
            if ($args['array']['reminder_' . $i] == 0) {
                continue;
            }
            // Ignore dates we have already done
            if ($args['array']['reminder_done_' . $i] == 1) {
                continue;
            }
            $dates[] = (int)$args['array']['reminder_' . $i];
        }
    } else {
        // Need to add support for objects
    }
    // Sort the dates DESC (earliest first)
    rsort($dates);
    return $dates;
}
