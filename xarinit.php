<?php
/**
 * SIGMApersonnel initialization functions
 *
 * @package modules
 * @copyright (C) 2005-2006 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author MichelV.
 */

/**
 * initialise the sigmapersonnel module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function sigmapersonnel_init()
{
    // Get database setup
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $sigmapersonneltable = $xartable['sigmapersonnel_person'];

xarDBLoadTableMaintenanceAPI();

// Rewrite
    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    $fields = "
      xar_personid      I         AUTO       PRIMARY,
      xar_userid        I NOTNULL default 0,
      xar_pnumber       I NOTNULL default 0,
      xar_persstatus    I NOTNULL default 0,
      xar_firstname     C(100) NOTNULL DEFAULT '',
      xar_lastname      C(100) NOTNULL DEFAULT '',
      xar_tussenvgsl    C(100) NOTNULL DEFAULT '',
      xar_initials      C(50) NOTNULL DEFAULT '',
      xar_sex           C(10) NOTNULL DEFAULT '',
      xar_title         C(100) NOTNULL DEFAULT '',
      xar_street        C(100) NOTNULL DEFAULT '',
      xar_zip           C(20) NOTNULL DEFAULT '',
      xar_cityid        I NOTNULL default 0,
      xar_phonehome     C(100) NOTNULL DEFAULT '',
      xar_mobile        C(100) NOTNULL DEFAULT '',
      xar_phonework     C(100) NOTNULL DEFAULT '',
      xar_email         C(100) NOTNULL DEFAULT '',
      xar_privphonehome L NOTNULL default 0,
      xar_privwork      L NOTNULL default 0,
      xar_privemail     L NOTNULL default 0,
      xar_privbirthdate L NOTNULL default 0,
      xar_privaddress   L NOTNULL default 0,
      xar_privphonework L NOTNULL default 0,
      xar_contactname   C(100) NOTNULL DEFAULT '',
      xar_contactphone  C(100) NOTNULL DEFAULT '',
      xar_contactstreet C(100) NOTNULL DEFAULT '',
      xar_contactcityid I NOTNULL default 0,
      xar_contactrelation C(100) NOTNULL DEFAULT '',
      xar_contactmobile C(100) NOTNULL DEFAULT '',
      xar_birthdate     I default 0,
      xar_birthplace    C(100) NOTNULL DEFAULT '',
      xar_nrkdistrict   I NOTNULL default 0,
      xar_nrknumber     I NOTNULL default 0,
      xar_ehbonr        I NOTNULL default 0,
      xar_ehboplus      L NOTNULL default 0,
      xar_ehbodate      I default NULL,
      xar_ehboplace     C(100) NOTNULL DEFAULT '',
      xar_dateintake    I default 0,
      xar_intakeby      C(100) NOTNULL DEFAULT '',
      xar_dateemploy    I NOTNULL default 0,
      xar_dateout       I NOTNULL default 0,
      xar_dateouttalk   I NOTNULL default 0,
      xar_outreason     X2,
      xar_outtalkwith   C(100) NOTNULL DEFAULT '',
      xar_dateshoes     I NOTNULL default 0,
      xar_sizeshoes     I NOTNULL default 0,
      xar_banknr        C(15) NOTNULL  DEFAULT '',
      xar_bankplaceid   I NOTNULL default 0,
      xar_others        X2,
      xar_educationremarks X2,
      xar_lastmodified  T DEFTIMESTAMP,
      xar_lastmodifiedby I NOTNULL default 0
    ";

    /* C:  Varchar, capped to 255 characters.
       X:  Larger varchar, capped to 4000 characters
       XL: For Oracle, returns CLOB, otherwise the largest varchar size.
       C2: Multibyte varchar
       X2: Multibyte varchar (largest size)
       B:  BLOB (binary large object)
       D:  Date
       T:  Datetime or Timestamp
       L:  Integer field suitable for storing booleans (0 or 1)
       I:  Integer (mapped to I4)
       I1: 1-byte integer
       I2: 2-byte integer
       I4: 4-byte integer
       I8: 8-byte integer
       F:  Floating point number
       N:  Numeric or decimal number
    */
    /*
       AUTO          For autoincrement numbers and sets NOTNULL also.
       KEY           Primary key field. Sets NOTNULL also.
       PRIMARY       Same as KEY.
       DEFAULT       The default value. Character strings are auto-quoted unless
                        the string begins and ends with spaces, eg ' SYSDATE '.
       NOTNULL       If field is not null.
       DEFDATE       Set default value to call function to get today's date.
       DEFTIMESTAMP  Set default to call function to get today's datetime.
       NOQUOTE       Prevents autoquoting of default string values.
       CONSTRAINTS   Additional constraints defined at end of field definition.
    */

    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($sigmapersonneltable, $fields);
    if (!$result) {return;}


    // Table for presence
    $presencetable = $xartable['sigmapersonnel_presence'];
    $fields = " xar_pid       I         AUTO       PRIMARY,
                xar_userid    I NOTNULL default 0,
                xar_personid  I NOTNULL default 0,
                xar_start     I default 0,
                xar_end       I default 0,
                xar_typeid    I default 0
                ";
    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($presencetable, $fields);
    if (!$result) {return;}

    // Table for Presence types
    $presencetypestable = $xartable['sigmapersonnel_presencetypes'];
    $fields = array(
        'xar_typeid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_type'=>array('null'=>TRUE, 'type'=>'varchar','size'=>100, 'default'=>'NULL'));
    $query = xarDBCreateTable($presencetypestable, $fields);
    if (empty($query)) return; // throw back
    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Table for places/Cities
    $citiestable = $xartable['sigmapersonnel_cities'];
    $fields = array(
        'xar_cityid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_city'=>array('null'=>TRUE, 'type'=>'varchar','size'=>100, 'default'=>'NULL'));
    $query = xarDBCreateTable($citiestable, $fields);
    if (empty($query)) return; // throw back
    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Table for status
    $statustable = $xartable['sigmapersonnel_status'];
    $fields = array(
        'xar_statusid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_statustype'=>array('null'=>TRUE, 'type'=>'varchar','size'=>100, 'default'=>'NULL'));
    $query = xarDBCreateTable($statustable, $fields);
    if (empty($query)) return; // throw back
    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Table for Districts
    $districtstable = $xartable['sigmapersonnel_districts'];
    $fields = array(
        'xar_districtid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_district'=>array('null'=>TRUE, 'type'=>'varchar','size'=>100, 'default'=>'NULL'));
    $query = xarDBCreateTable($districtstable, $fields);
    if (empty($query)) return; // throw back
    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;


    // If Categories API loaded and available, generate proprietary
    // module master category cid and child subcids
    if (xarModIsAvailable('categories')) {
        $sigmapersonnelcid = xarModAPIFunc('categories',
                                           'admin',
                                           'create',
                                            Array('name' => 'sigmapersonneltypes',
                                                  'description' => 'SIGMA personnel Groups',
                                                  'parent_id' => 0));
        // Note: you can have more than 1 mastercid (cfr. articles module)
        xarModSetVar('sigmapersonnel', 'number_of_categories', 2);
        xarModSetVar('sigmapersonnel', 'mastercids', $sigmapersonnelcid);
        $sigmapersonnelcategories = array();
        $sigmapersonnelcategories[] = array('name' => "group one",
            'description' => "description one");
        $sigmapersonnelcategories[] = array('name' => "group two",
            'description' => "description two");
        $sigmapersonnelcategories[] = array('name' => "group three",
            'description' => "description three");
        foreach($sigmapersonnelcategories as $subcat) {
            $sigmapersonnelsubcid = xarModAPIFunc('categories',
                'admin',
                'create',
                Array('name' => $subcat['name'],
                    'description' =>
                    $subcat['description'],
                    'parent_id' => $sigmapersonnelcid));
        }
    }

    // Set up an initial value for a module variable.
    xarModSetVar('sigmapersonnel', 'defaultstatus', 1);
    xarModSetVar('sigmapersonnel', 'itemsperpage', 10);
    xarModSetVar('sigmapersonnel', 'OnCallID', 1);
    // If your module supports short URLs, the website administrator should
    // be able to turn it on or off in your module administration
    xarModSetVar('sigmapersonnel', 'SupportShortURLs', 0);
    // Register Block types (this *should* happen at activation/deactivation)
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'sigmapersonnel',
                'blockType' => 'statusall'))) return;
    // Register blocks
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'sigmapersonnel',
                'blockType' => 'lastentry'))) return;
    // Register our hooks that we are providing to other modules.  The sigmapersonnel
    // module shows an sigmapersonnel hook in the form of the user menu.
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'sigmapersonnel', 'user', 'usermenu')) {
        return false;
    }
    // Set the dyn data object that we use as the module parameters
    $path = "modules/sigmapersonnel/xardata/";
    /*
     * The Cities Object
     */
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'sp_cities-def.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('sigmapersonnel','citiesobjectid',$objectid);
/*
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'mc_cities-data.xml'));
    if (empty($objectid)) return;
*/
    /*
     * The Districts Object
     */
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'sp_districts-def.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('sigmapersonnel','districtsobjectid',$objectid);
/*
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'mc_districts-data.xml'));
    if (empty($objectid)) return;
*/
    /*
     * The Presencetypes Object
     */
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'sp_presencetypes-def.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('sigmapersonnel','presencetypesobjectid',$objectid);

    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'sp_presencetypes-data.xml'));
    if (empty($objectid)) return;

    /*
     * The PersonStatus Object
     */
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'sp_status-def.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('sigmapersonnel','statusobjectid',$objectid);

    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'sp_status-data.xml'));
    if (empty($objectid)) return;

    /**
     * Define instances for this module
     * Format is
     * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
     */
    /*********************************************************************
    * Define instances for this module
    * Format is
    * xarDefineInstance(Module,Component,Querystring,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
    *********************************************************************/
     $xartable = xarDBGetTables();
     $instances = array(
     array('header' => 'external', // this keyword indicates an external "wizard"
     'query'  => xarModURL('sigmapersonnel','admin','privileges'),
     'limit'  => 0 )
     );
     xarDefineInstance('sigmapersonnel', 'PersonnelItem', $instances);
    // For the presence items
    $query1 = "SELECT DISTINCT xar_pid FROM " . $presencetable;
    $query2 = "SELECT DISTINCT xar_userid FROM " . $presencetable;
    $query3 = "SELECT DISTINCT xar_typeid FROM " . $presencetable;
    $instances = array(
        array('header' => 'Presence ID:',
            'query' => $query1,
            'limit' => 20
            ),
        array('header' => 'Presence enteredby userid:',
            'query' => $query2,
            'limit' => 20
            ),
        array('header' => 'Presence Type:',
            'query' => $query3,
            'limit' => 20
            )
        );
    xarDefineInstance('sigmapersonnel', 'PresenceItem', $instances);


    $instancestable = $xartable['block_instances'];
    $typestable = $xartable['block_types'];
    $query = "SELECT DISTINCT i.xar_title FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'sigmapersonnel'";
    $instances = array(
        array('header' => 'SIGMA Personnel Block Title:',
            'query' => $query,
            'limit' => 20
            )
        );
    xarDefineInstance('sigmapersonnel', 'Block', $instances);

    // You can also use some external "wizard" function to specify instances :

    // $instances = array(
    // array('header' => 'external', // this keyword indicates an external "wizard"
    // 'query'  => xarModURL('example','admin','privileges',array('foo' =>'bar')),
    // 'limit'  => 0
    // )
    // );

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */
    //Blocks
    xarRegisterMask('ReadSIGMAPersonnelBlock', 'All', 'sigmapersonnel', 'Block', 'All', 'ACCESS_OVERVIEW');
    //Personnel Items
    // PersonID:catid:persstatus
    xarRegisterMask('ViewSIGMAPersonnel', 'All', 'sigmapersonnel', 'PersonnelItem', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadSIGMAPersonnel', 'All', 'sigmapersonnel', 'PersonnelItem', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditSIGMAPersonnel', 'All', 'sigmapersonnel', 'PersonnelItem', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddSIGMAPersonnel', 'All', 'sigmapersonnel', 'PersonnelItem', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteSIGMAPersonnel', 'All', 'sigmapersonnel', 'PersonnelItem', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminSIGMAPersonnel', 'All', 'sigmapersonnel', 'PersonnelItem', 'All:All:All', 'ACCESS_ADMIN');
    //PresenceItems
    xarRegisterMask('ViewSIGMAPresence', 'All', 'sigmapersonnel', 'PresenceItem', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadSIGMAPresence', 'All', 'sigmapersonnel', 'PresenceItem', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditSIGMAPresence', 'All', 'sigmapersonnel', 'PresenceItem', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddSIGMAPresence', 'All', 'sigmapersonnel', 'PresenceItem', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteSIGMAPresence', 'All', 'sigmapersonnel', 'PresenceItem', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminSIGMAPresence', 'All', 'sigmapersonnel', 'PresenceItem', 'All:All:All', 'ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * upgrade the sigmapersonnel module from an old version
 * This function can be called multiple times
 */
function sigmapersonnel_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '0.1':
            // You can also use some external "wizard" function to specify instances :
    /*********************************************************************
    * Define instances for the core modules
    * Format is
    * xarDefineInstance(Module,Component,Querystring,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
    *********************************************************************/
     $xartable = xarDBGetTables();
     $instances = array(
     array('header' => 'external', // this keyword indicates an external "wizard"
     'query'  => xarModURL('sigmapersonnel','admin','privileges'),
     'limit'  => 0 )
     );
     xarDefineInstance('sigmapersonnel', 'PersonnelItem', $instances);

            return sigmapersonnel_upgrade('0.1.1');
        case '0.1.1':
        case '0.1.2':
        case '0.1.3':
        case '0.1.4':
            xarModSetVar('sigmapersonnel', 'OnCallID', 1);
            return sigmapersonnel_upgrade('0.1.5');
        case '0.1.5':
            // Code to upgrade from version 1.0 goes here
            break;
    }
    // Update successful
    return true;
}

