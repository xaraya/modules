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

function hookbridge_hookapi_item_transformoutput ( $args ) 
{
    extract($args);

    // Argument check
    if (!isset($extrainfo)) {
        $msg = xarML('Invalid Parameter Count in #(3), #(1)api_#(2)', 'hook', 'transformoutput', 'hookbridge');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (is_array($extrainfo)) {
        $result = array();
        if (isset($extrainfo['transform']) and is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] = hookbridge_transformoutput($extrainfo[$key]);
                }
            }
            return $extrainfo;
        }
        foreach ($extrainfo as $key => $value ) {
            $result[$key] = hookbridge_transformoutput($value);
        }
    } else {
        $result = hookbridge_transformoutput($text);
    }

    return $result;
}

function hookbridge_transformoutput( $text ) 
{
    return '[ My Hook: Change me in xarhookapi/item_transformoutput.xsl ] ' . $text;

}


/*
 * END OF FILE
 */
?>
