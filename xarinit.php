<?php

/**
 * File: $Id$
 *
 * Initialisation of xmlrpcserver module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xmlrpcserver
 * @author Marcel van der Boom
*/

/**
 * initialise the xmlrpcserver module
 *
 * The intialisation of xmlrpcserver is very simple as
 * it uses no database tables yet.
 *
 */
function xmlrpcserver_init()
{
    xarRegisterMask('UseXmlRpcServer','All','xmlrpcserver','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('AdminXmlRpcServer','All','xmlrpcserver','All','All','ACCESS_READ');
    return true;
}

/**
 * upgrade the xmlrpcserver module from an old version
 * This function can be called multiple times
 */
function xmlrpcserver_upgrade($oldversion) 
{
    return true; 
}

/**
 * delete the xmlrpcserver module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xmlrpcserver_delete()
{
    // Remove Masks and Instances
    xarRemoveMasks('xmlrpcserver');
    xarRemoveInstances('xmlrpcserver');

    return true;
}

?>