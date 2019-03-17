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


function reminders_adminapi_process($args)
{
    if (!isset($args['test'])) $args['test'] = false;

    sys::import('xaraya.structures.query');
    $tables = xarDB::getTables();
    $q = new Query('SELECT');
    $q->addtable($tables['reminders_entries'], 'entries');
    $q->addtable($tables['reminders_emails'],  'emails');
    $q->join('entries.email_id', 'emails.id');
    
    // Only active reminders
    $q->eq('entries.state', 3);
    
    $q->run();
    $result = $q->output();
    
    foreach ($result as $row) {
        if ($args['test']) {
            // If we are testing, then send to this user
            $recipientname    = xarUser::getVar('name');
            $recipientaddress = xarUser::getVar('email');
            $bccaddress = array();
        } else {
            // If we are not testing, then send to the chosen participant
            $recipientname    = $result['name'];
            $recipientaddress = $result['address'];
        }
    }
    
    echo("Dorky");
    return true;
}
?>