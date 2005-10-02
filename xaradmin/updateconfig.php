<?php
/**
 * File: $Id:
 * 
 * Standard function to update configuration variables
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

function julian_admin_updateconfig($args)
{ 
    // Security Check
    if (!xarSecurityCheck('Adminjulian')) return;

    extract($args);

    if (!xarVarFetch('ical_links', 'checkbox', $ical_links, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('share_group', 'str', $share_group, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('from_name', 'str', $from_name, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('from_email', 'str', $from_email, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startDayOfWeek', 'str', $startDayOfWeek, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('TelFieldType', 'str', $TelFieldType, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('BulletForm', 'str', $BulletForm, false, XARVAR_DONT_REUSE)) return;
    if (!xarVarFetch('dateformat', 'str', $dateformat, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('timeform', 'str', $timeform, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int', $numitems, 10, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;
    
    xarModSetVar('julian','ical_links',$ical_links);
    xarModSetVar('julian','share_group',$share_group);
    xarmodSetVar('julian','from_name',$from_name);
    xarModSetVar('julian','from_email',$from_email);
    xarModSetVar('julian','startDayOfWeek',$startDayOfWeek);
    xarModSetVar('julian','TelFieldType',$TelFieldType);
    xarModSetVar('julian','BulletForm',$BulletForm);
    xarModSetVar('julian','dateformat',$dateformat);
    xarModSetVar('julian','timeform',$timeform);
    xarModSetVar('julian','numitems',$numitems);

    xarModCallHooks('module','updateconfig','julian', array('module' => 'julian')); //Call hooks

    xarSessionSetVar('statusmsg',xarML('Configuration Updated'));
    xarResponseRedirect(xarModURL('julian', 'admin', 'modifyconfig')); 
    return true;
} 
?>
