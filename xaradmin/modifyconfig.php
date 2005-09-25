<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 */
/**
 * 
 * Function that allows modification of configurable variables for the julian calendar
 *
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @link http://www.metrostat.net
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 */

function julian_admin_modifyconfig()
{ 
    // Security Check
    if (!xarSecurityCheck('Adminjulian')) return;

    $data=array();
    $data['share_group'] = xarModGetVar('julian','share_group');
    $data['dateformat']  = xarModGetVar('julian','dateformat');
    //$data['timeform']  = xarModGetVar('julian','timeform');
    
    //xarModSetVar('julian', 'BulletForm', 'bull');
    $data['BulletForm'] = xarModGetVar('julian', 'BulletForm');
    $data['numitems']   = xarModGetVar('julian','numitems');
    $data['from_name']  = xarModGetVar('julian','from_name');
    $data['from_email'] = xarModGetVar('julian','from_email');
    
    // Call hooks
    $hooks = xarModCallHooks('module', 'modifyconfig', 'julian', array('module' => 'julian'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } else {
        $data['hooks'] = $hooks;
    }
    return $data;
} 

?>
