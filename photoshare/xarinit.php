<?php
/**
 * Photoshare by Chris van de Steeg
 * based on Jorn Lind-Nielsen 's photoshare
 * module for PostNuke
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage photoshare
 * @author Chris van de Steeg
 */

function photoshare_init()
{
	// Verify that we have the GD extension installed
	if (!extension_loaded('gd')) 
	{
	    $msg=xarML('Your server does unfortunately not have the image library "GD" installed, so Photoshare cannot be installed. Please contact your web administrator in order to get GD installed.');
	    xarExceptionSet(XAR_SYSTEM_EXCEPTION,'MODULE_DEPENDENCY',
	                    new SystemException($msg));
	    return;
	}
	
	$dbconn =& xarDBGetConn();
	$xartables =& xarDBGetTables();
	xarDBLoadTableMaintenanceAPI();

    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules

    // Folder creation

	$folderTable = $xartables['photoshare_folders'];
	$fields = array(
		'ps_id' 		   => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
		'ps_owner'         => array('type'=>'integer','null'=>false),
		'ps_createddate'   => array('type'=>'datetime','null'=>false),
		'ps_modifieddate'  => array('type'=>'timestamp','null'=>false),
		'ps_title'         => array('type'=>'varchar', 'size' => 255,'null'=>true),
		'ps_description'   => array('type'=>'text','null'=>true),
		//'ps_topic'         => array('type'=>'integer','null'=>false),
		'ps_template'      => array('type'=>'varchar', 'size'=>255,'null'=>false,'default'=>'slideshow'),
		'ps_blockfromlist' => array('type'=>'boolean','null'=>false, 'default'=>'0'),
		'ps_hideframe'   => array('type'=>'boolean','null'=>false, 'default'=>'0'),
		'ps_parentfolder'  => array('type'=>'integer','null'=>false),
		'ps_accesslevel'   => array('type'=>'integer','size'=>'small','null'=>false),
		'ps_viewkey'       => array('type'=>'varchar', 'size' => 32,'null'=>true),
		'ps_mainimage'     => array('type'=>'integer','null'=>true)
		);

	$query = xarDBCreateTable($folderTable,$fields);
	if (empty($query)) return; // throw back
	
	$result =& $dbconn->Execute($query);
	if (!$result) return;
		
    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_owner',
        'fields'    => array('ps_owner'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($folderTable,$index);
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_viewkey',
        'fields'    => array('ps_viewkey'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($folderTable,$index);
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $imagetable = $xartables['photoshare_images'];
	$fields = array(
		'ps_id' 		   => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
		'ps_owner'         => array('type'=>'integer','null'=>false),
		'ps_createddate'   => array('type'=>'datetime','null'=>false),
		'ps_modifieddate'  => array('type'=>'timestamp','null'=>false),
		'ps_title'         => array('type'=>'varchar', 'size' => 255,'null'=>true),
		'ps_description'   => array('type'=>'text','null'=>true),
		'ps_parentfolder'  => array('type'=>'integer','null'=>false),
		'ps_bytesize'      => array('type'=>'integer','null'=>false),
		'ps_uploadid'      => array('type'=>'integer','null'=>false),
		'ps_position'      => array('type'=>'integer','null'=>false));

	$query = xarDBCreateTable($imagetable,$fields);
	if (empty($query)) return; // throw back
	
	$result =& $dbconn->Execute($query);
	if (!$result) return;

    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_owner',
        'fields'    => array('ps_owner'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($imagetable,$index);
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_ps_parentfolder',
        'fields'    => array('ps_parentfolder'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($imagetable,$index);
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $setuptable = $xartables['photoshare_setup'];
	$fields = array(
		'ps_setupid'	  => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
		//not necessary in xaraya: everything's a role:
		//'ps_kind'         => array('type'=>'integer','null'=>false, 'size'=>'small'),
		'ps_storage'      => array('type'=>'integer'),
		'ps_id'			  => array('type'=>'integer','null'=>false)
		);
	$query = xarDBCreateTable($setuptable,$fields);
	if (empty($query)) return; // throw back
		
	$result =& $dbconn->Execute($query);
	if (!$result) return;
	
    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . 'psstorage',
        'fields'    => array('ps_storage'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($setuptable,$index);
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;
	
    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . 'ps_id',
        'fields'    => array('ps_id'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($setuptable,$index);
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    xarModSetVar('photoshare', 'SupportShortURLs', 1);
  	xarModSetVar('photoshare', 'tmpdirname', '*');
  	xarModSetVar('photoshare', 'imagedirname', '*');
	xarModSetVar('photoshare', 'useImageDirectory', '1');
	xarModSetVar('photoshare', 'thumbnailsize', '80');
	xarModSetVar('photoshare', 'imageSizeLimitSingle', 250000);
	xarModSetVar('photoshare', 'imageSizeLimitTotal', 5000000);
	xarModSetVar('photoshare', 'allowframeremove', false);
 	xarModSetVar('photoshare', 'defaultTemplate', 'unknown');
  	xarModSetVar('photoshare', 'mainlist', 'flat');

    $query1 = "SELECT DISTINCT ps_id FROM " . $xartables['photoshare_folders'];
    $query2 = "SELECT DISTINCT ps_owner FROM " . $xartables['photoshare_folders'];
    $query3 = "SELECT DISTINCT ps_id FROM " . $xartables['photoshare_folders'];
    $instances = array(
        array('header' => 'Folder id:',
            'query' => $query1,
            'limit' => 20
            ),
        array('header' => 'Folder owner:',
            'query' => $query2,
            'limit' => 20
            ),
        array('header' => 'Parent folder id:',
            'query' => $query3,
            'limit' => 20
            )
        );
    xarDefineInstance('photoshare', 'folder', $instances);

    $query1 = "SELECT DISTINCT ps_id FROM " . $xartables['photoshare_images'];
    $query2 = "SELECT DISTINCT ps_owner FROM " . $xartables['photoshare_images'];
    $query3 = "SELECT DISTINCT ps_id FROM " . $xartables['photoshare_folders'];
    $instances = array(
        array('header' => 'Photo id:',
            'query' => $query1,
            'limit' => 20
            ),
        array('header' => 'Photo owner:',
            'query' => $query2,
            'limit' => 20
            ),
        array('header' => 'Parent folder id:',
            'query' => $query3,
            'limit' => 20
            )
        );
    xarDefineInstance('photoshare', 'item', $instances);
	
    xarRegisterMask('ViewFolder', 'All', 'photoshare', 'folder', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadFolder', 'All', 'photoshare', 'folder', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditFolder', 'All', 'photoshare', 'folder', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddFolder', 'All', 'photoshare', 'folder', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteFolder', 'All', 'photoshare', 'folder', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminFolder', 'All', 'photoshare', 'folder', 'All:All:All', 'ACCESS_ADMIN');
    xarRegisterMask('ViewPhoto', 'All', 'photoshare', 'item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadPhoto', 'All', 'photoshare', 'item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditPhoto', 'All', 'photoshare', 'item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddPhoto', 'All', 'photoshare', 'item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeletePhoto', 'All', 'photoshare', 'item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminPhoto', 'All', 'photoshare', 'item', 'All:All:All', 'ACCESS_ADMIN');
    
    xarRegisterPrivilege('AdminOwnItems', 'All', 'photoshare', 'All', 'All:MySelf:All', 'ACCESS_ADMIN');
    
    // Initialisation successful
	return true;
}

// -----------------------------------------------------------------------
// Module upgrade
// -----------------------------------------------------------------------
function photoshare_upgrade($oldversion)
{
    switch($oldversion){
        case '3.0a':

    }
	return true;
}

// -----------------------------------------------------------------------
// Module delete
// -----------------------------------------------------------------------
function photoshare_delete()
{
	$dbconn =& xarDBGetConn();
	$xartables =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    // Generate the SQL to drop the table using the API
    
    $query = xarDBDropTable($xartables['photoshare_folders']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;

	// Drop the images table
    $query = xarDBDropTable($xartables['photoshare_images']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    //TODO: Delete optional image files on disc
    /*
	if (xarModGetVar('photoshare', 'useImageDirectory'))	{
		$dirName = xarModGetVar('photoshare', 'imagedirname');
		$dirHandle = opendir($dirName);
		if ($dirHandle != false) {
			while (($filename=readdir($dirHandle)) !=  false) {
				if (substr($filename,0,3) == 'img'  ||  substr($filename,0,3) == 'tmb') {
					unlink($dirName . '/' . $filename);
				}
		    }
		}
		  closedir($dirHandle);
	}*/

	xarModDelVar('photoshare', 'tmpdirname');
	xarModDelVar('photoshare', 'imagedirname');
	xarModDelVar('photoshare', 'useImageDirectory');
	xarModDelVar('photoshare', 'thumbnailsize');
	xarModDelVar('photoshare', 'imageSizeLimitSingle');
	xarModDelVar('photoshare', 'imageSizeLimitTotal');
	xarModDelVar('photoshare', 'allowframeremove');
	xarModDelVar('photoshare', 'defaultTemplate');

    xarRemoveMasks('example');
    xarRemoveInstances('example');
    xarRemovePrivileges('photoshare');

    // Deletion successful
	return true;
}
?>
