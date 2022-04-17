<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 * @author Ryan Walker
 */
/**
 * Initialise the messages module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */

sys::import('xaraya.structures.query');

function messages_init()
{
    $q = new Query();
    $prefix = xarDB::getPrefix();

    # --------------------------------------------------------
#
    # Table structure for table messages
#

    $query = "DROP TABLE IF EXISTS " . $prefix . "_messages";
    if (!$q->run($query)) {
        return;
    }
    $query = "CREATE TABLE " . $prefix . "_messages (
      id                integer unsigned NOT NULL auto_increment,
      from_id           integer unsigned NOT NULL default 0,
      to_id             integer unsigned NOT NULL default 0,
      time              integer unsigned NOT NULL default 0,
      from_status       tinyint unsigned NOT NULL default 0,
      to_status         tinyint unsigned NOT NULL default 0,
      from_delete       tinyint unsigned NOT NULL default 0,
      to_delete         tinyint unsigned NOT NULL default 0,
      anonpost          tinyint unsigned NOT NULL default 0,
      replyto           integer unsigned NOT NULL default 0,
      subject           varchar(254) default NULL,
      body              text,
      state             tinyint unsigned NOT NULL default 3,
      PRIMARY KEY  (id),
      KEY `messages_from_id` (`from_id`)
    )";
    if (!$q->run($query)) {
        return;
    }

    # --------------------------------------------------------
    #
    # Create DD objects
    #
    $module = 'messages';
    $objects = [
                    'messages_user_settings',
                    'messages_module_settings',
                    'messages_messages',
                     ];

    if (!xarMod::apiFunc('modules', 'admin', 'standardinstall', ['module' => $module, 'objects' => $objects])) {
        return;
    }

    xarModVars::set('messages', 'sendemail', false); // Note the 'e' in 'sendemail'
    xarModVars::set('messages', 'allowautoreply', true);
    xarModVars::set('messages', 'allowanonymous', false);
    xarModVars::set('messages', 'allowedsendmessages', serialize([]));
    xarModVars::set('messages', 'strip_tags', true);
    xarModVars::set('messages', 'send_redirect', 1);
    xarModVars::set('messages', 'allowusersendredirect', false);

    // not sure if the following are needed?
    xarModVars::set('messages', 'user_sendemail', true); // Note the 'e' in 'user_sendemail'
    xarModVars::set('messages', 'enable_autoreply', false);
    xarModVars::set('messages', 'autoreply', '');
    xarModVars::set('messages', 'user_send_redirect', 1);

    //xarModVars::set('messages', 'buddylist', 0);
    //xarModVars::set('messages', 'limitsaved', 12);
    //xarModVars::set('messages', 'limitout', 10);
    //xarModVars::set('messages', 'limitinbox', 10);
    //xarModVars::set('messages', 'smilies', false);
    //xarModVars::set('messages', 'allow_html', false);
    //xarModVars::set('messages', 'allow_bbcode', false);
    //xarModVars::set('messages', 'mailsubject', 'You have a new private message !');
    //xarModVars::set('messages', 'fromname', 'Webmaster');
    //xarModVars::set('messages', 'from', 'Webmaster@YourSite.com');
    //xarModVars::set('messages', 'inboxurl', 'http://www.yoursite.com/index.php?module=messages&type=user&func=display');
    //xarModVars::set('messages', 'serverpath', '/home/yourdir/public_html/modules/messages');
    //xarModVars::set('messages', 'away_message', '');

    # --------------------------------------------------------
    #
    # Set up configuration modvars (general)
    #

    $module_settings = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'messages']);
    $module_settings->initialize();


    /*
     * REGISTER BLOCKS
     */

    if (!xarMod::apiFunc(
        'blocks',
        'admin',
        'register_block_type',
        ['modName'  => 'messages',
                             'blockType'=> 'newmessages', ]
    )) {
        return;
    }
    /*
     * REGISTER HOOKS
     */

    // Hook into the roles module (Your Account page)
    xarMod::apiFunc(
        'modules',
        'admin',
        'enablehooks',
        [
            'hookModName'       => 'roles','callerModName'    => 'messages', ]
    );

    /*
         // Hook into the Dynamic Data module
        xarMod::apiFunc(
            'modules'
            ,'admin'
            ,'enablehooks'
            ,array(
                'hookModName'       => 'dynamicdata'
                ,'callerModName'    => 'messages'));

        $objectid = xarMod::apiFunc('dynamicdata','util','import',
                                  array('file' => 'modules/messages/messages.data.xml'));
        if (empty($objectid)) return;
        // save the object id for later
        xarModVars::set('messages','objectid',$objectid);
    */

    # --------------------------------------------------------
