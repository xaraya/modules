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
 * Get the saved values and insert them into an object
 */
function logconfig_adminapi_charge_loggerobject($args)
{
	if (!xarLog::configReadable()) die(xarML('Cannot read the configuration file'));
    if (!isset($args['logger'])) die(xarML('No logger object passed'));
    
	// Get the $systemConfiguration array from the configuration file
	require(xarLog::configFile());
	
    // Get the available loggers
	$availables = xarLog::availables();
    
	// Get all the fields used by our loggers
	$fields = xarMod::apiFunc('logconfig', 'admin', 'get_variables');

    // Get the type of this logger
    $loggertype = $args['logger']->properties['type']->value;

    // Get this logger's property names
    $propertynames = array_keys($args['logger']->properties);
    
    // Run through each of the logger object's properties and get the saved value
    $values = array();
    foreach ($propertynames as $propertyname) {

		if ($propertyname == 'state') {
			// The state is different. It corresponds to whether or not the logger is in the list of available loggers
			if (in_array($loggertype, $availables)) $value = 3;
			else $value = 1;
		} else  {
			$type = ucwords($loggertype);
			if (!isset($fields[$propertyname])) continue;
			$variable = $fields[$propertyname];
			
			// The full address of how variables are stored in the $systemConfiguration array, e.g. Log.Simple.Filename
			$address = 'Log.' . $type . '.' . $variable;
			
			if (isset($systemConfiguration[$address])) {
				$value = $systemConfiguration[$address];
			} else {
				$value = null;
			}
		}
		// If the value != null, add it to the values to be loaded into the logger object
    	if (null != $value) $values[$propertyname] = $value;
    }
    // Load the values into the logger object
    $args['logger']->setFieldValues($values, 1);
    return $args['logger'];
}

?>