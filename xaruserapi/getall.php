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
function reminders_userapi_getall($args)
{
    // Set up this function to get all entries and the emails AND names of gthe users
    $tables = xarDB::getTables();
    $q = new Query('SELECT');
    $q->addtable($tables['reminders_entries'], 'entries');
    $q->addtable($tables['reminders_emails'], 'email_1');
    $q->leftjoin('entries.email_id_1', 'email_1.id');
    $q->addtable($tables['reminders_emails'], 'email_2');
    $q->leftjoin('entries.email_id_2', 'email_2.id');
    
    // Add only these fields
    $q->addfields(array(
    				'entries.id',
    			  	'email_1.name AS name_1',
    			  	'email_1.address AS address_1',
    			  	'email_2.name AS name_2',
    			  	'email_2.address AS address_2',
    			  	'code',
    			  	'message',
    			  	'template_id',
    			  	'due_date',
    			  	'recurring',
    			  	'recur_period',
    			  	'reminder_warning_1 AS reminder_1',
    			  	'reminder_done_1',
    			  	'reminder_warning_2 AS reminder_2',
    			  	'reminder_done_2',
    			  	'reminder_warning_3 AS reminder_3',
    			  	'reminder_done_3',
    			  	'reminder_warning_4 AS reminder_4',
    			  	'reminder_done_4',
    			  	'reminder_warning_5 AS reminder_5',
    			  	'reminder_done_5',
    			  	'reminder_warning_6 AS reminder_6',
    			  	'reminder_done_6',
    			  	'reminder_warning_7 AS reminder_7',
    			  	'reminder_done_7',
    			  	'reminder_warning_8 AS reminder_8',
    			  	'reminder_done_8',
    			  	'reminder_warning_9 AS reminder_9',
    			  	'reminder_done_9',
    			  	'reminder_warning_10 AS reminder_10',
    			  	'reminder_done_10',
    			  )
    );
    
    // All reminders unless we passed a state
    if (!empty($args['state'])) {
    	$q->eq('entries.state', $args['state']);
    }

    // Check if a list of reminder IDs was passed
    if (isset($args['itemids'])) {
		if (!empty($args['itemids'])) {
			$entry_list = explode(',', $args['itemids']);
			$q->in('entries.id', $entry_list);
		} else {
			// If an empty list was passed, bail
			return array();
		}
	}
    
	$q->run();
    $items = $q->output();
    
    return $items;
}
?>
