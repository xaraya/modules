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
 * Show a overview of all available administration options.
 *
 * This is the main page if the admin 'Disabled Module Overview' in
 * 'adminpanels - configurations - configure overview'.
 */

function webdavserver_admin_view($args) {

    list( $itemtype ) = xarVarCleanFromInput('itemtype' );

    switch( $itemtype ) {
    

        default:
            return
                $data = xarModAPIFunc(
                    'webdavserver'
                    ,'private'
                    ,'common'
                    ,array(
                        'title' => xarML( 'Main Page' )
                        ,'type' => 'admin'
                        ));
    }

    return xarTplModule(
        'webdavserver'
        ,'admin'
        ,'view'
        ,$data
        ,$itemtype_name );
}



/*
 * END OF FILE
 */
?>
