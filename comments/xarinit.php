<?php

/**
 * File: $Id$
 *
 * init file for installing/upgrading Comments module
 *
 * @package modules
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @author Carl P. Corliss <rabbitt@xaraya.com>
*/

/**
 * Comments API
 * @package Xaraya
 * @subpackage Comments_API
 */

include_once('modules/comments/xarincludes/defines.php');

/**
 * Comments Initialization Function
 *
 * @author Carl P. Corliss (aka Rabbitt)
 *
 */
function comments_init() {

    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // Create tables
    $ctable = $xartable['comments'];
    $cctable = &$xartable['comments_column'];

    $fields = array(
        'xar_cid'       => array('type'=>'integer',  'null'=>FALSE,  'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_pid'       => array('type'=>'integer',  'null'=>FALSE),
        'xar_modid'     => array('type'=>'integer',  'null'=>TRUE),
        'xar_objectid'  => array('type'=>'varchar',  'null'=>FALSE,  'size'=>255),
        'xar_date'      => array('type'=>'integer',  'null'=>FALSE),
        'xar_author'    => array('type'=>'integer',  'null'=>FALSE,  'size'=>'medium','default'=>1),
        'xar_title'     => array('type'=>'varchar',  'null'=>FALSE,  'size'=>100),
        'xar_hostname'  => array('type'=>'varchar',  'null'=>FALSE,  'size'=>255),
        'xar_text'      => array('type'=>'text',     'null'=>TRUE,   'size'=>'medium'),
        'xar_left'      => array('type'=>'integer',  'null'=>FALSE),
        'xar_right'     => array('type'=>'integer',  'null'=>FALSE),
        'xar_status'    => array('type'=>'integer',  'null'=>FALSE,  'size'=>'tiny'),
        'xar_anonpost'  => array('type'=>'integer',  'null'=>TRUE,   'size'=>'tiny', 'default'=>0),
    );

    $query = xarDBCreateTable($xartable['comments'], $fields);

    $result =& $dbconn->Execute($query);
    if (!$result)
        return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_comments_left',
                   'fields'    => array('xar_left'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_comments_right',
                   'fields'    => array('xar_right'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_comments_pid',
                   'fields'    => array('xar_pid'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_comments_modid',
                   'fields'    => array('xar_modid'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_comments_objectid',
                   'fields'    => array('xar_objectid'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_comments_status',
                   'fields'    => array('xar_status'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Set up module variables
    xarModSetVar('comments','render',_COM_VIEW_THREADED);
    xarModSetVar('comments','sortby',_COM_SORTBY_THREAD);
    xarModSetVar('comments','order',_COM_SORT_ASC);
    xarModSetVar('comments','depth', _COM_MAX_DEPTH);
    xarModSetVar('comments','AllowPostAsAnon',1);
    xarModSetVar('comments','AuthorizeComments',0);
    xarModSetVar('comments','AllowCollapsableThreads',1);
    xarModSetVar('comments','CollapsedBranches',serialize(array()));

    // TODO: add delete hook

    // display hook
    if (!xarModRegisterHook('item', 'display', 'GUI','comments', 'user', 'display'))
        return false;

    // usermenu hook
    if (!xarModRegisterHook('item', 'usermenu', 'GUI','comments', 'user', 'usermenu'))
        return false;

    // search hook
    if (!xarModRegisterHook('item', 'search', 'GUI','comments', 'user', 'search'))
        return false;

    // module delete hook
    if (!xarModRegisterHook('module', 'remove', 'API','comments', 'admin', 'remove_module'))
        return false;


    /**
     * Define instances for this module
     * Format is
     * setInstance(Module, Type, ModuleTable, IDField, NameField,
     *             ApplicationVar, LevelTable, ChildIDField, ParentIDField)
     *
     */

    $query1 = "SELECT DISTINCT $xartable[modules].xar_name
                          FROM $ctable
                     LEFT JOIN $xartable[modules]
                            ON $cctable[modid] = $xartable[modules].xar_regid";

    $query2 = "SELECT DISTINCT $cctable[objectid]
                          FROM $ctable";

    $query3 = "SELECT DISTINCT $cctable[cid]
                          FROM $ctable
                         WHERE $cctable[status] != '"._COM_STATUS_ROOT_NODE."'";
    $instances = array(
                        array('header' => 'Module ID:',
                                'query' => $query1,
                                'limit' => 20
                            ),
                        array('header' => 'Module Page ID:',
                                'query' => $query2,
                                'limit' => 20
                            ),
                        array('header' => 'Comment ID:',
                                'query' => $query3,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('comments','All',$instances);

    /*
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     *
     */

    xarRegisterMask('Comments-Read',     'All','comments',
                    'All','All:All:All','ACCESS_READ',      'See and Read Comments');
    xarRegisterMask('Comments-Post',     'All','comments',
                    'All','All:All:All','ACCESS_COMMENT',   'Post a new Comment');
    xarRegisterMask('Comments-Reply',    'All','comments',
                    'All','All:All:All','ACCESS_COMMENT',   'Reply to a Comment');
    xarRegisterMask('Comments-Edit',     'All','comments',
                    'All','All:All:All','ACCESS_EDIT',      'Edit Comments');
    xarRegisterMask('Comments-Delete',   'All','comments',
                    'All','All:All:All','ACCESS_DELETE',    'Delete a Comment or Comments');
    xarRegisterMask('Comments-Moderator','All','comments',
                    'All','All:All:All','ACCESS_MODERATE',  'Moderate Comments');
    xarRegisterMask('Comments-Admin',    'All','comments',
                    'All','All:All:All','ACCESS_ADMIN',     'Administrate Comments');


    // Initialisation successful
    return true;
}

function comments_delete()
{
    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Get database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // Delete tables
    $query = xarDBDropTable($xartable['comments']);
    $result =& $dbconn->Execute($query);

    if(!$result)
        return;

    // Delete module variables
    xarModDelVar('comments','render');
    xarModDelVar('comments','sortby');
    xarModDelVar('comments','order');
    xarModDelVar('comments','depth');
    xarModDelVar('comments','AllowPostAsAnon');
    xarModDelVar('comments','AuthorizeComments');

    if (!xarModUnregisterHook('item', 'display', 'GUI',
                            'comments', 'user', 'display')) {
        return false;
    }

    // Remove Masks and Instances
    xarRemoveMasks('comments');
    xarRemoveInstances('comments');

    // Deletion successful
    return true;

}

/**
* upgrade the comments module from an old version
*/
function comments_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case 1.0:
            // Code to upgrade from version 1.0 goes here
            break;
        case 2.0:
            // Code to upgrade from version 2.0 goes here
            break;
        case 2.5:
            // Code to upgrade from version 2.5 goes here
            break;
    }
}

?>
