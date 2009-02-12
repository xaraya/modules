<?php
/**
 * Access Methods Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Access Methods Module
 * @link http://xaraya.com/index.php/release/732.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */

/**
 * initialise the Access Methods Module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function accessmethods_init()
{
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    
    $accessmethods_table = $xarTables['accessmethods'];
    $accessmethods_fields = array('siteid'          =>  array('type'=>'integer','size'=>'medium','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
                                'accesstype'        =>  array('type'=>'varchar','size'=>64,'null'=>FALSE,'default'=>''),
                                'clientid'          =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0'),
                                'webmasterid'       =>  array('type'=>'integer','size'=>11,'null'=>FALSE,'default'=>'0'),
                                'site_name'         =>  array('type'=>'varchar','size'=>255,'null'=>FALSE,'default'=>''),
                                'url'               =>  array('type'=>'varchar','size'=>255,'null'=>FALSE,'default'=>''),
                                'description'       =>  array('type'=>'text','null'=>FALSE,'default'=>''),
                                'sla'               =>  array('type'=>'varchar','size'=>32,'null'=>FALSE,'default'=>''),
                                'accesslogin'       =>  array('type'=>'varchar','size'=>32,'null'=>FALSE,'default'=>''),
                                'accesspwd'         =>  array('type'=>'varchar','size'=>32,'null'=>FALSE,'default'=>''),
                                'related_sites'     =>  array('type'=>'varchar','size'=>255,'null'=>FALSE,'default'=>'') );
    $query = xarDBCreateTable($accessmethods_table,$accessmethods_fields);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $ddata_is_available = xarModIsAvailable('dynamicdata');
    if (!isset($ddata_is_available)) return;

    if (!$ddata_is_available) {
        $msg = xarML('Please activate the Dynamic Data module first...');
        xarErrorSet(XAR_USER_EXCEPTION, 'MODULE_NOT_ACTIVE',
                        new DefaultUserException($msg));
        return;
    }

    $accessmethods_objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/accessmethods/xardata/accessmethods.xml'));
    if (empty($accessmethods_objectid)) return;
    // save the object id for later
    xarModSetVar('accessmethods','accessmethods_objectid',$accessmethods_objectid);

    $modulesettings = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/accessmethods/xardata/modulesettings.xml'));
    if (empty($modulesettings)) return;
    xarModSetVar('accessmethods','modulesettings',$modulesettings);

    xarModSetVar('accessmethods','bold',0);
    xarModSetVar('accessmethods','itemsperpage',20);
                             
    xarRegisterMask('ViewAccessMethods', 'All', 'accessmethods', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadAccessMethods', 'All', 'accessmethods', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditAccessMethods', 'All', 'accessmethods', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddAccessMethods', 'All', 'accessmethods', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteAccessMethods', 'All', 'accessmethods', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminAccessMethods', 'All', 'accessmethods', 'Item', 'All:All:All', 'ACCESS_ADMIN');
    
    return true;
}

/**
 * upgrade the example module from an old version
 * This function can be called multiple times
 */
function accessmethods_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '1.0.0':
            
            $modulesettings = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/accessmethods/xardata/modulesettings.xml'));
            if (empty($modulesettings)) return;
            xarModSetVar('accessmethods','modulesettings',$modulesettings);
            
            
        case '1.0.1':
            break;
    }

    // Update successful
    return true;
}

/**
 * delete the example module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function accessmethods_delete()
{
    xarDBLoadTableMaintenanceAPI();

    $dbconn   =& xarDBGetConn();
    $xartable =  xarDBGetTables();

    $query = xarDBDropTable($xartable['accessmethods']);
    $result =& $dbconn->Execute($query);

    $accessmethods_objectid = xarModGetVar('accessmethods','accessmethods_objectid');
    if (!empty($accessmethods_objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $accessmethods_objectid));
    }
    xarModDelVar('accessmethods','accessmethods_objectid');

    $modulesettings = xarModGetVar('accessmethods','modulesettings');
    if (!empty($modulesettings)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $modulesettings));
    }
    xarModDelVar('accessmethods','modulesettings');
    
    $aliasname =xarModGetVar('accessmethods','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias =='accessmethods')){
        xarModDelAlias($aliasname,'accessmethods');
    }

//    xarModAPIFunc('categories', 'admin', 'deletecat', array('cid' => xarModGetVar('xproject', 'mastercid')));
    /* Delete any module variables */
    xarModDelAllVars('accessmethods');
    
    xarRemoveMasks('accessmethods');
    xarRemoveInstances('accessmethods');

    // Deletion successful
    return true;
}

?>
