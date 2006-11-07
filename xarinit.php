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
function polls_init()
{
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();

    // Create the main table
    $pollstable = $xartable['polls'];
    $pollscolumn = &$xartable['polls_column'];

    $fields = array(
        'xar_pid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_title'=>array('type'=>'varchar','size'=>255,'null'=>FALSE),
        'xar_type'=>array('type'=>'varchar','size'=>'16','null'=>FALSE,'default'=>''),
        'xar_open'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'1'),
        'xar_private'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'0'),
        'xar_modid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>xarModGetIDFromName('polls')),
        'xar_itemtype'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'),
        'xar_itemid'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'),
        'xar_opts'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'0'),
        'xar_votes'=>array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>'0'),
        'xar_start_date'=>array('type'=>'integer','size'=>'large','null'=>FALSE,'default'=>time()),
        'xar_end_date'=>array('type'=>'integer','size'=>'large','null'=>FALSE,'default'=>'0'),
        'xar_reset'=>array('type'=>'integer','size'=>'large','null'=>FALSE,'default'=>'0')
    );

    $sql = xarDBCreateTable($pollstable,$fields);
    if (empty($sql)) return; // throw back
    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }


    // Create the information table
    $pollsinfotable = $xartable['polls_info'];
   // $pollsinfocolumn = &$xartable['polls_info_column'];

    $fields = array(
        'xar_pid'=>array('type'=>'integer','null'=>FALSE),
        'xar_optnum'=>array('type'=>'integer','null'=>FALSE),
        'xar_optname'=>array('type'=>'varchar','size'=>'255','null'=>FALSE),
        'xar_votes'=>array('type'=>'integer','null'=>FALSE,'default'=>'0')
    );

