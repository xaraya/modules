<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: XarayaGeek
// Purpose of file:  Table information for example module
// ----------------------------------------------------------------------

/**
 * initialise the messages module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function messages_init()
{
    xarModSetVar('messages', 'buddylist', 0);
    xarModSetVar('messages', 'itemsperpage', 10);
    xarModSetVar('messages', 'limitsaved', 12);
    xarModSetVar('messages', 'limitout', 10);
    xarModSetVar('messages', 'limitinbox', 10);
    xarModSetVar('messages', 'mailsubject', 'You have a new private message !');
    xarModSetVar('messages', 'fromname', 'Webmaster');
    xarModSetVar('messages', 'from', 'Webmaster@YourSite.com');
    xarModSetVar('messages', 'inboxurl', 'http://www.yoursite.com/index.php?module=messages&type=user&func=view');
    xarModSetVar('messages', 'serverpath', '/home/yourdir/public_html/modules/messages');
    xarModSetVar('messages', 'SupportShortURLs', 0 );

    // read_messages is intended only for users
    // it will store the message id of each message that
    // the user has seen
    xarModSetVar('messages', 'read_messages', serialize(array()));

    /*
     * REGISTER BLOCKS
     */

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'messages',
                             'blockType'=> 'newmessages'))) return;
    /*
     * REGISTER HOOKS
     */

    // Hook into the roles module (Your Account page)
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'roles'
            ,'callerModName'    => 'messages'));
/*
     // Hook into the Dynamic Data module
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'dynamicdata'
            ,'callerModName'    => 'messages'));



    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/messages/messages.data.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('messages','objectid',$objectid);
*/

    /*
     * REGISTER MASKS
     */

    // Register Block types (this *should* happen at activation/deactivation)
    //xarBlockTypeRegister('messages', 'incomming');
    xarRegisterMask('ReadMessagesBlock','All','messages','Block','All','ACCESS_OVERVIEW');
    xarRegisterMask('ViewMessages','All','messages','Item','All:All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadMessages','All','messages','Item','All:All:All','ACCESS_READ');
    xarRegisterMask('EditMessages','All','messages','Item','All:All:All','ACCESS_EDIT');
    xarRegisterMask('AddMessages','All','messages','Item','All:All:All','ACCESS_ADD');
    xarRegisterMask('DenyReadMessages','All','messages','Item','All:All:All','ACCESS_NONE');
    xarRegisterMask('DeleteMessages','All','messages','Item','All:All:All','ACCESS_DELETE');
    xarRegisterMask('AdminMessages','All','messages','Item','All:All:All','ACCESS_ADMIN');
    /*********************************************************************
    * Enter some default privileges
    * Format is
    * register(Name,Realm,Module,Component,Instance,Level,Description)
    *********************************************************************/
    xarRegisterPrivilege('AddMessages','All','messages','All','All','ACCESS_ADD',xarML('Add access to messages'));
    xarRegisterPrivilege('DenyReadMessages','All','messages','All','All','ACCESS_NONE',xarML('Deny access to messages'));
    /*********************************************************************
    * Assign the default privileges to groups/users
    * Format is
    * assign(Privilege,Role)
    *********************************************************************/

    xarAssignPrivilege('AddMessages','Users');
    xarAssignPrivilege('DenyReadMessages','Everybody');

    // Initialisation successful
    return true;
}

/**
 * upgrade the messages module from an old version
 * This function can be called multiple times
 */
function messages_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.5':
            break;
        case '1.0':
            // Code to upgrade from version 1.0 goes here
            break;
        case '1.8':
            // compatability upgrade
        case '2.0.0':
            // Code to upgrade from version 2.0 goes here
            break;
    }

    // Update successful
    return true;
}

/**
 * delete the messages module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function messages_delete()
{
    /*
     * REMOVE MODULE VARS
     */
    if ( !xarModDelAllVars( 'messages' ) )
        return;

    /*
     * REMOVE all messages (which are stored via the comments api)
     */
    xarModAPIFunc('comments',
                  'admin',
                  'delete_module_nodes',
                   array('modid' => xarModGetIDFromName('messages')));
    /*
     * UNREGISTER BLOCKS
     */

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'messages',
                             'blockType'=> 'newmessages'))) return;
    /*
     * REMOVE MASKS AND INSTANCES
     */
    xarRemoveMasks( 'messages' );
    xarRemoveInstances( 'messages' );

    // Deletion successful
    return true;
}

?>
