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


function hookbridge_hookapi_item_delete ( $args )
{
    extract( $args );

    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    // Check to see if the site admin has enabled hookbridge for this type of hook
    $hookenabled_delete = xarModGetVar('hookbridge', 'hookenabled_delete' );
    if( !$hookenabled_delete )
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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)','module name', 'admin', 'item_delete', 'hookbridge');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    /*
     * ADD YOUR CODE HERE
     */

    // Get the list of active hookbridge functions for the Create Hook
    $hookfunctions_delete = unserialize(xarModGetVar('hookbridge', 'hookfunctions_delete' ));

    if( isset($hookfunctions_delete) && (count($hookfunctions_delete) > 0) )
    {
        // Get the path to where the hookbridge functions are stored
        $hookbridge_functionpath = xarModGetVar('hookbridge', 'HookBridge_FunctionPath' );

        // Loop through'em
        foreach( $hookfunctions_delete as $bridgefunctionfile )
        {
            $includeFile = $hookbridge_functionpath.'/'.$bridgefunctionfile;

            $functionName = 'hookbridge_'.str_replace(".php","",$bridgefunctionfile);
            include_once($includeFile);

            $extrainfo = call_user_func($functionName, $modname, $modid, $extrainfo);
        }
    }

    return $extrainfo;
}

/*
 * END OF FILE
 */
?>
