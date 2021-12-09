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
?>
