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
    $menu[] = array(
            'title' =>  xarML( 'Config' ),
            'url'   =>  xarModURL(
                'hookbridge',
                'admin',
                'config' ));

    $menu[] = array(
            'title' =>  xarML( 'Create Hook' ),
            'url'   =>  xarModURL('hookbridge','admin','config',array('itemtype'=>1)));
    
    $menu[] = array(
            'title' =>  xarML( 'Update Hook' ),
            'url'   =>  xarModURL('hookbridge','admin','config',array('itemtype'=>2)));
    

//    $menu[$itemtype]['url'] = "";

    return $menu;

}

/*
 * END OF FILE
 */
?>
