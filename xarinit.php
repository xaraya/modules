<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author St.Ego
 */
/**
 * initialise the xproject module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xproject_init()
{
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    
    $xProjects_table = $xarTables['xProjects'];
    $xProjects_fields = array('projectid'           =>  array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
                                'project_name'      =>  array('type'=>'varchar','size'=>255,'null'=>FALSE,'default'=>''),
                                'reference'         =>  array('type'=>'varchar','size'=>255,'null'=>FALSE,'default'=>''),
                                'description'       =>  array('type'=>'text','null'=>FALSE,'default'=>''),
                                'ownerid'           =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0'),
                                'clientid'          =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0'),
                                'status'            =>  array('type'=>'varchar','size'=>32,'null'=>FALSE,'default'=>''),
                                'importance'        =>  array('type'=>'integer','size'=>1,'null'=>FALSE,'default'=>'0'),
                                'priority'          =>  array('type'=>'integer','size'=>1,'null'=>FALSE,'default'=>'0'),
                                'private'           =>  array('type'=>'char','size'=>1,'null'=>FALSE,'default'=>''),
                                'projecttype'       =>  array('type'=>'char','size'=>64,'null'=>FALSE,'default'=>''),
                                'date_approved'     =>  array('type'=>'date','null'=>TRUE),
                                'planned_start_date'=>  array('type'=>'date','null'=>TRUE),
                                'planned_end_date'  =>  array('type'=>'date','null'=>TRUE),
                                'actual_start_date' =>  array('type'=>'date','null'=>TRUE),
                                'actual_end_date'   =>  array('type'=>'date','null'=>TRUE),
                                'hours_planned'     =>  array('type'=>'float', 'size' =>'decimal', 'width'=>6, 'decimals'=>2),
                                'hours_spent'       =>  array('type'=>'float', 'size' =>'decimal', 'width'=>6, 'decimals'=>2),
                                'hours_remaining'   =>  array('type'=>'float', 'size' =>'decimal', 'width'=>6, 'decimals'=>2),
                                'budget'            =>  array('type'=>'float', 'size' =>'decimal', 'width'=>12, 'decimals'=>2),
                                'estimate'          =>  array('type'=>'float', 'size' =>'decimal', 'width'=>12, 'decimals'=>2),
                                'associated_sites'  =>  array('type'=>'varchar','size'=>255,'null'=>FALSE,'default'=>'') );
    $query = xarDBCreateTable($xProjects_table,$xProjects_fields);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_ownerid',
                   'fields'    => array('ownerid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($xProjects_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_clientid',
                   'fields'    => array('clientid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($xProjects_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $features_table = $xarTables['xProject_features'];
    $features_fields = array('featureid'            =>  array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
                                'parentid'          =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0'),
                                'projectid'         =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0'),
                                'feature_name'      =>  array('type'=>'varchar','size'=>255,'null'=>FALSE,'default'=>''),
                                'details'           =>  array('type'=>'text','null'=>FALSE,'default'=>''),
                                'tech_notes'        =>  array('type'=>'text','null'=>FALSE,'default'=>''),
                                'importance'        =>  array('type'=>'integer','size'=>1,'null'=>FALSE,'default'=>'0'),
                                'date_approved'     =>  array('type'=>'date','null'=>TRUE),
                                'date_available'    =>  array('type'=>'date','null'=>TRUE) );
    $query = xarDBCreateTable($features_table,$features_fields);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_projectid',
                   'fields'    => array('projectid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($features_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $pages_table = $xarTables['xProject_pages'];
    $pages_fields = array('pageid'      =>  array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
                        'parentid'      =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0'),
                        'projectid'     =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0'),
                        'page_name'     =>  array('type'=>'varchar','size'=>255,'null'=>FALSE,'default'=>''),
                        'status'        =>  array('type'=>'varchar','size'=>32,'null'=>FALSE,'default'=>''),
                        'sequence'      =>  array('type'=>'float', 'size' =>'decimal', 'width'=>4, 'decimals'=>1),
                        'description'   =>  array('type'=>'text','null'=>FALSE,'default'=>''),
                        'relativeurl'   =>  array('type'=>'varchar','size'=>255,'null'=>TRUE) );
    $query = xarDBCreateTable($pages_table,$pages_fields);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_projectid',
                   'fields'    => array('projectid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($pages_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $log_table = $xarTables['xProject_log'];
    $log_fields = array('logid'         =>  array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
                        'projectid'     =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0'),
                        'userid'        =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0'),
                        'changetype'    =>  array('type'=>'varchar','size'=>255,'null'=>FALSE,'default'=>''),
                        'createdate'    =>  array('type'=>'datetime','null'=>TRUE,'default'=>''),
                        'details'       =>  array('type'=>'text','null'=>FALSE,'default'=>'') );
    $query = xarDBCreateTable($log_table,$log_fields);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_projectid',
                   'fields'    => array('projectid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($log_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $team_table = $xarTables['xProject_team'];
    $team_fields = array('projectid'    =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0'),
                        'projectrole'   =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0'),
                        'roleid'        =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0'),
                        'memberid'      =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0'),
                        'membersource'  =>  array('type'=>'varchar','size'=>32,'null'=>FALSE,'default'=>'') );
    $query = xarDBCreateTable($team_table,$team_fields);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_teamid',
                   'fields'    => array('projectid', 'roleid'),
                   'unique'    => TRUE);
    $query = xarDBCreateIndex($team_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $ddata_is_available = xarModIsAvailable('dynamicdata');
    if (!isset($ddata_is_available)) return;

    if (!$ddata_is_available) {
        $msg = xarML('Please activate the Dynamic Data module first...');
        xarErrorSet(XAR_USER_EXCEPTION, 'MODULE_NOT_ACTIVE',
                        new DefaultUserException($msg));
        return;
    }

    $projects_objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/xproject/xardata/projects.xml'));
    if (empty($projects_objectid))   {
        $msg = xarML('Failed to import projects.xml...');
        xarErrorSet(XAR_USER_EXCEPTION, 'MODULE_NOT_ACTIVE',
                        new DefaultUserException($msg));
        return;
    }
    // save the object id for later
    xarModSetVar('xproject','projects_objectid',$projects_objectid);

    $features_objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/xproject/xardata/features.xml'));
    if (empty($features_objectid))   {
        $msg = xarML('Failed to import features.xml...');
        xarErrorSet(XAR_USER_EXCEPTION, 'MODULE_NOT_ACTIVE',
                        new DefaultUserException($msg));
        return;
    }
    xarModSetVar('xproject','features_objectid',$features_objectid);


    $pages_objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/xproject/xardata/pages.xml'));
    if (empty($pages_objectid)) {
        $msg = xarML('Failed to import pages.xml...');
        xarErrorSet(XAR_USER_EXCEPTION, 'MODULE_NOT_ACTIVE',
                        new DefaultUserException($msg));
        return;
    }
    xarModSetVar('xproject','pages_objectid',$pages_objectid);

    $log_objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/xproject/xardata/log.xml'));
    if (empty($log_objectid))  {
        $msg = xarML('Failed to import log.xml...');
        xarErrorSet(XAR_USER_EXCEPTION, 'MODULE_NOT_ACTIVE',
                        new DefaultUserException($msg));
        return;
    }
    xarModSetVar('xproject','log_objectid',$log_objectid);
    
    $team_objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/xproject/xardata/team.xml'));
    if (empty($team_objectid))  {
        $msg = xarML('Failed to import team.xml...');
        xarErrorSet(XAR_USER_EXCEPTION, 'MODULE_NOT_ACTIVE',
                        new DefaultUserException($msg));
        return;
    }
    xarModSetVar('xproject','team_objectid',$team_objectid);
            

    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/xproject/xardata/usersettings.xml'));
    if (empty($objectid)) return;
    xarModSetVar('xproject','usersettings',$objectid);

    xarModSetVar('xproject', 'SupportShortURLs', 0);
    /* If you provide short URL encoding functions you might want to also
     * provide module aliases and have them set in the module's administration.
     * Use the standard module var names for useModuleAlias and aliasname.
     */
    xarModSetVar('xproject', 'useModuleAlias', false);
    xarModSetVar('xproject', 'aliasname', '');
    xarModSetVar('xproject', 'mymemberid', false);

