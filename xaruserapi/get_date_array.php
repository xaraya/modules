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
 * Get an array of all email dates for a reminder, sorted by age
 *
 */
function reminders_userapi_get_date_array($args)
{
	// Support both objects and arrays
	if (!empty($args['object'])) {
		$fields = $data['object']->getFieldValues(array(), 1);
	} else {
		$fields = $args['array'];
	}
	
	// Get the time in seconds before the due date for each of the possible periods
    xarMod::load('reminders', 'admin');
    $seconds = xarMod::apiFunc('reminders', 'admin', 'get_warning_period_time', array('timeframe' => 'seconds'));

    $steps = array();
    for ($i=1;$i<=10;$i++) {
		$this_step = (int)$fields['reminder_' . $i];
		// Translate the step into a number of seconds
		$this_step = $seconds[$this_step];
		
		$this_done = (int)$fields['reminder_done_' . $i];
		$stepvalue = $this_step == 0 ? 0 : $fields['due_date'] - $this_step;
		$steps[] = array('index' => $i, 'date' => $stepvalue, 'done' => $this_done);
    }
    
    // Sort the array by steps ASC; this means by age, oldest first
	// Obtain a list of columns
	$date  = array_column($steps, 'date');
	$done = array_column($steps, 'done');
	// Sort the data with step ascending
	// Add the array as the last parameter, to sort by the common key
	array_multisort($date, SORT_ASC, $steps);
	
	$datetime = new XarDateTime();
	$datetime->setTimestamp($fields['due_date']);
	$msg1 = xarML('Due date: #(1)', $datetime->display());
	$msg2 = xarML('Reminder steps array:');
	xarLog::message($msg1, xarLog::LEVEL_INFO);
	xarLog::message($msg2, xarLog::LEVEL_INFO);
	foreach($steps as $step) {
		// Ignore slots with no chosen date
		if ($step['date'] == 0) continue;
		$datetime->setTimestamp($step['date']);
		$msg3 = xarML('#(1) #(2)', $step['index'], $datetime->display());
		xarLog::message($msg3, xarLog::LEVEL_INFO);
	}

	// Debug display
	if (xarModVars::get('reminders','debugmode') && 
	in_array(xarUser::getVar('id'),xarConfigVars::get(null, 'Site.User.DebugAdmins'))) {
		echo $msg1;
		echo "<br/>";
		echo $msg2;
		echo "<br/>";
		foreach($steps as $step) {
			// Ignore slots with no chosen date
			if ($step['date'] == 0) continue;
			$datetime->setTimestamp($step['date']);
			$msg = xarML('#(1) #(2)', $step['index'], $datetime->display());
			echo $msg;
			echo "<br/>";
        	xarLog::message($msg, xarLog::LEVEL_INFO);
		}
	}
	
	return $steps;
}
?>
