<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 * 
 * PayPal IPN
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * @subpackage paypalsetup module
 * @author John Cox <niceguyeddie@xaraya.com> 
 */

//Load Table Maintainance API
xarDBLoadTableMaintenanceAPI();

function paypalipn_init()
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['ipnlog'];

    $fields = array(
    'xar_id'           => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
    'xar_log'          => array('type'=>'text')
    );

    $query = xarDBCreateTable($table,$fields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    $index = array('name'      => 'i_xar_ipnlog_1',
                   'fields'    => array('xar_id'),
                   'unique'    => TRUE);
    $query = xarDBCreateIndex($table,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    xarRegisterMask('AdminPayPalIPN', 'All', 'paypalipn', 'All', 'All', 'ACCESS_ADMIN');
    return true;
} 

function paypalipn_upgrade($oldversion)
{
    switch($oldversion){
       case '1.0':
       case '1.0.0':
            // Set up database tables
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $table = $xartable['ipnlog'];

            $fields = array(
            'xar_id'           => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
            'xar_log'          => array('type'=>'text')
            );

            $query = xarDBCreateTable($table,$fields);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
            $index = array('name'      => 'i_xar_ipnlog_1',
                           'fields'    => array('xar_id'),
                           'unique'    => TRUE);
            $query = xarDBCreateIndex($table,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            break;
    }
    return true;
}

function paypalipn_delete()
{
    // Drop the table
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['ipnlog'];
    $query = xarDBDropTable($table);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Remove module variables
    xarModDelAllVars('paypalipn');

    // Remove Masks and Instances
    xarRemoveMasks('paypalipn');
    xarRemoveInstances('paypalipn');
    return true;
}
?>