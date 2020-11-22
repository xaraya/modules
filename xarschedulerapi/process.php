<?php
/**
 * Reminders Module
 *
 * @package modules
 * @subpackage reminders
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2020 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Process the active reminders
 *
 */

function reminders_schedulerapi_process($args)
{
    $results = xarMod::apiFunc('reminders', 'admin', 'process');

    // Tell the scheduler that the job ran, but nothing was sent
    if (empty($results)) $results = xarML('No reminders sent');
    
    // Make the result human readable for the scheduler
    if (is_array($results)) {
		$readable_result = '';
		foreach ($results as $result) {
			foreach ($result as $key => $value) $readable_result .= $key . ": " . $value . " ";
			$readable_result = trim($readable_result) . "<br/>";
		}
		// Remove the final <br/>
		$results = substr($readable_result, 0, -5);
    }
    return $results;
}

?>