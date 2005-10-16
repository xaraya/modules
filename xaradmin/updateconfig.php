<?php
/**
 * xarLinkMe update onfiguration
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarlinkme
 * @link http://xaraya.com/index.php/release/889.html
 * @author jojodee <jojodee@xaraya.com>
 */

/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function xarlinkme_admin_updateconfig()
{
  if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'basic', XARVAR_NOT_REQUIRED)) return;
    switch ($data['tab']) {
        case 'basicconfig':
            if (!xarVarFetch('itemsperpage', 'str:1:', $itemsperpage,  XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('shorturls',    'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('aliasname',    'str:1:',   $aliasname, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('modulealias',  'checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;
            xarModSetVar('xarlinkme', 'itemsperpage', $itemsperpage);
            xarModSetVar('xarlinkme', 'SupportShortURLs', $shorturls);
            if (isset($aliasname) && trim($aliasname)<>'') {
                xarModSetVar('xarlinkme', 'useModuleAlias', $modulealias);
            } else{
                xarModSetVar('xarlinkme', 'useModuleAlias', 0);
            }
            $currentalias = xarModGetVar('xarlinkme','aliasname');
            $newalias = trim($aliasname);
            /* Get rid of the spaces if any, it's easier here and use that as the alias*/
            if ( strpos($newalias,'_') === FALSE )
            {
                $newalias = str_replace(' ','_',$newalias);
            }
            $hasalias= xarModGetAlias($currentalias);
            $useAliasName= xarModGetVar('xarlinkme','useModuleAlias');

            if (($useAliasName==1) && !empty($newalias)){
                /* we want to use an aliasname */
                /* First check for old alias and delete it */
                if (isset($hasalias) && ($hasalias =='xarlinkme')){
                   xarModDelAlias($currentalias,'xarlinkme');
                }
                /* now set the new alias if it's a new one */
                xarModSetAlias($newalias,'xarlinkme');
            }
           /* now set the alias modvar */
           xarModSetVar('xarlinkme', 'aliasname', $newalias);

           break;
        case 'clientconfig':
            if (!xarVarFetch('usebanners',   'checkbox', $usebanners, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('excludedips',  'str:1', $excludedips, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
            
            $excludedips = serialize($excludedips);
            xarModSetVar('xarlinkme', 'excludedips', $excludedips);
            xarModSetVar('xarlinkme', 'activebanners', $usebanners);

           break;
  
        case 'siteconfig':
            if (!xarVarFetch('imagedir', 'str:1:', $imagedir)) return;
            if (!xarVarFetch('pagetitle', 'str:1:', $pagetitle)) return;
            if (!xarVarFetch('instructions', 'str:1:', $instructions)) return;
            if (!xarVarFetch('instructions2', 'str:1:', $instructions2)) return;
            if (!xarVarFetch('txtintro', 'str:1:', $txtintro)) return;
            if (!xarVarFetch('txtadlead', 'str:1:', $txtadlead)) return;
            if (!xarVarFetch('linkdirect', 'checkbox', $linkdirect, false, XARVAR_NOT_REQUIRED)) return;

            xarModSetVar('xarlinkme', 'imagedir', $imagedir);
            xarModSetVar('xarlinkme', 'pagetitle', $pagetitle);
            xarModSetVar('xarlinkme', 'instructions', $instructions);
            xarModSetVar('xarlinkme', 'instructions2', $instructions2);
            xarModSetVar('xarlinkme', 'txtintro', $txtintro);
            xarModSetVar('xarlinkme', 'txtadlead',$txtadlead);
            xarModSetVar('xarlinkme', 'allowlinking',$linkdirect);
        break;
    }

    xarModCallHooks('module','updateconfig','xarlinkme',
                   array('module' => 'xarlinkme'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('xarlinkme', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>