<?php
/**
 * labAccounting Module - Fiscal account management suite
 *
 * @package modules
 * @copyright (C) 2002-2008 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage labAccounting Module
 * @link http://xaraya.com/index.php/release/706.html
 * @author St.Ego
 */
/**
 * initialise the labAccounting module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function labaccounting_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    
    $journals_table = $xartable['labaccounting_journals'];
    $journals_fields = array(
        'journalid'	    =>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'parentid'		=>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'owneruid'	    =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'contactid'	    =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'currency'	    =>array('type'=>'varchar', 'size' =>'32', 'null'=>False),
        'account_title' =>array('type'=>'varchar', 'size' =>'255', 'null'=>False),
        'journaltype'	=>array('type'=>'varchar', 'size' =>'255', 'null'=>False),
        'agentuid'	    =>array('type'=>'integer', 'null'=>FALSE, 'default'=>'0'),
        'acctnum'	    =>array('type'=>'varchar', 'size' =>'64', 'null'=>False),
        'acctlogin'	    =>array('type'=>'varchar', 'size' =>'255', 'null'=>False),
        'accturl'	    =>array('type'=>'varchar', 'size' =>'255', 'null'=>False),
        'acctpwd'	    =>array('type'=>'text'),
        'notes'	        =>array('type'=>'text'),
        'opendate'	    =>array('type'=>'date'),
        'closedate'	    =>array('type'=>'date'),
        'billdate'	    =>array('type'=>'date'),
        'status'	    =>array('type'=>'varchar', 'size' =>'32', 'null'=>False),
        'invoicefreq'	=>array('type'=>'integer', 'null'=>FALSE, 'default'=>'0'),
        'invoicefrequnits'	=>array('type'=>'char', 'size' =>'1', 'null'=>False, 'default'=>'M'));
    $sql = xarDBCreateTable($journals_table,$journals_fields);
    if (empty($sql)) return; // throw back
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $journaltransactions_table = $xartable['labaccounting_journaltransactions'];
    $journaltransactions_fields = array(
        'transactionid'	=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'journalid'		=>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'transtype'		=>array('type'=>'varchar', 'size' =>'100', 'null'=>False),
        'creatoruid'    =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'title'	        =>array('type'=>'varchar', 'size' =>'255', 'null'=>False),
        'details'	    =>array('type'=>'text'),
        'transnum'	    =>array('type'=>'varchar', 'size' =>'64', 'null'=>False),
        'source'		=>array('type'=>'varchar', 'size' =>'255', 'null'=>FALSE),
        'sourceid'	    =>array('type'=>'integer', 'null'=>FALSE, 'default'=>'0'),
        'amount'	    =>array('type'=>'float', 'size' =>'decimal', 'width'=>8, 'decimals'=>2),
        'transdate'	    =>array('type'=>'date'),
        'verified'	    =>array('type'=>'char', 'size' =>'1', 'null'=>FALSE),
        'cleared'	    =>array('type'=>'char', 'size' =>'1', 'null'=>FALSE),
        'status'	    =>array('type'=>'varchar', 'size' =>'32', 'null'=>False));
    $sql = xarDBCreateTable($journaltransactions_table,$journaltransactions_fields);
    if (empty($sql)) return; // throw back
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $ledgers_table = $xartable['labaccounting_ledgers'];
    $ledgers_fields = array(
        'ledgerid'		=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE, 'primary_key'=>TRUE),
        'parentid'		=>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'ownerid'		=>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'accttype'	    =>array('type'=>'varchar', 'size' => '255', 'null'=>FALSE),
//        'accttype'	    =>array('type'=>'enum', 'size' => array('Assets','Liabilities','Equity','Revenue','Cost of Goods Sold','Expenses'), 'null'=>FALSE),
        'chartacctnum'	=>array('type'=>'char', 'size' =>'8', 'null'=>FALSE),
        'account_title' =>array('type'=>'varchar', 'size' =>'255', 'null'=>FALSE),
        'normalbalance'	=>array('type'=>'varchar', 'size' =>'16', 'null'=>FALSE), // "CREDIT" or "DEBIT"
        'notes'	        =>array('type'=>'text'));
    $sql = xarDBCreateTable($ledgers_table,$ledgers_fields);
    if (empty($sql)) return; // throw back
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $ledgertransactions_table = $xartable['labaccounting_ledgertransactions'];
    $ledgertransactions_fields = array(
        'transactionid'	=>array('type'=>'integer', 'null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'ledgerid'		=>array('type'=>'integer', 'null'=>FALSE, 'default'=>'0'),
        'journaltransid'=>array('type'=>'integer', 'null'=>FALSE, 'default'=>'0'),
        'creatorid'	    =>array('type'=>'integer', 'null'=>FALSE, 'default'=>'0'),
        'title'	        =>array('type'=>'varchar', 'size' =>'255', 'null'=>FALSE),
        'details'	    =>array('type'=>'text'),
        'transnum'	    =>array('type'=>'varchar', 'size' =>'64', 'null'=>FALSE),
        'amount'	    =>array('type'=>'float', 'size' =>'decimal', 'width'=>8, 'decimals'=>2),
        'status'	    =>array('type'=>'varchar', 'size' =>'32', 'null'=>FALSE),
        'transdate'	    =>array('type'=>'date'));
    $sql = xarDBCreateTable($ledgertransactions_table,$ledgertransactions_fields);
    if (empty($sql)) return; // throw back
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $journalXledger_table = $xartable['labaccounting_journalXledger'];
    $journalXledger_fields = array(
        'journalid'			=>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'ledgerid'			=>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'normalbalance'		=>array('type'=>'varchar', 'size' =>'8', 'null'=>FALSE));
    $sql = xarDBCreateTable($journalXledger_table,$journalXledger_fields);
    if (empty($sql)) return; // throw back
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $ddata_is_available = xarModIsAvailable('dynamicdata');
    if (!isset($ddata_is_available)) return;
    
    xarModAPILoad('dynamicdata', 'user');

	/* Create DD objects */
    $moduleid = xarModGetIdFromName('labaccounting');
    
    /* Journals DD object */
    $journals_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 1));
    if($journals_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $journals_object->objectid));
    }
	$journals_object = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'modules/labaccounting/xardata/journals.xml'));
	if (empty($journals_object)) return;
    
    /* Ledgers DD object */
    $ledgers_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 2));
    if($ledgers_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $ledgers_object->objectid));
    }
	$ledgers_object = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'modules/labaccounting/xardata/ledgers.xml'));
	if (empty($ledgers_object)) return;
    
    /* Journal Transactions DD object */
    $journaltransactions_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 3));
    if($journaltransactions_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $journaltransactions_object->objectid));
    }
	$journaltransactions_object = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'modules/labaccounting/xardata/journaltransactions.xml'));
	if (empty($journaltransactions_object)) return;
    
    /* Ledger Transactions DD object */
    $ledgertransactions_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 4));
    if($ledgertransactions_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $ledgertransactions_object->objectid));
    }
	$ledgertransactions_object = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'modules/labaccounting/xardata/ledgertransactions.xml'));
	if (empty($ledgertransactions_object)) return;
    
    /* Chart of Accounts DD object */
    $chartofaccounts_objectid = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 5));
    if($chartofaccounts_objectid->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $chartofaccounts_objectid->objectid));
    }
	$chartofaccounts_objectid = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'modules/labaccounting/xardata/chartofaccounts.xml'));
	if (empty($chartofaccounts_objectid)) return;
    
    /* Module Settings DD object */
    $modulesettings_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => 11, 'itemtype' => $moduleid));
    if($modulesettings_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $modulesettings_object->objectid));
    }
	$modulesettings_object = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'modules/labaccounting/xardata/modulesettings.xml'));
	if (empty($modulesettings_object)) return;
    
    /* User Settings DD object */
    $usersettings_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => 27, 'itemtype' => $moduleid));
    if($usersettings_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $usersettings_object->objectid));
    }
	$usersettings_object = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'modules/labaccounting/xardata/usersettings.xml'));
	if (empty($usersettings_object)) return;

    xarModSetVar('labaccounting', 'firsttransid', '');
    xarModSetVar('labaccounting', 'mastercid', '');
    xarModSetVar('labaccounting', 'accountcid', '');
    xarModSetVar('labaccounting', 'defaultaccountid', '');
    xarModSetVar('labaccounting', 'firstaccountid', '');
    xarModSetVar('labaccounting', 'numfields', '');
    xarModSetVar('labaccounting', 'allowuseraccess', '');
    xarModSetVar('labaccounting', 'enabledossier', '');
            
    xarModSetVar('labaccounting', 'transcid', '');
    xarModSetVar('labaccounting', 'boilerplate', '');
    xarModSetVar('labaccounting', 'maxmultinew', '');

    xarModSetVar('labaccounting', 'SupportShortURLs', 0);
    xarModSetVar('labaccounting', 'useModuleAlias',false);
    xarModSetVar('labaccounting', 'aliasname','');
    
    // required for xarModGetUserVar(...)
    xarModSetVar('labaccounting', 'myagentid', '');
    xarModSetVar('labaccounting', 'mycontactid', '');
    xarModSetVar('labaccounting', 'myaddressid', '');
    
    $instances = array(
                       array('header' => 'external', // this keyword indicates an external "wizard"
                             'query'  => xarModURL('labaccounting', 'admin', 'privileges'),
                             'limit'  => 0
                            )
                    );
    
    xarDefineInstance('labaccounting','Journals',$instances);
    xarDefineInstance('labaccounting','Ledgers',$instances);
    
    if (!xarModRegisterHook('item', 'usermenu', 'GUI','labaccounting', 'user', 'usermenu'))
        return false;

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */
    
    // security mask: category:user/owner:user/agent
    
    xarRegisterMask('ViewJournal',      'All', 'labaccounting', 'Journals', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadJournal',      'All', 'labaccounting', 'Journals', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('JournalClient',    'All', 'labaccounting', 'Journals', 'All:All:All', 'ACCESS_COMMENT');
    xarRegisterMask('ManageJournal',    'All', 'labaccounting', 'Journals', 'All:All:All', 'ACCESS_MODERATE');
    xarRegisterMask('EditJournal',      'All', 'labaccounting', 'Journals', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddJournal',       'All', 'labaccounting', 'Journals', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteJournal',    'All', 'labaccounting', 'Journals', 'All:All:All', 'ACCESS_DELETE');
    
    xarRegisterMask('ViewLedger',       'All', 'labaccounting', 'Ledgers', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadLedger',       'All', 'labaccounting', 'Ledgers', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('AccountManager',   'All', 'labaccounting', 'Ledgers', 'All:All:All', 'ACCESS_COMMENT');
    xarRegisterMask('AuditLedgers',     'All', 'labaccounting', 'Ledgers', 'All:All:All', 'ACCESS_MODERATE');
    xarRegisterMask('EditLedger',       'All', 'labaccounting', 'Ledgers', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddLedger',        'All', 'labaccounting', 'Ledgers', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteLedger',     'All', 'labaccounting', 'Ledgers', 'All:All:All', 'ACCESS_DELETE');

    xarRegisterMask('AccessAccounting',  'All', 'labaccounting', 'All', 'All', 'ACCESS_READ');
    xarRegisterMask('AdminAccounting',  'All', 'labaccounting', 'All', 'All', 'ACCESS_ADMIN');
    
    return true;
}

function labaccounting_upgrade($oldversion)
{    
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    
    $ledgertransactions_table = $xartable['labaccounting_ledgertransactions'];
    
    $journaltransactions_table = $xartable['labaccounting_journaltransactions'];
    
	$datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    
    $ddata_is_available = xarModIsAvailable('dynamicdata');
    
    if (!isset($ddata_is_available)) return;

    if (!$ddata_is_available) {
        $msg = xarML('Please activate the Dynamic Data module first...');
        xarErrorSet(XAR_USER_EXCEPTION, 'MODULE_NOT_ACTIVE',
                        new DefaultUserException($msg));
        return;
    }
    
    switch($oldversion) {

        case '1.0.1':
            
            $result = $datadict->ChangeTable($ledgertransactions_table, 'journaltransid I NotNull');
            if (!$result) return;
            
        case '1.0.2':
            $result = $datadict->dropColumn($ledgertransactions_table, 'source');
            if (!$result) return;
            $result = $datadict->dropColumn($ledgertransactions_table, 'sourceid');
            if (!$result) return;
            $result = $datadict->ChangeTable($journaltransactions_table, 'source C(255) NotNull');
            if (!$result) return;
            $result = $datadict->ChangeTable($journaltransactions_table, 'sourceid I NotNull Default 0');
            if (!$result) return;
        case '1.0.3':
            $result = $datadict->ChangeTable($journaltransactions_table, 'isinvoice I NotNull Default 0');
            if (!$result) return;
        case '1.0.4':
    
            if (!xarModRegisterHook('item', 'usermenu', 'GUI','labaccounting', 'user', 'usermenu'))
                return false;
        case '1.0.5':
        case '1.0.6':
        case '1.0.7':
        case '1.0.9':
        case '1.1.0':
        case '1.1.1':
        case '1.1.2':
        case '1.1.3':
        case '1.1.4':
            xarRemoveInstances('labaccounting');
            xarRemoveMasks('labaccounting');
    
            $instances = array(
                               array('header' => 'external', // this keyword indicates an external "wizard"
                                     'query'  => xarModURL('labaccounting', 'admin', 'privileges'),
                                     'limit'  => 0
                                    )
                            );
            
            xarDefineInstance('labaccounting','Journals',$instances);
            xarDefineInstance('labaccounting','Ledgers',$instances);
    
            xarRegisterMask('ViewJournal',      'All', 'labaccounting', 'Journals', 'All:All:All', 'ACCESS_OVERVIEW');
            xarRegisterMask('ReadJournal',      'All', 'labaccounting', 'Journals', 'All:All:All', 'ACCESS_READ');
            xarRegisterMask('JournalClient',    'All', 'labaccounting', 'Journals', 'All:All:All', 'ACCESS_COMMENT');
            xarRegisterMask('ManageJournal',    'All', 'labaccounting', 'Journals', 'All:All:All', 'ACCESS_MODERATE');
            xarRegisterMask('EditJournal',      'All', 'labaccounting', 'Journals', 'All:All:All', 'ACCESS_EDIT');
            xarRegisterMask('AddJournal',       'All', 'labaccounting', 'Journals', 'All:All:All', 'ACCESS_ADD');
            xarRegisterMask('DeleteJournal',    'All', 'labaccounting', 'Journals', 'All:All:All', 'ACCESS_DELETE');
            
            xarRegisterMask('ViewLedger',       'All', 'labaccounting', 'Ledgers', 'All:All:All', 'ACCESS_OVERVIEW');
            xarRegisterMask('ReadLedger',       'All', 'labaccounting', 'Ledgers', 'All:All:All', 'ACCESS_READ');
            xarRegisterMask('AccountManager',   'All', 'labaccounting', 'Ledgers', 'All:All:All', 'ACCESS_COMMENT');
            xarRegisterMask('AuditLedgers',     'All', 'labaccounting', 'Ledgers', 'All:All:All', 'ACCESS_MODERATE');
            xarRegisterMask('EditLedger',       'All', 'labaccounting', 'Ledgers', 'All:All:All', 'ACCESS_EDIT');
            xarRegisterMask('AddLedger',        'All', 'labaccounting', 'Ledgers', 'All:All:All', 'ACCESS_ADD');
            xarRegisterMask('DeleteLedger',     'All', 'labaccounting', 'Ledgers', 'All:All:All', 'ACCESS_DELETE');
        
            xarRegisterMask('AccessAccounting',  'All', 'labaccounting', 'All', 'All', 'ACCESS_READ');
            xarRegisterMask('AdminAccounting',  'All', 'labaccounting', 'All', 'All', 'ACCESS_ADMIN');
        case '1.1.5':
            break;

    }

    return true;
}

function labaccounting_delete()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    
    $sql = xarDBDropTable($xartable['labaccounting_journals']);
    if (empty($sql)) return;
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0 && false) { // xarDBDropTable does not account for IF EXISTS
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $sql = xarDBDropTable($xartable['labaccounting_journaltransactions']);
    if (empty($sql)) return;
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0 && false) { // xarDBDropTable does not account for IF EXISTS
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $sql = xarDBDropTable($xartable['labaccounting_ledgers']);
    if (empty($sql)) return;
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0 && false) { // xarDBDropTable does not account for IF EXISTS
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $sql = xarDBDropTable($xartable['labaccounting_ledgertransactions']);
    if (empty($sql)) return;
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0 && false) { // xarDBDropTable does not account for IF EXISTS
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $sql = xarDBDropTable($xartable['labaccounting_journalXledger']);
    if (empty($sql)) return;
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0 && false) { // xarDBDropTable does not account for IF EXISTS
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    xarErrorFree(); // xarDBDropTable does not account for IF EXISTS
    
    xarModAPILoad('dynamicdata', 'user');
    
    $moduleid = xarModGetIdFromName('labaccounting');
    
    /* delete journals ddata object */
    $journals_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 1));
    if($journals_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $journals_object->objectid));
    }
    
    /* delete ledgers ddata object */
    $ledgers_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 2));
    if($ledgers_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $ledgers_object->objectid));
    }
    
    /* delete journaltransactions ddata object */
    $journaltransactions_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 3));
    if($journaltransactions_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $journaltransactions_object->objectid));
    }
    
    /* delete ledgertransactions ddata object */
    $ledgertransactions_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 4));
    if($ledgertransactions_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $ledgertransactions_object->objectid));
    }
    
    /* delete chart of accounts ddata object */
    $chartofaccounts_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 5));
    if($chartofaccounts_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $chartofaccounts_object->objectid));
    }
    
    /* delete Module Settings ddata object */
    $modulesettings_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => 11, 'itemtype' => $moduleid));
    if($modulesettings_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $modulesettings_object->objectid));
    }
    
    /* delete User Settings ddata object */
    $usersettings_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => 27, 'itemtype' => $moduleid));
    if($usersettings_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $usersettings_object->objectid));
    }
    
    $aliasname =xarModGetVar('labaccounting','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias =='labaccounting')){
        xarModDelAlias($aliasname,'labaccounting');
    }
    
    xarModDelAllVars('labaccounting');

    xarRemoveMasks('labaccounting');
    xarRemoveInstances('labaccounting');

    return true;
}

