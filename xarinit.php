<?php
/**
 * LabAffiliate Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage LabAffiliate Module
 * @link http://xaraya.com/index.php/release/919
 * @author LabAffiliate Module Development Team
 */
/**
 * Initialise the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance. It holds all the installation routines and sets the variables used
 * by this module. This function is the place to create you database structure and define
 * the privileges your module uses.
 *
 * @author LabAffiliate Module Development Team
 * @param none
 * @return bool true on success of installation
 */

function labaffiliate_init()
{
	$dbconn =& xarDBGetConn();
	$xartable =& xarDBGetTables();

	$datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

	$labaffiliate_programs_table = $xartable['labaffiliate_programs'];

	$fields = "xar_programid		I		AUTO		PRIMARY,
				xar_program_name	C(255)	NotNull		DEFAULT '',
				xar_programurl		C(255)	NotNull		DEFAULT '',
				xar_affiliateurl	C(255)	NotNull		DEFAULT '',
				xar_details			X		NotNull		DEFAULT '',
				xar_marketing_copy	X		NotNull		DEFAULT '',
				xar_status          C(16)	NotNull		DEFAULT ''";

	/* Create or alter the table as necessary */
	$result = $datadict->changeTable($labaffiliate_programs_table, $fields);
	if (!$result) {return;}


	$labaffiliate_affiliates_table = $xartable['labaffiliate_affiliates'];

	$fields = "xar_affiliateid			I		AUTO		PRIMARY,
				xar_uplineid			I		NotNull		DEFAULT 0,
				xar_userid				I		NotNull		DEFAULT 0,
				xar_primaryprogramid	I		NotNull		DEFAULT 0,
				xar_secondaryprogramid 	I		NotNull		DEFAULT 0,
				xar_status          	C(16)	NotNull		DEFAULT '',
				xar_marketing_copy	    X		NotNull		DEFAULT ''";


	/* Create or alter the table as necessary */
	$result = $datadict->changeTable($labaffiliate_affiliates_table, $fields);
	if (!$result) {return;}

	$labaffiliate_membership_table = $xartable['labaffiliate_membership'];

	$fields = "xar_membershipid	        I		AUTO		PRIMARY,
				xar_programid	        I		NotNull		DEFAULT 0,
				xar_affiliateid	        I		NotNull		DEFAULT 0,
				xar_program_key	        C(128)	NotNull		DEFAULT '',
				xar_active 	            I(1)	NotNull		DEFAULT 1,
				xar_marketing_copy	    X		NotNull		DEFAULT ''";

	/* Create or alter the table as necessary */
	$result = $datadict->changeTable($labaffiliate_membership_table, $fields);
	if (!$result) {return;}

    // Create indexes.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_programaffiliate',
        $labaffiliate_membership_table,
        array('xar_programid','xar_affiliateid'),
        'unique' // This doesn't work properly
    );
    if (!$result) {return;}
    
    $ddata_is_available = xarModIsAvailable('dynamicdata');
    if (!isset($ddata_is_available)) return;
    
    xarModAPILoad('dynamicdata', 'user');

	/* Create DD objects */
    $moduleid = xarModGetIdFromName('labaffiliate');
    
    /* programs DD object */
    $programs_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 1));
    if($programs_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $programs_object->objectid));
    }
	$journals_object = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'modules/labaffiliate/xardata/programs.xml'));
	if (empty($journals_object)) return;
    
    /* affiliates DD object */
    $affiliates_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 2));
    if($affiliates_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $affiliates_object->objectid));
    }
	$ledgers_object = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'modules/labaffiliate/xardata/affiliates.xml'));
	if (empty($ledgers_object)) return;
    
    /* memberships DD object */
    $memberships_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 3));
    if($memberships_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $memberships_object->objectid));
    }
	$memberships_object = xarModAPIFunc('dynamicdata','util','import',
                                array('file' => 'modules/labaffiliate/xardata/memberships.xml'));
	if (empty($memberships_object)) return;
    
    if (!xarModRegisterHook('item', 'usermenu', 'GUI','labaffiliate', 'user', 'usermenu'))
        return false;

	xarModSetVar('labaffiliate', 'itemsperpage', 20);
	xarModSetVar('labaffiliate', 'inviteonly', 0);
    
    /* Register blocks. */
    if (!xarModAPIFunc('blocks','admin','register_block_type',
            array('modName' => 'labaffiliate',
                'blockType' => 'uplinecapture'))) return;

	/*					
	 * Register the module components that are privileges objects
	 * Format is
	 * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
	 * These masks are used in the module for the security checks
	 */
	xarRegisterMask('ViewProgram',	 'All', 'program', 'Program', 'All:All:All', 'ACCESS_OVERVIEW');
	xarRegisterMask('ReadProgram',	 'All', 'program', 'Program', 'All:All:All', 'ACCESS_READ');
	xarRegisterMask('EditProgram',	 'All', 'program', 'Program', 'All:All:All', 'ACCESS_EDIT');
	xarRegisterMask('AddProgram',	 'All', 'program', 'Program', 'All:All:All', 'ACCESS_ADD');
	xarRegisterMask('DeleteProgram', 'All', 'program', 'Program', 'All:All:All', 'ACCESS_DELETE');
	xarRegisterMask('AdminProgram', 'All', 'program', 'Program', 'All:All:All', 'ACCESS_ADMIN');

	xarRegisterMask('ViewProgramAffiliate', 'All', 'program', 'Affiliate', 'All:All:All', 'ACCESS_OVERVIEW');
	xarRegisterMask('ReadProgramAffiliate', 'All', 'program', 'Affiliate', 'All:All:All', 'ACCESS_READ');
	xarRegisterMask('EditProgramAffiliate', 'All', 'program', 'Affiliate', 'All:All:All', 'ACCESS_EDIT');
	xarRegisterMask('AddProgramAffiliate', 'All', 'program', 'Affiliate', 'All:All:All', 'ACCESS_ADD');
	xarRegisterMask('DeleteProgramAffiliate', 'All', 'program', 'Affiliate', 'All:All:All', 'ACCESS_DELETE');
	xarRegisterMask('AdminProgramAffiliate', 'All', 'program', 'Affiliate', 'All:All:All', 'ACCESS_ADMIN');

	xarRegisterMask('ViewProgramMembership', 'All', 'program', 'Membership', 'All:All:All', 'ACCESS_OVERVIEW');
	xarRegisterMask('ReadProgramMembership', 'All', 'program', 'Membership', 'All:All:All', 'ACCESS_READ');
	xarRegisterMask('EditProgramMembership', 'All', 'program', 'Membership', 'All:All:All', 'ACCESS_EDIT');
	xarRegisterMask('AddProgramMembership', 'All', 'program', 'Membership', 'All:All:All', 'ACCESS_ADD');
	xarRegisterMask('DeleteProgramMembership', 'All', 'program', 'Membership', 'All:All:All', 'ACCESS_DELETE');
	xarRegisterMask('AdminProgramMembership', 'All', 'program', 'Membership', 'All:All:All', 'ACCESS_ADMIN');

