<?php

/**
 * File: $Id$
 *
 * Initialisation of bloggerapi module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage bloggerapi
 * @author Marcel van der Boom <marcel@xarara.com>
*/

/**
 * initialise the bloggerapi module
 *
 * The intialisation of bloggerapi is very simple as
 * it uses no database tables yet.
 *
 */
function bloggerapi_init() 
{

    // Publication type for blogger api 0 = no pubtype
    xarModSetVar('bloggerapi','bloggerpubtype','0');
    xarRegisterMask('AdminBloggerAPI','All','bloggerapi','All','All','ACCESS_ADMIN');

    return true; }

/**
 * upgrade the bloggerapi module from an old version
 * This function can be called multiple times
 */
function bloggerapi_upgrade($oldversion) 
{ 
    return true; 
}

/**
 * delete the bloggerapi module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function bloggerapi_delete()
{

    xarModDelVar('bloggerapi','bloggerpubtype');

    // Remove Masks and Instances
    xarRemoveMasks('bloggerapi');
    xarRemoveInstances('bloggerapi');

    return true;
}

?>