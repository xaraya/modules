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


function reminders_adminapi_generate_random_entry($args)
{
    // We need a user in order to filter for just one set of lookups
    if (empty($args['user'])) die(xarML('No user passed'));
    
    // We will disregard recipients of recent emails
    if (empty($args['recent_lookups'])) {
    	$recent_lookups = array();
    } else {
    	$recent_lookups = unserialize($args['recent_lookups']);
    }
    
    // Get the number entries for this user in the lookup table
    $tables =& xarDB::getTables();
    sys::import('xaraya.structures.query');
    $q = new Query('SELECT', $tables['reminders_lookups']);
    $q->addfield('max(id) AS highest');
    // Only get lookups of this user
    $q->eq('owner', $args['user']);
    
//    $q->qecho();

    $q->run();
    $result = $q->row();
    
    // Generate a random id
    $random_id = rand(1, max((int)$result['highest'], 1));

    // Remove fields and conditions
    $q->clearfields();
    $q->clearconditions();
    
    // Only get lookups of this user
    $q->eq('owner', $args['user']);
    // Ignore lookups we contacted recently
    if (!empty($recent_lookups)) $q->notin('id', $recent_lookups);

    $q->setstartat($random_id);
    $q->setrowstodo(100);
    
    $q->qecho();

    // Get one row
    $q->run();
    $result = $q->row();

    return $result;
}
?>