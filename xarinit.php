<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
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
    //Psspl:Added the code for anonpost_to field.
    $fields = array(
        'id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
//        'id'       => array('type'=>'integer',  'null'=>FALSE,  'increment'=>TRUE,'primary_key'=>TRUE),
        'pid'       => array('type'=>'integer',  'null'=>FALSE),
        'modid'     => array('type'=>'integer',  'null'=>TRUE),
        'itemtype'  => array('type'=>'integer',  'null'=>false),
        'objectid'  => array('type'=>'varchar',  'null'=>FALSE,  'size'=>255),
        'date'      => array('type'=>'integer',  'null'=>FALSE),
        'author'    => array('type'=>'integer',  'null'=>FALSE,  'size'=>'medium','default'=>1),
        'title'     => array('type'=>'varchar',  'null'=>FALSE,  'size'=>100),
		'objecturl'     => array('type'=>'text',  'null'=>FALSE,  'size'=>'medium'),
        'hostname'  => array('type'=>'varchar',  'null'=>FALSE,  'size'=>255),
        'text'      => array('type'=>'text',     'null'=>TRUE,   'size'=>'medium'),
        'left_id'      => array('type'=>'integer',  'null'=>FALSE),
        'right_id'     => array('type'=>'integer',  'null'=>FALSE),
        'status'    => array('type'=>'integer',  'null'=>FALSE,  'size'=>'tiny'),
        'anonpost'  => array('type'=>'integer',  'null'=>TRUE,   'size'=>'tiny', 'default'=>0),
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
    if (!$result) return;

	$module = 'comments';
    $objects = array(
                'comments',
				'comments_module_settings',
				'blacklist'
                );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

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
    xarModVars::set('comments', 'allowhookoverride', false);
    xarModVars::set('comments', 'edittimelimit', 0);
    xarModVars::set('comments','numstats',100);
    xarModVars::set('comments','rssnumitems',25);
    xarModVars::set('comments', 'wrap', false);
    xarModVars::set('comments', 'showtitle', false);
    xarModVars::set('comments', 'useblacklist', false);
	xarModVars::set('comments','enable_filters',1);     
	xarModVars::set('comments','filters_min_item_count',3);

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

    if (!xarModRegisterHook('module', 'modifyconfig', 'GUI',
                            'comments', 'admin', 'modifyconfighook')) {
        return false;
    }
    if (!xarModRegisterHook('module', 'updateconfig', 'API',
                            'comments', 'admin', 'updateconfighook')) {
        return false;
    }
    /**
     * Define instances for this module
     * Format is
     * setInstance(Module, Type, ModuleTable, IDField, NameField,
     *             ApplicationVar, LevelTable, ChildIDField, ParentIDField)
     *
     */

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
	xarRegisterMask('AddComments',     'All','comments', 'All','All:All:All','ACCESS_ADD',      'Add Comments');
    xarRegisterMask('DeleteComments',   'All','comments', 'All','All:All:All','ACCESS_DELETE',    'Delete a Comment or Comments');
    xarRegisterMask('AdminComments',    'All','comments', 'All','All:All:All','ACCESS_ADMIN',     'Administrate Comments');

    /*xarRegisterPrivilege('ViewComments','All','comments','All','All','ACCESS_OVERVIEW');
    xarRegisterPrivilege('ReadComments','All','comments','All','All','ACCESS_READ');
    xarRegisterPrivilege('CommmentComments','All','comments','All','All','ACCESS_COMMENT');
    xarRegisterPrivilege('ModerateComments','All','comments','All','All','ACCESS_MODERATE');
    xarRegisterPrivilege('EditComments','All','comments','All','All','ACCESS_EDIT');
    xarRegisterPrivilege('AddComments','All','comments','All','All','ACCESS_ADD');
    xarRegisterPrivilege('ManageComments','All','comments','All','All:All','ACCESS_DELETE');
    xarRegisterPrivilege('AdminComments','All','comments','All','All','ACCESS_ADMIN');*/

    // Register blocks
    /*if (!xarMod::apiFunc('blocks', 'admin', 'register_block_type',
                       array('modName'  => 'comments',
                             'blockType'=> 'latestcomments'))) return;*/
    // TODO: define blocks mask & instances here, or re-use some common one ?

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
        case '1.0':
            // Code to upgrade from version 1.0 goes here
            // Register blocks
        if (!xarMod::apiFunc('blocks', 'admin', 'block_type_exists',
                               array('modName'  => 'comments',
                                     'blockType'=> 'latestcomments'))) {
                 if (!xarMod::apiFunc('blocks', 'admin', 'register_block_type',
                               array('modName'  => 'comments',
                                     'blockType'=> 'latestcomments'))) return;
        }
            // fall through to the next upgrade
        case '1.1':
            // Code to upgrade from version 1.1 goes here
            if (xarModIsAvailable('articles')) {
                // load API for table definition etc.
                if (!xarModAPILoad('articles','user')) return;
            }

            $dbconn =& xarDB::getConn();
            $xartable =& xarDB::getTables();
            $commentstable = $xartable['comments'];

            sys::import('xaraya.tableddl');

            // add the xar_itemtype column
            $query = xarDBAlterTable($commentstable,
                                     array('command' => 'add',
                                           'field' => 'xar_itemtype',
                                           'type' => 'integer',
                                           'null' => false,
                                           'default' => '0'));
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            // make sure all current records have an itemtype 0 (just in case)
            $query = "UPDATE $commentstable SET xar_itemtype = 0";
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // update the itemtype field for all articles
            if (xarModIsAvailable('articles')) {
                $modid = xarMod::getRegID('articles');
                $articlestable = $xartable['articles'];

                $query = "SELECT xar_aid, xar_pubtypeid FROM $articlestable";
                $result =& $dbconn->Execute($query);
                if (!$result) return;

                while (!$result->EOF) {
                    list($aid,$ptid) = $result->fields;
                    $update = "UPDATE $commentstable SET xar_itemtype = $ptid WHERE xar_objectid = '$aid' AND xar_modid = $modid";
                    $test =& $dbconn->Execute($update);
                    if (!$test) return;

                    $result->MoveNext();
                }
                $result->Close();
            }

            // TODO: any other modules where we need to insert the right itemtype here ?

            // add an index for the xar_itemtype column
            $index = array('name'      => 'i_' . xarDB::getPrefix() . '_comments_itemtype',
                           'fields'    => array('xar_itemtype'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($commentstable,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // fall through to the next upgrade
        case '1.2':
        case '1.2.0':
            $dbconn =& xarDB::getConn();
            $xartable =& xarDB::getTables();
            sys::import('xaraya.tableddl');
            // Create blacklist tables
            $btable = $xartable['blacklist'];
            $bbtable = &$xartable['blacklist_column'];

            $fields = array(
                'xar_id'       => array('type'=>'integer',  'null'=>FALSE,  'increment'=> TRUE, 'primary_key'=>TRUE),
                'xar_domain'   => array('type'=>'varchar',  'null'=>FALSE,  'size'=>255)
            );

            $query = xarDBCreateTable($xartable['blacklist'], $fields);

            $result =& $dbconn->Execute($query);
            if (!$result)
                return;
        case '1.3.0':
            if (!xarModRegisterHook('module', 'modifyconfig', 'GUI',
                                    'comments', 'admin', 'modifyconfighook')) {
                return false;
            }
            if (!xarModRegisterHook('module', 'updateconfig', 'API',
                                    'comments', 'admin', 'updateconfighook')) {
                return false;
            }
            xarModVars::set('comments', 'allowhookoverride', false);
            xarModVars::set('comments', 'edittimelimit', 0);
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