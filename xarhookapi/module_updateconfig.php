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
 * Helper function to invoke registered functions for Create Hook
 *
 * @returns array
 * @return  array containing possibly modified $extrainfo
 */

function hookbridge_hookapi_module_updateconfig ( $args ) 
{
    extract( $args );

    if (!isset($extrainfo)) {
        $extrainfo = array();
    }


    // Check to see if the site admin has enabled hookbridge for this type of hook
    $hookenabled_updateconfig = xarModGetVar('hookbridge', 'hookenabled_updateconfig' );
    if( !$hookenabled_updateconfig )
    {
        return $extrainfo;
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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)','module name', 'admin', 'module_updateconfig', 'hookbridge');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    /*
     * ADD YOUR CODE HERE
     */

    return $args['extrainfo'];
}

/*
 * END OF FILE
 */
?>