//	return labaffiliate_upgrade('1.0.0');

	return true;
}

function labaffiliate_upgrade($oldversion)
{
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    
    $programs_table = $xarTables['labaffiliate_programs'];
    $affiliates_table = $xarTables['labaffiliate_affiliates'];
    $membership_table = $xarTables['labaffiliate_membership'];

	switch($oldversion) {
		case '1.0.0':
            if (!xarModRegisterHook('item', 'usermenu', 'GUI','labaffiliate', 'user', 'usermenu'))
                return false;
                
        case '1.0.1':
    
            $ddata_is_available = xarModIsAvailable('dynamicdata');
            if (!isset($ddata_is_available)) return;
            
            xarModAPILoad('dynamicdata', 'user');
        
        	/* Create DD objects */
            $moduleid = xarModGetIdFromName('labaffiliate');
            
            /* programs DD object */
            $programs_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 1));
            if($programs_object->objectid == true) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $programs_object->objectid));
            }
        	$programs_object = xarModAPIFunc('dynamicdata','util','import',
                                        array('file' => 'modules/labaffiliate/xardata/programs.xml'));
        	if (empty($programs_object)) return;
            
            /* affiliates DD object */
            $affiliates_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 2));
            if($affiliates_object->objectid == true) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $affiliates_object->objectid));
            }
        	$affiliates_object = xarModAPIFunc('dynamicdata','util','import',
                                        array('file' => 'modules/labaffiliate/xardata/affiliates.xml'));
        	if (empty($affiliates_object)) return;
            
            /* memberships DD object */
            $memberships_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 3));
            if($memberships_object->objectid == true) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $memberships_object->objectid));
            }
        	$memberships_object = xarModAPIFunc('dynamicdata','util','import',
                                        array('file' => 'modules/labaffiliate/xardata/memberships.xml'));
        	if (empty($memberships_object)) return;
        
        case '1.0.2': 
        
            $result = $datadict->addColumn($programs_table, 'xar_status C(16) NotNull');
            if (!$result) return;
            
            $result = $datadict->addColumn($affiliates_table, 'xar_status C(16) NotNull');
            if (!$result) return;
            
            $result = $datadict->addColumn($membership_table, 'xar_active I(1) NotNull Default 1');
            if (!$result) return;
        
    
        
        case '1.1.0':
    
            /* Register blocks. */
            if (!xarModAPIFunc('blocks','admin','register_block_type',
                    array('modName' => 'labaffiliate',
                        'blockType' => 'uplinecapture'))) return;
        
        case '1.1.1':
            
            $result = $datadict->addColumn($affiliates_table, 'xar_marketing_copy X NotNull');
            if (!$result) return;
            
            $result = $datadict->addColumn($membership_table, 'xar_marketing_copy X NotNull');
            if (!$result) return;
        
			break;
        
        case '1.2.0':
    
            $ddata_is_available = xarModIsAvailable('dynamicdata');
            if (!isset($ddata_is_available)) return;
            
            xarModAPILoad('dynamicdata', 'user');
        
        	/* Create DD objects */
            $moduleid = xarModGetIdFromName('labaffiliate');
            
            /* programs DD object */
            $programs_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 1));
            if($programs_object->objectid == true) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $programs_object->objectid));
            }
        	$programs_object = xarModAPIFunc('dynamicdata','util','import',
                                        array('file' => 'modules/labaffiliate/xardata/programs.xml'));
        	if (empty($programs_object)) return;
            
            /* affiliates DD object */
            $affiliates_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 2));
            if($affiliates_object->objectid == true) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $affiliates_object->objectid));
            }
        	$affiliates_object = xarModAPIFunc('dynamicdata','util','import',
                                        array('file' => 'modules/labaffiliate/xardata/affiliates.xml'));
        	if (empty($affiliates_object)) return;
            
            /* memberships DD object */
            $memberships_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 3));
            if($memberships_object->objectid == true) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $memberships_object->objectid));
            }
        	$memberships_object = xarModAPIFunc('dynamicdata','util','import',
                                        array('file' => 'modules/labaffiliate/xardata/memberships.xml'));
        	if (empty($memberships_object)) return;
        
        case '1.2.1': // current version
            
	}

	return true;
}

