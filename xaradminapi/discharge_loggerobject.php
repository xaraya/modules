<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @subpackage logconfig
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2022 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Save the values of a logger object to the configuration files
 */
function logconfig_adminapi_discharge_loggerobject($args)
{
    if (!isset($args['logger'])) die(xarML('No logger object passed'));
    
	if (!xarLog::configReadable()) die(xarML('Cannot read the configuration file'));
		
	// Load the configuration file
	require(xarLog::configFile());
		
	// This holds the values to be saved
	$to_be_saved = array();
	// Get the loggers variables and their values
	$variables = $args['logger']->getFieldValues(array(),1);

	// Remove the state: we handle that separately
	$state = $variables['state'];
	unset($variables['state']);
	// Get the type
	$type = ucwords($variables['type']);
	// Get all the fields used by our loggers
    $fields = xarMod::apiFunc('logconfig', 'admin', 'get_variables');
	
    // Run through each of the object's properties and get the values to be saved
    foreach ($variables as $variable => $value) {
		if (!isset($fields[$variable])) continue;  //die(xarML('Error getting the field associated with #(1)', $variable));
		$fieldname = $fields[$variable];
		$address = 'Log.' . $type . '.' . $fieldname;

		// If this variable exists in the configuration file, then add it to those to be saved
		if (isset($systemConfiguration[$address])) {
			$to_be_saved[$address] = $value;
		}
    }
    // Save the values to the configuration file
	xarMod::apiFunc('installer','admin','modifysystemvars', array('variables' => $to_be_saved, 'scope' => 'Log'));
	
	// Handle state property. 
	
    // Get the available loggers
	$availables = xarLog::availables();
	if ($state == 1) {
		if (in_array($variables['type'], $availables)) {
			if (($key = array_search($variables['type'], $availables)) !== false) {
				unset($availables[$key]);
			}
		}
	} elseif ($state == 3) {
		if (!in_array($variables['type'], $availables)) array_push($availables, $variables['type']);
	}
	$active_loggers['Log.Available'] = implode(',', $availables);
    // Save the values to the configuration file
	xarMod::apiFunc('installer','admin','modifysystemvars', array('variables' => $active_loggers, 'scope' => 'System'));
	return true;
}

?>