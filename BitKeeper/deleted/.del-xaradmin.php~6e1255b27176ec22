<?php

/**
 * File: $Id$
 *
 * Admin gui for reports module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage reports
 * @author Marcel van der Boom <marcel@hsdev.com>
*/


/**
 * Entry function, just display the menu
 */
function reports_admin_main() {
    return array();
}

/**
 * Standard view function
 *
 */
function reports_admin_view() {
    xarResponseRedirect(xarModUrl('report','admin','view_reports'));
    return true;
}

/**
 * Display a list of defined connections
 *
 */
function reports_admin_view_connections() {
	// Get a list of connections
	$connections = xarModAPIFunc('reports','user','connection_getall',array());

    // FIXME: it shouldn't be necessary to prep here
    foreach ($connections as $key => $connection) {
        $connections[$key]['name']=xarVarPrepForDisplay($connections[$key]['name']);
        $connections[$key]['description']=xarVarPrepForDisplay($connections[$key]['description']);
    }

    $data['connections']=$connections;
    return $data;
}

/*
 * Display a list of defined reports
 */
function reports_admin_view_reports() {
	// Get a list of reports
	$reports = xarModAPIFunc('reports','user','report_getall',array());
    
    // Include connection info for each report
    foreach ($reports as $key => $report) {
        $reports[$key]['name']=xarVarPrepForDisplay($reports[$key]['name']);
        $reports[$key]['connection'] = xarModAPIFunc('reports','user','connection_get',array('conn_id'=>$report['conn_id']));
    }

    $data['reports']=$reports;
    return $data;
}

/**
 * Show form to define a new connection
 */
function reports_admin_new_connection() {
    $data=array(
                'authid' => xarSecGenAuthKey(),
                'conn_id' => 0,
                'createlabel' => xarML('Create Connection'),
                'name' => xarML('(untitled connection)'),
                'description' => xarML('no description'),
                'type' => 'mysql',
                'server' => 'localhost',
                'database' => 'dbname',
                'user' => 'username',
                'password' => '');
	return $data;
}

/**
 * Gather entered info and let admin api process creation of new connection
 */
function reports_admin_create_connection($args) {
    list($conn_name, $conn_desc,$conn_type,$conn_server,$conn_database,$conn_user,$conn_password) = 
		xarVarCleanFromInput('conn_name','conn_desc','conn_type','conn_server','conn_database','conn_user','conn_password');
	extract($args);
    
    // Only desc, user and password may be empty, rest must have values
    
	// Confirm authorization key
    if (!xarSecConfirmAuthKey()) {
        // TODO: exception?
        return false;
    }
    
    if (!xarModAPIFunc('reports','admin','create_connection',array('conn_name'=>$conn_name,'conn_desc'=>$conn_desc,	
                                                                   'conn_type'=>$conn_type,'conn_server'=>$conn_server,
                                                                   'conn_database'=>$conn_database,'conn_user'=>$conn_user,
                                                                   'conn_password'=>$conn_password))) {
        // Create failed
        // TODO: exception
        xarSessionSetVar('errormsg', xarML("Report creation failed"));
    }
    
    // Redisplay the connection screen (thus showing the newly added connection
    xarResponseRedirect(xarModUrl('reports','admin','view_connections',array()));
    return true;
}

/** 
 * Modify a connection
 */
function reports_admin_modify_connection($args) {
	list($conn_id) = xarVarCleanFromInput('conn_id');
	extract($args);

	$conn = xarModAPIFunc('reports','user','connection_get',array('conn_id'=>$conn_id));
    extract($conn);
    $data=array(
                'authid' => xarSecGenAuthKey(),
                'updatelabel' => xarML('Update Connection'),
                'conn_id' => $conn_id,
                'name' => $conn['name'],
                'description' => $conn['description'],
                'type' => $conn['type'],
                'server' => $conn['server'],
                'database' => $conn['database'],
                'user' => $conn['user'],
                'password' => $conn['password']);    
	
	return $data;
}

/**
 * Pass update to admin api
 */
function reports_admin_update_connection($args) {
	list($conn_name, $conn_desc,$conn_type,$conn_server,$conn_database,$conn_user,$conn_password,$conn_id) = 
		xarVarCleanFromInput('conn_name','conn_desc','conn_type','conn_server','conn_database','conn_user','conn_password','conn_id');
	extract($args);
    
	// Only desc, user and password may be empty, rest must have values
    
	// Confirm authorization key
    if (!xarSecConfirmAuthKey()) {
        return false;
    } else {
        if (!xarModAPIFunc('reports','admin','update_connection',array('conn_name'=>$conn_name,'conn_desc'=>$conn_desc,	
                                                                       'conn_type'=>$conn_type,'conn_server'=>$conn_server,
                                                                       'conn_database'=>$conn_database,'conn_user'=>$conn_user,
                                                                       'conn_password'=>$conn_password,'conn_id'=>$conn_id))) {
            // Create failed
            xarSessionSetVar('errormsg', xarML("Update connection failed"));
        }
    }
	    
	// Redisplay the connection screen (thus showing the newly added connection
	xarResponseRedirect(xarModUrl('reports','admin','view_connections',array()));
    
	return true;
}

/**
 * Process a delete request for connections
 */
function reports_admin_delete_connection($args) {
	list($conn_id) = xarVarCleanFromInput('conn_id');
	extract($args);
    
    if (!xarModAPIFunc('reports','admin','delete_connection',array('conn_id'=>$conn_id))) {
        xarSessionSetVar('errormsg',xarML("Delete connection failed"));
    }
	
	xarResponseRedirect(xarModUrl('reports','admin','view_connections',array()));
}

