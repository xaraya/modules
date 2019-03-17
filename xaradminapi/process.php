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
    
    // Check if we get a copy of the email(s)
    $checkbox = DataPropertyMaster::getProperty(array('name' => 'checkbox'));
    $checkbox->checkInput('copy_emails');
    $bccaddress = $checkbox->value ? array(xarUser::getVar('email')) : array();
    
    //Assemble the parameters for the email
    $params['message_id'] = $data['message_id'];
    $params['message_body'] = $data['message_body'];
    $params['subject'] = $data['subject'];

    $data['results'] = array();
    // Run through the active reminders and send emails
    foreach ($result as $row) {
        $data['result'] = xarMod::apiFunc('reminders', 'admin', 'send_email', array('row' => $row, 'params' => $params, 'copy_emails' => $bccaddress, 'test' => $data['test']));        	
        $data['results'] = array_merge($data['results'], array($data['result']));
    }
    
    echo("Dorky");
    return true;
}
?>