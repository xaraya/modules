<?php

/**
 * File: $Id$
 *
 * Initialisation of xmlrpcvalidatorapi module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xmlrpcvalidatorapi
 * @author Marcel van der Boom <marcel@xarara.com>
*/

/**
 * initialise the xmlrpcvalidatorapi module
 *
 * The intialisation of bloggerapi is very simple as
 * it uses no database tables yet.
 *
 */
function xmlrpcvalidatorapi_init() { 
    // The xmlrpcvalidatorapii needs xmlrpcserver
    if(!xarModIsAvailable('xmlrpcserver')) {
        $msg=xarML('The xmlrpcserver module should be activated first');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'MODULE_DEPENDENCY',$msg);
        return;
    }

    return true; }

/**
 * upgrade the xmlrpcvalidatorapi module from an old version
 * This function can be called multiple times
 */
function xmlrpcvalidatorapi_upgrade($oldversion) { return true; }

/**
 * delete the xmlrpcvalidatorapi module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xmlrpcvalidatorapi_delete() { 
    return true; 
}

?>