<?php 

/**
 * Hook Bridge
 *
 * @copyright   by Michael Cortez
 * @license     GPL (http://www.gnu.org/licenses/gpl.html)
 * @author      Michael Cortez
 * @link        
 *
 * @package     Xaraya eXtensible Management System
 * @subpackage  Hook Bridge
 * @version     $Id$
 *
 */

/**
 * Utility function to pass individual menu items to the main menu.
 *
 * This function is invoked by the core to retrieve the items for the
 * usermenu.
 *
 * @returns array
 * @return  array containing the menulinks for the main menu items
 */

function hookbridge_hookapi_item_create ( $args ) 
{
    extract( $args );

    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)','module name', 'admin', 'item_create', 'hookbridge');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

	/*
	 * ADD YOUR CODE HERE
	 */
	 
	// Get the path to where the hookbridge functions are stored
	$hookbridge_functionpath = xarModGetVar('hookbridge', 'HookBridge_FunctionPath' );
	 
	// Get the list of active hookbridge functions for the Create Hook
	$hookfunctions_create = unserialize(xarModGetVar('hookbridge', 'hookfunctions_create' ));
	
	// Loop through'em
	foreach( $hookfunctions_create as $bridgefunctionfile )
	{
		$includeFile = $hookbridge_functionpath.'/'.$bridgefunctionfile;
		
		$functionName = 'hookbridge_'.str_replace(".php","",$bridgefunctionfile);
		include_once($includeFile);
		
		$extrainfo = call_user_func($functionName, $modname, $modid, $extrainfo);
	}
	
	
	return $extrainfo;
}

/*
 * END OF FILE
 */
?>