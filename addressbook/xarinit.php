<?php
/**
 * File: $Id: xarinit.php,v 1.10 2004/01/24 18:36:22 garrett Exp $
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
function addressbook_init()
{

    //FIXME: until we figure out module globals
    // if this does get changed, $abModVars will no longer be scoped here..
    include_once ('modules/addressbook/xarglobal.php');

    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    /**
     * create main address table
     */
    $abAddressTable = $xarTables['addressbook_address'];
    $fields = array(
         'nr'       =>  array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
        ,'cat_id'   =>  array('type'=>'integer','size'=>'small','null'=>TRUE,'default'=>'NULL')
        ,'prefix'   =>  array('type'=>'integer','size'=>'small','null'=>TRUE,'default'=>'NULL')
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
        ,'user_id'  =>  array('type'=>'integer','null'=>TRUE,'default'=>'NULL')
        ,'private'  =>  array('type'=>'integer','size'=>'tiny','null'=>FALSE)
        ,'last_updt'=>  array('type'=>'integer','null'=>FALSE)
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
         'nr'   =>  array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
        ,'name' =>  array('type'=>'varchar','size'=>30,'null'=>TRUE,'default'=>'NULL')
        );
    $query = xarDBCreateTable($abLabelsTable,$fields);
    if (empty($query)) return; // throw back

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    /**
     * insert default values for Label table
     */
    $insertRows = array(
                    xarVarPrepForStore(_AB_WORK)
                   ,xarVarPrepForStore(_AB_FAX)
                   ,xarVarPrepForStore(_AB_MOBILE)
                   ,xarVarPrepForStore(_AB_HOME)
                   ,xarVarPrepForStore(_AB_EMAIL)
                   ,xarVarPrepForStore(_AB_URL)
                   ,xarVarPrepForStore(_AB_OTHER)
                   );

    foreach ($insertRows as $row) {
        $nextId = $dbconn->GenId($abLabelsTable);
        $query = sprintf ("INSERT INTO %s (nr,name) VALUES (%d,'%s')"
                         ,$abLabelsTable
                         ,$nextId
                         ,$row);
        $result =& $dbconn->Execute($query);
        if (!$result) return;
    }

    /**
     * create category table
     */
    $abCategoriesTable = $xarTables['addressbook_categories'];
    $fields = array(
         'nr'   =>  array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
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
                    xarVarPrepForStore(_AB_BUSINESS)
                   ,xarVarPrepForStore(_AB_PERSONAL)
                   ,xarVarPrepForStore(_AB_QUICKLIST)
                   );

    foreach ($insertRows as $row) {
        $nextId = $dbconn->GenId($abCategoriesTable);
        $query = sprintf ("INSERT INTO %s (nr,name) VALUES (%d,'%s')"
                         ,$abCategoriesTable
                         ,$nextId
                         ,$row);
        $result =& $dbconn->Execute($query);
        if (!$result) return;
    }

    /**
     * create custom field table
     */
    $abCustomfieldsTable = $xarTables['addressbook_customfields'];
    $fields = array(
         'nr'       =>  array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
        ,'label'    =>  array('type'=>'varchar','size'=>30,'null'=>TRUE,'default'=>'NULL')
        ,'type'     =>  array('type'=>'varchar','size'=>30,'null'=>TRUE,'default'=>'NULL')
        ,'position' =>  array('type'=>'integer','size'=>'small','null'=>FALSE)
        );
    $query = xarDBCreateTable($abCustomfieldsTable,$fields);
    if (empty($query)) return; // throw back

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    /**
     * insert default values for Custom table. These primary keys are
     * intentionally numbered from 1 to 4 as they cannot be removed from
     * the application.
     */
    $insertRows = array(xarVarPrepForStore(_AB_CUSTOM_1)
                       ,xarVarPrepForStore(_AB_CUSTOM_2)
                       ,xarVarPrepForStore(_AB_CUSTOM_3)
                       ,xarVarPrepForStore(_AB_CUSTOM_4)
                       );

    $nextId = 1;
    foreach ($insertRows as $row) {
        $query = sprintf ("INSERT INTO %s (nr,label,type,position) VALUES (%d,'%s','varchar(60) default NULL',%d)"
                         ,$abCustomfieldsTable
                         ,$nextId
                         ,$row
                         ,$nextId);
        $result =& $dbconn->Execute($query);
        if (!$result) return;
        $nextId++;
    }

    /**
     * create prefix table
     */
    $abPrefixesTable = $xarTables['addressbook_prefixes'];
    $fields = array(
         'nr'   =>  array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
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
                    xarVarPrepForStore(_AB_MR)
                   ,xarVarPrepForStore(_AB_MRS)
                   );

    foreach ($insertRows as $row) {
        $nextId = $dbconn->GenId($abPrefixesTable);
        $query = sprintf ("INSERT INTO %s (nr,name) VALUES (%d,'%s')"
                         ,$abPrefixesTable
                         ,$nextId
                         ,$row);
        $result =& $dbconn->Execute($query);
        if (!$result) return;
    }

    xarModSetVar(__ADDRESSBOOK__, 'abtitle', 'Xaraya Address Book');
    xarModSetVar(__ADDRESSBOOK__, 'guestmode', 1);
    xarModSetVar(__ADDRESSBOOK__, 'usermode', 7);
    xarModSetVar(__ADDRESSBOOK__, 'itemsperpage', 30);
    xarModSetVar(__ADDRESSBOOK__, 'globalprotect', 0);
    xarModSetVar(__ADDRESSBOOK__, 'menu_off', 0);
    xarModSetVar(__ADDRESSBOOK__, 'custom_tab', '');
    xarModSetVar(__ADDRESSBOOK__, 'zipbeforecity', 0);
    xarModSetVar(__ADDRESSBOOK__, 'hidecopyright', 0);
    xarModSetVar(__ADDRESSBOOK__, 'use_prefix', 0);
    xarModSetVar(__ADDRESSBOOK__, 'use_img', 0);
    xarModSetVar(__ADDRESSBOOK__, 'textareawidth', 60);
    xarModSetVar(__ADDRESSBOOK__, 'dateformat', 0);
    xarModSetVar(__ADDRESSBOOK__, 'numformat', '9,999.99');
    xarModSetVar(__ADDRESSBOOK__, 'sortorder_1', 'sortname,sortcompany');
    xarModSetVar(__ADDRESSBOOK__, 'sortorder_2', 'sortcompany,sortname');
    xarModSetVar(__ADDRESSBOOK__, 'menu_semi', 0);
    xarModSetVar(__ADDRESSBOOK__, 'name_order', 0);
    xarModSetVar(__ADDRESSBOOK__, 'special_chars_1', 'ÄÖÜäöüß');
    xarModSetVar(__ADDRESSBOOK__, 'special_chars_2', 'AOUaous');
    xarModSetVar(__ADDRESSBOOK__, 'SupportShortURLs', 0);
    xarModSetVar(__ADDRESSBOOK__, 'rptErrAdminFlag', 1);
    xarModSetVar(__ADDRESSBOOK__, 'rptErrAdminEmail', xarModGetVar('mail','adminmail'));
    xarModSetVar(__ADDRESSBOOK__, 'rptErrDevFlag', 1);

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
function addressbook_upgrade($oldversion) 
{

    //FIXME: until we figure out module globals
    include_once ('modules/addressbook/xarglobal.php');

    switch($oldversion) {
        case '1.0':
            // New Admin Message mod vars in 1.1 & later
            xarModSetVar (__ADDRESSBOOK__,'rptErrAdminFlag', 1);
            xarModSetVar (__ADDRESSBOOK__,'rptErrAdminEmail', xarModGetVar('mail','adminmail'));
            xarModSetVar (__ADDRESSBOOK__,'rptErrDevFlag', 1);

        case '1.1':
            // Alter the table to for cross DB compatibility and rename a column
            $dbconn =& xarDBGetConn();
            $xarTables =& xarDBGetTables();

            $abAddressTable = $xarTables['addressbook_address'];

            // FIXME: <garrett> non-portable SQL
            $sql = "ALTER TABLE $abAddressTable
                         CHANGE date last_updt MEDIUMINT NOT NULL";

            $result =& $dbconn->Execute($sql);
            if (!$result) return;

        case '1.2':
            // Fix the
            $dbconn =& xarDBGetConn();
            $xarTables =& xarDBGetTables();

            $abAddressTable = $xarTables['addressbook_address'];

            // FIXME: <garrett> non-portable SQL
            $sql = "ALTER TABLE $abAddressTable
                         CHANGE user_id user_id INTEGER DEFAULT NULL
                        ,CHANGE last_updt last_updt INTEGER NOT NULL";

            $result =& $dbconn->Execute($sql);
            if (!$result) return;

        case '1.2.1':
        case '1.3.0':
        case '1.4.0':

            break;
    }

    return true;
}

/**
 * delete the AddressBook module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function addressbook_delete() 
{

    //FIXME: until we figure out module globals
    include_once ('modules/addressbook/xarglobal.php');

    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

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

    // Remove remaining Variables, Masks, and Instances
    xarModDelAllVars(__ADDRESSBOOK__);
    xarRemoveMasks(__ADDRESSBOOK__);
    xarRemoveInstances(__ADDRESSBOOK__);

    // Deletion successful
    return true;
}
?>