#
    # Create privilege instances
#

    xarPrivileges::defineInstance('messages', 'Block', []);
    xarPrivileges::defineInstance('messages', 'Item', []);

    /*
     * REGISTER MASKS
     */

    // Register Block types (this *should* happen at activation/deactivation)
    //xarBlockTypeRegister('messages', 'incomming');
    xarMasks::register('ReadMessagesBlock', 'All', 'messages', 'Block', 'All', 'ACCESS_OVERVIEW');
    xarMasks::register('ViewMessages', 'All', 'messages', 'Item', 'All', 'ACCESS_OVERVIEW');
    xarMasks::register('ReadMessages', 'All', 'messages', 'Item', 'All', 'ACCESS_READ');
    xarMasks::register('EditMessages', 'All', 'messages', 'Item', 'All', 'ACCESS_EDIT');
    xarMasks::register('AddMessages', 'All', 'messages', 'Item', 'All', 'ACCESS_ADD');
    xarMasks::register('DenyReadMessages', 'All', 'messages', 'Item', 'All', 'ACCESS_NONE');
    xarMasks::register('ManageMessages', 'All', 'messages', 'Item', 'All', 'ACCESS_DELETE');
    xarMasks::register('AdminMessages', 'All', 'messages', 'Item', 'All', 'ACCESS_ADMIN');
    /*********************************************************************
    * Enter some default privileges
    * Format is
    * register(Name,Realm,Module,Component,Instance,Level,Description)
    *********************************************************************/
    xarPrivileges::register('ManageMessages', 'All', 'messages', 'All', 'All', 'ACCESS_DELETE', xarML('Delete access to messages'));
    xarPrivileges::register('DenyReadMessages', 'All', 'messages', 'All', 'All', 'ACCESS_NONE', xarML('Deny access to messages'));
    /*********************************************************************
    * Assign the default privileges to groups/users
    * Format is
    * assign(Privilege,Role)
    *********************************************************************/

    xarPrivileges::assign('ManageMessages', 'Users');
    xarPrivileges::assign('DenyReadMessages', 'Everybody');

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
    switch ($oldversion) {
        case '0.5':
            break;
        case '1.0':
            // Code to upgrade from version 1.0 goes here
            break;
        case '1.8':
        case '1.8.0':
            // compatability upgrade
            xarModVars::set('messages', 'away_message', '');
            // no break
        case '1.8.1':
            // nothing to do for this rev
            break;
        case '1.9':
        case '1.9.0':

            xarMod::apiFunc('dynamicdata', 'util', 'import', [
                        'file' => sys::code() . 'modules/messages/xardata/messages_module_settings-def.xml',
                        'overwrite' => true,
                        ]);

            // new module vars
            xarModVars::set('messages', 'allowautoreply', true);
            xarModVars::set('messages', 'send_redirect', true);
            xarModVars::set('messages', 'allowusersendredirect', false);

            xarMod::apiFunc('dynamicdata', 'util', 'import', [
                        'file' => sys::code() . 'modules/messages/xardata/messages_user_settings-def.xml',
                        'overwrite' => true,
                        ]);

            // new user vars
            xarModVars::set('messages', 'user_send_redirect', 1);

            break;
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
     * UNREGISTER BLOCKS
     */

    if (!xarMod::apiFunc(
        'blocks',
        'admin',
        'unregister_block_type',
        ['modName'  => 'messages',
                             'blockType'=> 'newmessages', ]
    )) {
        return;
    }

    //	xarPrivileges::removeModule('messages');

    return xarMod::apiFunc('modules', 'admin', 'standarddeinstall', ['module' => 'messages']);
}
