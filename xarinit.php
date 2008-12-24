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
 */
/**
 * initialise the messages module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function messages_init()
{
    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $sql = "DROP TABLE IF EXISTS " . $xartable['messages'];

    $result =& $dbconn->Execute($sql);
    if (!$result)
        return;

    //Psspl:Added the code for anonpost_to field.
    $fields = array(
        'id'                     => array('type'=> 'integer', 'unsigned'=>true, 'null'=>false, 'increment'=>true, 'primary_key'=>true),     
        'pid'                    => array('type'=>'integer', 'unsigned'=>true, 'null'=>FALSE),
        'date'                   => array('type'=>'integer', 'unsigned'=>true, 'null'=>FALSE),
        'author'                 => array('type'=>'integer', 'unsigned'=>true, 'null'=>FALSE, 'size'=>'medium', 'default'=>5),
        'recipient'              => array('type'=>'integer', 'unsigned'=>true, 'null'=>FALSE, 'size'=>'medium', 'default'=>5),
        'left_id'                => array('type'=>'integer', 'unsigned'=>true, 'null'=>FALSE, 'default'=>1),
        'right_id'               => array('type'=>'integer', 'unsigned'=>true, 'null'=>FALSE, 'default'=>1),
        'author_status'          => array('type'=>'integer', 'null'=>FALSE, 'size'=>'tiny'),
        'recipient_status'       => array('type'=>'integer', 'null'=>FALSE, 'size'=>'tiny'),
        'author_delete'          => array('type'=>'integer', 'null'=>FALSE, 'size'=>'tiny', 'default'=>'0'),
        'recipient_delete'       => array('type'=>'integer', 'null'=>FALSE, 'size'=>'tiny', 'default'=>'0'),
        'anonpost'               => array('type'=>'integer', 'unsigned'=>true, 'null'=>TRUE, 'size'=>'tiny', 'default'=>'0'),
        'title'                  => array('type'=>'varchar', 'null'=>FALSE, 'size'=>'100'),
        'text'                   => array('type'=>'text', 'null'=>TRUE, 'size'=>'medium')
    );

    $query = xarDBCreateTable($xartable['messages'], $fields);

    $result =& $dbconn->Execute($query);
    if (!$result)
        return;

    $index = array('name'      => 'i_' . xarDB::getPrefix() . '_messages_left',
                   'fields'    => array('left_id'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['messages'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    xarModVars::set('messages', 'buddylist', 0);
    xarModVars::set('messages', 'itemsperpage', 10);
    xarModVars::set('messages', 'limitsaved', 12);
    xarModVars::set('messages', 'limitout', 10);
    xarModVars::set('messages', 'limitinbox', 10);
    xarModVars::set('messages', 'smilies', false);
    xarModVars::set('messages', 'allow_html', false);
    xarModVars::set('messages', 'allow_bbcode', false);
    xarModVars::set('messages', 'mailsubject', 'You have a new private message !');
    xarModVars::set('messages', 'fromname', 'Webmaster');
    xarModVars::set('messages', 'from', 'Webmaster@YourSite.com');
    xarModVars::set('messages', 'inboxurl', 'http://www.yoursite.com/index.php?module=messages&type=user&func=display');
    xarModVars::set('messages', 'serverpath', '/home/yourdir/public_html/modules/messages');
    xarModVars::set('messages', 'SupportShortURLs', false );
    xarModVars::set('messages', 'awaymsg', true );
    xarModVars::set('messages', 'drafts', true );
    xarModVars::set('messages', 'allowedSendMessages', serialize(array()));

    xarModVars::set('messages', 'away_message', '');

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
    xarModVars::set('messages','objectid',$objectid);
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
    xarRegisterMask('ManageMessages','All','messages','Item','All:All:All','ACCESS_DELETE');
    xarRegisterMask('AdminMessages','All','messages','Item','All:All:All','ACCESS_ADMIN');
    /*********************************************************************
    * Enter some default privileges
    * Format is
    * register(Name,Realm,Module,Component,Instance,Level,Description)
    *********************************************************************/
    xarRegisterPrivilege('ManageMessages','All','messages','All','All','ACCESS_DELETE',xarML('Delete access to messages'));
    xarRegisterPrivilege('DenyReadMessages','All','messages','All','All','ACCESS_NONE',xarML('Deny access to messages'));
    /*********************************************************************
    * Assign the default privileges to groups/users
    * Format is
    * assign(Privilege,Role)
    *********************************************************************/

    xarAssignPrivilege('ManageMessages','Users');
    xarAssignPrivilege('DenyReadMessages','Everybody');

# --------------------------------------------------------
#
# Create DD objects
#
    $module = 'messages';
    $objects = array(
                   'messages_messages',
                     );

    if(!xarModAPIFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

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
        case '1.8.0':
            // compatability upgrade
            xarModVars::set('messages', 'away_message', '');
        case '1.8.1':
            // nothing to do for this rev
            break;
        case '1.9':
        case '1.9.0':
            // Code to upgrade from version 2.0 goes here
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

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'messages',
                             'blockType'=> 'newmessages'))) return;

    return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => 'messages'));
}

?>
