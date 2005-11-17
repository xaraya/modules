<?php
/**
 * maxercalls initialization functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage maxercalls
 * @author Michel V. maxercalls module development team 
 */

/**
 * initialise the maxercalls module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function maxercalls_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

	//Main table
    $maxercallstable = $xartable['maxercalls'];
    xarDBLoadTableMaintenanceAPI();
    $fields = array(
    'xar_callid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
    'xar_enteruid'=>array('null'=>FALSE, 'type'=>'integer','size'=>'small',  'default'=>'0'),
    'xar_owner'=>array('null'=>FALSE, 'type'=>'integer','size'=>'small',  'default'=>'0'),
    'xar_calltext'=>array('null'=>FALSE, 'type'=>'integer','size'=>'small',  'default'=>'0'),
    'xar_remarks'=>array('null'=>TRUE,  'type'=>'varchar','size'=>100,  'default'=>'NULL'),
    'xar_calldate'=>array('null'=>FALSE, 'type'=>'date'),//,'default'=>array('year'=>2005,'month'=>01,'day'=>17,'hour'=>'12','minute'=>59,'second'=>0)),
    'xar_calltime'=>array('null'=>FALSE, 'type'=>'time', 'size'=>'HHMM'),
    'xar_enterts'=>array('type'=>'timestamp')

	);
    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($maxercallstable, $fields);
    if (empty($query)) return; // throw back

    $result = $dbconn->Execute($query);
    if (!$result) return;
	
    //The call types table. This one will be organised by dyn data.	
    $maxercallstypeTable = $xartable['maxercalls_types'];
    xarDBLoadTableMaintenanceAPI();
    $fields = array(
	 'xar_typeid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
     'xar_calltext'=>array('null'=>FALSE, 'type'=>'varchar','size'=>150),
     'xar_color'=>array('null'=>FALSE, 'type'=>'varchar','size'=>10)
	);
    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($maxercallstypeTable, $fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    $maxerstable = $xartable['maxercalls_maxers'];
    // Create table to hold the maxers themselves
    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $fields = "xar_maxerid      I  AUTO    PRIMARY,
               xar_personid     I  NOTNULL DEFAULT 0,
               xar_ric          I  NOTNULL DEFAULT 0,
               xar_maxernumber  I4 NOTNUll DEFAULT 0,
               xar_function     I  NOTNULL DEFAULT 0,
               xar_program      C(200)  NOTNULL DEFAULT '',
               xar_maxerstatus  I  NOTNULL DEFAULT 0,
               xar_remark       X  NOTNULL DEFAULT ''
              ";
    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($maxerstable, $fields);
    if (!$result) {return;}

    // If Categories API loaded and available, generate proprietary
    // module master category cid and child subcids
	// Do I want this?? Wouldn't it be better to have the call take a category... not systemwide?
    if (xarModIsAvailable('categories')) {
        $maxercallscid = xarModAPIFunc('categories',
            'admin',
            'create',
            Array('name' => 'maxercalls',
                'description' => 'Maxercalls Categories',
                'parent_id' => 0));
        // Note: you can have more than 1 mastercid (cfr. articles module)
        xarModSetVar('maxercalls', 'number_of_categories', 1);
        xarModSetVar('maxercalls', 'mastercids', $maxercallscid);
		
        $maxercallscategories = array();
        $maxercallscategories[] = array('name' => "call type one",
            'description' => "call type one");
        $maxercallscategories[] = array('name' => "call type two",
            'description' => "call type two");
        $maxercallscategories[] = array('name' => "call type three",
            'description' => "call type three");
        foreach($maxercallscategories as $subcat) {
            $maxercallssubcid = xarModAPIFunc('categories',
                'admin',
                'create',
                Array('name' => $subcat['name'],
                    'description' =>
                    $subcat['description'],
                    'parent_id' => $maxercallscid));
        }
    }
    // Set up an initial value for a module variable.
    xarModSetVar('maxercalls', 'itemsperpage', 10);
    // If your module supports short URLs, the website administrator should
    // be able to turn it on or off in your module administration
    xarModSetVar('maxercalls', 'SupportShortURLs', 0);
    /* If you provide short URL encoding functions you might want to also
     * provide module aliases and have them set in the module's administration.
     * Use the standard module var names for useModuleAlias and aliasname.
     */
    xarModSetVar('example', 'useModuleAlias',false);
    xarModSetVar('example','aliasname','');
    // Register our hooks that we are providing to other modules.  The maxercalls
    // module shows an maxercalls hook in the form of the user menu.
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'maxercalls', 'user', 'usermenu')) {
        return false;
    }
     xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'roles'
            ,'callerModName'    => 'maxercalls'));

    // Hook for module Search
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'search'
            ,'callerModName'    => 'maxercalls'));
     // Hook for Categories
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'categories'
            ,'callerModName'    => 'maxercalls'));
    // Hook for Dynamic Data
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'dynamicdata'
            ,'callerModName'    => 'maxercalls'));
	
    /*
    * The Calltype Object
    
    $path = "modules/maxercalls/xardata/";
	
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'mc_types.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('maxercalls','calltypeid',$objectid);
	//This doesn't work well
    //$objectid = xarModAPIFunc('dynamicdata','util','import',
    //                          array('file' => $path . 'mc_types.data.xml'));
    //if (empty($objectid)) return;
    */
    /**
     * Define instances for this module
     * Format is
     * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
     */
    // Instance definitions serve two purposes:
    // 1. The define "filters" that are added to masks at runtime, allowing us to set
    // security checks over single objects or groups of objects
    // 2. They generate dropdowns the UI uses to present the user with choices when
    // defining or modifying privileges.
    // For each component we need to tell the system how to generate
    // a list (dropdown) of all the component's instances.
    // In addition, we add a header which will be displayed for greater clarity, and a number
    // (limit) which defines the maximum number of rows a dropdown can have. If the number of
    // instances is greater than the limit (e.g. registered users), the UI instead presents an
    // input field for manual input, which is then checked for validity.
    $query1 = "SELECT DISTINCT xar_callid FROM " . $maxercallstable;
    $query2 = "SELECT DISTINCT xar_owner FROM " . $maxercallstable;
    $query3 = "SELECT DISTINCT xar_enteruid FROM " . $maxercallstable;
    $instances = array(
        array('header' => 'Maxercalls Call ID:',
            'query' => $query1,
            'limit' => 20
            ),
        array('header' => 'Maxercalls Maxer Owner:',
            'query' => $query2,
            'limit' => 20
            ),
        array('header' => 'Maxercalls entered by UID:',
            'query' => $query3,
            'limit' => 20
            )
        );
    xarDefineInstance('maxercalls', 'Item', $instances);
    // You can also use some external "wizard" function to specify instances :

    // $instances = array(
    // array('header' => 'external', // this keyword indicates an external "wizard"
    // 'query'  => xarModURL('maxercalls','admin','privileges',array('foo' =>'bar')),
    // 'limit'  => 0
    // )
    // );
    // xarDefineInstance('maxercalls', 'Item', $instances);
    
    $instancestable = $xartable['block_instances'];
    $typestable = $xartable['block_types'];
    $query = "SELECT DISTINCT i.xar_title FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'maxercalls'";
    $instances = array(
        array('header' => 'maxercalls Block Title:',
            'query' => $query,
            'limit' => 20
            )
        );
    xarDefineInstance('maxercalls', 'Block', $instances);

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarRegisterMask('ReadMaxercallsBlock', 'All', 'maxercalls', 'Block', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ViewMaxercalls', 'All', 'maxercalls', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadMaxercalls', 'All', 'maxercalls', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditMaxercalls', 'All', 'maxercalls', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddMaxercalls', 'All', 'maxercalls', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteMaxercalls', 'All', 'maxercalls', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminMaxercalls', 'All', 'maxercalls', 'Item', 'All:All:All', 'ACCESS_ADMIN');
    // Initialisation successful
    return true;
}

