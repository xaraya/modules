<?php
/**
 * Chat Module - Port of PJIRC for Xaraya
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Chat Module
 * @link http://xaraya.com/index.php/release/158.html
 * @author John Cox
 */
/**
 * initialise the chat module
 * @return bool
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
 * @return bool
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