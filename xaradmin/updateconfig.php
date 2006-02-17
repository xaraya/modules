<?php
/**
 * Update module configuration parameters
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */

/**
 * Standard function to update module configuration parameters
 *
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 *
 * This package (Julian):
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.metrostat.net
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @author MichelV <michelv@xarayahosting.nl>
 * @return bool true with a redirect
 */

function julian_admin_updateconfig($args)
{
    // Security Check
    if (!xarSecurityCheck('AdminJulian')) {
        return;
    }

    extract($args);

    if (!xarVarFetch('ical_links',      'checkbox', $ical_links,    false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('share_group',     'int', $share_group,        false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('from_name',       'str', $from_name,          '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('from_email',      'str', $from_email,         '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startDayOfWeek',  'int:0:1', $startDayOfWeek, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('TelFieldType',    'str', $TelFieldType,       '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('BulletForm',      'str', $BulletForm,         '', XARVAR_DONT_REUSE)) return;
    if (!xarVarFetch('dateformat',      'str', $dateformat,         '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('timeform',        'str', $timeform,           '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems',        'int::', $numitems,         10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname',       'str:1:',   $aliasname,     '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias',     'checkbox', $modulealias,   false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('DurMinInterval',  'int:1:15', $DurMinInterval,15, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('StartMinInterval','int:1:15', $StartMinInterval,15, XARVAR_NOT_REQUIRED)) return;

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
    // Duration minute interval
    xarModSetVar('julian', 'DurMinInterval', $DurMinInterval);
    // Starttime Minute interval
    xarModSetVar('julian', 'StartMinInterval', $StartMinInterval);
    // Module Alias
    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModSetVar('julian', 'useModuleAlias', $modulealias);
    } else{
         xarModSetVar('julian', 'useModuleAlias', 0);
    }
    $currentalias = xarModGetVar('julian','aliasname');
    $newalias = trim($aliasname);
    /* Get rid of the spaces if any, it's easier here and use that as the alias*/
    if ( strpos($newalias,'_') === FALSE )
    {
        $newalias = str_replace(' ','_',$newalias);
    }
    $hasalias= xarModGetAlias($currentalias);
    $useAliasName= xarModGetVar('julian','useModuleAlias');

    if (($useAliasName==1) && !empty($newalias)){
        /* we want to use an aliasname */
        /* First check for old alias and delete it */
        if (isset($hasalias) && ($hasalias =='julian')){
            xarModDelAlias($currentalias,'julian');
        }
        /* now set the new alias if it's a new one */
          xarModSetAlias($newalias,'julian');
    }
    /* now set the alias modvar */
    xarModSetVar('julian', 'aliasname', $newalias);
    // Call the hooks
    xarModCallHooks('module','updateconfig','julian', array('module' => 'julian'));

    xarSessionSetVar('statusmsg',xarML('Configuration Updated'));
    xarResponseRedirect(xarModURL('julian', 'admin', 'modifyconfig'));
    return true;
}
?>
