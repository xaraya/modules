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

    // Item Types Array
    $itemtypes[1] = xarML( 'Create Hook' );
    $itemtypes[2] = xarML( 'Update Hook' );
    $itemtypes[3] = xarML( 'Delete Hook' );
    $itemtypes[4] = xarML( 'Transform Input Hook' );
    $itemtypes[5] = xarML( 'Transform Output Hook' );
    $itemtypes[6] = xarML( 'GUI Display Hook' );
    $itemtypes[7] = xarML( 'GUI Modify Hook' );
    $itemtypes[8] = xarML( 'GUI New Hook' );
    $itemtypes[9] = xarML( 'Module Update Config Hook' );
    $itemtypes[10] = xarML( 'Module Remove Hook' );
    $itemtypes[11] = xarML( 'Module GUI Modify Config Hook' );
    $common['itemtype_array'] = $itemtypes;

    return array( 'common' => $common );
}

/*
 * END OF FILE
 */
?>
