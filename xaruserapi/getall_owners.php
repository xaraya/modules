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
 * Return the entries
 *
 */
function reminders_userapi_getall_owners($args)
{
    // Set up this function to get all entries and the emails AND names of the users
    $tables = xarDB::getTables();
    $q = new Query('SELECT', $tables['reminders_emails']);

    // All owners unless we passed a state
    if (!empty($args['state'])) {
        $q->eq('state', $args['state']);
    }

    // All owners unless we passed a do_lookup state
    if (!empty($args['do_lookup'])) {
        $q->eq('do_lookup', $args['do_lookup']);
    }

    $q->run();
    $items = $q->output();

    return $items;
}
