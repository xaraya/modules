<?php
/**
 * File: $Id$
 *
 * AddressBook utility functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

/**
 * initialise the AddressBook module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function AddressBook_init()
{

    list($dbconn) = xarDBGetConn();
    $xarTables = xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

	/**
     * create main address table
     */
    $abAddressTable = $xarTables['addressbook_address'];
    $fields = array(
         'nr'       =>  array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
        ,'cat_id'   =>  array('type'=>'integer','null'=>TRUE,'default'=>'NULL')
        ,'prefix'   =>  array('type'=>'integer','null'=>TRUE,'default'=>'NULL')
        ,'lname'    =>  array('type'=>'varchar','size'=>100,'null'=>TRUE,'default'=>'NULL')
        ,'fname'    =>  array('type'=>'varchar','size'=>60,'null'=>TRUE,'default'=>'NULL')
        ,'sortname' =>  array('type'=>'varchar','size'=>180,'null'=>TRUE,'default'=>'NULL')
        ,'title'    =>  array('type'=>'varchar','size'=>100,'null'=>TRUE,'default'=>'NULL')
        ,'company'  =>  array('type'=>'varchar','size'=>100,'null'=>TRUE,'default'=>'NULL')
        ,'sortcompany'=>array('type'=>'varchar','size'=>100,'null'=>TRUE,'default'=>'NULL')
        ,'img'      =>  array('type'=>'varchar','size'=>100,'null'=>TRUE,'default'=>'NULL')
        ,'zip'      =>  array('type'=>'varchar','size'=>30,'null'=>TRUE,'default'=>'NULL')
        ,'city'     =>  array('type'=>'varchar','size'=>100,'null'=>TRUE,'default'=>'NULL')
        ,'address_1' =>  array('type'=>'varchar','size'=>100,'null'=>TRUE,'default'=>'NULL')
        ,'address_2' =>  array('type'=>'varchar','size'=>100,'null'=>TRUE,'default'=>'NULL')
        ,'state'    =>  array('type'=>'varchar','size'=>60,'null'=>TRUE,'default'=>'NULL')
        ,'country'  =>  array('type'=>'varchar','size'=>60,'null'=>TRUE,'default'=>'NULL')
        ,'contact_1'=>  array('type'=>'varchar','size'=>80,'null'=>TRUE,'default'=>'NULL')
        ,'contact_2'=>  array('type'=>'varchar','size'=>80,'null'=>TRUE,'default'=>'NULL')
        ,'contact_3'=>  array('type'=>'varchar','size'=>80,'null'=>TRUE,'default'=>'NULL')
        ,'contact_4'=>  array('type'=>'varchar','size'=>80,'null'=>TRUE,'default'=>'NULL')
        ,'contact_5'=>  array('type'=>'varchar','size'=>80,'null'=>TRUE,'default'=>'NULL')
        ,'c_label_1'=>  array('type'=>'integer','size'=>'tiny','null'=>TRUE,'default'=>'NULL')
        ,'c_label_2'=>  array('type'=>'integer','size'=>'tiny','null'=>TRUE,'default'=>'NULL')
        ,'c_label_3'=>  array('type'=>'integer','size'=>'tiny','null'=>TRUE,'default'=>'NULL')
        ,'c_label_4'=>  array('type'=>'integer','size'=>'tiny','null'=>TRUE,'default'=>'NULL')
        ,'c_label_5'=>  array('type'=>'integer','size'=>'tiny','null'=>TRUE,'default'=>'NULL')
        ,'c_main'   =>  array('type'=>'integer','size'=>'tiny','null'=>TRUE,'default'=>'NULL')
        ,'custom_1' =>  array('type'=>'varchar','size'=>60,'null'=>TRUE,'default'=>'NULL')
        ,'custom_2' =>  array('type'=>'varchar','size'=>60,'null'=>TRUE,'default'=>'NULL')
        ,'custom_3' =>  array('type'=>'varchar','size'=>60,'null'=>TRUE,'default'=>'NULL')
        ,'custom_4' =>  array('type'=>'varchar','size'=>60,'null'=>TRUE,'default'=>'NULL')
        ,'note'     =>  array('type'=>'blob','null'=>TRUE,'default'=>'NULL')
        ,'user_id'  =>  array('type'=>'integer','unsigned'=>TRUE,'null'=>TRUE,'default'=>'NULL')
        ,'private'  =>  array('type'=>'integer','size'=>'tiny','null'=>FALSE,'default'=>'0')
        ,'date'     =>  array('type'=>'integer','null'=>FALSE,'default'=>'0')
    );
    $query = xarDBCreateTable($abAddressTable,$fields);
    if (empty($query)) return; // throw back

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    /**
     * create label table
     */
    $abLabelsTable = $xarTables['addressbook_labels'];
    $fields = array(
         'nr'   =>  array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
        ,'name' =>  array('type'=>'varchar','size'=>30,'null'=>TRUE,'default'=>'NULL')
        );
    $query = xarDBCreateTable($abLabelsTable,$fields);
    if (empty($query)) return; // throw back

    $result =& $dbconn->Execute($query);
    if (!$result) return;

	/**
     * insert default values
     */
    $insertRows = array(
                    "INSERT INTO $abLabelsTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_WORK)."')"
                   ,"INSERT INTO $abLabelsTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_FAX)."')"
                   ,"INSERT INTO $abLabelsTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_MOBILE)."')"
                   ,"INSERT INTO $abLabelsTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_HOME)."')"
                   ,"INSERT INTO $abLabelsTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_EMAIL)."')"
                   ,"INSERT INTO $abLabelsTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_URL)."')"
                   ,"INSERT INTO $abLabelsTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_OTHER)."')"
                   );

