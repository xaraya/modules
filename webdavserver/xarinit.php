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
 *
 */

/**
 * initialise the module.  This function is only ever called once during the
 * lifetime of a particular module instance
 */
function webdavserver_init()
{
    // Most privileges will be managed by the underlying modules, we just make distinction between
    // admining the server and using it, so we can at least divide some operations over 2 groups.
    xarRegisterMask( 'Usewebdavserver' ,'All' ,'webdavserver' ,'All' ,'All', 'ACCESS_DELETE' );
    xarRegisterMask( 'Adminwebdavserver' ,'All' ,'webdavserver' ,'All' ,'All', 'ACCESS_ADMIN' );

    // Initialisation successful
    return true;
}

/**
 * Remove the module instance from the xaraya installation.
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance.
 */
function webdavserver_delete()
{
    // Deletion successful
    return true;
}

/**
 * upgrade the module from an older version.
 * This function can be called multiple times
 */
function webdavserver_upgrade($oldversion)
{
    return true;
}

?>