/**
 * delete the sigmapersonnel module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function sigmapersonnel_delete()
{
    // Drop tables
    /* Get database setup */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    /* Get a data dictionary object with item create and delete methods */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    // Initialise table array
    $basename = 'sigmapersonnel';

    foreach(array('person', 'presencetypes', 'presence', 'cities', 'status', 'districts') as $table) {

    /* Drop the tables */
     $result = $datadict->dropTable($xartable[$basename . '_' . $table]);
    }

    /* Drop the Dyn data objects */
    $objectid = xarModGetVar('sigmapersonnel','citiesobjectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }
    $objectid = xarModGetVar('sigmapersonnel','districtsobjectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }
    $objectid = xarModGetVar('sigmapersonnel','presencetypesobjectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }
    $objectid = xarModGetVar('sigmapersonnel','statusobjectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }



    // Delete any module variables
    xarModDelAllVars('sigmapersonnel');
    if (xarModIsAvailable('categories')) {
        xarModDelVar('sigmapersonnel', 'number_of_categories');
        xarModDelVar('sigmapersonnel', 'mastercids');
    }

    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'sigmapersonnel',
                'blockType' => 'statusall'))) return;
    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'sigmapersonnel',
                'blockType' => 'lastentry'))) return;
    // Remove module hooks
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
            'sigmapersonnel', 'user', 'usermenu')) {
        return false;
    }
    // Remove Masks and Instances
    xarRemoveMasks('sigmapersonnel');
    xarRemoveInstances('sigmapersonnel');

    // Deletion successful
    return true;
}

?>
