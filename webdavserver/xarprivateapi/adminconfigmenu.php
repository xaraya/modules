<?php 

/**
 * webdavserver
 *
 * @copyright   by Marcel van der Boom
 * @license     GPL (http://www.gnu.org/licenses/gpl.html)
 * @author      Marcel van der Boom
 * @link        
 *
 * @package     Xaraya eXtensible Management System
 * @subpackage  webdavserver
 * @version     $Id$
 *
 */

/**
 * Create a little submenu for the configuration screen.
 */
function webdavserver_privateapi_adminconfigmenu( $itemtype ) {

    /*
     * Build the configuration submenu
     */
    $menu = array();
    $menu[0] = array(
            'title' =>  xarML( 'Config' ),
            'url'   =>  xarModURL(
                'webdavserver',
                'admin',
                'config' ));

    

    $menu[$itemtype]['url'] = "";

    return $menu;

}

/*
 * END OF FILE
 */
?>
