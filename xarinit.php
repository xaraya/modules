<?php
/**
 * File: $Id$
 *
 * Xaraya Chat
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 * @subpackage Chat Module
 * @author John Cox
 */

/**
 * initialise the chat module
 */
function chat_init()
{
    // Set up module variables
    xarModSetVar('chat', 'server', 'irc.xaraya.com');
    xarModSetVar('chat', 'port', 6667);
    xarModSetVar('chat', 'channel', '#support');

    xarRegisterMask('ReadChat', 'All', 'chat', 'All', 'All', 'ACCESS_READ');
    xarRegisterMask('AdminChat', 'All', 'chat', 'All', 'All', 'ACCESS_ADMIN');
    // Initialisation successful
    return true;
}

/**
 * delete the chat module
 */
function chat_delete()
{
    // Delete any module variables
    xarModDelAllVars('chat');
    xarRemoveMasks('chat');
    xarRemoveInstances('chat');
    // Deletion successful
    return true;
}
?>