/**
 * Show form to define a new report
 */
function reports_admin_new_report() {
    // Get all connections
    $connections = xarModAPIFunc('reports','user','connection_getall',array());
	
    $data = array ('rep_id' => 0,
                   'authid' => xarSecGenAuthKey(),
                   'name' => '(untitled report)',
                   'description' => 'no description',
                   'xmlfile' => 'empty.xml',
                   'rep_conn_id' => 0,
                   'createlabel' => xarML('Create report'),
                   'connections' => $connections
                   );
    return $data;
}

/**
 * Gather entered info and let admin api process new report creation
 */
function reports_admin_create_report($args) {
	list($rep_id, $rep_name, $rep_desc,$rep_xmlfile, $rep_conn_id) = 
		xarVarCleanFromInput('rep_id','rep_name','rep_desc','rep_xmlfile', 'rep_conn_id');
	extract($args);
    
	// Only desc, user and password may be empty, rest must have values
    if (!xarSecConfirmAuthKey()) {
        // TODO: exception?
        return false;
    } else {
        if (!xarModAPIFunc('reports',
                           'admin',
                           'create_report',
                           array('rep_id'=>$rep_id,
                                 'rep_name'=>$rep_name,
                                 'rep_desc'=>$rep_desc,
                                 'rep_xmlfile'=>$rep_xmlfile,
                                 'rep_conn_id'=>$rep_conn_id
                                 )
                           )
            ) { 
            // Create failed
            xarSessionSetVar('errormsg', xarML("Create report failed"));
        }
    }
	
	// Go back to reports menu and display status and or errors
	xarResponseRedirect(xarModUrl('reports','admin','view_reports',array()));
	return true;
}

/** 
 * Modify a report
 */
function reports_admin_modify_report($args) {
	list($rep_id) = xarVarCleanFromInput('rep_id');
	extract($args);
    
	$rep = xarModAPIFunc('reports','user','report_get',array('rep_id'=>$rep_id));
	$connections= xarModAPIFunc('reports','user','connection_getall',array());
    $data=array ('authid' => xarSecGenAuthKey(),
                 'updatelabel' => xarML('Update Report'),
                 'rep_id' => $rep_id,
                 'name' => $rep['name'],
                 'description' => $rep['description'],
                 'xmlfile' => $rep['xmlfile'],
                 'rep_conn_id' => $rep['conn_id'],
                 'connections' => $connections
                 );
	return $data;
}

/**
 * Pass update to admin api
 */
function reports_admin_update_report($args) {
	list($rep_id, $rep_name, $rep_desc,$rep_conn,$rep_xmlfile) = 
		xarVarCleanFromInput('rep_id','rep_name','rep_desc','rep_conn_id','rep_xmlfile');
	extract($args);
    
	// Only desc, user and password may be empty, rest must have values
    
    if (!xarSecConfirmAuthKey()) {
        return false;
        // TODO: exception?
    } else {
        if (!xarModAPIFunc('reports',
                           'admin',
                           'update_report',
                           array('rep_id'=>$rep_id, 'rep_name'=>$rep_name,
                                 'rep_desc'=>$rep_desc,'rep_conn'=>$rep_conn,
                                 'rep_xmlfile'=>$rep_xmlfile
                                 )
                           )
            ) {
            // Create failed
            return false;
            // TODO: exception?
        }
    }
    
	// Redisplay the connection screen (thus showing the newly added connection
	xarResponseRedirect(xarModUrl('reports','admin','view_reports',array()));
    return true;
}

/**
 * Process a delete request for reports
 */
function reports_admin_delete_report($args) {
	list($rep_id) = xarVarCleanFromInput('rep_id');
	extract($args);
    
    if (!xarModAPIFunc('reports','admin','delete_report',array('rep_id'=>$rep_id))) {
        return false;
    }
	
	xarResponseRedirect(xarModUrl('reports','admin','view_reports',array()));
	return true;
}

//-----------------------------------------------------------------
//
//  Config display functions
//
//-----------------------------------------------------------------
function reports_admin_modify_config() {
	$backends= array( array('id'=>'ezpdf',
                            'name'=> xarML('EzPDF (pure PHP)')), 
                      array('id'=>'yaps',
                            'name'=> xarML('YaPS (GS based)')), 
                      array('id'=>'pdflib',
                            'name'=> xarML('pdfLib (C-library)'))
                      );
    
	$data = array('authid' => xarSecGenAuthKey(),
                  'rep_location' => xarModGetVar('reports','reports_location'),
                  'img_location' => xarModGetVar('reports','images_location'),
                  'backends' => $backends,
                  'selectedbackend' => xarModGetVar('reports','pdf_backend')
                  );
    
	return $data;
}

/**
 * Update configuration
 */
function reports_admin_update_config($args) {
	// Get parameters
	list($config_replocation, $config_imglocation, $config_pdfbackend) =
		xarVarCleanFromInput('config_replocation','config_imglocation','config_pdfbackend');
	extract($args);
	
	if (!xarSecConfirmAuthKey()) {
		//TODO: exception
        return false;
	} else {
        // Do the actual work
        if (!xarModAPIFunc('reports',
                           'admin',
                           'update_config',
                           array('config_replocation'=>$config_replocation,
                                 'config_imglocation'=>$config_imglocation,
                                 'config_pdfbackend'=>$config_pdfbackend
                                 )
                           )
            ) {
            return false;
        }
    }
		
	xarResponseRedirect(xarModURL('reports', 'admin', 'main'));
	return true;
	
}
?>