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
function hookbridge_privateapi_adminconfigmenu( $args ) 
{
    extract($args);

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

    foreach( $itemtype_array as $itemtype_key => $itemtype_info )
    {
        $menu[] = array(
                'title' =>  $itemtype_info['name'],
                'url'   =>  xarModURL('hookbridge','admin','config',array('itemtype'=>$itemtype_key)));
    }
    
//    $menu[$itemtype]['url'] = "";

    return $menu;

}

/*
 * END OF FILE
 */
?>
