<?php
/**
 * Update configuration parameters of the module with information passed back by the modification form
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 *
 * @return true
 */
function courses_admin_updateconfig()
{
    if (!xarVarFetch('HideEmptyFields', 'checkbox', $HideEmptyFields, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage',    'int:1:',   $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls',       'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hidecoursemsg',   'str::',    $hidecoursemsg, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('AlwaysNotify',    'str::',    $AlwaysNotify, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hideplanningmsg', 'str::',    $hideplanningmsg, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('DefaultTeacherType', 'int',    $DefaultTeacherType, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ShowShortDesc',   'checkbox', $ShowShortDesc, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname',       'str:1:',   $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias',     'checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    // Update module variables.
    xarModSetVar('courses', 'HideEmptyFields', $HideEmptyFields);
    xarModSetVar('courses', 'itemsperpage', $itemsperpage);
    xarModSetVar('courses', 'SupportShortURLs', $shorturls);
    xarModSetVar('courses', 'hidecoursemsg', $hidecoursemsg);
    xarModSetVar('courses', 'hideplanningmsg', $hideplanningmsg);
    xarModSetVar('courses', 'AlwaysNotify', $AlwaysNotify);
    xarModSetVar('courses', 'ShowShortDesc', $ShowShortDesc);
    xarModSetVar('courses', 'DefaultTeacherType', $DefaultTeacherType);
    // Alias name
    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModSetVar('courses', 'useModuleAlias', $modulealias);
    } else{
         xarModSetVar('courses', 'useModuleAlias', 0);
    }
    $currentalias = xarModGetVar('courses','aliasname');
    $newalias = trim($aliasname);
    /* Get rid of the spaces if any, it's easier here and use that as the alias*/
    if ( strpos($newalias,'_') === FALSE )
    {
        $newalias = str_replace(' ','_',$newalias);
    }
    $hasalias= xarModGetAlias($currentalias);
    $useAliasName= xarModGetVar('courses','useModuleAlias');

    if (($useAliasName==1) && !empty($newalias)){
        /* we want to use an aliasname */
        /* First check for old alias and delete it */
        if (isset($hasalias) && ($hasalias =='courses')){
            xarModDelAlias($currentalias,'courses');
        }
        /* now set the new alias if it's a new one */
          xarModSetAlias($newalias,'courses');
    }
    /* now set the alias modvar */
    xarModSetVar('courses', 'aliasname', $newalias);
    // Call hooks
    xarModCallHooks('module','updateconfig','courses',
                   array('module' => 'courses'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('courses', 'admin', 'modifyconfig'));
    // Return
    return true;
}

?>
