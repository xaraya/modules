<?php

/**
 * File: $Id$
 * 
 * Wiki
 * 
 * @package Wiki
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * @subpackage Initialisation functions for Wiki encoding
 * @author Jim McDonald 
 * @todo check if these functions are used, what are they for?
 */

/**
 * Initialise the wiki module
 * 
 * @author Jim McDonald 
 * @access public 
 * @param none $ 
 * @return true on success or void or false on failure
 * @throws 'DATABASE_ERROR'
 * @todo nothing
 */
function wiki_init()
{ 
    // Set up module variables
    // these are the authorised links.
    xarModSetVar('wiki', 'AllowedProtocols', 'http|https|mailto|ftp|news|gopher'); 
    // an image may be one of these.
    xarModSetVar('wiki', 'InlineImages', 'png|jpg|gif'); 
    // if a link is http://something, it can be directed in a new window, or in the same one.
    xarModSetVar('wiki', 'ExtlinkNewWindow', true);
    xarModSetVar('wiki', 'IntlinkNewWindow', false); 
    // dont touch this one.
    xarModSetVar('wiki', 'FieldSeparator', "\263"); 
    // Set up module hooks
    if (!xarModRegisterHook('item',
            'transform',
            'API',
            'wiki',
            'user',
            'transform')) return; 
    // Initialisation successful
    return true;
} 

/**
 * Upgrade the wiki module from an old version
 * 
 * @author Jim McDonald 
 * @access public 
 * @param  $oldVersion 
 * @return true on success or false on failure
 * @throws no exceptions
 * @todo nothing
 */
function wiki_upgrade($oldversion)
{
    switch($oldversion){
        case '1.0':
            break;
    }
    return true;
} 

/**
 * Delete the wiki module
 * 
 * @author Jim McDonald 
 * @access public 
 * @param no $ parameters
 * @return true on success or false on failure
 * @todo restore the default behaviour prior to 1.0 release
 */
function wiki_delete()
{ 
    // Remove module hooks
    if (!xarModUnregisterHook('item',
            'transform',
            'API',
            'wiki',
            'user',
            'transform')) return; 
    // Remove module variables
    xarModDelVar('wiki', 'FieldSeparator');
    xarModDelVar('wiki', 'IntlinkNewWindow');
    xarModDelVar('wiki', 'ExtlinkNewWindow');
    xarModDelVar('wiki', 'AllowedProtocols');
    xarModDelVar('wiki', 'InlineImages'); 
    // Deletion successful
    return true;
} 

?>