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
 * This function provides information to the templates which are common to all
 * pageviews.
 *
 * It provides the following informations:
 *
 *      'menu'      => Array with information about the module menu
 *      'statusmsg' => Status message if set
 */
function webdavserver_privateapi_common( $args ) 
{

    extract( $args );

    $common = array();

    $common['menu'] = array();

    // Initialize the statusmessage
    $statusmsg = xarSessionGetVar( 'webdavserver_statusmsg' );
    if ( isset( $statusmsg ) ) {
        xarSessionDelVar( 'webdavserver_statusmsg' );
        $common['statusmsg'] = $statusmsg;
    }

    

    // Initialize the title
    $common['pagetitle'] = $title;
    if ( isset( $type ) and $type == 'admin' ) {
        $common['type']      = xarML( 'webdavserver Administration' );
    }

    return array( 'common' => $common );
}

/*
 * END OF FILE
 */
?>
