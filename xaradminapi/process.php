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
    if (!isset($args['test']))        $args['test'] = false;
    if (!isset($args['params']))      $args['params'] = array();
    if (!isset($args['copy_emails'])) $args['copy_emails'] = false;

    sys::import('modules.dynamicdata.class.objects.master');
    $entries = DataObjectMaster::getObjectList(array('name' => 'reminders_entries'));
    $mailer_template = DataObjectMaster::getObject(array('name' => 'mailer_mails'));
    
    // Set all the relevant properties active here
    foreach($entries->properties as $name => $property) {
        if ($property->getDisplayStatus() == DataPropertyMaster::DD_DISPLAYSTATE_DISABLED) continue;
        $entries->properties[$name]->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE);
    }
    $entries->setFieldList();
    
    $q = $entries->dataquery;
    // Only active reminders
    $q->eq('entries.state', 3);
    $items = $entries->getItems();

    /*
    sys::import('xaraya.structures.query');
    $tables = xarDB::getTables();
    $q = new Query('SELECT');
    $q->addtable($tables['reminders_entries'], 'entries');
    $q->addtable($tables['reminders_emails'],  'emails');
    $q->join('entries.email_id', 'emails.id');
    $q->run();
    $result = $q->output();
*/    
    // Run through the active reminders and send emails
    $current_id = 0;
    $previous_id = 0;
    $templates = array();
    $data['results'] = array();
    foreach ($items as $key => $row) {
        $current_id = $row['id'];
        if ($current_id != $previous_id) {
            $found = false;
            for ($i=1;$i<=10;$i++) {
                $this_id = 'reminder_' . $i;
                $this_done_id = 'reminder_done_' . $i;
                $this_reminder = $row[$this_id];
                $this_reminder_done = $row[$this_done_id];
                // If we already sent an email for this date, then move on
                if ($this_reminder_done) continue;
                // If the reminder period is 0, then consider it done and move on
                if (empty($this_reminder)) {
                    $items[$key][$this_reminder_done] = 1;
                    continue;
                }
            }
        }
        // Get the template information for this message
        $this_template_id = $row['template'];
        if (isset($templates[$this_template_id])) {
            // We already have the information.
        } else {
            // Get the information
            $mailer_template->getItem(array('itemid' => $this_template_id));
            $values = $mailer_template->getFieldValues();
            $templates[$this_template_id]['message_id']   = $values['id'];
            $templates[$this_template_id]['message_body'] = $values['body'];
            $templates[$this_template_id]['subject']      = $values['subject'];
        }
        // Assemble the parameters for the email
        $params['message_id']   = $templates[$this_template_id]['message_id'];
        $params['message_body'] = $templates[$this_template_id]['message_body'];
        $params['subject']      = $templates[$this_template_id]['subject'];
        // Send the email
        $data['result'] = xarMod::apiFunc('reminders', 'admin', 'send_email', array('info' => $row, 'params' => $params, 'copy_emails' => $args['copy_emails'], 'test' => $args['test']));        	
        $data['results'] = array_merge($data['results'], array($data['result']));
        $previous_id = $current_id;
    }
    
    return true;
}
?>