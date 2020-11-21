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
    $seconds = xarMod::apiFunc('reminders', 'admin', 'get_warning_period_time', array('timeframe' => 'seconds'));

    $steps = array();
    for ($i=1;$i<=10;$i++) {
		$this_step = $fields['reminder_' . $i];
		// Translate the step into a number of seconds
		$this_step = $seconds[$this_step];
		
		$this_done = $fields['reminder_done_' . $i];
		$steps[] = array('step' => $fields['due_date'] - $this_step, 'done' => $this_done);
    }
    
    // Sort the array by steps ASC; this means by age, oldest first
	// Obtain a list of columns
	$step  = array_column($steps, 'step');
	$done = array_column($steps, 'done');
	// Sort the data with step ascending
	// Add the array as the last parameter, to sort by the common key
	array_multisort($step, SORT_ASC, $steps);
	
	return $steps;
}
?>