//    $sql = $dbconn->Execute("INSERT INTO $abLablesTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_WORK)."')");
//    $sql = $dbconn->Execute("INSERT INTO $abLabelsTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_FAX)."')");
//    $sql = $dbconn->Execute("INSERT INTO $abLabelsTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_MOBILE)."')");
//    $sql = $dbconn->Execute("INSERT INTO $abLabelsTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_HOME)."')");
//    $sql = $dbconn->Execute("INSERT INTO $abLabelsTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_EMAIL)."')");
//    $sql = $dbconn->Execute("INSERT INTO $abLabelsTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_URL)."')");
//    $sql = $dbconn->Execute("INSERT INTO $abLabelsTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_OTHER)."')");
    foreach ($insertRows as $row) {
        $result =& $dbconn->Execute($row);
        if (!$result) return;
    }

    /**
     * create category table
     */
    $abCategoriesTable = $xarTables['addressbook_categories'];
    $fields = array(
         'nr'   =>  array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
        ,'name' =>  array('type'=>'varchar','size'=>30,'null'=>TRUE,'default'=>'NULL')
        );
    $query = xarDBCreateTable($abCategoriesTable,$fields);
    if (empty($query)) return; // throw back

    $result =& $dbconn->Execute($query);
    if (!$result) return;

	/**
     * insert default values
     */
