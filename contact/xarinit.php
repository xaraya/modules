<?php
// $Id: s.xarinit.php 1.11 03/01/25 20:44:46-05:00 Scot.Gardner@ws75. $ $Name: <Not implemented> $
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Bjarne Varöystrand and Bjarne Varöystrand
// Modifications by: Richard Cave
// Purpose of file:  Initialization functions for template
// ----------------------------------------------------------------------


function contact_init()
{
    // Get datbase setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For xarDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $contact_departments = $xartable['contact_departments'];

    // Load table maintenance API
    xarDBLoadTableMaintenanceAPI();

    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules
    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    // ==================== NEXT TABLE =====================
    //                 departments_table
    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    $fields = array(
        'xar_id'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_email'=>array('type'=>'varchar','size'=>250,'null'=>FALSE),
        'xar_name'=>array('type'=>'varchar','size'=>250,'null'=>FALSE),
        'xar_phone'=>array('type'=>'varchar','size'=>20,'null'=>FALSE),
        'xar_fax'=>array('type'=>'varchar','size'=>20,'null'=>FALSE),
        'xar_state'=>array('type'=>'varchar','size'=>60,'null'=>FALSE),
        'xar_country'=>array('type'=>'varchar','size'=>20,'null'=>FALSE),
        'xar_cid'=>array('type'=>'varchar','size'=>11,'null'=>FALSE),
        'xar_hide'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE)
    );

    // Create the Table - the function will return the SQL is successful or
    // FALSE if it fails to build the SQL
    $sql = xarDBCreateTable($contact_departments,$fields);
     if (empty($sql)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

   $contact_dept_members = $xartable['contact_dept_members'];

     $fields = array(
        'xar_id'=>array('type'=>'integer','null'=>FALSE,'increment'=>FALSE,'primary_key'=>TRUE),
        'xar_depid'=>array('type'=>'integer','null'=>FALSE)
    );

     // Create the Table - the function will return the SQL is successful or
    // FALSE if it fails to build the SQL
    $sql = xarDBCreateTable($contact_dept_members,$fields);
     if (empty($sql)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    // ==================== NEXT TABLE =====================
    //                   titles_table
    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

    $contact_titles = $xartable['contact_titles'];

     $fields = array(
        'xar_id'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_name'=>array('type'=>'varchar','size'=>200, 'null'=>FALSE),
        'xar_cid'=>array('type'=>'varchar','size'=>11, 'null'=>FALSE)
    );
     // Create the Table - the function will return the SQL is successful or
    // FALSE if it fails to build the SQL
    $sql = xarDBCreateTable($contact_titles,$fields);
     if (empty($sql)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    // ==================== NEXT TABLE =====================
    //                city_table
    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    $contact_city = $xartable['contact_city'];

     $fields = array(
        'xar_id'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_name'=>array('type'=>'varchar','size'=>200, 'null'=>FALSE),
        'xar_cid'=>array('type'=>'varchar','size'=>11, 'null'=>FALSE)
    );
    // Create the Table - the function will return the SQL is successful or
    // FALSE if it fails to build the SQL
    $sql = xarDBCreateTable($contact_city,$fields);
     if (empty($sql)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    // ==================== NEXT TABLE =====================
    //                  country_table
    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

    $contact_country = $xartable['contact_country'];

     $fields = array(
        'xar_id'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_name'=>array('type'=>'varchar','size'=>90, 'null'=>FALSE),
        'xar_lang'=>array('type'=>'varchar','size'=>90, 'null'=>FALSE)
    );

     // Create the Table - the function will return the SQL is successful or
    // FALSE if it fails to build the SQL
    $sql = xarDBCreateTable($contact_country,$fields);
     if (empty($sql)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    // ==================== NEXT TABLE =====================
    //                  infotype_table
    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    $contact_infotype = $xartable['contact_infotype'];

     $fields = array(
        'xar_id'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_name'=>array('type'=>'varchar','size'=>40, 'null'=>FALSE),
        'xar_cid'=>array('type'=>'varchar','size'=>11, 'null'=>FALSE)
    );

    // Create the Table - the function will return the SQL is successful or
    // FALSE if it fails to build the SQL
    $sql = xarDBCreateTable($contact_infotype,$fields);
     if (empty($sql)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    // ==================== NEXT TABLE =====================
    //                  contact_persons
    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
     $contact_persons = $xartable['contact_persons'];

     $fields = array(
        'xar_id'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_firstname'=>array('type'=>'varchar','size'=>250, 'null'=>FALSE),
        'xar_lastname'=>array('type'=>'varchar','size'=>250, 'null'=>FALSE),
        'xar_address'=>array('type'=>'varchar','size'=>220, 'null'=>FALSE),
        'xar_address2'=>array('type'=>'varchar','size'=>220, 'null'=>FALSE),
        'xar_city'=>array('type'=>'varchar','size'=>60, 'null'=>FALSE),
        'xar_state'=>array('type'=>'varchar','size'=>60, 'null'=>FALSE),
        'xar_zip'=>array('type'=>'varchar','size'=>20, 'null'=>FALSE),
        'xar_country'=>array('type'=>'varchar','size'=>60, 'null'=>FALSE),
        'xar_mail'=>array('type'=>'varchar','size'=>250, 'null'=>FALSE),
        'xar_phone'=>array('type'=>'varchar','size'=>20, 'null'=>FALSE),
        'xar_fax'=>array('type'=>'varchar','size'=>20, 'null'=>FALSE),
        'xar_mobile'=>array('type'=>'varchar','size'=>20, 'null'=>FALSE),
        'xar_pager'=>array('type'=>'varchar','size'=>60, 'null'=>FALSE),
        'xar_typephone'=>array('type'=>'integer','null'=>FALSE),
        'xar_typefax'=>array('type'=>'integer','null'=>FALSE),
        'xar_typemobile'=>array('type'=>'integer','null'=>FALSE),
        'xar_typepager'=>array('type'=>'integer','null'=>FALSE),
        'xar_active'=>array('type'=>'varchar','size'=>60, 'null'=>FALSE),
        'xar_ICQ'=>array('type'=>'varchar','size'=>60, 'null'=>FALSE),
        'xar_AIM'=>array('type'=>'varchar','size'=>60, 'null'=>FALSE),
        'xar_YIM'=>array('type'=>'varchar','size'=>60, 'null'=>FALSE),
        'xar_MSNM'=>array('type'=>'varchar','size'=>60, 'null'=>FALSE),
        'xar_titleID'=>array('type'=>'integer','null'=>FALSE),
        'xar_image'=>array('type'=>'varchar','size'=>60, 'null'=>FALSE),
        'xar_hide'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE)
    );
    // Create the Table - the function will return the SQL is successful or
    // FALSE if it fails to build the SQL
    $sql = xarDBCreateTable($contact_persons,$fields);
     if (empty($sql)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    // ==================== NEXT TABLE =====================
    //                  contact_attributes
    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
     $contact_attributes = $xartable['contact_attributes'];

     $fields = array(
        'xar_id'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_contacttype'=>array('type'=>'varchar','size'=>2,'default'=>'P','null'=>FALSE),
        'xar_showname'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showaddress'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showaddress2'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showcity'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showstate'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showzip'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showcountry'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showemail'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showphone'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showfax'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showmobile'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showpager'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showICQ'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showAIM'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showYIM'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showMSNM'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showtitle'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showdepartment'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
        'xar_showimage'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE)
    );

    // Create the Table - the function will return the SQL is successful or
    // FALSE if it fails to build the SQL
    $sql = xarDBCreateTable( $contact_attributes,$fields);
     if (empty($sql)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    // ==================== NEXT TABLE =====================
    //                  company_table
    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
     $contact_company = $xartable['contact_company'];

     $fields = array(
        'xar_id'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_name'=>array('type'=>'varchar','size'=>250, 'null'=>FALSE),
        'xar_address'=>array('type'=>'varchar','size'=>250, 'null'=>FALSE),
        'xar_address2'=>array('type'=>'varchar','size'=>250, 'null'=>FALSE),
        'xar_city'=>array('type'=>'varchar','size'=>60, 'null'=>FALSE),
        'xar_state'=>array('type'=>'varchar','size'=>60, 'null'=>FALSE),
        'xar_zip'=>array('type'=>'varchar','size'=>20, 'null'=>FALSE),
        'xar_country'=>array('type'=>'varchar','size'=>60, 'null'=>FALSE),
        'xar_phone'=>array('type'=>'varchar','size'=>20, 'null'=>FALSE),
        'xar_fax'=>array('type'=>'varchar','size'=>20, 'null'=>FALSE),
        'xar_mail'=>array('type'=>'varchar','size'=>250, 'null'=>FALSE),
        'xar_logo'=>array('type'=>'varchar','size'=>200, 'null'=>FALSE),
        'xar_hide'=>array('type'=>'integer','size'=>'tiny','default'=>'0', 'null'=>FALSE),
        'xar_defaultcountry'=>array('type'=>'varchar','size'=>60,'default'=>'eng', 'null'=>FALSE)

    );
     // Create the Table - the function will return the SQL is successful or
    // FALSE if it fails to build the SQL
    $sql = xarDBCreateTable($contact_company,$fields);
     if (empty($sql)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    // ==================== NEXT TABLE =====================
    //                langmembers_table
    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
     $contact_langmembers = $xartable['contact_langmembers'];

     $fields = array(
        'xar_id'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_cid'=>array('type'=>'varchar','size'=>11, 'null'=>FALSE)
    );
     // Create the Table - the function will return the SQL is successful or
    // FALSE if it fails to build the SQL
    $sql = xarDBCreateTable($contact_langmembers,$fields);
     if (empty($sql)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    // ==================== NEXT TABLE =====================
    //               countrymembers_table
    // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    $contact_country_members = $xartable['contact_countrymembers'];

     $fields = array(
        'xar_id'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_cid'=>array('type'=>'varchar','size'=>11, 'null'=>FALSE)
    );

     // Create the Table - the function will return the SQL is successful or
    // FALSE if it fails to build the SQL
    $sql = xarDBCreateTable($contact_country_members,$fields);
     if (empty($sql)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // ----------------------------------------------------------------------------------
    // Initialisation successful

    // ----------------------------------------------------------------------------------------
    // Contact Type

   $id = $dbconn->GenId($contact_departments);
    $query = "INSERT INTO $contact_departments (xar_id, xar_email, xar_name, xar_phone, xar_fax, xar_state, xar_country, xar_cid, xar_hide) VALUES ($id, 'support@yourdomain.com', 'Support', '111-111-11111', '222-222-2222', 'PA', 'USA', '1', '1');";
    $dbconn->Execute($query);

    // Check for db errors
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $dbconn->ErrorMsg(), $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return NULL;
    }

  $id = $dbconn->GenId($contact_infotype);
    $query = "INSERT INTO $contact_infotype (xar_id, xar_name, xar_cid) VALUES ($id, 'Home', '1');";
    $dbconn->Execute($query);

    // Check for db errors
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $dbconn->ErrorMsg(), $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return NULL;
    }

    $id = $dbconn->GenId($contact_infotype);
    $query = "INSERT INTO $contact_infotype (xar_id, xar_name, xar_cid) VALUES ($id, 'Work', '2');";
    $dbconn->Execute($query);

    // Check for db errors
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $dbconn->ErrorMsg(), $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return NULL;
    }

    $id = $dbconn->GenId($contact_infotype);
    $query = "INSERT INTO $contact_infotype (xar_id, xar_name, xar_cid) VALUES ($id, 'Private', '3');";
    $dbconn->Execute($query);

    // Check for db errors
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $dbconn->ErrorMsg(), $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return NULL;
    }

    // ----------------------------------------------------------------------------------------
    //Company
    $id = $dbconn->GenId($contact_company);
    $query = "INSERT INTO $contact_company (xar_id, xar_name, xar_address, xar_address2, xar_city, xar_state, xar_zip, xar_country, xar_phone, xar_fax, xar_mail, xar_logo, xar_hide, xar_defaultcountry) VALUES ($id, 'Company Name', 'Your Address', ' ', 'Your City', 'Your State', 'Postal Code', 'Country', '111-111-1111','222-222-2222','your@email','/modules/contact/xarimages/logo.gif','0','eng');";
    $dbconn->Execute($query);

    // Check for db errors
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $dbconn->ErrorMsg(), $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return NULL;
    }

    //City
    $id = $dbconn->GenId($contact_city);
    $query = "INSERT INTO $contact_city (xar_id, xar_name, xar_cid) VALUES ($id, 'Add City Below','1');";
    $dbconn->Execute($query);

    // Check for db errors
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $dbconn->ErrorMsg(), $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return NULL;
    }
     //Country
    $id = $dbconn->GenId($contact_country);
    $query = "INSERT INTO $contact_country (xar_id, xar_name, xar_lang) VALUES ($id, 'USA','eng');";
    $dbconn->Execute($query);

    // Check for db errors
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $dbconn->ErrorMsg(), $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return NULL;
    }
    // Titles
    $id = $dbconn->GenId($contact_titles);
    $query = "INSERT INTO $contact_titles (xar_id, xar_name, xar_cid) VALUES ($id, 'Support', '3');";
    $dbconn->Execute($query);

    // Check for db errors
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $dbconn->ErrorMsg(), $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return NULL;
    }
      $id = $dbconn->GenId($contact_titles);
    $query = "INSERT INTO $contact_titles (xar_id, xar_name, xar_cid) VALUES ($id, 'Programmer', '4');";
    $dbconn->Execute($query);

    // Check for db errors
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $dbconn->ErrorMsg(), $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return NULL;
    }
       $id = $dbconn->GenId($contact_titles);
    $query = "INSERT INTO $contact_titles (xar_id, xar_name, xar_cid) VALUES ($id, 'Sales', '5');";
    $dbconn->Execute($query);

    // Check for db errors
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $dbconn->ErrorMsg(), $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return NULL;
    }
      $id = $dbconn->GenId($contact_titles);
    $query = "INSERT INTO $contact_titles (xar_id, xar_name, xar_cid) VALUES ($id, 'Webmaster', '6');";
    $dbconn->Execute($query);

    // Check for db errors
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $dbconn->ErrorMsg(), $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return NULL;
    }

    /*********************************************************************
    * Define instances for this module
    * Format is
    * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
    *********************************************************************/
    // Define instance for person
    $query1 = "SELECT DISTINCT xar_lastname FROM $contact_persons";
    $query2 = "SELECT DISTINCT xar_mail FROM $contact_persons";

    $instances = array(
                        array('header' => 'Contact Last Name:',
                                'query' => $query1,
                                'limit' => 20
                            ),
                        array('header' => 'Contact Email:',
                                'query' => $query2,
                                'limit' => 20
                            )
                    );

     xarDefineInstance('contact', 'ContactPerson', $instances, 0, '', '', '', 'Security instance for Contact Persons.');

    // Define Instance for company
    $query1 = "SELECT DISTINCT xar_name FROM $contact_company";
    $query2 = "SELECT DISTINCT xar_mail FROM $contact_company";

    $instances = array(
                        array('header' => 'Contact Company Name:',
                                'query' => $query1,
                                'limit' => 20
                            ),
                        array('header' => 'Contact Company Email:',
                                'query' => $query2,
                                'limit' => 20
                            )
                    );

     xarDefineInstance('contact', 'ContactCompany', $instances, 0, '', '', '', 'Security instance for Contact Companies.');

    // Define Instance for department
    $query1 = "SELECT DISTINCT xar_name FROM $contact_departments";
    $query2 = "SELECT DISTINCT xar_email FROM $contact_departments";

    $instances = array(
                        array('header' => 'Contact Department Name:',
                                'query' => $query1,
                                'limit' => 20
                            ),
                        array('header' => 'Contact Department Email:',
                                'query' => $query2,
                                'limit' => 20
                            )
                    );

     xarDefineInstance('contact', 'ContactDepartment', $instances, 0, '', '', '', 'Security instance for Contact Departments.');

    // Define Instance for city
    $query1 = "SELECT DISTINCT xar_name FROM $contact_city";

    $instances = array(
                        array('header' => 'Contact City Name:',
                                'query' => $query1,
                                'limit' => 20
                            )
                    );

     xarDefineInstance('contact', 'ContactCity', $instances, 0, '', '', '', 'Security instance for Contact Cities.');

    // Define Instance for country
    $query1 = "SELECT DISTINCT xar_name FROM $contact_country";

    $instances = array(
                        array('header' => 'Contact Country Name:',
                                'query' => $query1,
                                'limit' => 20
                            )
                    );

     xarDefineInstance('contact', 'ContactCountry', $instances, 0, '', '', '', 'Security instance for Contact Countries.');


    /*********************************************************************
    * Register the module components that are privileges objects
    * Format is
    * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    *********************************************************************/
    xarRegisterMask('ContactReadBlock','All','Contact','Block','All','ACCESS_OVERVIEW');
    xarRegisterMask('ContactView','All','Contact','Item','All:All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ContactRead','All','Contact','Item','All:All:All','ACCESS_READ');
    xarRegisterMask('ContactAdd','All','Contact','Item','All','ACCESS_ADD');
    xarRegisterMask('ContactEdit','All','Contact','Item','All','ACCESS_EDIT');
    xarRegisterMask('ContactDelete','All','Contact','Item','All','ACCESS_DELETE');
    xarRegisterMask('ContactOverview','All','Contact','Item','All','ACCESS_OVERVIEW');
    xarRegisterMask('ContactRead','All','Contact','Item','All','ACCESS_READ');
   return true;
}

