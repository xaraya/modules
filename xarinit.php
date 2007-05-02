<?php

/**
 * File: $Id$
 *
 * Initialisation of xmlrpcsystemapi module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xmlrpcsystemapi
 * @author Marcel van der Boom <marcel@xarara.com>
*/

/**
 * initialise the xmlrpcsystemapi module
 *
 * The intialisation of bloggerapi is very simple as
 * it uses no database tables yet.
 *
 */
function xmlrpcsystemapi_init() 
{ 
    return true; 
}

/**
 * upgrade the xmlrpcsystemapi module from an old version
 * This function can be called multiple times
 */
function xmlrpcsystemapi_upgrade($oldversion) 
{ 
    return true; 
}

/**
 * delete the xmlrpcsystemapi module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xmlrpcsystemapi_delete() 
{ 
    return true; 
}

?>