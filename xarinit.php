<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Comments API
 * @package Xaraya
 * @subpackage Comments_API
 */

sys::import('modules.comments.xarincludes.defines');

/**
 * Comments Initialization Function
 *
 * @author Carl P. Corliss (aka Rabbitt)
 *
 */
function comments_init()
{
    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    // Create tables
    $ctable = $xartable['comments'];
    $cctable = &$xartable['comments_column'];

    $fields = array(
        'id'       => array('type'=>'integer',  'null'=>FALSE,  'increment'=>TRUE,'primary_key'=>TRUE),
        'pid'       => array('type'=>'integer',  'null'=>FALSE),
        'modid'     => array('type'=>'integer',  'null'=>TRUE),
        'itemtype'  => array('type'=>'integer',  'null'=>false),
        'objectid'  => array('type'=>'varchar',  'null'=>FALSE,  'size'=>255),
        'date'      => array('type'=>'integer',  'null'=>FALSE),
        'author'    => array('type'=>'integer',  'null'=>FALSE,  'size'=>'medium','default'=>1),
        'title'     => array('type'=>'varchar',  'null'=>FALSE,  'size'=>100),
        'hostname'  => array('type'=>'varchar',  'null'=>FALSE,  'size'=>255),
        'text'      => array('type'=>'text',     'null'=>TRUE,   'size'=>'medium'),
        'cleft'      => array('type'=>'integer',  'null'=>FALSE),
        'cright'     => array('type'=>'integer',  'null'=>FALSE),
        'status'    => array('type'=>'integer',  'null'=>FALSE,  'size'=>'tiny'),
        'anonpost'  => array('type'=>'integer',  'null'=>TRUE,   'size'=>'tiny', 'default'=>0),
    );

    $query = xarDBCreateTable($xartable['comments'], $fields);

    $result =& $dbconn->Execute($query);
    if (!$result)
        return;

    $index = array('name'      => 'i_' . xarDB::getPrefix() . '_comments_left',
                   'fields'    => array('cleft'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDB::getPrefix() . '_comments_right',
                   'fields'    => array('cright'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDB::getPrefix() . '_comments_pid',
                   'fields'    => array('pid'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDB::getPrefix() . '_comments_modid',
                   'fields'    => array('modid'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDB::getPrefix() . '_comments_itemtype',
                   'fields'    => array('itemtype'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDB::getPrefix() . '_comments_objectid',
                   'fields'    => array('objectid'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDB::getPrefix() . '_comments_status',
                   'fields'    => array('status'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDB::getPrefix() . '_comments_author',
                   'fields'    => array('author'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Create blacklist tables

    $btable = $xartable['blacklist'];
    $bbtable = &$xartable['blacklist_column'];

    $fields = array(
        'id'       => array('type'=>'integer',  'null'=>FALSE,  'increment'=> TRUE, 'primary_key'=>TRUE),
        'domain'   => array('type'=>'varchar',  'null'=>FALSE,  'size'=>255)
    );

    $query = xarDBCreateTable($xartable['blacklist'], $fields);

    $result =& $dbconn->Execute($query);
    if (!$result)
        return;

# --------------------------------------------------------
#
# Set up modvars
#
    xarModVars::set('comments','render',_COM_VIEW_THREADED);
    xarModVars::set('comments','sortby',_COM_SORTBY_THREAD);
    xarModVars::set('comments','order',_COM_SORT_ASC);
    xarModVars::set('comments','depth', _COM_MAX_DEPTH);
    xarModVars::set('comments','AllowPostAsAnon',1);
    xarModVars::set('comments','AuthorizeComments',0);
    xarModVars::set('comments','AllowCollapsableThreads',1);
    xarModVars::set('comments','CollapsedBranches',serialize(array()));
    xarModVars::set('comments','editstamp',1);
    xarModVars::set('comments','usersetrendering',false);
    xarModVars::set('comments','numstats',100);
    xarModVars::set('comments','rssnumitems',25);
    xarModVars::set('comments', 'wrap', false);
    xarModVars::set('comments', 'showtitle', false);
    xarModVars::set('comments', 'showoptions', false);
    xarModVars::set('comments', 'useblacklist', false);

# --------------------------------------------------------
#
# Set up hooks
#
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

    $query1 = "SELECT DISTINCT $xartable[modules].name
                          FROM $ctable
                     LEFT JOIN $xartable[modules]
                            ON $cctable[modid] = $xartable[modules].regid";

    $query2 = "SELECT DISTINCT $cctable[objectid]
                          FROM $ctable";

    $query3 = "SELECT DISTINCT $cctable[id]
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

# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ReadComments',     'All','comments', 'All','All:All:All','ACCESS_READ',      'See and Read Comments');
    xarRegisterMask('PostComments',     'All','comments', 'All','All:All:All','ACCESS_COMMENT',   'Post a new Comment');
    xarRegisterMask('ReplyComments',    'All','comments', 'All','All:All:All','ACCESS_COMMENT',   'Reply to a Comment');
    xarRegisterMask('EditComments',     'All','comments', 'All','All:All:All','ACCESS_EDIT',      'Edit Comments');
    xarRegisterMask('DeleteComments',   'All','comments', 'All','All:All:All','ACCESS_DELETE',    'Delete a Comment or Comments');
    xarRegisterMask('ModerateComments', 'All','comments', 'All','All:All:All','ACCESS_MODERATE',  'Moderate Comments');
    xarRegisterMask('AdminComments',    'All','comments', 'All','All:All:All','ACCESS_ADMIN',     'Administrate Comments');


    // Register blocks
    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                       array('modName'  => 'comments',
                             'blockType'=> 'latestcomments'))) return;
    // TODO: define blocks mask & instances here, or re-use some common one ?

    // Initialisation successful
    return true;
}

/**
* upgrade the comments module from an old version
*/
function comments_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '1.3':
        case '1.3.0':

        case '2.0':
            // Code to upgrade from version 2.0 goes here
            // fall through to the next upgrade
        case '2.5':
            // Code to upgrade from version 2.5 goes here
            break;
    }
    return true;
}

/**
* uninstall the comments module
*/
function comments_delete()
{
    return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => 'comments'));

}

?>