<?php
/**
 * File: $Id$
 *
 * Polls initialization functions
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * initialise the polls module
 */
function polls_init()
{
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $prefix = xarConfigGetVar('prefix');
    xarDBLoadTableMaintenanceAPI();

    // Create the main table
    $pollstable = $xartable['polls'];
    $pollscolumn = &$xartable['polls_column'];

// FIXME: why are we using variable prefixes for column fields here ?

    $fields = array(
        $prefix.'_pid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        $prefix.'_title'=>array('type'=>'varchar','size'=>255,'null'=>FALSE),
        $prefix.'_type'=>array('type'=>'varchar','size'=>'16','null'=>FALSE,'default'=>''),
        $prefix.'_open'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'1'),
        $prefix.'_private'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'0'),
        $prefix.'_modid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>xarModGetIDFromName('polls')),
        $prefix.'_itemtype'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'),
        $prefix.'_itemid'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'),
        $prefix.'_opts'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'0'),
        $prefix.'_votes'=>array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>'0'),
        $prefix.'_reset'=>array('type'=>'integer','size'=>'large','null'=>FALSE,'default'=>'0')
    );
    xarModGetIDFromName('polls');
    $sql = xarDBCreateTable($pollstable,$fields);
    if (empty($sql)) return; // throw back
    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }


    // Create the information table
    $pollsinfotable = $xartable['polls_info'];
    $pollsinfocolumn = &$xartable['polls_info_column'];

    $fields = array(
        $prefix.'_pid'=>array('type'=>'integer','null'=>FALSE),
        $prefix.'_optnum'=>array('type'=>'integer','null'=>FALSE),
        $prefix.'_optname'=>array('type'=>'varchar','size'=>'255','null'=>FALSE),
        $prefix.'_votes'=>array('type'=>'integer','null'=>FALSE,'default'=>'0')
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
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // Set up module variables
    xarModSetVar('polls', 'barscale', 1);
    xarModSetVar('polls', 'itemsperpage', 20);
    xarModSetVar('polls', 'defaultopts', 10);
    xarModSetVar('polls', 'comments', 1);
    xarModSetVar('polls', 'imggraph', 0);
    xarModSetVar('polls', 'voteinterval', '-1');
    xarModSetVar('polls', 'previewresults', 1);
    xarModSetVar('polls', 'uservotes', serialize(array()));

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

    $query1 = "SELECT DISTINCT ".$prefix."_title FROM ".$prefix."_polls";
    $query2 = "SELECT DISTINCT ".$prefix."_type FROM ".$prefix."_polls";
    $query3 = "SELECT DISTINCT ".$prefix."_pid FROM ".$prefix."_polls";
    $instances = array(
                        array('header' => 'Poll Title:',
                                'query' => $query1,
                                'limit' => 20
                            ),
                        array('header' => 'Poll Type:',
                                'query' => $query2,
                                'limit' => 20
                            ),
                        array('header' => 'Poll ID:',
                                'query' => $query3,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('polls', 'Polls', $instances, 0, '', '', '', 'Security instance for Polls.');

    $query1 = "SELECT DISTINCT ".$prefix."_pid FROM ".$prefix."_polls_info";
    $query2 = "SELECT DISTINCT ".$prefix."_optnum FROM ".$prefix."_polls_info";
    $query3 = "SELECT DISTINCT ".$prefix."_optname FROM ".$prefix."_polls_info";
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

    /*********************************************************************
    * Register the module components that are privileges objects
    * Format is
    * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    *********************************************************************/

    xarRegisterMask('AdminPolls','All','polls','Polls','All:All:All','ACCESS_ADMIN');
    xarRegisterMask('DeletePolls','All','polls','Polls','All:All:All','ACCESS_DELETE');
    xarRegisterMask('AddPolls','All','polls','Polls','All:All:All','ACCESS_ADD');
    xarRegisterMask('EditPolls','All','polls','Polls','All:All:All','ACCESS_EDIT');
    xarRegisterMask('VotePolls','All','polls','Polls','All:All:All','ACCESS_COMMENT');
    xarRegisterMask('CommentPolls','All','polls','Polls','All:All:All','ACCESS_COMMENT');
    xarRegisterMask('ViewPolls','All','polls','Polls','All:All:All','ACCESS_READ');
    xarRegisterMask('ViewResultsPolls','All','polls','Polls','All:All:All','ACCESS_READ');
    xarRegisterMask('ListPolls','All','polls','Polls','All:All:All','ACCESS_OVERVIEW');

    xarRegisterMask('AdminPollOptions','All','polls','All','All','ACCESS_ADMIN');
    xarRegisterMask('DeletePollOptions','All','polls','All','All','ACCESS_DELETE');
    xarRegisterMask('AddPollOptions','All','polls','All','All','ACCESS_ADD');
    xarRegisterMask('EditPollOptions','All','polls','All','All','ACCESS_EDIT');

    xarRegisterMask('ViewPollBlock','All','polls','PollBlock','All','ACCESS_READ');

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
            $prefix = xarConfigGetVar('prefix');
            xarDBLoadTableMaintenanceAPI();

            $pollstable = $xartable['polls'];

            $args = array('command' => 'add',
                          'field' => $prefix . '_private',
                          'type' => 'integer',
                          'null' => false,
                          'default' => '0');

            $sql = xarDBAlterTable($pollstable, $args);
            if (empty($sql)) return; // throw back

            // Pass the Table Create DDL to adodb to create the table
            $dbconn->Execute($sql);

            // Check for an error with the database code, and if so raise the
            // appropriate exception
            if ($dbconn->ErrorNo() != 0) {
                $msg = xarML('DATABASE_ERROR', $sql);
                xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                               new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
                return;
            }
            xarRegisterMask('CommentPolls','All','polls','Polls','All:All:All','ACCESS_COMMENT');

        case 1.1:
            // Code to upgrade from version 1.1 goes here
            // Get database information
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();

            $prefix = xarConfigGetVar('prefix');

            //Load Table Maintenance API
            xarDBLoadTableMaintenanceAPI();

            $query = xarDBAlterTable($xartable['polls'],
                                     array('command'  => 'add',
                                           'field'    => $prefix.'_itemtype',
                                           'type'     => 'integer',
                                           'unsigned' => true,
                                           'null'     => false,
                                           'default'  => '0'));

            $result =& $dbconn->Execute($query);
            if (!$result) return;

            $query = xarDBAlterTable($xartable['polls'],
                                     array('command'  => 'add',
                                           'field'    => $prefix.'_itemid',
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
        case '1.3':
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
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Drop the tables
    $sql = "DROP TABLE $xartable[polls_info]";
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        // Report failed deletion attempt
        return false;
    }

    $sql = "DROP TABLE $xartable[polls]";
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        // Report failed deletion attempt
        return false;
    }

    // Delete any module variables
    xarModDelVar('polls', 'barscale');
    xarModDelVar('polls', 'comments');
    xarModDelVar('polls', 'defaultopts');
    xarModDelVar('polls', 'itemsperpage');
    xarModDelVar('polls', 'imggraph');
    xarModDelVar('polls', 'voteinterval');
    xarModDelVar('polls', 'previewresults');
    xarModDelVar('polls', 'uservotes');

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
