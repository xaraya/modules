<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
    $xartable =& xarDB::getTables();
    //Psspl:Added the code for anonpost_to field.
    $fields = array(
        'id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
        'date'      => array('type'=>'integer',  'null'=>FALSE),
        'author'    => array('type'=>'integer',  'null'=>FALSE,  'size'=>'medium','default'=>1),
        'title'     => array('type'=>'varchar',  'null'=>FALSE,  'size'=>100),
        'text'      => array('type'=>'text',     'null'=>TRUE,   'size'=>'medium'),
        'parent_id' => array('type'=>'integer',  'null'=>FALSE),
        'parent_url'=> array('type'=>'text',     'null'=>FALSE,  'size'=>'medium'),
        'module_id' => array('type'=>'integer',  'null'=>TRUE),
        'itemtype'  => array('type'=>'integer',  'null'=>false),
        'itemid'    => array('type'=>'varchar',  'null'=>FALSE,  'size'=>255),
        'hostname'  => array('type'=>'varchar',  'null'=>FALSE,  'size'=>255),
        'left_id'   => array('type'=>'integer',  'null'=>FALSE),
        'right_id'  => array('type'=>'integer',  'null'=>FALSE),
        'anonpost'  => array('type'=>'integer',  'null'=>TRUE,   'size'=>'tiny', 'default'=>0),
        'status'    => array('type'=>'integer',  'null'=>FALSE,  'size'=>'tiny'),
    );

    $query = xarDBCreateTable($xartable['comments'], $fields);

    $result =& $dbconn->Execute($query);
    if (!$result)
        return;

    $index = array('name'      => 'i_' . xarDB::getPrefix() . '_comments_left',
                   'fields'    => array('left_id'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDB::getPrefix() . '_comments_right',
                   'fields'    => array('right_id'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDB::getPrefix() . '_comments_parent_id',
                   'fields'    => array('parent_id'),
                   'unique'    => FALSE);

    $query = xarDBCreateIndex($xartable['comments'],$index);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDB::getPrefix() . '_comments_moduleid',
                   'fields'    => array('module_id'),
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

    $index = array('name'      => 'i_' . xarDB::getPrefix() . '_comments_itemid',
                   'fields'    => array('itemid'),
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
    if (!$result) return;

    $module = 'comments';
    $objects = array(
                'comments_comments',
                'comments_module_settings',
                'comments_blacklist'
                );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

# --------------------------------------------------------
#
# Set up modvars
#
    xarModVars::set('comments', 'render',_COM_VIEW_THREADED);
    xarModVars::set('comments', 'sortby',_COM_SORTBY_THREAD);
    xarModVars::set('comments', 'order',_COM_SORT_ASC);
    xarModVars::set('comments', 'depth', _COM_MAX_DEPTH);
    xarModVars::set('comments', 'AllowPostAsAnon',1);
    xarModVars::set('comments', 'AuthorizeComments',0);
    xarModVars::set('comments', 'AllowCollapsableThreads',1);
    xarModVars::set('comments', 'CollapsedBranches',serialize(array()));
    xarModVars::set('comments', 'editstamp',1);
    xarModVars::set('comments', 'usersetrendering',false);
    xarModVars::set('comments', 'allowhookoverride', false);
    xarModVars::set('comments', 'edittimelimit', 0);
    xarModVars::set('comments', 'numstats',100);
    xarModVars::set('comments', 'rssnumitems',25);
    xarModVars::set('comments', 'wrap', false);
    xarModVars::set('comments', 'showtitle', false);
    xarModVars::set('comments', 'useblacklist', false);
    xarModVars::set('comments', 'enable_filters',1);
    xarModVars::set('comments', 'filters_min_item_count',3);

# --------------------------------------------------------
#
# Set up configuration modvars (general)
#
        $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'comments'));
        $module_settings->initialize();

# --------------------------------------------------------
#
# Set up hooks
#
    // TODO: add delete hook

    // display hook
    if (!xarModRegisterHook('item', 'display', 'GUI','comments', 'user', 'display')) return false;

    // usermenu hook
    if (!xarModRegisterHook('item', 'usermenu', 'GUI','comments', 'user', 'usermenu')) return false;

    // search hook
    if (!xarModRegisterHook('item', 'search', 'GUI','comments', 'user', 'search')) return false;

    // module delete hook
    if (!xarModRegisterHook('module', 'remove', 'API','comments', 'admin', 'remove_module')) return false;

# --------------------------------------------------------
#
# Define instances for this module
# Format is
#  setInstance(Module, Type, ModuleTable, IDField, NameField,
#             ApplicationVar, LevelTable, ChildIDField, ParentIDField)
#
    $ctable = $xartable['comments'];
    $query1 = "SELECT DISTINCT $xartable[modules].name
                          FROM $ctable
                     LEFT JOIN $xartable[modules]
                            ON modid = $xartable[modules].regid";

    $query2 = "SELECT DISTINCT objectid
                          FROM $ctable";

    $query3 = "SELECT DISTINCT id
                          FROM $ctable
                         WHERE status != '"._COM_STATUS_ROOT_NODE."'";
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
    xarRegisterMask('ModerateComments', 'All','comments', 'All','All:All:All','ACCESS_MODERATE',  'Moderate Comments');
    xarRegisterMask('EditComments',     'All','comments', 'All','All:All:All','ACCESS_EDIT',      'Edit Comments');
    xarRegisterMask('AddComments',      'All','comments', 'All','All:All:All','ACCESS_ADD',      'Add Comments');
    xarRegisterMask('ManageComments',   'All','comments', 'All','All:All:All','ACCESS_DELETE',    'Delete a Comment or Comments');
    xarRegisterMask('AdminComments',    'All','comments', 'All','All:All:All','ACCESS_ADMIN',     'Administrate Comments');

    xarRegisterPrivilege('ViewComments','All','comments','All','All','ACCESS_OVERVIEW');
    xarRegisterPrivilege('ReadComments','All','comments','All','All','ACCESS_READ');
    xarRegisterPrivilege('CommmentComments','All','comments','All','All','ACCESS_COMMENT');
    xarRegisterPrivilege('ModerateComments','All','comments','All','All','ACCESS_MODERATE');
    xarRegisterPrivilege('EditComments','All','comments','All','All','ACCESS_EDIT');
    xarRegisterPrivilege('AddComments','All','comments','All','All','ACCESS_ADD');
    xarRegisterPrivilege('ManageComments','All','comments','All','All:All','ACCESS_DELETE');
    xarRegisterPrivilege('AdminComments','All','comments','All','All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * Upgrade the comments module from an old version
 */
function comments_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
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
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => 'comments'));
}

?>