//    xarBlockTypeRegister('xproject', 'first');
//    xarBlockTypeRegister('xproject', 'others');
    
    /**
     * Define instances for this module
     * Format is
     * setInstance(Module, Type, ModuleTable, IDField, NameField,
     *             ApplicationVar, LevelTable, ChildIDField, ParentIDField)
     *
     */

    $query1 = "SELECT DISTINCT projectid FROM $xarTables[xProjects]";

    $query2 = "SELECT DISTINCT project_name FROM $xarTables[xProjects]";
    
    $instances = array(
                        array('header' => 'Project ID:',
                                'query' => $query1,
                                'limit' => 20
                            ),
                        array('header' => 'Project Name:',
                                'query' => $query2,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('xproject','All',$instances);

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

   // Tasks and projects: pid, tid, owner?
    xarRegisterMask('ViewXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_ADMIN');
   // Groups: gid
    xarRegisterMask('ViewXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_ADMIN');
    return true;
}

function xproject_upgrade($oldversion)
{
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
            
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    
    $ddata_is_available = xarModIsAvailable('dynamicdata');
    if (!isset($ddata_is_available)) return;
    
    switch($oldversion) {
        case '0.2.0':
        case '1.0':
            $xproject_table = $xarTables['xProjects'];
            $features_table = $xarTables['xProject_features'];
            $pages_table = $xarTables['xProject_pages'];
            
            // ADD WEBSITEPROJECT FIELD TO PROJECTS
            $result = $datadict->ChangeTable($xproject_table, 'websiteproject C(1) NotNull');
            if (!$result) return;

            // ADD IMPORTANCE FIELD TO FEATURES
            $result = $datadict->ChangeTable($features_table, 'importance C(64) NotNull DEFAULT ""');
            if (!$result) return;

            // ADD SEQUENCE FIELD TO PAGES
            $result = $datadict->ChangeTable($pages_table, 'sequence F NotNull Default 0');
            if (!$result) return;
            
            // ADD RELATIVEURL CHECKBOX FIELD TO PROJECTS
            $result = $datadict->ChangeTable($pages_table, 'relativeurl C(1) NotNull');
            if (!$result) return;
            
            // REPLACE PROJECTS SCHEMA
            $projects_objectid = xarModGetVar('xproject','projects_objectid');
            if (!empty($projects_objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $projects_objectid));
            }
            $projects_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xproject/projects.xml'));
            if (empty($projects_objectid)) return;
            xarModSetVar('xproject','projects_objectid',$projects_objectid);
            
            // REPLACE FEATURES SCHEMA
            $features_objectid = xarModGetVar('xproject','features_objectid');
            if (!empty($features_objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $features_objectid));
            }
            $features_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xproject/features.xml'));
            if (empty($features_objectid)) return;
            xarModSetVar('xproject','features_objectid',$features_objectid);
        
            // REPLACE PAGES SCHEMA
            $pages_objectid = xarModGetVar('xproject','pages_objectid');
            if (!empty($pages_objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $pages_objectid));
            }
            $pages_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xproject/pages.xml'));
            if (empty($pages_objectid)) return;
            xarModSetVar('xproject','pages_objectid',$pages_objectid);
            
        case '1.1':
    
            $log_table = $xarTables['xProject_log'];
            $log_fields = array('logid'         =>  array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
                                'projectid'     =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>''),
                                'userid'        =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>''),
                                'changetype'    =>  array('type'=>'varchar','size'=>255,'null'=>FALSE,'default'=>''),
                                'createdate'    =>  array('type'=>'date','null'=>TRUE,'default'=>''),
                                'details'       =>  array('type'=>'text','null'=>FALSE,'default'=>'') );
            $query = xarDBCreateTable($log_table,$log_fields);
            if (empty($query)) return;
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_projectid',
                           'fields'    => array('projectid'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($log_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            $log_objectid = xarModGetVar('xproject','log_objectid');
            if (!empty($log_objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $log_objectid));
            }
            $log_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xproject/log.xml'));
            if (empty($log_objectid)) return;
            // save the object id for later
            xarModSetVar('xproject','log_objectid',$log_objectid);
            
            // CHANGE WEBSITEPROJECT CHECKBOX TO A TYPE SELECTION
            $projects_table = $xarTables['xProjects'];
            $result = $datadict->dropColumn($projects_table, 'websiteproject');
            if (!$result) return;
            $result = $datadict->addColumn($projects_table, 'projecttype C(64) NotNull');
            if (!$result) return;
            
            // REPLACE PROJECTS SCHEMA
            $projects_objectid = xarModGetVar('xproject','projects_objectid');
            if (!empty($projects_objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $projects_objectid));
            }
            $projects_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xproject/projects.xml'));
            if (empty($projects_objectid)) return;
            xarModSetVar('xproject','projects_objectid',$projects_objectid);
        
        
        case '1.2':
            // ADD REFERENCE FIELD TO PROJECTS TABLE
            $projects_table = $xarTables['xProjects'];
            $result = $datadict->addColumn($projects_table, 'reference C(250) NotNull');
            if (!$result) return;
            
        case '1.3':            
            // REPLACE PROJECTS SCHEMA
            $projects_objectid = xarModGetVar('xproject','projects_objectid');
            if (!empty($projects_objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $projects_objectid));
            }
            $projects_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xproject/projects.xml'));
            if (empty($projects_objectid)) return;
            xarModSetVar('xproject','projects_objectid',$projects_objectid);
            
        case '1.4':
            
            // CHANGE WEBSITEPROJECT CHECKBOX TO A TYPE SELECTION
            $projects_table = $xarTables['xProjects'];
            $result = $datadict->dropColumn($projects_table, 'date_approved');
            if (!$result) return;
            $result = $datadict->addColumn($projects_table, 'date_approved D');
            if (!$result) return;

        case '1.5':
    
            $team_table = $xarTables['xProject_team'];
            $team_fields = array('projectid'    =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0'),
                                'memberid'      =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0'),
                                'projectrole'   =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0') );
            $query = xarDBCreateTable($team_table,$team_fields);
            if (empty($query)) return;
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_teamid',
                           'fields'    => array('projectid', 'memberid'),
                           'unique'    => TRUE);
            $query = xarDBCreateIndex($team_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            
        case '1.6':
            $team_table = $xarTables['xProject_team'];
            $result = $datadict->addColumn($team_table, 'membersource C(32)');
            if (!$result) return;
            
        case '1.7':
            $log_table = $xarTables['xProject_log'];
            $result = $datadict->alterColumn($log_table, 'createdate T');
            if (!$result) return;
            
        case '1.8':
            $team_table = $xarTables['xProject_team'];
            $result = $datadict->addColumn($team_table, 'roleid I(11) NotNull Default 0');
            if (!$result) return;
            
        case '1.9':
            $team_table = $xarTables['xProject_team'];
            
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_teamid');
            $query = xarDBDropIndex($team_table, $index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_projectrole',
                           'fields'    => array('projectid', 'roleid'),
                           'unique'    => TRUE);
            $query = xarDBCreateIndex($team_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            
        case '2.0':
            $team_table = $xarTables['xProject_team'];
            
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_projectrole');
            $query = xarDBDropIndex($team_table, $index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_teamid',
                           'fields'    => array('projectid', 'memberid'),
                           'unique'    => TRUE);
            $query = xarDBCreateIndex($team_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            
            
        case '2.1':
            
            // REPLACE PROJECTS SCHEMA
            $projects_objectid = xarModGetVar('xproject','projects_objectid');
            if (!empty($projects_objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $projects_objectid));
            }
            $projects_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xproject/xardata/projects.xml'));
            if (empty($projects_objectid)) return;
            xarModSetVar('xproject','projects_objectid',$projects_objectid);
            
            // REPLACE FEATURES SCHEMA
            $features_objectid = xarModGetVar('xproject','features_objectid');
            if (!empty($features_objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $features_objectid));
            }
            $features_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xproject/xardata/features.xml'));
            if (empty($features_objectid)) return;
            xarModSetVar('xproject','features_objectid',$features_objectid);
            
        case '2.2':
            $team_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xproject/xardata/team.xml'));
            if (empty($team_objectid)) return;
            xarModSetVar('xproject','team_objectid',$team_objectid);
        
        
        
        case '2.3':
            $team_table = $xarTables['xProject_team'];
            $result = $datadict->alterColumn($team_table, 'projectrole C(32)');
            if (!$result) return;
        
        case '2.4':  
        case '2.5':
        case '2.6':
        case '2.7':
            xarModDelVar('xproject','usersettings');
            $objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xproject/xardata/usersettings.xml'));
            if (empty($objectid)) return;
            xarModSetVar('xproject','usersettings',$objectid);  
        case '2.8':  
            
        case '2.9':
            $projects_table = $xarTables['xProjects'];
            $result = $datadict->addColumn($projects_table, 'budget F NotNull Default 0');
            if (!$result) return;
            $result = $datadict->addColumn($projects_table, 'estimate F NotNull Default 0');
            if (!$result) return;
            
        case '3.0':
            
            // UPDATE PROJECTS SCHEMA
            $projects_objectid = xarModGetVar('xproject','projects_objectid');
            if (!empty($projects_objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $projects_objectid));
            }
            $projects_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xproject/xardata/projects.xml'));
            if (empty($projects_objectid)) return;
            xarModSetVar('xproject','projects_objectid',$projects_objectid);

        case '3.1':
            $pages_table = $xarTables['xProject_pages'];
            $result = $datadict->ChangeTable($pages_table, 'parentid I NotNull Default 0');
            if (!$result) return;

        case '3.2':
            $team_objectid = xarModGetVar('xproject','team_objectid');
            if (!empty($team_objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $team_objectid));
            }
            $team_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xproject/xardata/team.xml'));
            if (empty($team_objectid)) return;
            xarModSetVar('xproject','team_objectid',$team_objectid);

        case '3.3':
            break;
            
    }

    return true;
}

