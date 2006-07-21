<?php
/**
 * Initialization of reports
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage reports
 * @link http://xaraya.com/index.php/release/4704.html
 * @author Marcel van der Boom <marcel@hsdev.com>
 */
/**
 * Init function
 * @return bool true on success
 * @todo MichelV Should this module install when the required extension is not present?
 */
function reports_init()
{
    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();

    // Create tables for the reports module one by one
    $varcharlen=255;

    // Table reports
    $tabname ='reports';
    $tab=$xartable[$tabname];
    $cols = &$xartable[$tabname.'_column'];

    $fields = array($cols['id']         =>array('type'=>'integer','null'=>false,'increment' =>true, 'primary_key'=>true),
                    $cols['conn_id']    =>array('type'=>'integer','null'=>false,'default'   =>'0'),
                    $cols['name']       =>array('type'=>'varchar','null'=>false,'default'   =>'<untitled report>',    'size'=>$varcharlen),
                    $cols['description']=>array('type'=>'varchar','null'=>false,'default'   =>'no description given', 'size'=>$varcharlen),
                    $cols['xmlfile']    =>array('type'=>'varchar','null'=>false,'default'   =>'empty.xml',            'size'=>$varcharlen)
                    );
    $query = xarDBCreateTable($tab,$fields);
    $res =& $dbconn->Execute($query);
    if(!$res) return;

    // Create indexes
    $index = array('name' => 'i_'.$tab.'_name',
                   'fields' => array($cols['name']),
                   'unique' => 'false');
    $query = xarDBCreateIndex($tab,$index);
    $res =& $dbconn->Execute($query);
    if(!$res) return;

    // Report connections
    $tabname = 'report_connections';
    $tab = $xartable[$tabname];
    $cols = &$xartable[$tabname.'_column'];

    $defhost = xarDBGetHost();
    $defdb   = xarDBGetName();
    $deftype = xarDBGetType();
    $fields = array($cols['id']         =>array('type'=>'integer','null'=>false,'increment' =>true, 'primary_key'=>true),
                    $cols['name']       =>array('type'=>'varchar','null'=>false,'default'   =>'<untitled report>',   'size'=>$varcharlen),
                    $cols['description']=>array('type'=>'varchar','null'=>false,'default'   =>'no description given','size'=>$varcharlen),
                    $cols['server']     =>array('type'=>'varchar','null'=>false,'default'   =>$defhost,              'size'=>$varcharlen),
                    $cols['type']       =>array('type'=>'varchar','null'=>false,'default'   =>$deftype,              'size'=>$varcharlen),
                    $cols['database']   =>array('type'=>'varchar','null'=>false,'default'   =>$defdb,                'size'=>$varcharlen),
                    $cols['user']       =>array('type'=>'varchar','null'=>false,'default'   =>'username',            'size'=>$varcharlen),
                    $cols['password']   =>array('type'=>'varchar','null'=>false,'default'   =>'password',            'size'=>$varcharlen)
                    );

    $query = xarDBCreateTable($tab, $fields);
    $res =& $dbconn->Execute($query);
    if(!$res) return;

    // Create indexes
    $index = array('name' => 'i_'.$tab.'_name',
                   'fields' => array($cols['name']),
                   'unique' => 'false');
    $query = xarDBCreateIndex($tab,$index);
    $res =& $dbconn->Execute($query);
    if(!$res) return;

    // Create a default connection to this database itself
    $conn_id = $dbconn->GenId($tab);
    $conn_name = 'default';
    $conn_type =strtolower($deftype);
    $conn_desc = 'Xaraya connection itself';

    $sql = "INSERT INTO $tab ($cols[id],$cols[name],$cols[type],$cols[description]) VALUES (?,?,?,?)";
    $bindvars = array($conn_id, $conn_name, $conn_type, $conn_desc);

    $res =& $dbconn->Execute($sql,$bindvars);
    if(!$res) return;

    // Set up module variables with default values
    // Template: xarModSetVar('reports', 'varname', 1);
    // Default location of report definitions is reports directory under module directory
    // We cannot use modinfo yet.(we're in init here, so reconstruct it)
    $moddir = dirname(__FILE__) ."/reports";
    xarModSetVar('reports','reports_location',$moddir);
    xarModSetVar('reports','images_location',$moddir."/images");
    xarModSetVar('reports','pdf_backend','ezpdf');
    // The initialize installs version 0.0.1, do the upgrades next
    return reports_upgrade('0.0.1');
}
/**
 * Upgrade the reports module
 */
function reports_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
    case '0.0.1':
        // Default output format introduced in 0.0.2
        xarModSetVar('reports','default_output','html');
        $flags = XAR_TPL_TAG_ISPHPCODE + XAR_TPL_TAG_ISASSIGNABLE;
        xarTplRegisterTag('reports','reports-dataset',array(),'reports_userapi_datasettag',$flags);
        //$flags = XAR_TPL_TAG_ISPHPCODE;
        xarTplRegisterTag('reports','reports-dataitem',array(),'reports_userapi_dataitemtag',$flags);
    case '0.0.2':
        // Current version
        break;
    }
    return true;
}
/**
 * Remove the module
 */
function reports_delete()
{
    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Delete tables
    // This delete stuff is too easy, even for admins, no warning at all!!!!
    // Consider confirm action method from API

    $sql = "DROP TABLE IF EXISTS $xartable[reports], $xartable[report_connections]";
    $res =& $dbconn->Execute($sql);
    if(!$res) return;


    // Delete module variables
    // Template: xarModDelVar('reports', 'varname');
    xarModDelAllVars('reports');

    // Delete the custom tags
    xarTplUnregisterTag('reports-dataset');
    xarTplUnregisterTag('reports-dataitem');

    // Deletion successful
    return true;
}

?>