function labaffiliate_delete()
{
	$dbconn =& xarDBGetConn();
	$xartable =& xarDBGetTables();

	/* Get a data dictionary object with item create and delete methods */
	$datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

	$labaffiliate_programs_table = $xartable['labaffiliate_programs'];
	$labaffiliate_affiliates_table = $xartable['labaffiliate_affiliates'];
	$labaffiliate_membership_table = $xartable['labaffiliate_membership'];

	$result = $datadict->dropTable($labaffiliate_programs_table);
	$result = $datadict->dropTable($labaffiliate_affiliates_table);
	$result = $datadict->dropTable($labaffiliate_membership_table);
    
    xarModAPILoad('dynamicdata', 'user');
    
    $moduleid = xarModGetIdFromName('labaffiliate');

    /* delete journals ddata object */
    $programs_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 1));
    if($programs_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $programs_object->objectid));
    }
    
    /* delete ledgers ddata object */
    $affiliates_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 2));
    if($affiliates_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $affiliates_object->objectid));
    }
    
    /* delete journaltransactions ddata object */
    $memberships_object = xarModAPIFunc('dynamicdata','user','getobject',array('moduleid' => $moduleid, 'itemtype' => 3));
    if($memberships_object->objectid == true) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $memberships_object->objectid));
    }

	/* Delete any module variables */
	xarModDelAllVars('labaffiliate');

	/* Remove Masks and Instances
	 * These functions remove all the registered masks and instances of a module
	 * from the database. This is not strictly necessary, but it's good housekeeping.
	 */
	xarRemoveMasks('labaffiliate');
	xarRemoveInstances('labaffiliate');

	return true;
}
?>