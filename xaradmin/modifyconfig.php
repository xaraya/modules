<?php
/**
 * File: $Id:
 * 
 * Function that allows modification of configurable variables for the julian calendar
 *
 * @package Xaraya eXtensible Management System
* @copyright (C) 2004 by Metrostat Technologies, Inc.
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.metrostat.net
*
* @subpackage julian
* initial template: Roger Raymond
* @author Jodie Razdrh/John Kevlin/David St.Clair
 */

function julian_admin_modifyconfig($args)
{ 
    // Security Check
    if (!xarSecurityCheck('Adminjulian')) return;

    //get the data supplied
    extract($args);
    
    $data=array();
    
    //establish a db connection
    $dbconn =& xarDBGetConn();
    //get db tables
    $xartable = xarDBGetTables();
    $roles_table = $xartable['roles'];
    
    //get all the users that are groups (xar_type=1)
    $sql = "SELECT xar_uid,xar_name FROM " . $roles_table . " WHERE xar_type=1";
    $rs = $dbconn->Execute($sql);
    //get the group allowed to share calendar events
    $share_group = xarModGetVar('julian','share_group');
    $group_options = array();
    while(!$rs->EOF)
    {
        $groupObj = $rs->FetchObject(false);
        $group_options[] = array(
                             'uid'      => $groupObj->xar_uid,
                             'name'     => $groupObj->xar_name,
                             'selected' => ($groupObj->xar_uid == $share_group) ? true : false
                           );
        $rs->MoveNext();
    }
    $data['group_options'] = $group_options;
    
    $data['dateformat'] = xarModGetVar('julian','dateformat');
    //$data['timeform'] = xarModGetVar('julian','timeform');
    
    //xarModSetVar('julian', 'BulletForm', 'bull');
    $data['BulletForm'] = xarModGetVar('julian', 'BulletForm');
    
    // Hooks calling
    $hooks = xarModCallHooks('module', 'modifyconfig', 'julian', array('module' => 'julian'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    return $data;
} 

?>
