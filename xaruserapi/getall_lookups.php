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
function reminders_userapi_getall_Lookups($args)
{
    // Set up this function to get all entries and the emails AND names of gthe users
    $tables = xarDB::getTables();
    $q = new Query('SELECT');
    $q->addtable($tables['reminders_lookups'], 'lookups');
    $q->addtable($tables['reminders_emails'], 'email_1');
    $q->leftjoin('lookups.email_id_1', 'email_1.id');
    $q->addtable($tables['reminders_emails'], 'email_2');
    $q->leftjoin('lookups.email_id_2', 'email_2.id');

    // Add only these fields
    $q->addfields(
        [
                    'lookups.id',
                    'lookups.lookup AS lookup_name',
                    'lookups.email AS lookup_email',
                    'lookups.phone AS lookup_phone',
                    'email_1.name AS name_1',
                    'email_1.address AS address_1',
                    'email_2.name AS name_2',
                    'email_2.address AS address_2',
                    'message',
                    'template_id',
                  ]
    );

    // All reminders unless we passed a state
    if (!empty($args['state'])) {
        $q->eq('lookups.state', $args['state']);
    }

    // Check if a list of reminder IDs was passed
    if (!empty($args['itemids'])) {
        $entry_list = explode(',', $args['itemids']);
        $q->in('lookups.id', $entry_list);
    }

    $q->run();
    $items = $q->output();

    return $items;
}
