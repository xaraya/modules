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
 * This function provides information to the templates which are common to all
 * pageviews.
 *
 * It provides the following informations:
 *
 *      'menu'      => Array with information about the module menu
 *      'statusmsg' => Status message if set
 */
function hookbridge_privateapi_common( $args ) 
{
    extract( $args );

    $common = array();

    $common['menu'] = array();

    // Initialize the statusmessage
    $statusmsg = xarSessionGetVar( 'hookbridge_statusmsg' );
    if ( isset( $statusmsg ) ) {
        xarSessionDelVar( 'hookbridge_statusmsg' );
        $common['statusmsg'] = $statusmsg;
    }

    
    // Set the page title
    xarTplSetPageTitle( 'hookbridge :: ' . $title );
    

    // Initialize the title
    $common['pagetitle'] = $title;
    if ( isset( $type ) and $type == 'admin' ) {
        $common['type']      = xarML( 'Hook Bridge Administration' );
    }

    return array( 'common' => $common );
}

/*
 * END OF FILE
 */
?>
