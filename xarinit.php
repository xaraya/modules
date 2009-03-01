<?php
/**
 * DOSSIER utility functions
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @author Chad Kraeft <cdavidkraeft@miragelab.com>
 * Based on labDossier (on PostNuke) by Chad Kraeft <cdavidkraeft@miragelab.com>
 */
/**
 * initialise the DOSSIER module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function dossier_init()
{
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    
    $dossier_table = $xarTables['dossier_contacts'];
    $fields = array(
        'contactid'    =>  array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'cat_id'       =>  array('type'=>'integer','null'=>TRUE),
        'userid'       =>  array('type'=>'integer','null'=>TRUE,'default'=>'NULL'),
        'agentuid'     =>  array('type'=>'integer','null'=>TRUE,'default'=>'NULL'),
        'private'      =>  array('type'=>'integer','size'=>'tiny'),
        'contactcode'  =>  array('type'=>'varchar','size'=>16,'null'=>TRUE,'default'=>'NULL'),
        'prefix'       =>  array('type'=>'varchar','size'=>6,'null'=>TRUE,'default'=>'NULL'),
        'lname'        =>  array('type'=>'varchar','size'=>64,'null'=>TRUE,'default'=>'NULL'),
        'fname'        =>  array('type'=>'varchar','size'=>64,'null'=>TRUE,'default'=>'NULL'),
        'sortname'     =>  array('type'=>'varchar','size'=>255,'null'=>TRUE,'default'=>'NULL'),
        'dateofbirth'  =>  array('type'=>'date','null'=>TRUE,'default'=>'NULL'),
        'title'        =>  array('type'=>'varchar','size'=>255,'null'=>TRUE,'default'=>'NULL'),
        'company'      =>  array('type'=>'varchar','size'=>255,'null'=>TRUE,'default'=>'NULL'),
        'sortcompany'  =>  array('type'=>'varchar','size'=>255,'null'=>TRUE,'default'=>'NULL'),
        'img'          =>  array('type'=>'varchar','size'=>255,'null'=>TRUE,'default'=>'NULL'),
        'phone_work'   =>  array('type'=>'varchar','size'=>80,'null'=>TRUE,'default'=>'NULL'),
        'phone_cell'   =>  array('type'=>'varchar','size'=>80,'null'=>TRUE,'default'=>'NULL'),
        'phone_fax'    =>  array('type'=>'varchar','size'=>80,'null'=>TRUE,'default'=>'NULL'),
        'phone_home'   =>  array('type'=>'varchar','size'=>80,'null'=>TRUE,'default'=>'NULL'),
        'email_1'      =>  array('type'=>'varchar','size'=>80,'null'=>TRUE,'default'=>'NULL'),
        'email_2'      =>  array('type'=>'varchar','size'=>80,'null'=>TRUE,'default'=>'NULL'),
        'chat_AIM'     =>  array('type'=>'varchar','size'=>80,'null'=>TRUE,'default'=>'NULL'),
        'chat_YIM'     =>  array('type'=>'varchar','size'=>80,'null'=>TRUE,'default'=>'NULL'),
        'chat_MSNM'    =>  array('type'=>'varchar','size'=>80,'null'=>TRUE,'default'=>'NULL'),
        'chat_ICQ'     =>  array('type'=>'varchar','size'=>80,'null'=>TRUE,'default'=>'NULL'),
        'contactpref'  =>  array('type'=>'varchar','size'=>80,'null'=>TRUE,'default'=>'NULL'),
        'mailinglocid' =>  array('type'=>'integer','null'=>TRUE,'default'=>'NULL'),
        'billinglocid' =>  array('type'=>'integer','null'=>TRUE,'default'=>'NULL'),
        'notes'        =>  array('type'=>'text'),
        'datecreated'  =>  array('type'=>'datetime','null'=>FALSE,'default'=>'0000-00-00 00:00:00'),
        'datemodified' =>  array('type'=>'datetime','null'=>FALSE)
    );
    $query = xarDBCreateTable($dossier_table,$fields);
    if (empty($query)) return; // throw back

    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    // add field indexes

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_userid',
                   'fields'    => array('userid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($dossier_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_sortcompany',
                   'fields'    => array('sortcompany'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($dossier_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_sortname',
                   'fields'    => array('sortname'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($dossier_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_phone_work',
                   'fields'    => array('phone_work'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($dossier_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_phone_cell',
                   'fields'    => array('phone_cell'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($dossier_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_phone_home',
                   'fields'    => array('phone_home'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($dossier_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_email_1',
                   'fields'    => array('email_1'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($dossier_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;  
    
    $locations_table = $xarTables['dossier_locations'];
    $fields = array(
        'locationid'    =>  array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'cat_id'        =>  array('type'=>'integer','null'=>TRUE),
        'address_1'     =>  array('type'=>'varchar','size'=>255,'null'=>TRUE,'default'=>'NULL'),
        'address_2'     =>  array('type'=>'varchar','size'=>255,'null'=>TRUE,'default'=>'NULL'),
        'city'          =>  array('type'=>'varchar','size'=>100,'null'=>TRUE,'default'=>'NULL'),
        'us_state'      =>  array('type'=>'varchar','size'=>60,'null'=>TRUE,'default'=>'NULL'),
        'postalcode'    =>  array('type'=>'varchar','size'=>16,'null'=>TRUE,'default'=>'NULL'),
        'country'       =>  array('type'=>'varchar','size'=>60,'null'=>TRUE,'default'=>'NULL'),
        'latitude'      =>  array('type'=>'varchar','size'=>16,'null'=>TRUE,'default'=>'NULL'),
        'longitude'     =>  array('type'=>'varchar','size'=>16,'null'=>TRUE,'default'=>'NULL')
    );
    $query = xarDBCreateTable($locations_table,$fields);
    if (empty($query)) return; // throw back

    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    // add field indexes

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_cat_id',
                   'fields'    => array('cat_id'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($locations_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_address_1',
                   'fields'    => array('address_1'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($locations_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_city',
                   'fields'    => array('city'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($locations_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_us_state',
                   'fields'    => array('us_state'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($locations_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_postalcode',
                   'fields'    => array('postalcode'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($locations_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_country',
                   'fields'    => array('country'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($locations_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $locationdata_table = $xarTables['dossier_locationdata'];
    $fields = array(
        'locationid'    =>  array('type'=>'integer','null'=>FALSE),
        'contactid'     =>  array('type'=>'integer','null'=>FALSE),
        'startdate'     =>  array('type'=>'date','null'=>TRUE),
        'enddate'       =>  array('type'=>'date','null'=>TRUE)
    );
    $query = xarDBCreateTable($locationdata_table,$fields);
    if (empty($query)) return; // throw back

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_contactloc',
                   'fields'    => array('locationid', 'contactid'),
                   'unique'    => TRUE);
    $query = xarDBCreateIndex($locationdata_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_contactid',
                   'fields'    => array('contactid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($locationdata_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $logs_table = $xarTables['dossier_logs'];
    $fields = array(
        'logid'         =>  array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'contactid'     =>  array('type'=>'integer','null'=>TRUE,'default'=>'NULL'),
        'ownerid'       =>  array('type'=>'integer','null'=>TRUE,'default'=>'NULL'),
        'logtype'       =>  array('type'=>'varchar','size'=>60,'null'=>TRUE,'default'=>'NULL'),
        'logdate'       =>  array('type'=>'datetime','null'=>TRUE),
        'createdate'    =>  array('type'=>'datetime','null'=>TRUE),
        'notes'         =>  array('type'=>'text')
    );
    $query = xarDBCreateTable($logs_table,$fields);
    if (empty($query)) return; // throw back

    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    // add field indexes

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_contactid',
                   'fields'    => array('contactid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($logs_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_ownerid',
                   'fields'    => array('ownerid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($logs_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_logtype',
                   'fields'    => array('logtype'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($logs_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_logdate',
                   'fields'    => array('logdate'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($logs_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $reminders_table = $xarTables['dossier_reminders'];
    $fields = array(
        'reminderid'    =>  array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'contactid'     =>  array('type'=>'integer','null'=>TRUE,'default'=>'NULL'),
        'ownerid'       =>  array('type'=>'integer','null'=>TRUE,'default'=>'NULL'),
        'reminderdate'  =>  array('type'=>'datetime','null'=>TRUE),
        'warningtime'   =>  array('type'=>'integer','null'=>TRUE),
        'notes'         =>  array('type'=>'text')
    );
    $query = xarDBCreateTable($reminders_table,$fields);
    if (empty($query)) return; // throw back

    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    // add field indexes

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_contactid',
                   'fields'    => array('contactid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($reminders_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_ownerid',
                   'fields'    => array('ownerid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($reminders_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_reminderdate',
                   'fields'    => array('reminderdate'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($reminders_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $addressbook_links_table = $xarTables['dossier_addressbook_links'];
    $fields = array(
        'contactid'         =>  array('type'=>'integer','null'=>FALSE,'primary_key'=>TRUE),
        'addressbook_id'    =>  array('type'=>'integer','null'=>FALSE)
    );
    $query = xarDBCreateTable($addressbook_links_table,$fields);
    if (empty($query)) return; // throw back

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_addressbook_link',
                   'fields'    => array('contactid','addressbook_id'),
                   'unique'    => TRUE);
    $query = xarDBCreateIndex($addressbook_links_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $friendslist_table = $xarTables['dossier_friendslist'];
    $fields = array(
        'contactid'    =>  array('type'=>'integer','null'=>FALSE),
        'friendid'     =>  array('type'=>'integer','null'=>FALSE),
        'dateadded'     =>  array('type'=>'date','null'=>TRUE),
        'featured'       =>  array('type'=>'date','null'=>TRUE),
        'private'       =>  array('type'=>'date','null'=>TRUE)
    );
    $query = xarDBCreateTable($friendslist_table,$fields);
    if (empty($query)) return; // throw back

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_friendid',
                   'fields'    => array('contactid', 'friendid'),
                   'unique'    => TRUE);
    $query = xarDBCreateIndex($friendslist_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_featured',
                   'fields'    => array('featured'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($friendslist_table,$index);
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
    
    $relationships_table = $xarTables['dossier_relationships'];
    $fields = array(
        'relationshipid'    =>  array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'contactid'         =>  array('type'=>'integer','null'=>FALSE),
        'connectedid'       =>  array('type'=>'integer','null'=>FALSE),
        'relationship'      =>  array('type'=>'varchar','size'=>32,'null'=>TRUE,'default'=>'NULL'),
        'dateadded'         =>  array('type'=>'date','null'=>TRUE),
        'private'           =>  array('type'=>'integer','size'=>'small','null'=>FALSE),
        'notes'             =>  array('type'=>'text')
    );
    $query = xarDBCreateTable($relationships_table,$fields);
    if (empty($query)) return; // throw back

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_contactid',
                   'fields'    => array('contactid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($relationships_table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_connectedid',
                   'fields'    => array('connectedid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($relationships_table,$index);
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

	/* Create DD objects */
    $moduleid = xarModGetIdFromName('dossier');
    
    $contacts_objectinfo = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 1));
    if($contacts_objectinfo->objectid == false) {
    	$contacts_objectinfo = xarModAPIFunc('dynamicdata','util','import',
                                    array('file' => 'modules/dossier/xardata/contacts.xml'));
    	if (empty($contacts_objectinfo)) return;
    }
    
    $locations_objectinfo = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 2));
    if($locations_objectinfo->objectid == false) {
    	$locations_objectinfo = xarModAPIFunc('dynamicdata','util','import',
                                    array('file' => 'modules/dossier/xardata/locations.xml'));
    	if (empty($locations_objectinfo)) return;
    }
    
    $locationdata_objectinfo = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 3));
    if($locationdata_objectinfo->objectid == false) {
    	$locationdata_objectinfo = xarModAPIFunc('dynamicdata','util','import',
                                    array('file' => 'modules/dossier/xardata/locationdata.xml'));
    	if (empty($locationdata_objectinfo)) return;
    }
    
    $logs_objectinfo = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 4));
    if($logs_objectinfo->objectid == false) {
    	$logs_objectinfo = xarModAPIFunc('dynamicdata','util','import',
                                    array('file' => 'modules/dossier/xardata/logs.xml'));
    	if (empty($logs_objectinfo)) return;
    }
    
    $reminders_objectinfo = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 5));
    if($reminders_objectinfo->objectid == false) {
    	$reminders_objectinfo = xarModAPIFunc('dynamicdata','util','import',
                                    array('file' => 'modules/dossier/xardata/reminders.xml'));
    	if (empty($reminders_objectinfo)) return;
    }
    
    $usersettings = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/dossier/xardata/usersettings.xml'));
    if (empty($usersettings)) return;
    xarModSetVar('dossier','usersettings',$usersettings);

    $modulesettings = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/dossier/xardata/modulesettings.xml'));
    if (empty($modulesettings)) return;
    xarModSetVar('dossier','modulesettings',$modulesettings);
    
    if (xarModIsAvailable('categories')) {
        $contactcid = xarModAPIFunc('categories',
            'admin',
            'create',
            Array('name' => 'Dossier Categories',
                'description' => 'Dossier Contact Type Categories',
                'parent_id' => 0));
        /* Store the generated master category id and the number of possible categories
         * Note: you can have more than 1 mastercid (cfr. articles module)
         */
        xarModSetVar('dossier', 'contactcid', $contactcid);
        $contactcategories = array();
        $contactcategories[] = array('name' => "Private",
            'description' => "Personal contacts");
        $contactcategories[] = array('name' => "Staff",
            'description' => "Current and former employees");
        $contactcategories[] = array('name' => "Leads",
            'description' => "Potential clients");
        $contactcategories[] = array('name' => "Clients",
            'description' => "Existing customers");
        $contactcategories[] = array('name' => "Vendors",
            'description' => "Business to business contacts");
        foreach($contactcategories as $subcat) {
            $contactsubcid = xarModAPIFunc('categories',
                'admin',
                'create',
                Array('name' => $subcat['name'],
                    'description' => $subcat['description'],
                    'parent_id' => $contactcid));
        }
        
        $locationcid = xarModAPIFunc('categories',
            'admin',
            'create',
            Array('name' => 'Dossier Locations',
                'description' => 'Dossier Contact Locations',
                'parent_id' => 0));
        /* Store the generated master category id and the number of possible categories
         * Note: you can have more than 1 mastercid (cfr. articles module)
         */
        xarModSetVar('dossier', 'locationcid', $locationcid);
        $locationcategories = array();
        $locationcategories[] = array('name' => "Shipping",
            'description' => "Shipping Address");
        $locationcategories[] = array('name' => "Billing",
            'description' => "Billing Address");
        foreach($locationcategories as $subcat) {
            $locationsubcid = xarModAPIFunc('categories',
                'admin',
                'create',
                Array('name' => $subcat['name'],
                    'description' => $subcat['description'],
                    'parent_id' => $locationcid));
        }
        
        $logcid = xarModAPIFunc('categories',
            'admin',
            'create',
            Array('name' => 'Dossier Log Types',
                'description' => 'Dossier Contact Log Types',
                'parent_id' => 0));
        /* Store the generated master category id and the number of possible categories
         * Note: you can have more than 1 mastercid (cfr. articles module)
         */
        xarModSetVar('dossier', 'logcid', $logcid);
        $logcategories = array();
        $logcategories[] = array('name' => "Phone Call",
            'description' => "");
        $logcategories[] = array('name' => "Email",
            'description' => "");
        $logcategories[] = array('name' => "Meeting",
            'description' => "");
        foreach($logcategories as $subcat) {
            $logsubcid = xarModAPIFunc('categories',
                'admin',
                'create',
                Array('name' => $subcat['name'],
                    'description' => $subcat['description'],
                    'parent_id' => $logcid));
        }
    }
    
    // hook for user menu
    if (!xarModRegisterHook('item', 'usermenu', 'GUI', 'dossier', 'user', 'usermenu')) {
        return false;
    }
    
    // meant for roles/registration: create new contact entry for each registered user
    if (!xarModRegisterHook('item', 'create', 'API', 'dossier', 'admin', 'createhook')) {
        return false;
    }
    
    xarModAPIFunc('modules','admin','enablehooks',
                  array('callerModName' => 'roles', 'hookModName' => 'dossier'));

    xarModSetVar('dossier', 'displaytitle', 'Contact Relationship Manager');
    xarModSetVar('dossier', 'itemsperpage', 30);
    xarModSetVar('dossier', 'SupportShortURLs', 0);

    /*********************************************************************
    * Define instances for this module
    * Format is
    * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
    *********************************************************************/
    
    $instances = array(
                       array('header' => 'external', // this keyword indicates an external "wizard"
                             'query'  => xarModURL('dossier', 'admin', 'privileges'),
                             'limit'  => 0
                            )
                    );
    xarDefineInstance('dossier', 'Contact', $instances);
    xarDefineInstance('dossier', 'Reminders', $instances);
    xarDefineInstance('dossier', 'Log', $instances);
    xarDefineInstance('dossier', 'Friend', $instances);


    /*********************************************************************
    * Register the module components that are privileges objects
    * Format is
    * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    *********************************************************************/
    xarRegisterMask('PublicDossierAccess',      'All','dossier','Contact','All:All:All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ClientDossierAccess',      'All','dossier','Contact','All:All:All:All','ACCESS_READ');
    xarRegisterMask('TeamDossierAccess',        'All','dossier','Contact','All:All:All:All','ACCESS_COMMENT');
    xarRegisterMask('AuditDossier',             'All','dossier','Contact','All:All:All:All','ACCESS_MODERATE');
    xarRegisterMask('AdminDossier',             'All','dossier','Contact','All:All:All:All','ACCESS_ADMIN');
    
    xarRegisterMask('ViewDossierReminders',        'All','dossier','Reminders','All:All:All:All','ACCESS_READ');
    xarRegisterMask('UseDossierReminders',         'All','dossier','Reminders','All:All:All:All','ACCESS_COMMENT');
    xarRegisterMask('ShareDossierReminders',       'All','dossier','Reminders','All:All:All:All','ACCESS_MODERATE');
    xarRegisterMask('AuditDossierReminders',       'All','dossier','Reminders','All:All:All:All','ACCESS_ADMIN');
    
    xarRegisterMask('ReadDossierLog',       'All','dossier','Log','All:All:All:All','ACCESS_READ');
    xarRegisterMask('AddDossierLog',        'All','dossier','Log','All:All:All:All','ACCESS_COMMENT');
    xarRegisterMask('MyDossierLog',         'All','dossier','Log','All:All:All:All','ACCESS_MODERATE');
    xarRegisterMask('AuditDossierLog',      'All','dossier','Log','All:All:All:All','ACCESS_ADMIN');
    
    xarRegisterMask('ViewDossierFriends',   'All','dossier','Friend','All:All:All:All','ACCESS_READ');
    xarRegisterMask('MyDossierFriends',     'All','dossier','Friend','All:All:All:All','ACCESS_COMMENT');
    xarRegisterMask('AuditDossierFriends',  'All','dossier','Friend','All:All:All:All','ACCESS_MODERATE');


    // Initialisation successful
    return true;
}

