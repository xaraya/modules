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
    $itemtypes[1]['name'] = xarML( 'Create Hook' );
    $itemtypes[1]['var']  = 'create';
    $itemtypes[2]['name'] = xarML( 'Update Hook' );
    $itemtypes[2]['var']  = 'update';
    $itemtypes[3]['name'] = xarML( 'Delete Hook' );
    $itemtypes[3]['var']  = 'delete';
    $itemtypes[4]['name'] = xarML( 'Transform Input Hook' );
    $itemtypes[4]['var']  = 'transforminput';
    $itemtypes[5]['name'] = xarML( 'Transform Output Hook' );
    $itemtypes[5]['var']  = 'transformoutput';
    $itemtypes[6]['name'] = xarML( 'GUI Display Hook' );
    $itemtypes[6]['var']  = 'display';
    $itemtypes[7]['name'] = xarML( 'GUI Modify Hook' );
    $itemtypes[7]['var']  = 'modify';
    $itemtypes[8]['name'] = xarML( 'GUI New Hook' );
    $itemtypes[8]['var']  = 'new';
    $itemtypes[9]['name'] = xarML( 'Module Update Config Hook' );
    $itemtypes[9]['var']  = 'updateconfig';
    $itemtypes[10]['name'] = xarML( 'Module Remove Hook' );
    $itemtypes[10]['var']  = 'remove';
    $itemtypes[11]['name'] = xarML( 'Module GUI Modify Config Hook' );
    $itemtypes[11]['var']  = 'modifyconfig';
    $common['itemtype_array'] = $itemtypes;

    return array( 'common' => $common );
}

/*
 * END OF FILE
 */
?>
