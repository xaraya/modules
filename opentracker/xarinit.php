<?php
/**
 * Opentracker event API functions
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage opentracker
 * @author Chris "Alley" van de Steeg
 */

 function opentracker_init()
{
    if (!function_exists('version_compare') || ! version_compare(PHP_VERSION,'4.3','>=')) {
        $msg=xarML('Your PHP configuration does not seem to be the correct version. OpenTracker requires PHP version 4.3 or newer.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'SYSTEM_ERROR',
                        new SystemException($msg));
        return;

    }

	@set_time_limit(0);
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables(); 
    xarDBLoadTableMaintenanceAPI();
	$databaseType = xarDBGetType();
    
    $fields = array(
    	'accesslog_id' => array('type' => 'integer', 'size'=>11, 'null' => false, 'default' =>'0'),
		'client_id' => array('type' => 'integer', 'size'=>10,'unsigned' => true, 'null' => false, 'default' =>'0'),
		'timestamp' => array('type' => 'integer', 'size'=>10,'unsigned' => true, 'null' => false, 'default' =>'0'),
		'document_id' => array('type' => 'integer', 'size'=>11, 'null' => false, 'default' =>'0'),
		'exit_target_id' => array('type' => 'integer', 'size'=>11, 'null' => false, 'default' =>'0'),
		'xar_uid' => array('type' => 'integer', 'size'=>11, 'null' => false, 'default' =>'0'),
		'xar_modname' => array('type' => 'varchar', 'size'=>100, 'null' => false, 'default' =>''),
		'xar_modtype' => array('type' => 'varchar', 'size'=>100, 'null' => false, 'default' =>''),
		'xar_modfunc' => array('type' => 'varchar', 'size'=>100, 'null' => false, 'default' =>''),
		'xar_instanceid' => array('type' => 'integer', 'size'=>11, 'null' => false, 'default' =>'0'),
		'entry_document' => array('type' => 'integer', 'size' => 'tiny', 'null' => false, 'default' => '0'));
	
	$query = xarDBCreateTable($xartable['accesslog'], $fields);
    if (empty($query)) return; // throw back	
	if ($databaseType == 'mysql') // TODO: find out if and how other db's support this
		$query .= ' DELAY_KEY_WRITE=1';
		
    $result = &$dbconn->Execute($query);
    if (!$result) return;
	
    $index = array(
        'name'      => 'xar_uid',
        'fields'    => array('xar_uid'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($xartable['accesslog'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array(
        'name'      => 'xar_modname',
        'fields'    => array('xar_modname'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($xartable['accesslog'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array(
        'name'      => 'full_xar_mod',
        'fields'    => array('xar_modname', 'xar_modtype', 'xar_modfunc', 'xar_instanceid'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($xartable['accesslog'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array(
        'name'      => 'entry_document',
        'fields'    => array('entry_document'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($xartable['accesslog'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    //KEY accesslog_id (accesslog_id),
    $index = array(
        'name'      => 'accesslog_id',
        'fields'    => array('accesslog_id'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($xartable['accesslog'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    //KEY client_time  (client_id, timestamp),
    $index = array(
        'name'      => 'client_time',
        'fields'    => array('client_id', 'timestamp'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($xartable['accesslog'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    //KEY document_id  (document_id)
    $index = array(
        'name'      => 'document_id',
        'fields'    => array('document_id'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($xartable['accesslog'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $fields = array(
  		'accesslog_id' => array('type' => 'integer', 'size' => 11, 'null' => false),
  		'data_field' => array('type' => 'varchar', 'size' => 32, 'null' => false),
  		'data_value' => array('type' => 'varchar', 'size' => 255, 'null' => false));
  	
	$query = xarDBCreateTable($xartable['add_data'], $fields);
    if (empty($query)) return; // throw back	
	if ($databaseType == 'mysql') // TODO: find out if and how other db's support this
		$query .= ' DELAY_KEY_WRITE=1';
		
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    //KEY accesslog_id (accesslog_id)
    $index = array(
        'name'      => 'accesslog_id',
        'fields'    => array('accesslog_id'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($xartable['add_data'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $fields = array(
		'data_id' => array('type' => 'integer', 'size' => 11, 'null' => false, 'primary_key' => true),
		'string' => array('type' => 'varchar', 'size' => 255, 'null' => false),
		'document_url' => array('type' => 'varchar', 'size' => 255, 'null' => false)    
    );
	$query = xarDBCreateTable($xartable['documents'], $fields);
    if (empty($query)) return; // throw back	
	if ($databaseType == 'mysql') // TODO: find out if and how other db's support this
		$query .= ' DELAY_KEY_WRITE=1';
		
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    $fields = array(
		'accesslog_id' => array('type' => 'integer', 'size' => 11, 'null' => false),
		'search_engine' => array('type' => 'varchar', 'size' => 64, 'null' => false),
		'keywords' => array('type' => 'varchar', 'size' => 254, 'null' => false)
    );
	
    $query = xarDBCreateTable($xartable['search_engines'], $fields);
    if (empty($query)) return; // throw back	
	if ($databaseType == 'mysql') // TODO: find out if and how other db's support this
		$query .= ' DELAY_KEY_WRITE=1';
		
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $fields = array(
		'data_id' => array('type' => 'integer', 'size' => 11, 'null' => false, 'primary_key' => true),
		'string' => array('type' => 'varchar', 'size' => 255, 'null' => false)
    );
	
    $query = xarDBCreateTable($xartable['exit_targets'], $fields);
    if (empty($query)) return; // throw back	
	if ($databaseType == 'mysql') // TODO: find out if and how other db's support this
		$query .= ' DELAY_KEY_WRITE=1';
    $result = &$dbconn->Execute($query);
    if (!$result) return;

	$query = xarDBCreateTable($xartable['hostnames'], $fields);
    if (empty($query)) return; // throw back	
	if ($databaseType == 'mysql') // TODO: find out if and how other db's support this
		$query .= ' DELAY_KEY_WRITE=1';
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateTable($xartable['operating_systems'], $fields);
    if (empty($query)) return; // throw back	
	if ($databaseType == 'mysql') // TODO: find out if and how other db's support this
		$query .= ' DELAY_KEY_WRITE=1';
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateTable($xartable['referers'], $fields);
    if (empty($query)) return; // throw back	
	if ($databaseType == 'mysql') // TODO: find out if and how other db's support this
		$query .= ' DELAY_KEY_WRITE=1';
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateTable($xartable['user_agents'], $fields);
    if (empty($query)) return; // throw back	
	if ($databaseType == 'mysql') // TODO: find out if and how other db's support this
		$query .= ' DELAY_KEY_WRITE=1';
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $fields = array(
		'accesslog_id' => array('type'=>'integer', 'size'=>11, 'null' => false, 'primary_key' => true),
		'visitor_id' => array('type'=>'integer', 'size'=>11, 'null' => false),
		'client_id' => array('type'=>'integer', 'size'=>10, 'null' => false, 'unsigned' => true),
		'operating_system_id' => array('type'=>'integer', 'size' => 11,'null' => false), 
		'user_agent_id' => array('type'=>'integer', 'size' => 11,'null' => false), 
		'host_id' => array('type'=>'integer', 'size' => 11,'null' => false), 
		'referer_id' => array('type'=>'integer', 'size' => 11,'null' => false), 
		'timestamp' => array('type'=>'integer', 'size'=>10, 'null' => false, 'unsigned' => true),
		'returning_visitor' => array('type' => 'integer', 'size' => 'tiny', 'null' => false, 'default' => '0')
    );

    $query = xarDBCreateTable($xartable['visitors'], $fields);
    if (empty($query)) return; // throw back	
	if ($databaseType == 'mysql') // TODO: find out if and how other db's support this
		$query .= ' DELAY_KEY_WRITE=1';
		
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    //KEY client_time     (client_id, timestamp),
    $index = array(
        'name'      => 'client_time',
        'fields'    => array('client_id', 'timestamp'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($xartable['visitors'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    //KEY os_ua           (operating_system_id, user_agent_id),
    $index = array(
        'name'      => 'os_ua',
        'fields'    => array('operating_system_id', 'user_agent_id'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($xartable['visitors'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

 	//KEY host_id         (host_id),
    $index = array(
        'name'      => 'host_id',
        'fields'    => array('host_id'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($xartable['visitors'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

 	//KEY referer_id      (referer_id)
    $index = array(
        'name'      => 'referer_id',
        'fields'    => array('referer_id'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($xartable['visitors'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    xarRegisterMask('OverviewOpentracker','All','opentracker','All','All','ACCESS_OVERVIEW');
    
    session_unregister('_phpOpenTracker_Container');

    return true;
} 

/**
 * upgrade the example module from an old version
 * This function can be called multiple times
 */
function opentracker_upgrade($oldversion)
{ 
    return true;
} 

/**
 * delete the example module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function opentracker_delete()
{ 
	session_unregister('_phpOpenTracker_Container');
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // Load the Table Maintainance API
    xarDBLoadTableMaintenanceAPI();

    $xartables = opentracker_xartables();
    foreach ($xartables as $key => $value) {
	    // Generate the deletion query
	    $query = xarDBDropTable($value);
	    if (empty($query)) return; // throw back
	    $result =& $dbconn->Execute($query);
	    if (!$result) return false;
    }
    
    xarRemoveMasks('opentracker');
    xarRemoveInstances('opentracker');
    
    // Deletion successful
    return true;
} 

?>
