<?php
/**
 * File: $Id: xarinit.php,v 1.2 2003/07/09 11:20:20 garrett Exp $
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

    //FIXME: until we figure out module globals
    include_once ('modules/addressbook/xarglobal.php');

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
    $insertRows = array(
                    "INSERT INTO $abPrefixesTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_MR)."')"
                   ,"INSERT INTO $abPrefixesTable (nr,name) VALUES ('','".xarVarPrepForStore(_AB_MRS)."')"
                   );
    foreach ($insertRows as $row) {
        $result =& $dbconn->Execute($row);
        if (!$result) return;
    }

	// $abModVars set in xarglobal.php and is used to ease maintenance
    foreach ($abModVars as $modvar=>$value) {
        xarModSetVar(__ADDRESSBOOK__, $modvar, $value);
    }

    /*********************************************************************
    * Define instances for this module
    * Format is
    * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
    *********************************************************************/

    $query1 = "SELECT DISTINCT nr FROM ".$abAddressTable;
    $instances = array(
                        array('header' => 'Address Book ID:',
                              'query'  => $query1,
                              'limit'  => 20
                            ),
                    );
    xarDefineInstance(__ADDRESSBOOK__, 'Item', $instances);


    /*********************************************************************
    * Register the module components that are privileges objects
    * Format is
    * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    *********************************************************************/
    xarRegisterMask('AdminAddressBook',   'All',__ADDRESSBOOK__,'Item','All:All:All','ACCESS_ADMIN');

    xarRegisterMask('ModerateAddressBook', 'All',__ADDRESSBOOK__,'Item','All:All:All','ACCESS_MODERATE');
    xarRegisterMask('EditAddressBook',     'All',__ADDRESSBOOK__,'Item','All:All:All','ACCESS_EDIT');

    xarRegisterMask('ViewAddressBook',     'All',__ADDRESSBOOK__,'Item','All:All:All','ACCESS_READ');

    // Initialisation successful
    return true;
}

/**
 * upgrade the module from an old version
 * This function can be called multiple times
 */
function AddressBook_upgrade($oldversion) {

    //FIXME: until we figure out module globals
    include_once ('modules/addressbook/xarglobal.php');

	switch($oldversion) {
        case '1.0':
			// New Admin Message mod vars in 1.1 & later 
            xarModSetVar (__ADDRESSBOOK__,'rptErrAdminFlag', 1);
            xarModSetVar (__ADDRESSBOOK__,'rptErrAdminEmail', xarModGetVar('mail','adminmail'));
            xarModSetVar (__ADDRESSBOOK__,'rptErrDevFlag', 1);
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

    //FIXME: until we figure out module globals
    include_once ('modules/addressbook/xarglobal.php');

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

        //FIXME: <garrett> It would *nice* to return an error here ONLY if there
        // was some sort of catastrophic error and the tables were NOT deleted
        // could care less if the tables did not exist in the first place
    }

    /**
     * Delete any module variables
     */
    foreach ($abModVars as $modvar=>$value) {
        xarModDelVar(__ADDRESSBOOK__, $modvar);
    }

    // Remove Masks and Instances
    xarRemoveMasks(__ADDRESSBOOK__);
    xarRemoveInstances(__ADDRESSBOOK__);

    // Deletion successful
    return true;
}
?>