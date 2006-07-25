<?php
/**
 * AddressBook utility functions
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
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
    $insertRows = array(xarML('Work')
                       ,xarML('Fax')
                       ,xarML('Mobile')
                       ,xarML('Home')
                       ,xarML('E-Mail')
                       ,xarML('URL')
                       ,xarML('Other')
                   );
    foreach ($insertRows as $row) {
        $nextId = $dbconn->GenId($abLabelsTable);
        $query = sprintf ("INSERT INTO %s (nr,name) VALUES (?, ?)"
                         ,$abLabelsTable);
        $bindvars = array ($nextId,$row);
        $result =& $dbconn->Execute($query,$bindvars);
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
    $insertRows = array(xarML('Business')
                       ,xarML('Personal')
                       ,xarML('Quicklist')
                       );

    foreach ($insertRows as $row) {
        $nextId = $dbconn->GenId($abCategoriesTable);
        $query = sprintf ("INSERT INTO %s (nr,name) VALUES (?, ?)"
                         ,$abCategoriesTable);
        $bindvars = array ($nextId,$row);
        $result =& $dbconn->Execute($query,$bindvars);
        if (!$result) return;
    }

    /**
     * create custom field table
     */
    $abCustomfieldsTable = $xarTables['addressbook_customfields'];
    $fields = array(
         'nr'          =>  array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE)
        ,'label'       =>  array('type'=>'varchar','size'=>30,'null'=>TRUE,'default'=>'NULL')
        ,'type'        =>  array('type'=>'varchar','size'=>30,'null'=>TRUE,'default'=>'NULL')
        ,'position'    =>  array('type'=>'integer','size'=>'small','null'=>FALSE)
        ,'short_label' =>  array('type'=>'varchar','size'=>8,'null'=>TRUE,'default'=>'NULL')
        ,'display'     =>  array('type'=>'integer','size'=>'tiny','null'=>FALSE, 'default'=>'0')
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
    $insertRows = array(xarML('Custom Label 1')
                       ,xarML('Custom Label 2')
                       ,xarML('Custom Label 3')
                       ,xarML('Custom Label 4')
                       );

    $nextId = 1;
    foreach ($insertRows as $row) {
        $query = sprintf ("INSERT INTO %s (nr,label,type,position) VALUES (?,?,'"._AB_CUSTOM_TEXT_SHORT."',?)"
                         ,$abCustomfieldsTable);
        $bindvars = array ($nextId,$row,$nextId);
        $result =& $dbconn->Execute($query,$bindvars);
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
    $insertRows = array(xarML('Mr.')
                       ,xarML('Mrs.')
                   );

    foreach ($insertRows as $row) {
        $nextId = $dbconn->GenId($abPrefixesTable);
        $query = sprintf ("INSERT INTO %s (nr,name) VALUES (?,?)"
                         ,$abPrefixesTable);
        $bindvars = array ($nextId,$row);
        $result =& $dbconn->Execute($query,$bindvars);
        if (!$result) return;
    }

    xarModSetVar('addressbook', 'abtitle', 'Xaraya Address Book');
    xarModSetVar('addressbook', 'guestmode', 1);
    xarModSetVar('addressbook', 'usermode', 7);
    xarModSetVar('addressbook', 'itemsperpage', 30);
    xarModSetVar('addressbook', 'globalprotect', 0);
    xarModSetVar('addressbook', 'menu_off', 0);
    xarModSetVar('addressbook', 'custom_tab', '');
    xarModSetVar('addressbook', 'zipbeforecity', 0);
    xarModSetVar('addressbook', 'hidecopyright', 0);
    xarModSetVar('addressbook', 'use_prefix', 0);
    xarModSetVar('addressbook', 'display_prefix', 0);
    xarModSetVar('addressbook', 'use_img', 0);
    xarModSetVar('addressbook', 'textareawidth', 60);
    xarModSetVar('addressbook', 'dateformat', 0);
    xarModSetVar('addressbook', 'numformat', '9,999.99');
    xarModSetVar('addressbook', 'sortorder_1', 'sortname,sortcompany');
    xarModSetVar('addressbook', 'sortorder_2', 'sortcompany,sortname');
    xarModSetVar('addressbook', 'menu_semi', 0);
    xarModSetVar('addressbook', 'name_order', 0);
    xarModSetVar('addressbook', 'special_chars_1', '???????');
    xarModSetVar('addressbook', 'special_chars_2', 'AOUaous');
    xarModSetVar('addressbook', 'SupportShortURLs', 0);
    xarModSetVar('addressbook', 'rptErrAdminFlag', 1);
    xarModSetVar('addressbook', 'rptErrAdminEmail', xarModGetVar('mail','adminmail'));
    xarModSetVar('addressbook', 'rptErrDevFlag', 1);

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
    xarDefineInstance('addressbook', 'Item', $instances);


    /*********************************************************************
    * Register the module components that are privileges objects
    * Format is
    * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    *********************************************************************/
    xarRegisterMask('ReadAddressBook',   'All','addressbook','Item','All:All:All','ACCESS_READ');
    xarRegisterMask('EditAddressBook',   'All','addressbook','Item','All:All:All','ACCESS_EDIT');
    xarRegisterMask('AddAddressBook',    'All','addressbook','Item','All:All:All','ACCESS_ADD');
    xarRegisterMask('DeleteAddressBook', 'All','addressbook','Item','All:All:All','ACCESS_DELETE');
    xarRegisterMask('AdminAddressBook',  'All','addressbook','Item','All:All:All','ACCESS_ADMIN');


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
            xarModSetVar ('addressbook','rptErrAdminFlag', 1);
            xarModSetVar ('addressbook','rptErrAdminEmail', xarModGetVar('mail','adminmail'));
            xarModSetVar ('addressbook','rptErrDevFlag', 1);

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
        case '1.2.2':
        case '1.2.3':
        case '1.2.4':
        case '1.2.5':
        case '1.2.6':

            // Add two columns to custom fields tables
            $dbconn =& xarDBGetConn();
            $xarTables =& xarDBGetTables();

            xarDBLoadTableMaintenanceAPI();

            $abCustomTable = $xarTables['addressbook_customfields'];
            $abAddressTable = $xarTables['addressbook_address'];

            $sql = xarDBAlterTable ($abCustomTable, array('command'=>'add','field'=>'short_label','type'=>'varchar','size'=>8,'null'=>TRUE,'default'=>'NULL'));
            $result =& $dbconn->Execute($sql);
            if (!$result) return FALSE;

            $sql = xarDBAlterTable ($abCustomTable, array('command'=>'add','field'=>'display','type'=>'integer','size'=>'tiny','null'=>FALSE,'default'=>'0'));
            $result =& $dbconn->Execute($sql);
            if (!$result) return FALSE;

            // Change custom field "date" type from "date" to integer(11) for easier manipulation
            $sql = "SELECT nr
                      FROM $abCustomTable
                     WHERE type like ?";
            $results =& $dbconn->Execute($sql,array('date%'));
            if (!$results) return FALSE;

            if ($results->RecordCount() > 0) {
                /**
                 * We found some custom fields to convert. We will form the following actions
                 * 1. update the custom fields table to reflect the new column type
                 * 2. using the custom field ID save a copy of all the old custom field data
                 * 3. drop that column from the address table
                 * 4. add the column back using the new type
                 * 5. convert the old data
                 * 6. reload the old data
                 */

                // Step 1
                $sql = "UPDATE $abCustomTable
                           SET type = ?
                         WHERE type like ?";
                $colUpdate =& $dbconn->Execute($sql, array(_AB_CUSTOM_DATE,'date%'));

                for (; !$results->EOF; $results->MoveNext()) {
                    list ($index) = $results->fields;

                    // Step 2 - clean up bad data then copy old data
                    $sql = "UPDATE $abAddressTable
                               SET custom_$index = NULL
                             WHERE custom_$index = '0000-00-00'";
                    $oldData =& $dbconn->Execute($sql);
                    if (!$oldData) return FALSE;

                    $sql = "SELECT nr, custom_$index
                              FROM $abAddressTable";
                    $oldData =& $dbconn->Execute($sql);
                    if (!$oldData) return FALSE;

                    // Step 3
                    $sql = "ALTER TABLE $abAddressTable
                                   DROP custom_$index";
                    $dropCol =& $dbconn->Execute($sql);
                    if (!$dropCol) return FALSE;
                    $dropCol->Close();
                    //Step 4
                    $sql = xarDBAlterTable ($abAddressTable, array('command'=>'add','field'=>'custom_'.$index,'type'=>'integer','null'=>TRUE,'default'=>'NULL'));
                    $addCol =& $dbconn->Execute($sql);
                    if (!$addCol) return FALSE;
                    $addCol->Close();

                    for (; !$oldData->EOF; $oldData->MoveNext()) {
                        list ($nr,$date) = $oldData->fields;
                    //Step 5
                        if (!empty($date)) {
                            if (ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})", $date, $tgRegs)) {
                                $date = mktime(0,0,0,$tgRegs[2],$tgRegs[3],$tgRegs[1]);
                            } else {
                                $date = 'NULL';
                            }
                        } else {
                            $date = 'NULL';
                        }

                    //Step 6
                        $sql = "UPDATE $abAddressTable
                                   SET custom_$index = ?
                                 WHERE nr = ?";
                        $updateResult =& $dbconn->Execute($sql,array($date,$nr));
                        if (!$updateResult) return FALSE;
                        $updateResult->Close();
                    }
                    $oldData->Close();
                }
                $results->Close();
            }

            // Add a config var to allow the prefix to be displayed in the search results
            xarModSetVar('addressbook', 'display_prefix', 0);

            break;

        case '1.2.7':
        /*
         * This release will fix the user privileges problem where only Admins may insert records
         */
            xarRemoveMasks('addressbook');

            xarRegisterMask('ReadAddressBook',   'All','addressbook','Item','All:All:All','ACCESS_READ');
            xarRegisterMask('EditAddressBook',   'All','addressbook','Item','All:All:All','ACCESS_EDIT');
            xarRegisterMask('AddAddressBook',    'All','addressbook','Item','All:All:All','ACCESS_ADD');
            xarRegisterMask('DeleteAddressBook', 'All','addressbook','Item','All:All:All','ACCESS_DELETE');
            xarRegisterMask('AdminAddressBook',  'All','addressbook','Item','All:All:All','ACCESS_ADMIN');

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
    xarModDelAllVars('addressbook');
    xarRemoveMasks('addressbook');
    xarRemoveInstances('addressbook');

    // Deletion successful
    return true;
}
?>
