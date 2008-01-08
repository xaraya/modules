<?php
/**
 * Polls initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */
/**
 * initialise the polls module
 */
 
sys::import('xaraya.tableddl');
        
function polls_init()
{
    // Get database setup
    $dbconn = xarDB::getConn();
    $xarTables = xarDB::getTables();
    
    // Create the main table
    $pollstable = $xarTables['polls'];
    $pollsinfotable = $xarTables['polls_info'];
    
    
    try {
        $dbconn->begin();
        
        $fields = array(
            'pid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
            'title'=>array('type'=>'varchar','size'=>255,'null'=>FALSE),
            'type'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'0'),
            'open'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'1'),
            'private'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'0'),
            'modid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>xarModGetIDFromName('polls')),
            'itemtype'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'),
            'itemid'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'),
            'opts'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'0'),
            'votes'=>array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>'0'),
            'start_date'=>array('type'=>'integer','size'=>'large','null'=>FALSE,'default'=>time()),
            'end_date'=>array('type'=>'integer','size'=>'large','null'=>FALSE,'default'=>'0'),
            'reset'=>array('type'=>'integer','size'=>'large','null'=>FALSE,'default'=>'0')
        );

        $query = xarDBCreateTable($pollstable,$fields);
        $dbconn->Execute($query);

        $fields = array(
            'pid'=>array('type'=>'integer','null'=>FALSE),
            'optnum'=>array('type'=>'integer','null'=>FALSE),
            'optname'=>array('type'=>'varchar','size'=>'255','null'=>FALSE),
            'votes'=>array('type'=>'integer','null'=>FALSE,'default'=>'0')
        );

        $sql = xarDBCreateTable($pollsinfotable,$fields);
        $dbconn->Execute($sql);

        $index = array('name'   => 'i_' . xarDB::getPrefix() . '_polls_title',
                       'fields' => array('title'),
                       'unique' => true);

        $query = xarDBCreateIndex($pollstable,$index);
        $dbconn->Execute($query);
        
        $index = array('name'   => 'i_' . xarDB::getPrefix() . '_polls_pid',
                       'fields' => array('pid','optnum'),
                       'unique' => true);

        $query = xarDBCreateIndex($pollsinfotable,$index);
        $dbconn->Execute($query);
    
        $dbconn->commit();
        } catch (Exception $e) {
            $dbconn->rollback();
            throw $e;
        }

    // Set up module variables
    xarModVars::set('polls', 'barscale', 1);
    xarModVars::set('polls', 'defaultopts', 10);
    xarModVars::set('polls', 'imggraph', 0);
    xarModVars::set('polls', 'voteinterval', '-1');
    xarModVars::set('polls', 'previewresults', 1);
    xarModVars::set('polls', 'showtotalvotes', 1);
    xarModVars::set('polls', 'uservotes', serialize(array()));
    xarModVars::set('polls', 'SupportShortURLs', 1);

    // Register blocks
    if (!xarModAPIFunc('blocks','admin','register_block_type',
                                   array('modName'  => 'polls',
                                         'blockType'=> 'poll'))) return;

    // Register Hooks
    if (!xarModRegisterHook('item', 'search', 'GUI',
                           'polls', 'user', 'search')) {
        return;
    }
    if (!xarModRegisterHook('item', 'display', 'GUI',
                            'polls', 'user', 'displayhook')) {
        return;
    }
    if (!xarModRegisterHook('item', 'new', 'GUI',
                           'polls', 'admin', 'newhook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'create', 'API',
                           'polls', 'admin', 'createhook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'modify', 'GUI',
                           'polls', 'admin', 'modifyhook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'update', 'API',
                           'polls', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'delete', 'API',
                           'polls', 'admin', 'deletehook')) {
        return false;
    }
    if (!xarModRegisterHook('module', 'remove', 'API',
                           'polls', 'admin', 'removehook')) {
        return false;
    }

    /*********************************************************************
    * Define instances for this module
    * Format is
    * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
    *********************************************************************/

    // TODO: define instances based on module, itemtype and itemid later

    $query1 = "SELECT DISTINCT pid FROM ".xarDBGetSiteTablePrefix()."_polls";
    //$query2 = "SELECT DISTINCT title FROM ".xarDBGetSiteTablePrefix()."_polls";
    $query2 = "SELECT DISTINCT type FROM ".xarDBGetSiteTablePrefix()."_polls";
    $instances = array( 
                        array('header' => 'Poll Id:',
                                'query' => $query1,
                                'limit' => 20
                            ),
                       /* array('header' => 'Poll title:',
                                'query' => $query2,
                                'limit' => 20
                            ),*/
                        array('header' => 'Poll Type:',
                                'query' => $query2,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('polls', 'Polls', $instances);

    /*
    $query1 = "SELECT DISTINCT pid FROM ".xarDBGetSiteTablePrefix()."_polls_info";
    $query2 = "SELECT DISTINCT optnum FROM ".xarDBGetSiteTablePrefix()."_polls_info";
    $query3 = "SELECT DISTINCT optname FROM ".xarDBGetSiteTablePrefix()."_polls_info";
    $instances = array(
                        array('header' => 'Polls Info ID:',
                                'query' => $query1,
                                'limit' => 20
                            ),
                        array('header' => 'Polls Info Number:',
                                'query' => $query2,
                                'limit' => 20
                            ),
                        array('header' => 'Polls Info Name:',
                                'query' => $query3,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('polls', 'PollsInfo', $instances, 0, '', '', '', 'Security instance for Poll Options.');
   */
    /*********************************************************************
    * Register the module components that are privileges objects
    * Format is
    * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    *********************************************************************/

    xarRegisterMask('AdminPolls','All','polls','Polls','All:All','ACCESS_ADMIN');
    xarRegisterMask('DeletePolls','All','polls','Polls','All:All','ACCESS_DELETE');
    xarRegisterMask('AddPolls','All','polls','Polls','All:All','ACCESS_ADD');
    xarRegisterMask('EditPolls','All','polls','Polls','All:All','ACCESS_EDIT');
    xarRegisterMask('VotePolls','All','polls','Polls','All:All','ACCESS_COMMENT');
    xarRegisterMask('ViewPolls','All','polls','Polls','All:All','ACCESS_READ');
    xarRegisterMask('ListPolls','All','polls','Polls','All:All','ACCESS_OVERVIEW');

    xarRegisterMask('ViewPollBlock','All','polls','PollBlock','All:All','ACCESS_READ');

    /*
    xarRegisterMask('AdminPollOptions','All','polls','All','All','ACCESS_ADMIN');
    xarRegisterMask('DeletePollOptions','All','polls','All','All','ACCESS_DELETE');
    xarRegisterMask('AddPollOptions','All','polls','All','All','ACCESS_ADD');
    xarRegisterMask('EditPollOptions','All','polls','All','All','ACCESS_EDIT');
    */
    
     $module = 'polls';
     $objects = array('polls_main','polls_opts');
     xarModAPIFunc('modules','admin','standardinstall',array('module' => 'polls', 'objects' => $objects));
    
    // Initialisation successful
    return true;
}

/**
 * upgrade the polls module from an old version
 */
function polls_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {

        case '1.6.0': //current version

             break;
    }
    // Update successful
    return true;
}