/**
 * upgrade the module from an old version
 * This function can be called multiple times
 */
function dossier_upgrade($oldversion)
{
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    
    $ddata_is_available = xarModIsAvailable('dynamicdata');
    if (!isset($ddata_is_available)) return;

    if (!$ddata_is_available) {
        $msg = xarML('Please activate the Dynamic Data module first...');
        xarErrorSet(XAR_USER_EXCEPTION, 'MODULE_NOT_ACTIVE',
                        new DefaultUserException($msg));
        return;
    }

    switch($oldversion) {
        case '1.0.0':
            $contacts_table = $xarTables['dossier_contacts'];
            $result = $datadict->addColumn($contacts_table, 'datecreated T NotNull DEFAULT NOW()');
            if (!$result) return;
        case '1.0.1':
    
            // hook for user menu
            if (!xarModRegisterHook('item', 'usermenu', 'GUI', 'dossier', 'user', 'usermenu')) {
                return false;
            }
        case '1.0.2':
            $contacts_table = $xarTables['dossier_contacts'];
            $result = $datadict->addColumn($contacts_table, 'userid I(11) NotNull DEFAULT 0');
            if (!$result) return;

        case '1.0.3':
            $contacts_objectid = xarModGetVar('dossier','contacts_objectid');
            if (!empty($contacts_objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $contacts_objectid));
            }
            xarModDelVar('dossier','contacts_objectid');
            $contacts_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/dossier/xardata/contacts.xml'));
            if (empty($contacts_objectid)) return;
            xarModSetVar('dossier','contacts_objectid',$contacts_objectid);
        case '1.0.4':
            if (!xarModRegisterHook('item', 'create', 'API', 'dossier', 'admin', 'createhook')) {
                return false;
            }
        case '1.0.5':
        case '1.0.6':
            $dossier_table = $xarTables['dossier_contacts'];
            $locations_table = $xarTables['dossier_locations'];
            $locationdata_table = $xarTables['dossier_locationdata'];
            $reminders_table = $xarTables['dossier_reminders'];
            $logs_table = $xarTables['dossier_logs'];
            $addressbook_links_table = $xarTables['dossier_addressbook_links'];
            
            // add field indexes
/*
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_userid',
                           'fields'    => array('userid'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($dossier_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_sortcompany',
                           'fields'    => array('sortcompany'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($dossier_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_sortname',
                           'fields'    => array('sortname'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($dossier_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_phone_work',
                           'fields'    => array('phone_work'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($dossier_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_phone_cell',
                           'fields'    => array('phone_cell'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($dossier_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_phone_home',
                           'fields'    => array('phone_home'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($dossier_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_email_1',
                           'fields'    => array('email_1'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($dossier_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;    
    
            // add field indexes
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_cat_id',
                           'fields'    => array('cat_id'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($locations_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_address_1',
                           'fields'    => array('address_1'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($locations_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_city',
                           'fields'    => array('city'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($locations_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_us_state',
                           'fields'    => array('us_state'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($locations_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_postalcode',
                           'fields'    => array('postalcode'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($locations_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_country',
                           'fields'    => array('country'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($locations_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_contactid',
                           'fields'    => array('contactid'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($locationdata_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
    
            // add field indexes
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_contactid',
                           'fields'    => array('contactid'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($logs_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_ownerid',
                           'fields'    => array('ownerid'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($logs_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_logtype',
                           'fields'    => array('logtype'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($logs_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_logdate',
                           'fields'    => array('logdate'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($logs_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
    
            // add field indexes
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_contactid',
                           'fields'    => array('contactid'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($reminders_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_ownerid',
                           'fields'    => array('ownerid'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($reminders_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_reminderdate',
                           'fields'    => array('reminderdate'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($reminders_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            
            $fields = array(
                'contactid'         =>  array('type'=>'integer','null'=>FALSE,'primary_key'=>TRUE),
                'addressbook_id'    =>  array('type'=>'integer','null'=>FALSE)
            );
            $query = xarDBCreateTable($addressbook_links_table,$fields);
            if (empty($query)) return; // throw back
        
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_addressbook_link',
                           'fields'    => array('contactid','addressbook_id'),
                           'unique'    => TRUE);
            $query = xarDBCreateIndex($addressbook_links_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            */
            
        case '1.1.0':
    
            $friendslist_table = $xarTables['dossier_friendslist'];
            $fields = array(
                'contactid'    =>  array('type'=>'integer','null'=>FALSE),
                'friendid'     =>  array('type'=>'integer','null'=>FALSE),
                'dateadded'     =>  array('type'=>'date','null'=>TRUE),
                'featured'       =>  array('type'=>'date','null'=>TRUE),
                'private'       =>  array('type'=>'date','null'=>TRUE)
            );
            $query = xarDBCreateTable($friendslist_table,$fields);
            if (empty($query)) return; // throw back
        
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_friendid',
                           'fields'    => array('contactid', 'friendid'),
                           'unique'    => TRUE);
            $query = xarDBCreateIndex($friendslist_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_featured',
                           'fields'    => array('featured'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($friendslist_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            
            xarRegisterMask('Friendslist',   'All','dossier','Friends','All:All:All','ACCESS_COMMENT');
        case '1.2.0':
        case '1.2.1':
        case '1.2.2':
        case '1.2.10':
        case '1.2.11':
        case '1.3.1':            
        case '1.3.2':           
        case '1.3.3':        
        
            xarRemoveMasks('dossier');
            xarRemoveInstances('dossier');
    
            $instances = array(
                               array('header' => 'external', // this keyword indicates an external "wizard"
                                     'query'  => xarModURL('dossier', 'admin', 'privileges'),
                                     'limit'  => 0
                                    )
                            );
            xarDefineInstance('dossier', 'Contact', $instances);
            xarDefineInstance('dossier', 'Reminders', $instances);
            xarDefineInstance('dossier', 'Log', $instances);
            xarDefineInstance('dossier', 'Friend', $instances);
            
            xarRegisterMask('PublicDossierAccess',      'All','dossier','Contact','All:All:All:All','ACCESS_OVERVIEW');
            xarRegisterMask('ClientDossierAccess',      'All','dossier','Contact','All:All:All:All','ACCESS_READ');
            xarRegisterMask('TeamDossierAccess',        'All','dossier','Contact','All:All:All:All','ACCESS_COMMENT');
            xarRegisterMask('AuditDossier',             'All','dossier','Contact','All:All:All:All','ACCESS_MODERATE');
            xarRegisterMask('AdminDossier',             'All','dossier','Contact','All:All:All:All','ACCESS_ADMIN');
            
            xarRegisterMask('ViewDossierReminders',        'All','dossier','Reminders','All:All:All:All','ACCESS_READ');
            xarRegisterMask('UseDossierReminders',         'All','dossier','Reminders','All:All:All:All','ACCESS_COMMENT');
            xarRegisterMask('ShareDossierReminders',       'All','dossier','Reminders','All:All:All:All','ACCESS_MODERATE');
            xarRegisterMask('AuditDossierReminders',       'All','dossier','Reminders','All:All:All:All','ACCESS_ADMIN');
            
            xarRegisterMask('ReadDossierLog',       'All','dossier','Log','All:All:All:All','ACCESS_READ');
            xarRegisterMask('AddDossierLog',        'All','dossier','Log','All:All:All:All','ACCESS_COMMENT');
            xarRegisterMask('MyDossierLog',         'All','dossier','Log','All:All:All:All','ACCESS_MODERATE');
            xarRegisterMask('AuditDossierLog',      'All','dossier','Log','All:All:All:All','ACCESS_ADMIN');
            
            xarRegisterMask('ViewDossierFriends',   'All','dossier','Friend','All:All:All:All','ACCESS_READ');
            xarRegisterMask('MyDossierFriends',     'All','dossier','Friend','All:All:All:All','ACCESS_COMMENT');
            xarRegisterMask('AuditDossierFriends',  'All','dossier','Friend','All:All:All:All','ACCESS_MODERATE');
        case '1.3.4':
            $logs_table = $xarTables['dossier_logs'];
            $result = $datadict->addColumn($logs_table, 'createdate T');
            if (!$result) return;
            
        case '1.3.5':
    
            $relationships_table = $xarTables['dossier_relationships'];
            $fields = array(
                'relationshipid'    =>  array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
                'contactid'         =>  array('type'=>'integer','null'=>FALSE),
                'connectedid'       =>  array('type'=>'integer','null'=>FALSE),
                'relationship'      =>  array('type'=>'varchar','size'=>32,'null'=>TRUE,'default'=>'NULL'),
                'dateadded'         =>  array('type'=>'date','null'=>TRUE),
                'private'           =>  array('type'=>'integer','size'=>'small','null'=>FALSE),
                'notes'             =>  array('type'=>'text')
            );
            $query = xarDBCreateTable($relationships_table,$fields);
            if (empty($query)) return; // throw back
        
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_contactid',
                           'fields'    => array('contactid'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($relationships_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        
            $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_connectedid',
                           'fields'    => array('connectedid'),
                           'unique'    => FALSE);
            $query = xarDBCreateIndex($relationships_table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            
        case '1.4.0':
            break;
    }

    return true;
}

/**
 * delete the DOSSIER module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function dossier_delete()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    
    $sql = xarDBDropTable($xartable['dossier_contacts']);
    if (empty($sql)) return;
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0 && false) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $sql = xarDBDropTable($xartable['dossier_locations']);
    if (empty($sql)) return;
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0 && false) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $sql = xarDBDropTable($xartable['dossier_locationdata']);
    if (empty($sql)) return;
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0 && false) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $sql = xarDBDropTable($xartable['dossier_logs']);
    if (empty($sql)) return;
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0 && false) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $sql = xarDBDropTable($xartable['dossier_reminders']);
    if (empty($sql)) return;
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0 && false) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $sql = xarDBDropTable($xartable['dossier_addressbook_links']);
    if (empty($sql)) return;
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0 && false) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $moduleid = xarModGetIdFromName('dossier');
    
    $objectinfo = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 1));
    if($objectinfo->objectid) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectinfo->objectid));
	}
    
    $objectinfo = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 2));
    if($objectinfo->objectid) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectinfo->objectid));
	}
    
    $objectinfo = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 3));
    if($objectinfo->objectid) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectinfo->objectid));
	}
    
    $objectinfo = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 4));
    if($objectinfo->objectid) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectinfo->objectid));
	}
    
    $objectinfo = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 5));
    if($objectinfo->objectid) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectinfo->objectid));
	}

    $usersettings = xarModGetVar('dossier','usersettings');
    if (!empty($usersettings)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $usersettings));
    }
    xarModDelVar('dossier','usersettings');

    $modulesettings = xarModGetVar('dossier','modulesettings');
    if (!empty($modulesettings)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $modulesettings));
    }
    xarModDelVar('dossier','modulesettings');
    
    if (xarModIsAvailable('categories')) {
        $contactcid = xarModGetVar('dossier', 'contactcid');
        xarModAPIFunc('categories',
                    'admin',
                    'deletecat',
                    Array('cid' => $contactcid));
        $locationcid = xarModGetVar('dossier', 'locationcid');
        xarModAPIFunc('categories',
                    'admin',
                    'deletecat',
                    Array('cid' => $locationcid));
        $logcid = xarModGetVar('dossier', 'logcid');
        xarModAPIFunc('categories',
                    'admin',
                    'deletecat',
                    Array('cid' => $logcid));
        xarErrorFree();
    }

    // Remove remaining Variables, Masks, and Instances
    xarModDelAllVars('dossier');
    xarRemoveMasks('dossier');
    xarRemoveInstances('dossier');

    // Deletion successful
    return true;
}
?>