function labaccounting_recycle_ddata() {

    
    $ddata_is_available = xarModIsAvailable('dynamicdata');
    if (!isset($ddata_is_available)) return;
    
    xarModAPILoad('dynamicdata', 'user');

	/* Create DD objects */
    $moduleid = xarModGetIdFromName('labaccounting');
    
    /* Journals DD object */
    $journals_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 1));
    if($journals_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $journals_object->objectid));
    }
	$journals_object = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'modules/labaccounting/xardata/journals.xml'));
	if (empty($journals_object)) return;
    
    /* Ledgers DD object */
    $ledgers_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 2));
    if($ledgers_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $ledgers_object->objectid));
    }
	$ledgers_object = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'moduleslabaccounting/xardata/ledgers.xml'));
	if (empty($ledgers_object)) return;
    
    /* Journal Transactions DD object */
    $journaltransactions_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 3));
    if($journaltransactions_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $journaltransactions_object->objectid));
    }
	$journaltransactions_object = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'modules/labaccounting/xardata/journaltransactions.xml'));
	if (empty($journaltransactions_object)) return;
    
    /* Ledger Transactions DD object */
    $ledgertransactions_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 4));
    if($ledgertransactions_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $ledgertransactions_object->objectid));
    }
	$ledgertransactions_object = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'moduleslabaccounting/xardata/ledgertransactions.xml'));
	if (empty($ledgertransactions_object)) return;
    
    /* Chart of Accounts DD object */
    $chartofaccounts_objectid = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 5));
    if($chartofaccounts_objectid->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $chartofaccounts_objectid->objectid));
    }
	$chartofaccounts_objectid = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'moduleslabaccounting/xardata/chartofaccounts.xml'));
	if (empty($chartofaccounts_objectid)) return;
    
    /* Module Settings DD object */
    $modulesettings_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => 11, 'itemtype' => $moduleid));
    if($modulesettings_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $modulesettings_object->objectid));
    }
	$modulesettings_object = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'moduleslabaccounting/xardata/modulesettings.xml'));
	if (empty($modulesettings_object)) return;
    
    /* User Settings DD object */
    $usersettings_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => 27, 'itemtype' => $moduleid));
    if($usersettings_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $usersettings_object->objectid));
    }
	$usersettings_object = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'moduleslabaccounting/xardata/usersettings.xml'));
	if (empty($usersettings_object)) return;

    return true;
}

?>