/**
 * delete the polls module
 */
function polls_delete()
{
      
    // Get database setup
    $dbconn = xarDB::getConn();
    $xarTables = xarDB::getTables();

    try {
    $dbconn->begin();
    // Drop the tables
    $sql = xarDBDropTable($xarTables['polls_info']);
    $result = &$dbconn->Execute($sql);


    $sql = xarDBDropTable($xarTables['polls']);
    $result = &$dbconn->Execute($sql);
    
    //Delete all DD objects created
    $dd_objects = unserialize(xarModVars::get('polls','dd_objects'));
    foreach ($dd_objects as $key => $value)
            $result = xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $value));
    
    $dbconn->commit();
        } catch (Exception $e) {
            $dbconn->rollback();
            throw $e;
        }

    // Delete any module variables
     xarModVars::delete_all('polls');

    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'polls',
                             'blockType'=> 'poll'))) return;

    if (!xarModUnregisterHook('item', 'search', 'GUI',
                              'polls', 'user', 'search')) {
        return;
    }
    if (!xarModUnregisterHook('item', 'display', 'GUI',
                              'polls', 'user', 'displayhook')) {
        return;
    }
    // Remove module hooks
    if (!xarModUnregisterHook('item', 'new', 'GUI',
                              'polls', 'admin', 'newhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'create', 'API',
                              'polls', 'admin', 'createhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'modify', 'GUI',
                              'polls', 'admin', 'modifyhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'update', 'API',
                              'polls', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'delete', 'API',
                              'polls', 'admin', 'deletehook')) {
        return false;
    }
    // when a whole module is removed, e.g. via the modules admin screen
    // (set object ID to the module name !)
    if (!xarModUnregisterHook('module', 'remove', 'API',
                              'polls', 'admin', 'removehook')) {
        return false;
    }

    // Remove Masks and Instances
    xarRemoveMasks('polls');
    xarRemoveInstances('polls');
    
   
     

    // Deletion successful
    return true;
}

?>