/**
 * upgrade the maxercalls module from an old version
 * This function can be called multiple times
 */
function maxercalls_upgrade($oldversion)
{
    /* Upgrade dependent on old version number */
    switch ($oldversion) {
        case '0.1.5':
            $maxerstable = $xartable['maxercalls_maxers'];
            // Create table to hold the maxers themselves
            /* Get a data dictionary object with all the item create methods in it */
            $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
            $fields = "xar_maxerid      I  AUTO    PRIMARY,
                       xar_personid     I  NOTNULL DEFAULT 0,
                       xar_ric          I  NOTNULL DEFAULT 0,
                       xar_maxernumber  I4 NOTNUll DEFAULT 0,
                       xar_function     I  NOTNULL DEFAULT 0,
                       xar_program      C(200)  NOTNULL DEFAULT '',
                       xar_maxerstatus  I  NOTNULL DEFAULT 0,
                       xar_remark       X  NOTNULL DEFAULT ''
                      ";
            /* Create or alter the table as necessary */
            $result = $datadict->changeTable($maxerstable, $fields);
            if (!$result) {return;}

            xarModSetVar('example', 'useModuleAlias',false);
            xarModSetVar('example','aliasname','');
            return maxercalls_upgrade('0.1.6');
        case '0.1.6':
            break;
    }
    return true;
}

/**
 * delete the maxercalls module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function maxercalls_delete()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['maxercalls']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result = $dbconn->Execute($query);
    if (!$result) return;
	
    $query = xarDBDropTable($xartable['maxercalls_types']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result = $dbconn->Execute($query);
    if (!$result) return;
    
    $query = xarDBDropTable($xartable['maxercalls_maxers']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result = $dbconn->Execute($query);
    if (!$result) return;
    
    $objectid = xarModGetVar('maxercalls','calltypeid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }
	
    // Delete any module variables
    if (xarModIsAvailable('categories')) {
        xarModDelVar('maxercalls', 'number_of_categories');
        xarModDelVar('maxercalls', 'mastercids');
    }
    xarModDelAllVars('maxercalls');

    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'maxercalls',
                'blockType' => 'first'))) return;
    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'maxercalls',
                'blockType' => 'others'))) return;
    // Remove module hooks
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
            'maxercalls', 'user', 'usermenu')) {
        return false;
    }
    // Remove Masks and Instances
    xarRemoveMasks('maxercalls');
    xarRemoveInstances('maxercalls');

    // Deletion successful
    return true;
}

?>
