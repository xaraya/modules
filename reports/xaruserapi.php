<?php

/**
 * File: $Id$
 *
 * User api of reports module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage reports
 * @author Marcel van der Boom <marcel@hsdev.com>
*/


/**
 * Get a report connection
 *
 * Retrieves the connection info for a certain report
 *
 * @author  Marcel van der Boom <marcel@hsdev.com>
 * @access  public 
 * @param   conn_id integer identification of connection
 * @return  boolean 
*/
function reports_userapi_connection_get($args) {
	list($conn_id) = xarVarCleanFromInput('conn_id');
	extract($args);
    
	list($dbconn) = xarDBGetConn();
	$xartables = xarDBGetTables();
	$tab = $xartables['report_connections'];
	$cols = &$xartables['report_connections_column'];
    
	$sql = "SELECT $cols[id],$cols[name],$cols[description],$cols[server],$cols[type],$cols[database],$cols[user],$cols[password] "
		."FROM $tab WHERE $cols[id]='".xarVarPrepForStore($conn_id)."'";
	$res= $dbconn->Execute($sql);
	if ($res) {
		$row = $res->fields;
        return  array (
                       'id'=>$row[0],
                       'name'=>$row[1],
                       'description'=>$row[2],
                       'server'=>$row[3],
                       'type'=>$row[4],
                       'database'=>$row[5],
                       'user'=>$row[6],
                       'password'=>$row[7]);
	} else {
		return false;
	}
}
/**
 * Get all connections
 *
 */
function reports_userapi_connection_getall() {
	list($dbconn) = xarDBGetConn();
	$xartables = xarDBGetTables();
	$tab = $xartables['report_connections'];
	$cols = &$xartables['report_connections_column'];
    
	$sql = "SELECT $cols[id],$cols[name],$cols[description],$cols[server],$cols[type],$cols[database],$cols[user],$cols[password] "
		."FROM $tab";
	$res= $dbconn->Execute($sql);
	if ($res) {
		$ret = array();
		while (!($res->EOF)) {
			$row = $res->fields;
			$ret[] =  array (
                             'id'=>$row[0],
                             'name'=>$row[1],
                             'description'=>$row[2],
                             'server'=>$row[3],
                             'type'=>$row[4],
                             'database'=>$row[5],
                             'user'=>$row[6],
                             'password'=>$row[7]);
			$res->MoveNext();
		}
		return $ret;
	} else {
		return false;
	}
}

/**
 * Get a report
 *
 */
function reports_userapi_report_get($args) {
	list($rep_id) = xarVarCleanFromInput('rep_id');
	extract($args);
    
	list($dbconn) = xarDBGetConn();
	$xartables = xarDBGetTables();
	$tab = $xartables['reports'];
	$cols = &$xartables['reports_column'];
    
	$sql = "SELECT $cols[id],$cols[name],$cols[description],$cols[conn_id],$cols[xmlfile] "
		."FROM $tab WHERE $cols[id]='".xarVarPrepForStore($rep_id)."'";
	$res= $dbconn->Execute($sql);
	if ($res) {
		$row = $res->fields;
        return  array (
                       'id'=>$row[0],
                       'name'=>$row[1],
                       'description'=>$row[2],
                       'conn_id'=>$row[3],
                       'xmlfile'=>$row[4]);
        
		
	} else {
		return false;
	}
}

/**
 * Get all reports
 *
 */
function reports_userapi_report_getall() {
	list($dbconn) = xarDBGetConn();
	$xartables = xarDBGetTables();
	$tab = $xartables['reports'];
	$cols = &$xartables['reports_column'];
    
	$sql = "SELECT $cols[id],$cols[name],$cols[description],$cols[conn_id],$cols[xmlfile] "
		."FROM $tab";
	$res= $dbconn->Execute($sql);
	if ($res) {
		$ret = array();
		while (!($res->EOF)) {
			$row = $res->fields;
			$ret[] = array (
                            'id'=>$row[0],
                            'name'=>$row[1],
                            'description'=>$row[2],
                            'conn_id'=>$row[3],
                            'xmlfile'=>$row[4]);
			$res->MoveNext();
		}
		return $ret;
	} else {
		return false;
	}
}

?>