function xproject_delete()
{
    xarDBLoadTableMaintenanceAPI();

    $dbconn   =& xarDBGetConn();
    $xartable =  xarDBGetTables();

    $query = xarDBDropTable($xartable['xProjects']);
    $result =& $dbconn->Execute($query);

    $query = xarDBDropTable($xartable['xProject_features']);
    $result =& $dbconn->Execute($query);

    $query = xarDBDropTable($xartable['xProject_pages']);
    $result =& $dbconn->Execute($query);

    $query = xarDBDropTable($xartable['xProject_log']);
    $result =& $dbconn->Execute($query);

    $query = xarDBDropTable($xartable['xProject_team']);
    $result =& $dbconn->Execute($query);

    $projects_objectid = xarModGetVar('xproject','projects_objectid');
    if (!empty($projects_objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $projects_objectid));
    }
    xarModDelVar('xproject','projects_objectid');
    
    $features_objectid = xarModGetVar('xproject','features_objectid');
    if (!empty($features_objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $features_objectid));
    }
    xarModDelVar('xproject','features_objectid');

    $pages_objectid = xarModGetVar('xproject','pages_objectid');
    if (!empty($pages_objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $pages_objectid));
    }
    xarModDelVar('xproject','pages_objectid');

    $log_objectid = xarModGetVar('xproject','log_objectid');
    if (!empty($pages_objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $log_objectid));
    }
    xarModDelVar('xproject','log_objectid');

    $team_objectid = xarModGetVar('xproject','team_objectid');
    if (!empty($pages_objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $team_objectid));
    }
    xarModDelVar('xproject','team_objectid');

    $objectid = xarModGetVar('xproject','usersettings');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }
    xarModDelVar('xproject','usersettings');
    
     /* Remove any module aliases before deleting module vars */
    /* Assumes one module alias in this case */
    $aliasname =xarModGetVar('xproject','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias =='xproject')){
        xarModDelAlias($aliasname,'xproject');
    }

//    xarModAPIFunc('categories', 'admin', 'deletecat', array('cid' => xarModGetVar('xproject', 'mastercid')));
    /* Delete any module variables */
    xarModDelAllVars('xproject');

    //xarBlockTypeUnregister('xproject', 'first');
    //xarBlockTypeUnregister('xproject', 'others');

    /* Remove Masks and Instances
     * these functions remove all the registered masks and instances of a module
     * from the database. This is not strictly necessary, but it's good housekeeping.
     */
    xarRemoveMasks('xproject');
    xarRemoveInstances('xproject');

    return true;
}

?>