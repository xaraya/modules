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

function webdavserver_user_main() {

    // Security Check 
    // It is important to do this as early as possible to avoid potential
    // security holes or just too much wasted processing.  For the main
    // function we want to check that the user has at least edit privilege for
    // some item within this component, or else they won't be able to do
    // anything and so we refuse access altogether.  The lowest level of
    // access for administration depends on the particular module, but it is
    // generally either 'edit' or 'delete'. 
    //if (!xarSecurityCheck( 'Usewebdavserver')) return;

    $data = xarModAPIFunc(
        'webdavserver'
        ,'private'
        ,'common'
        ,array(
            'title' =>  xarML( 'WebDAV / WebShare / WebFolders access to Xaraya' )));

    return $data;
}

/*
 * END OF FILE
 */
?>
