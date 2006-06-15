<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
/**
 * Standard function to update module configuration parameters
 *
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @author XProject module development team
 */
function xproject_admin_updateconfig()
{
    if (!xarSecConfirmAuthKey()) return;

    if (!xarVarFetch('displaydates', 'checkbox', $displaydates, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('displayhours', 'checkbox', $displayhours, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('displayfrequency', 'checkbox', $displayfrequency, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('accessrestricted', 'checkbox', $accessrestricted, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateformat', 'int', $dateformat, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxdone', 'int:1:20', $maxdone, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mostimportantdays', 'isset', $mostimportantdays, 0, XARVAR_NOT_REQUIRED)) return; // TODO: ??
    if (!xarVarFetch('refreshmain', 'int:1:', $refreshmain, 600, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sendmails',    'checkbox', $sendmails, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showextraasterisk',    'checkbox', $showextraasterisk, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showlinenumbers',    'checkbox', $showlinenumbers, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showpercent',    'checkbox', $showpercent, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showpriority',    'checkbox', $showpriority, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('todoheading',    'str:1:',   $todoheading, xarML('Task Management Administration'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('veryimportantdays', 'int:1:', $veryimportantdays, 0, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('itemsperpage', 'int',      $itemsperpage, 20, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls',    'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname',    'str:1:',   $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias',  'checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;

    xarModSetVar('xproject', 'displaydates', $displaydates);
    xarModSetVar('xproject', 'displayhours', $displayhours);
    xarModSetVar('xproject', 'displayfrequency', $displayfrequency);
    xarModSetVar('xproject', 'accessrestricted', $accessrestricted);
    xarModSetVar('xproject', 'dateformat', $dateformat);
    xarModSetVar('xproject', 'maxdone', $maxdone);
    xarModSetVar('xproject', 'mostimportantdays', $mostimportantdays);
    xarModSetVar('xproject', 'refreshmain', $refreshmain);
    xarModSetVar('xproject', 'sendmails', $sendmails);
    xarModSetVar('xproject', 'showextraasterisk', $showextraasterisk);
    xarModSetVar('xproject', 'showlinenumbers', $showlinenumbers);
    xarModSetVar('xproject', 'showpercent', $showpercent);
    xarModSetVar('xproject', 'showpriority', $showpriority);
    xarModSetVar('xproject', 'todoheading', $todoheading);
    xarModSetVar('xproject', 'veryimportantdays', $veryimportantdays);
    xarModSetVar('xproject', 'itemsperpage', $itemsperpage);

    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModSetVar('xproject', 'useModuleAlias', $modulealias);
    } else{
         xarModSetVar('xproject', 'useModuleAlias', 0);
    }
    $currentalias = xarModGetVar('xproject','aliasname');
    $newalias = trim($aliasname);
    /* Get rid of the spaces if any, it's easier here and use that as the alias*/
    if ( strpos($newalias,'_') === FALSE )
    {
        $newalias = str_replace(' ','_',$newalias);
    }
    $hasalias= xarModGetAlias($currentalias);
    $useAliasName= xarModGetVar('xproject','useModuleAlias');

    if (($useAliasName==1) && !empty($newalias)){
        /* we want to use an aliasname */
        /* First check for old alias and delete it */
        if (isset($hasalias) && ($hasalias =='xproject')){
            xarModDelAlias($currentalias,'xproject');
        }
        /* now set the new alias if it's a new one */
          xarModSetAlias($newalias,'xproject');
    }

    xarModCallHooks('module','updateconfig','xproject',
                   array('module' => 'xproject'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'main'));

    return true;
}

?>