//	$sql = $dbconn->Execute("INSERT INTO $abCategoriesTable ($nr,$name) VALUES ('','".xarVarPrepForStore(_pnAB_BUSINESS)."')");
//	$sql = $dbconn->Execute("INSERT INTO $abCategoriesTable ($nr,$name) VALUES ('','".xarVarPrepForStore(_pnAB_PERSONAL)."')");
//	$sql = $dbconn->Execute("INSERT INTO $abCategoriesTable ($nr,$name) VALUES ('','".xarVarPrepForStore(_pnAB_QUICKLIST)."')");
    $insertRows = array(
                    "INSERT INTO $abCategoriesTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_BUSINESS)."')"
                   ,"INSERT INTO $abCategoriesTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_PERSONAL)."')"
                   ,"INSERT INTO $abCategoriesTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_QUICKLIST)."')"
                   );

    foreach ($insertRows as $row) {
        $result =& $dbconn->Execute($row);
        if (!$result) return;
    }

	/**
     * create custom field table
     */
    $abCustomfieldsTable = $xarTables['addressbook_customfields'];
    $fields = array(
         'nr'       =>  array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
        ,'label'    =>  array('type'=>'varchar','size'=>30,'null'=>TRUE,'default'=>'NULL')
        ,'type'     =>  array('type'=>'varchar','size'=>30,'null'=>TRUE,'default'=>'NULL')
        ,'position' =>  array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE)
        );
    $query = xarDBCreateTable($abCustomfieldsTable,$fields);
    if (empty($query)) return; // throw back

    $result =& $dbconn->Execute($query);
    if (!$result) return;

	$tempcus_1 = xarVarPrepForStore(_AB_CUSTOM_1);
	$tempcus_2 = xarVarPrepForStore(_AB_CUSTOM_2);
	$tempcus_3 = xarVarPrepForStore(_AB_CUSTOM_3);
	$tempcus_4 = xarVarPrepForStore(_AB_CUSTOM_4);

	/**
     * insert default values
     */
//	$sql = $dbconn->Execute("INSERT INTO $abCustomfieldsTable (nr,name,type,position) VALUES ('1','$tempcus_1','varchar(60)','1')");
//	$sql = $dbconn->Execute("INSERT INTO $abCustomfieldsTable (nr,name,type,position) VALUES ('2','$tempcus_2','varchar(60)','2')");
//	$sql = $dbconn->Execute("INSERT INTO $abCustomfieldsTable (nr,name,type,position) VALUES ('3','$tempcus_3','varchar(60)','3')");
//	$sql = $dbconn->Execute("INSERT INTO $abCustomfieldsTable (nr,name,type,position) VALUES ('4','$tempcus_4','varchar(60)','4')");
    $insertRows = array(
                    "INSERT INTO $abCustomfieldsTable (nr,label,type,position) VALUES ('1','$tempcus_1','varchar(60) default NULL','1')"
                   ,"INSERT INTO $abCustomfieldsTable (nr,label,type,position) VALUES ('2','$tempcus_2','varchar(60) default NULL','2')"
                   ,"INSERT INTO $abCustomfieldsTable (nr,label,type,position) VALUES ('3','$tempcus_3','varchar(60) default NULL','3')"
                   ,"INSERT INTO $abCustomfieldsTable (nr,label,type,position) VALUES ('4','$tempcus_4','varchar(60) default NULL','4')"
                   );

    foreach ($insertRows as $row) {
        $result =& $dbconn->Execute($row);
        if (!$result) return;
    }

	/**
     * create prefix table
     */
    $abPrefixesTable = $xarTables['addressbook_prefixes'];
    $fields = array(
         'nr'   =>  array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
        ,'name' =>  array('type'=>'varchar','size'=>30,'null'=>TRUE,'default'=>'NULL')
        );
    $query = xarDBCreateTable($abPrefixesTable,$fields);
    if (empty($query)) return; // throw back

    $result =& $dbconn->Execute($query);
    if (!$result) return;

	/**
     * insert default values
     */
