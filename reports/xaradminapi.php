<?php

/**
 * File: $Id$
 *
 * Administrative API for reports module
 *
 * @package module
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage reports
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


//-----------------------------------------------------------------
//
//  Config administrative functions
//
// Only the update functions exists, config cannot be deleted and is
// the only instance of the object
//-----------------------------------------------------------------
function reports_adminapi_update_config($args) {
	// Get parameters
	extract($args);
	
	// Update config variables
	xarModSetVar('reports','reports_location',$config_replocation);
	xarModSetVar('reports','images_location',$config_imglocation);
	xarModSetVar('reports','pdf_backend',$config_pdfbackend);
    return true;
}


//-----------------------------------------------------------------
//
//  Report administrative functions
//
//-----------------------------------------------------------------
function reports_adminapi_create_report($args) {
	//Get arguments
	extract($args);

	list($dbconn) = xarDbGetConn();
	$xartables = xarDbGetTables();
	$tab = $xartables['reports'];
	$cols = &$xartables['reports_column'];

	$conn_id = $dbconn->GenId();

	$sql = "INSERT INTO $tab ($cols[id],$cols[conn_id],$cols[name],$cols[description],$cols[xmlfile]) VALUES ('"
		.xarVarPrepForStore($rep_id)."','"
		.xarVarPrepForStore($rep_conn_id)."','"
		.xarVarPrepForStore($rep_name)."','"
		.xarVarPrepForStore($rep_desc)."','"
		.xarVarPrepForStore($rep_xmlfile)."')";

	if($dbconn->Execute($sql)) {
		return true;
	} else {
		return false;
	}
	return true;
}

function reports_adminapi_update_report($args) {
	//Get arguments
	extract($args);

	list($dbconn) = xarDbGetConn();
	$xartables = xarDbGetTables();
	$tab = $xartables['reports'];
	$cols = &$xartables['reports_column'];

	$sql = "UPDATE $tab SET "
		."$cols[name]='".xarVarPrepForStore($rep_name)."',"
    ."$cols[description]='".xarVarPrepForStore($rep_desc)."',"
    ."$cols[conn_id]='".xarVarPrepForStore($rep_conn)."',"
    ."$cols[xmlfile]='".xarVarPrepForStore($rep_xmlfile)."' "
		."WHERE $cols[id]='".xarVarPrepForStore($rep_id)."'";


	if($dbconn->Execute($sql)) {
		return true;
	} else {
		return false;
	}
	return true;
}

function reports_adminapi_delete_report($args) {
	//Get arguments
	extract($args);

	list($dbconn) = xarDbGetConn();
	$xartables = xarDbGetTables();
	$tab = $xartables['reports'];
	$cols = &$xartables['reports_column'];

	$sql = "DELETE FROM $tab WHERE $cols[id] = '".xarVarPrepForStore($rep_id)."'";
	if($dbconn->Execute($sql)) {
		return true;
	} else {
		return false;
	}
	return true;
}

//-----------------------------------------------------------------
//
//  Connection administrative operational functions
//
//-----------------------------------------------------------------
function reports_adminapi_create_connection($args) {
	//Get arguments
	extract($args);

	list($dbconn) = xarDbGetConn();
	$xartables = xarDbGetTables();
	$tab = $xartables['report_connections'];
	$cols = &$xartables['report_connections_column'];

	$conn_id = $dbconn->GenId();

	$sql = "INSERT INTO $tab ($cols[id],$cols[name],$cols[description],$cols[server],$cols[type],$cols[database],$cols[user],$cols[password]) "
		."VALUES ('"
		.xarVarPrepForStore($conn_id)."','"
		.xarVarPrepForStore($conn_name)."','"
		.xarVarPrepForStore($conn_desc)."','"
		.xarVarPrepForStore($conn_server)."','"
		.xarVarPrepForStore($conn_type)."','"
		.xarVarPrepForStore($conn_database)."','"
		.xarVarPrepForStore($conn_user)."','"
		.xarVarPrepForStore($conn_password)."')";


	if($dbconn->Execute($sql)) {
		return true;
	} else {
		return false;
	}
	return true;
}

function reports_adminapi_update_connection($args) {
	//Get arguments
	extract($args);

	list($dbconn) = xarDbGetConn();
	$xartables = xarDbGetTables();
	$tab = $xartables['report_connections'];
	$cols = &$xartables['report_connections_column'];

	$sql = "UPDATE $tab SET "
		."$cols[name]='".xarVarPrepForStore($conn_name)."',"
        ."$cols[description]='".xarVarPrepForStore($conn_desc)."',"
        ."$cols[server]='".xarVarPrepForStore($conn_server)."',"
        ."$cols[type]='".xarVarPrepForStore($conn_type)."',"
        ."$cols[database]='".xarVarPrepForStore($conn_database)."',"
        ."$cols[user]='".xarVarPrepForStore($conn_user)."',"
        ."$cols[password]='".xarVarPrepForStore($conn_password)."' "
		."WHERE $cols[id]='".xarVarPrepForStore($conn_id)."'";
    
    
	if($dbconn->Execute($sql)) {
		return true;
	} else {
		return false;
	}
	return true;
}

function reports_adminapi_delete_connection($args) {
	//Get arguments
	extract($args);
    
	list($dbconn) = xarDbGetConn();
	$xartables = xarDbGetTables();
	$tab = $xartables['report_connections'];
	$cols = &$xartables['report_connections_column'];

	$sql = "DELETE FROM $tab WHERE $cols[id] = '".xarVarPrepForStore($conn_id)."'";
	if($dbconn->Execute($sql)) {
		return true;
	} else {
		return false;
	}
	return true;
}

function reports_adminapi_getmenulinks() {
    $menulinks[] = array('url'   => xarModURL('reports','admin','view_connections'),
                         'label' => xarML('View connections'),
                         'title' => xarML('List registered report connections'));
    $menulinks[] = array('url'   => xarModURL('reports','admin','view_reports'),
                         'label' => xarML('View reports'),
                         'title' => xarML('List registered report definitions'));
    $menulinks[] = array('url'   => xarModURL('reports','admin','modify_config'),
                         'label' => xarML('Modify config'),
                         'title' => xarML('Modify reports configuration'));

    return $menulinks;
}
?>