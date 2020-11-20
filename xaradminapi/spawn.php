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
 
function reminders_adminapi_spawn($data)
{
# --------------------------------------------------------
#
# Strategy: 
# 1. Get the fields from the reminder passed
# 2. Adjust the fields that change in a recurring reminder
#    - due_date
#    - code
#    - reminder_done_1, reminder_done_2, ...
#    - time_created, time_modified
#    - state
#
	if (!empty($data['object'])) {
		// Get the raw values of this object
		$fields = $data['object']->getFieldValues(array(), 1);
	} else {
		$fields = $args['array'];
	}

	// Get a blank instcance of the object
	$new_entry = DataObjectMaster::getObject(array('name' => 'reminders_entries'));
	
	// 
	for ($i=1;$i<=10;$i++) unset($fields['reminder_done_' . $i]);
	$fields['code'] = xarMod::apiFunc('reminders', 'admin', 'generate_code', array('array' => $fields));
	$fields['time_created'] = time();
	$fields['time_modified'] = time();
	$fields['state'] = 3;
	
	// Calculate the new due date
	$date = new XarDateTime();
	$date->setTimestamp($fields['due_date']);	
	switch ($fields['recur_period']) {
		case 1:
			$date->addDays(1);
		break;
		case 2:
			$date->addWeeks(1);
		break;
		case 3:
			$date->addWeeks(2);
		break;
		case 4:
			$date->addMonths(1);
		break;
		case 5:
			$date->addMonths(2);
		break;
		case 6:
			$date->addMonths(3);
		break;
		case 7:
			$date->addMonths(6);
		break;
		case 8:
			$date->addYears(1);
		break;
	}
	$fields['due_date'] = $date->getTimestamp();
	
	// Create the new reminder
	$new_entry->createItem($fields);
	
	return true;
}
?>