//	$sql = $dbconn->Execute("INSERT INTO $abPrefixesTable (nr,name) VALUES ('','".xarVarPrepForStore(_pnAB_MR)."')");
//	$sql = $dbconn->Execute("INSERT INTO $abPrefixesTable (nr,name) VALUES ('','".xarVarPrepForStore(_pnAB_MRS)."')");
    $insertRows = array(
                    "INSERT INTO $abPrefixesTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_MR)."')"
                   ,"INSERT INTO $abPrefixesTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_MRS)."')"
                   );
    foreach ($insertRows as $row) {
        $result =& $dbconn->Execute($row);
        if (!$result) return;
    }

	// Set up an initial value for a module variable.
    xarModSetVar(__ADDRESSBOOK__, 'abtitle',         'Xaraya Address Book');
    xarModSetVar(__ADDRESSBOOK__, 'guestmode',       4);
	xarModSetVar(__ADDRESSBOOK__, 'usermode',        7);
	xarModSetVar(__ADDRESSBOOK__, 'itemsperpage',    30);
	xarModSetVar(__ADDRESSBOOK__, 'globalprotect',   0);
	xarModSetVar(__ADDRESSBOOK__, 'menu_off',        0);
	xarModSetVar(__ADDRESSBOOK__, 'custom_tab',      '');
	xarModSetVar(__ADDRESSBOOK__, 'zipbeforecity',   0);
	xarModSetVar(__ADDRESSBOOK__, 'hidecopyright',   0);
	// since version 2.0
	xarModSetVar(__ADDRESSBOOK__, 'use_prefix',      0);
	xarModSetVar(__ADDRESSBOOK__, 'use_img',         0);
	xarModSetVar(__ADDRESSBOOK__, 'textareawidth',   60);
	xarModSetVar(__ADDRESSBOOK__, 'dateformat',      0);
	xarModSetVar(__ADDRESSBOOK__, 'numformat',       '9,999.99');
	xarModSetVar(__ADDRESSBOOK__, 'sortorder_1',     'sortname,sortcompany');
	xarModSetVar(__ADDRESSBOOK__, 'sortorder_2',     'sortcompany,sortname');
	xarModSetVar(__ADDRESSBOOK__, 'menu_semi',       0);
	xarModSetVar(__ADDRESSBOOK__, 'name_order',      0);
	xarModSetVar(__ADDRESSBOOK__, 'special_chars_1', 'ÄÖÜäöüß');
    xarModSetVar(__ADDRESSBOOK__, 'special_chars_2', 'AOUaous');

    // If your module supports short URLs, the website administrator should
    // be able to turn it on or off in your module administration
    xarModSetVar(__ADDRESSBOOK__, 'SupportShortURLs', 0);

//// crazy stuff
    // Register Block types (this *should* happen at activation/deactivation)
//    if (!xarModAPIFunc('blocks',
//                       'admin',
//                       'register_block_type',
//                       array('modName'  => __ADDRESSBOOK__,
//                       'blockType'=> 'namecompany'))) return;

    // Register our hooks that we are providing to other modules.  The example
    // module shows an example hook in the form of the user menu.
//    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
//                            'example', 'user', 'usermenu')) {
//        return false;
//    }

    /*********************************************************************
    * Define instances for this module
    * Format is
    * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
    *********************************************************************/

    // Instance definitions serve two purposes:
    // 1. The define "filters" that are added to masks at runtime, allowing us to set
    //    security checks over single objects or groups of objects
    // 2. They generate dropdowns the UI uses to present the user with choices when
    //    definng or modifying privileges.

    // For each component we need to tell the system how to generate
    // a list (dropdown) of all the component's instances.
    // In addition, we add a header which will be displayed for greater clarity, and a number
    // (limit) which defines the maximum number of rows a dropdown can have. If the number of
    // instances is greater than the limit (e.g. registered users), the UI instead presents an
    // input field for manual input, which is then checked for validity.

    $query1 = "SELECT DISTINCT nr FROM ".$abAddressTable;
    $instances = array(
                        array('header' => 'Address Book ID:',
                              'query'  => $query1,
                              'limit'  => 20
                            ),
                    );
    xarDefineInstance(__ADDRESSBOOK__, 'Item', $instances);

// You can also use some external "wizard" function to specify instances :
//
//    $instances = array(
//                        array('header' => 'external', // this keyword indicates an external "wizard"
//                              'query'  => xarModURL('example','admin','privileges',array('foo' =>'bar')),
//                              'limit'  => 0
//                            )
//                    );
//    xarDefineInstance('example', 'Item', $instances);

