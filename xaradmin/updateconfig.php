<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
/**
 * Standard function to update module configuration parameters
 *
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @author St.Ego
 */
function xtasks_admin_updateconfig($args)
{
    extract($args);    

//    if (!xarVarFetch('displaydates', 'checkbox', $displaydates, false, XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('displayhours', 'checkbox', $displayhours, false, XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('displayfrequency', 'checkbox', $displayfrequency, false, XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('accessrestricted', 'checkbox', $accessrestricted, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateformat', 'str::', $dateformat, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxdone', 'int:1:20', $maxdone, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mostimportantdays', 'isset', $mostimportantdays, 0, XARVAR_NOT_REQUIRED)) return; // TODO: ??
    if (!xarVarFetch('refreshmain', 'int:1:', $refreshmain, 600, XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('sendmails',    'checkbox', $sendmails, false, XARVAR_NOT_REQUIRED)) return;
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
    
    if (!xarSecurityCheck('AdminXProject')) return;
    if (!xarSecConfirmAuthKey()) return;

//    xarModSetVar('xtasks', 'displaydates', $displaydates);
//    xarModSetVar('xtasks', 'displayhours', $displayhours);
//    xarModSetVar('xtasks', 'displayfrequency', $displayfrequency);
//    xarModSetVar('xtasks', 'accessrestricted', $accessrestricted);
    xarModSetVar('xtasks', 'dateformat', $dateformat);
    xarModSetVar('xtasks', 'maxdone', $maxdone);
    xarModSetVar('xtasks', 'mostimportantdays', $mostimportantdays);
    xarModSetVar('xtasks', 'refreshmain', $refreshmain);
//    xarModSetVar('xtasks', 'sendmails', $sendmails);
    xarModSetVar('xtasks', 'showextraasterisk', $showextraasterisk);
    xarModSetVar('xtasks', 'showlinenumbers', $showlinenumbers);
    xarModSetVar('xtasks', 'showpercent', $showpercent);
    xarModSetVar('xtasks', 'showpriority', $showpriority);
    xarModSetVar('xtasks', 'todoheading', $todoheading);
    xarModSetVar('xtasks', 'veryimportantdays', $veryimportantdays);
    xarModSetVar('xtasks', 'itemsperpage', $itemsperpage);

    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModSetVar('xtasks', 'useModuleAlias', $modulealias);
    } else{
         xarModSetVar('xtasks', 'useModuleAlias', 0);
    }
    $currentalias = xarModGetVar('xtasks','aliasname');
    $newalias = trim($aliasname);
    /* Get rid of the spaces if any, it's easier here and use that as the alias*/
    if ( strpos($newalias,'_') === FALSE )
    {
        $newalias = str_replace(' ','_',$newalias);
    }
    $hasalias= xarModGetAlias($currentalias);
    $useAliasName= xarModGetVar('xtasks','useModuleAlias');

    if (($useAliasName==1) && !empty($newalias)){
        /* we want to use an aliasname */
        /* First check for old alias and delete it */
        if (isset($hasalias) && ($hasalias =='xtasks')){
            xarModDelAlias($currentalias,'xtasks');
        }
        /* now set the new alias if it's a new one */
          xarModSetAlias($newalias,'xtasks');
    }

    xarModCallHooks('module','updateconfig','xtasks',
                   array('module' => 'xtasks'));
                   
    xarSessionSetVar('statusmsg', xarML('xTasks module settings updated.'));

    xarResponseRedirect(xarModURL('xtasks', 'admin', 'main'));

    return true;
}

?>