/**
 * upgrade the template module from an old version
 * This function Agent_Registration_user_can be called multiple times
 */
function contact_upgrade($oldversion)
{
    // Load table maintenance API
    xarDBLoadTableMaintenanceAPI();

    $dbconn =& xarDBGetConn();
    $xartable        =& xarDBGetTables();
    if(!is_array($xartable)){
        echo "<p><b>No tables found to update!</b></p>";
        return false;
    }

    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.2.3':
            $contact_attributes = $xartable['contact_attributes'];

            $fields = array(
                'xar_id'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
                'xar_contacttype'=>array('type'=>'varchar','size'=>2,'default'=>'P','null'=>FALSE),
                'xar_showname'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showaddress'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showaddress2'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showcity'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showstate'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showzip'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showcountry'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showemail'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showphone'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showfax'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showmobile'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showpager'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showICQ'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showAIM'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showYIM'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showMSNM'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showtitle'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showdepartment'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE),
                'xar_showimage'=>array('type'=>'integer','size'=>'tiny','default'=>'0','null'=>FALSE)
            );

            // Create the Table - the function will return the SQL is successful or
            // FALSE if it fails to build the SQL
            $sql = xarDBCreateTable( $contact_attributes,$fields);
            if (empty($sql)) return; // throw back

            // Pass the Table Create DDL to adodb to create the table
            $dbconn->Execute($sql);

            // Check for an error with the database code, and if so raise the
            // appropriate exception
            if ($dbconn->ErrorNo() != 0) {
                $msg = xarMLByKey('DATABASE_ERROR', $query);
                xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
                return;
            }


            // Now comes the hard work, get all the OLD user attributes
            // into the new table and then delete the fields that is no longer in use
            //
            $table = $xartable['contact_attributes'];
            $contactType = "P";
            $showState = 0;
            $fields = "xar_id, xar_contacttype
                ,xar_showname, xar_showaddress
                ,xar_showaddress2, xar_showcity
                ,xar_showstate, xar_showzip
                ,xar_showcountry, xar_showemail
                ,xar_showphone, xar_showfax
                ,xar_showmobile, xar_showpager
                ,xar_showICQ, xar_showAIM
                ,xar_showYIM, xar_showMSNM
                ,xar_showtitle, xar_showdepartment
                ,xar_showimage";

            // Old table
            $person_table        = $xartable['contact_persons'];
            $person_column        = array(
                'id'            => $person_table . '.personID',
                'showname'      => $person_table . '.personShowName',
                'showaddress'   => $person_table . '.personShowAddress',
                'showaddress2'  => $person_table . '.personShowAddress2',
                'showcity'      => $person_table . '.personShowCity',
                'showzip'       => $person_table . '.personShowZip',
                'showcountry'   => $person_table . '.personShowCountry',
                'showphone'     => $person_table . '.personShowPhone',
                'showfax'       => $person_table . '.personShowFax',
                'showmobile'    => $person_table . '.personShowMobile',
                'showpager'     => $person_table . '.personShowPager',
                'showemail'     => $person_table . '.personShowEmail',
                'showtitle'     => $person_table . '.personShowTitle',
                'showICQ'       => $person_table . '.personShowICQ',
                'showAIM'       => $person_table . '.personShowAIM',
                'showYIM'       => $person_table . '.personShowYIM',
                'showMSNM'      => $person_table . '.personShowMSNM',
                'showdepartment' => $person_table . '.personShowDepartment',
                'showimage'     => $person_table . '.personShowImage'
                );

            $sql = "SELECT ".$person_column['id'].
                ", ".$person_column['showname'].", ".$person_column['showaddress'].
                ", ".$person_column['showaddress2'].", ".$person_column['showcity'].
                ", ".$person_column['showzip'].", ".$person_column['showcountry'].
                ", ".$person_column['showphone'].", ".$person_column['showfax'].
                ", ".$person_column['showmobile'].", ".$person_column['showpager'].
                ", ".$person_column['showemail'].", ".$person_column['showtitle'].
                ", ".$person_column['showICQ'].", ".$person_column['showAIM'].
                ", ".$person_column['showYIM'].", ".$person_column['showMSNM'].
                ", ".$person_column['showdepartment'].", ".$person_column['showimage'].
                " FROM ".$person_table;

            $result = $dbconn->Execute( $query );
            if ( $result == false ) {
                xarExceptionSet(XAR_USER_EXCEPTION, 'DATABASE_ERROR_QUERY',
                   new SystemException($query));
                return false;   
            }

            for (; !$result->EOF; $result->MoveNext() ) {
                $values = "'".$result->fields[0].
                    "','".$contactType.
                    "','".$result->fields[1].
                    "','".$result->fields[2].
                    "','".$result->fields[3].
                    "','".$result->fields[4].
                    "','".$showState.
                    "','".$result->fields[5].
                    "','".$result->fields[6].
                    "','".$result->fields[7].
                    "','".$result->fields[8].
                    "','".$result->fields[9].
                    "','".$result->fields[10].
                    "','".$result->fields[11].
                    "','".$result->fields[12].
                    "','".$result->fields[13].
                    "','".$result->fields[14].
                    "','".$result->fields[15].
                    "','".$result->fields[16].
                    "','".$result->fields[17].
                    "','".$result->fields[18]."'";

                $sqlInsert = "INSERT INTO ".$table." (".$fields.") VALUES (".$values.")";
                $dbconn->Execute( $sqlInsert );
                if ($dbconn->ErrorNo() != 0) {
                    xarExceptionSet(XAR_USER_EXCEPTION, 'DATABASE_ERROR_QUERY',
                        new SystemException($sqlInsert));
                    return false;
                }
            }

        case '0.2.4':
            // Update the person table
            $personsTable = $xartable['contact_persons'];
            $departmentTable = $xartable['contact_departments'];
            $companyTable = $xartable['contact_company'];

            $queries = array ();

            $queries[] = xarDBAlterTable(
                $personsTable,
                array ( 
                    'command'   => 'add',
                    'field'     => 'state',
                    'type'      => 'varchar',
                    'size'      => '60',
                    'null'      => false
                )
            );

            $queries[] = xarDBAlterTable(
                $departmentTable,
                array ( 
                    'command'   => 'add',
                    'field'     => 'state',
                    'type'      => 'varchar',
                    'size'      => '60',
                    'null'      => false
                )
            );

            $queries[] = xarDBAlterTable(
                $companyTable,
                array ( 
                    'command'   => 'add',
                    'field'     => 'state',
                    'type'      => 'varchar',
                    'size'      => '60',
                    'null'      => false
                )
            );

             foreach ($queries as $query)
            {
                // Pass to ADODB, and send exception if the result isn't valid.
                $result =& $dbconn->Execute($query);
                if (!$result) {
                    //return;
                    // Until we have a better method of handling errors, it is safer to continue.
                    xarErrorHandled();
                }
            }

        case '0.2.5':
            //    Code to upgrade from version 0.2.4 goes here
            break;
    }

    // Update successful
    return true;
}

/**
 * delete the PostContact module
 * This function Agent_Registration_user_is only ever called once during the lifetime of a particular
 * module instance
 */
function contact_delete()
{
    // Get datbase setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For xarDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Load table maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['contact_departments']);
    if (empty($query)) return false; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return false;

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['contact_dept_members']);
    if (empty($query)) return false; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return false;
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['contact_titles']);
    if (empty($query)) return false; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return false;
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['contact_city']);
    if (empty($query)) return false; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return false;
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['contact_country']);
    if (empty($query)) return false; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return false;
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['contact_infotype']);
    if (empty($query)) return false; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return false;
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['contact_persons']);
    if (empty($query)) return false; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return false;
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['contact_attributes']);
    if (empty($query)) return false; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return false;
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['contact_langmembers']);
    if (empty($query)) return false; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return false;
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['contact_countrymembers']);
    if (empty($query)) return false; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return false;

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['contact_company']);
    if (empty($query)) return false; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return false;

    // Remove privileges, security masks and instances
    xarRemoveMasks('contact');
    xarRemoveInstances('contact');
    xarRemovePrivileges('contact');

    return true;
}

?>
