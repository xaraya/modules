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
 * Create a little submenu for the configuration screen.
 */
function hookbridge_privateapi_adminconfigmenu( $itemtype ) 
{
    /*
     * Build the configuration submenu
     */
    $menu = array();
    $menu[0] = array(
            'title' =>  xarML( 'Config' ),
            'url'   =>  xarModURL(
                'hookbridge',
                'admin',
                'config' ));

    

    $menu[$itemtype]['url'] = "";

    return $menu;

}

/*
 * END OF FILE
 */
?>