/*
    // FIXME - <Dracos> preserve until multi-field keys are possible

    $sql = "CREATE TABLE $pollsinfotable (
            $pollsinfocolumn[pid] int(10) NOT NULL,
            $pollsinfocolumn[optnum] int(10) NOT NULL,
            $pollsinfocolumn[optname] varchar(255) NOT NULL,
            $pollsinfocolumn[votes] int(2) NOT NULL default 0,
            UNIQUE KEY(xar_pid, xar_optnum))";
    $dbconn->Execute($sql);
*/

    $sql = xarDBCreateTable($pollsinfotable,$fields);
    if (empty($sql)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $index = array('name'   => 'i_' . xarDBGetSiteTablePrefix() . '_polls_pid',
                   'fields' => array('xar_pid'),
                   'unique' => false);

    $query = xarDBCreateIndex($pollsinfotable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Set up module variables
    xarModSetVar('polls', 'barscale', 1);
    xarModSetVar('polls', 'defaultopts', 10);
    xarModSetVar('polls', 'imggraph', 0);
    xarModSetVar('polls', 'voteinterval', '-1');
    xarModSetVar('polls', 'previewresults', 1);
    xarModSetVar('polls', 'showtotalvotes', 1);
    xarModSetVar('polls', 'uservotes', serialize(array()));
    xarModSetVar('polls', 'SupportShortURLs', 1);

    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
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

/* // TODO: show something in user menu someday ?
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
                            'polls', 'user', 'usermenu')) {
        return false;
    }
*/

    /*********************************************************************
    * Define instances for this module
    * Format is
    * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
    *********************************************************************/

// TODO: define instances based on module, itemtype and itemid later

    $query1 = "SELECT DISTINCT xar_title FROM ".xarDBGetSiteTablePrefix()."_polls";
    $query2 = "SELECT DISTINCT xar_type FROM ".xarDBGetSiteTablePrefix()."_polls";
    $instances = array(
                        array('header' => 'Poll title:',
                                'query' => $query1,
                                'limit' => 20
                            ),
                        array('header' => 'Poll Type:',
                                'query' => $query2,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('polls', 'Polls', $instances);

    /*
    $query1 = "SELECT DISTINCT xar_pid FROM ".xarDBGetSiteTablePrefix()."_polls_info";
    $query2 = "SELECT DISTINCT xar_optnum FROM ".xarDBGetSiteTablePrefix()."_polls_info";
    $query3 = "SELECT DISTINCT XAR_optname FROM ".xarDBGetSiteTablePrefix()."_polls_info";
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

    // Initialisation successful
    return true;
}

/**
 * upgrade the polls module from an old version
 */
function polls_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case 1.0:
            // Code to upgrade from version 1.0 goes here
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            xarDBLoadTableMaintenanceAPI();

            $pollstable = $xartable['polls'];

            $args = array('command' => 'add',
                          'field' => 'xar_private',
                          'type' => 'integer',
                          'null' => false,
                          'default' => '0');

            $sql = xarDBAlterTable($pollstable, $args);
            if (empty($sql)) return; // throw back

            // Pass the Table Create DDL to adodb to create the table
            $result =& $dbconn->Execute($sql);
            if (!$result) return;

            xarRegisterMask('CommentPolls','All','polls','Polls','All:All:All','ACCESS_COMMENT');

        case 1.1:
            // Code to upgrade from version 1.1 goes here
            // Get database information
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();

            //Load Table Maintenance API
            xarDBLoadTableMaintenanceAPI();

            $query = xarDBAlterTable($xartable['polls'],
                                     array('command'  => 'add',
                                           'field'    => 'xar_itemtype',
                                           'type'     => 'integer',
                                           'unsigned' => true,
                                           'null'     => false,
                                           'default'  => '0'));

            $result =& $dbconn->Execute($query);
            if (!$result) return;

            $query = xarDBAlterTable($xartable['polls'],
                                     array('command'  => 'add',
                                           'field'    => 'xar_itemid',
                                           'type'     => 'integer',
                                           'unsigned' => true,
                                           'null'     => false,
                                           'default'  => '0'));

            $result =& $dbconn->Execute($query);
            if (!$result) return;

            if (!xarModRegisterHook('item', 'display', 'GUI',
                                    'polls', 'user', 'displayhook')) {
                return;
            }

        case 1.2:
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
        case '1.3.0':

            xarModSetVar('polls', 'SupportShortURLs', 1);
            xarModDelVar('polls', 'comments');
            xarModDelVar('polls', 'itemsperpage');

           $dbconn =& xarDBGetConn();
           $xartable =& xarDBGetTables();
           xarDBLoadTableMaintenanceAPI();
           $pref = xarDBGetSiteTablePrefix();

           if ($pref !== 'xar') {


               $pollstable = $pref . '_temp_polls';
               $oldpollstable = $xartable['polls'];

               $tables = $pref . '_tables';

               $fields = array( 'xar_pid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
                                'xar_title'=>array('type'=>'varchar','size'=>255,'null'=>FALSE),
                                'xar_type'=>array('type'=>'varchar','size'=>'16','null'=>FALSE,'default'=>''),
                                'xar_open'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'1'),
                                'xar_private'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'0'),
                                'xar_modid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>xarModGetIDFromName('polls')),
                                'xar_itemtype'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'),
                                'xar_itemid'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'),
                                'xar_opts'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'0'),
                                'xar_votes'=>array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>'0'),
                                'xar_reset'=>array('type'=>'integer','size'=>'large','null'=>FALSE,'default'=>'0')
                              );

                $sql = xarDBCreateTable($pollstable,$fields);
                if (empty($sql)) return;
                $result =& $dbconn->Execute($sql);
                if (!$result) return;

                $sql = 'INSERT INTO ' . $pollstable . ' SELECT * FROM ' . $oldpollstable;
                $result =& $dbconn->Execute($sql);
                if (!$result) return;

                $sql = xarDBDropTable($oldpollstable);
                $result =& $dbconn->Execute($sql);
                if (!$result) return;

                $sql = xarDBAlterTable($pollstable, array('command'  => 'rename',
                                                           'new_name' => $pref . '_polls'));
                $result =& $dbconn->Execute($sql);
                if (!$result) return;

                $oldpollsinfotable = $xartable['polls_info'];
                $pollsinfotable = $pref . '_temp_polls_info';

                $fields1 = array( 'xar_pid'=>array('type'=>'integer','null'=>FALSE),
                                 'xar_optnum'=>array('type'=>'integer','null'=>FALSE),
                                 'xar_optname'=>array('type'=>'varchar','size'=>'255','null'=>FALSE),
                                 'xar_votes'=>array('type'=>'integer','null'=>FALSE,'default'=>'0')
                                );

                $sql = xarDBCreateTable($pollsinfotable,$fields1);
                if (empty($sql)) return;
                $result =& $dbconn->Execute($sql);
                if (!$result) return;

                $sql = 'INSERT INTO ' . $pollsinfotable . ' SELECT * FROM ' . $oldpollsinfotable;
                $result =& $dbconn->Execute($sql);
                if (!$result) return;

                $sql = xarDBDropTable($oldpollsinfotable);
                $result =& $dbconn->Execute($sql);
                if (!$result) return;

                $sql = xarDBAlterTable($pollsinfotable, array('command'  => 'rename',
                                                               'new_name' => $pref . '_polls_info'));
                $result =& $dbconn->Execute($sql);
                if (!$result) return;

        }
        $index = array('name'   => 'i_' . xarDBGetSiteTablePrefix() . '_polls_pid',
               'fields' => array('xar_pid'),
               'unique' => false);

        $query = xarDBCreateIndex($pref . '_polls_info',$index);
        $result =& $dbconn->Execute($query);
        if (!$result) return;

        case '1.4.0':
            xarModSetVar('polls', 'showtotalvotes', 1);

        case '1.4.1':

        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        $pollstable = $xartable['polls'];

            //Load Table Maintenance API
        xarDBLoadTableMaintenanceAPI();

            $query = xarDBAlterTable($pollstable,
                                     array('command'  => 'add',
                                           'field'    => 'xar_start_date',
                                           'type'     => 'integer',
                                           'unsigned' => true,
                                           'null'     => false,
                                           'default'  => time()));

            $result =& $dbconn->Execute($query);
            if (!$result) return;

            $query = xarDBAlterTable($pollstable,
                                     array('command'  => 'add',
                                           'field'    => 'xar_end_date',
                                           'type'     => 'integer',
                                           'unsigned' => true,
                                           'null'     => false,
                                           'default'  => '0'));

            $result =& $dbconn->Execute($query);
            if (!$result) return;

            $sql = 'update ' . $pollstable . ' set xar_start_date = '. time() . ' , xar_end_date = 0 where xar_open = 1';
            $result =& $dbconn->Execute($sql);
            if (!$result) return;

            $sql = 'update ' . $pollstable . ' set xar_start_date = '. time() . ' , xar_end_date = '. time() . ' where xar_open = 0';
            $result =& $dbconn->Execute($sql);
            if (!$result) return;

            xarUnregisterMask('ViewResultsPolls');
            xarUnregisterMask('CommentPolls');
            xarRemoveInstances('polls');
            $query1 = "SELECT DISTINCT xar_title FROM ".xarDBGetSiteTablePrefix()."_polls";
            $query2 = "SELECT DISTINCT xar_type FROM ".xarDBGetSiteTablePrefix()."_polls";
            $instances = array(
                        array('header' => 'Poll title:',
                                'query' => $query1,
                                'limit' => 20
                            ),
                        array('header' => 'Poll Type:',
                                'query' => $query2,
                                'limit' => 20
                            )
                    );
            xarDefineInstance('polls', 'Polls', $instances);

        case 2.0:
            // Code to upgrade from version 2.0 goes here
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
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();

    // Drop the tables
    $sql = xarDBDropTable($xartable['polls_info']);
    if (empty($sql)) return;
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    $sql = xarDBDropTable($xartable['polls']);
    if (empty($sql)) return;
    $result = &$dbconn->Execute($sql);
    if (!$result) return;

    // Delete any module variables
    xarModDelAllVars('polls');

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
/* // TODO: show something in user menu someday ?
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
                              'polls', 'user', 'usermenu')) {
        return false;
    }
*/

    // Remove Masks and Instances
    xarRemoveMasks('polls');
    xarRemoveInstances('polls');

    // Deletion successful
    return true;
}

?>
