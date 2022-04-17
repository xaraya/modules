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
 * Return the lookups
 *
 */
function reminders_userapi_getall_Lookups($args)
{
    // Set up this function to get all entries and the emails AND names of the users
    $tables = xarDB::getTables();
    $q = new Query('SELECT');
    $q->addtable($tables['reminders_lookups'], 'lookups');
    $q->addtable($tables['reminders_emails'], 'email_1');
    $q->leftjoin('lookups.email_id_1', 'email_1.id');
    $q->addtable($tables['reminders_emails'], 'email_2');
    $q->leftjoin('lookups.email_id_2', 'email_2.id');
        
    // Add only these fields
    $q->addfields(array(
    				'lookups.id AS lookup_id',
    				'lookups.name AS lookup_name',
    				'lookups.email AS lookup_email',
    				'lookups.phone AS lookup_phone',
    			  	'lookups.template_id AS template_id',
    			  	'email_1.id AS owner_id',
    			  	'email_1.name AS name',
    			  	'email_1.address AS address',
    			  	'email_1.lookup_interval as lookup_interval',
    			  	'email_1.lookup_template as lookup_template',
    			  	'email_1.subject as lookup_subject',
    			  	'email_1.message as lookup_message',
    			  	'email_2.name AS name_2',
    			  	'email_2.address AS address_2',
    			  )
    );
    
    // All lookups unless we passed a state
    if (!empty($args['state'])) {
    	$q->eq('lookups.state', $args['state']);
    }

    // Check if a list of reminder IDs was passed
    if (isset($args['itemids'])) {
		if (!empty($args['itemids'])) {
			$entry_list = explode(',', $args['itemids']);
			$q->in('lookups.id', $entry_list);
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