//    $instancestable = $xarTables['block_instances'];
//    $typestable = $xarTables['block_types'];
//    $query = "SELECT DISTINCT i.xar_title FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'example'";
//    $instances = array(
//                        array('header' => 'Example Block Title:',
//                                'query' => $query,
//                                'limit' => 20
//                            )
//                    );
//    xarDefineInstance('example','Block',$instances);

//// crazy stuff

    /*********************************************************************
    * Register the module components that are privileges objects
    * Format is
    * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    *********************************************************************/
    xarRegisterMask('AdminAddressBook',   'All',__ADDRESSBOOK__,'Item','All:All:All',ACCESS_ADMIN);
    xarRegisterMask('ModifyLabels',       'All',__ADDRESSBOOK__,'Item','All:All:All',ACCESS_ADMIN);
    xarRegisterMask('ModifyCategories',   'All',__ADDRESSBOOK__,'Item','All:All:All',ACCESS_ADMIN);
    xarRegisterMask('ModifyPrefixes',     'All',__ADDRESSBOOK__,'Item','All:All:All',ACCESS_ADMIN);
    xarRegisterMask('ModifyCustomFields', 'All',__ADDRESSBOOK__,'Item','All:All:All',ACCESS_ADMIN);

    xarRegisterMask('EditAddressBook',     'All',__ADDRESSBOOK__,'Item','All:All:All',ACCESS_EDIT);
    xarRegisterMask('ModerateAddressBook', 'All',__ADDRESSBOOK__,'Item','All:All:All',ACCESS_EDIT);

    xarRegisterMask('ViewAddressBook',     'All',__ADDRESSBOOK__,'Item','All:All:All',ACCESS_READ);

    // Initialisation successful
    return true;
}

/**
 * upgrade the module from an old version
 * This function can be called multiple times
 */
function AddressBook_upgrade($oldversion) {

	switch($oldversion) {
        case '0.0':
			break;
	}

	return true;
}

/**
 * delete the AddressBook module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function AddressBook_delete() {

    list($dbconn) = xarDBGetConn();
    $xarTables = xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    /**
     * Drop the table and send exception if returns false.
     */
    $abTables = array (
                         'addressbook_address'
                        ,'addressbook_labels'
                        ,'addressbook_categories'
                        ,'addressbook_customfields'
                        ,'addressbook_prefixes'
                      );

    foreach ($abTables as $abTable) {
        $query = xarDBDropTable($xarTables[$abTable]);
        $result =& $dbconn->Execute($query);
        if (!$result) return;
    }

    /**
     * Delete any module variables
     */
    $abModVars = array (
                         'guestmode'
                        ,'usermode'
                        ,'itemsperpage'
                        ,'globalprotect'
                        ,'menu_off'
                        ,'custom_tab'
                        ,'zipbeforecity'
                        ,'abtitle'
                        ,'hidecopyright'
                        ,'use_img'
                        ,'use_prefix'
                        ,'textareawidth'
                        ,'dateformat'
                        ,'numformat'
                        ,'sortorder_1'
                        ,'sortorder_2'
                        ,'menu_semi'
                        ,'name_order'
                        ,'special_chars_1'
                        ,'special_chars_2'
                        ,'SupportShortURLs'
                        );

    foreach ($abModVars as $abModVar) {
        xarModDelVar(__ADDRESSBOOK__, $abModVar);
    }

    // UnRegister blocks
//    if (!xarModAPIFunc('blocks',
//                       'admin',
//                       'unregister_block_type',
//                       array('modName'  => __ADDRESSBOOK__,
//                       'blockType'=> 'namecompany'))) return;

    // Remove module hooks
//    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
//                              'example', 'user', 'usermenu')) {
//        return false;
//    }

    // Remove Masks and Instances
    xarRemoveMasks(__ADDRESSBOOK__);
    xarRemoveInstances(__ADDRESSBOOK__);

    // Deletion successful
    return true;
}
?>