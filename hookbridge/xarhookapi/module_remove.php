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

function hookbridge_hookapi_module_remove ( $args ) 
{
    extract($args);

    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    // When called via hooks, we should get the real module name from objectid
    // here, because the current module is probably going to be 'modules' !!!
    if (!isset($objectid) || !is_string($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID (= module name)', 'hook', 'module_remove', 'hookbridge');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    $modid = xarModGetIDFromName($objectid);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module ID', 'hook', 'module_remove', 'hookbridge');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    /*
     * ADD YOUR CODE HERE
     */

    // Return the extra info
    return $extrainfo;
}

/*
 * END OF FILE
 */
?>
