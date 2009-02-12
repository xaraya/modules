<?php
/**
 * Update configuration
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/732.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function dossier_admin_updateconfig()
{
/* we'll let our dynamic module settings be handled by DD here (optional)

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarFetch(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    if (!xarVarFetch('bold', 'checkbox', $bold, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;

    // Update module variables.  Note that depending on the HTML structure used
    // to obtain the information from the user it is possible that the values
    // might be unset, so it is important to check them all and assign them
    // default values if required
    if (!isset($bold)) {
        $bold = 0;
    }
    xarModSetVar('dossier', 'bold', $bold);

    if (!isset($itemsperpage) || !is_numeric($itemsperpage)) {
        $itemsperpage = 10;
    }
    xarModSetVar('dossier', 'itemsperpage', $itemsperpage);

    if (!isset($shorturls)) {
        $shorturls = 0;
    }
    xarModSetVar('dossier', 'SupportShortURLs', $shorturls);
*/
    if (!xarSecConfirmAuthKey()) return false;

    // Other Settings
    if (!xarVarFetch ('displaytitle','str::60',$displaytitle, '', XARVAR_NOT_REQUIRED)) return;
    
    if (!xarVarFetch ('itemsperpage','int:1:100',$itemsperpage,  30)) return;
    
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname', 'str:1:', $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('useModuleAlias','checkbox', $useModuleAlias,false,XARVAR_NOT_REQUIRED)) return;

        
    // csr_group
    if (!xarVarFetch ('csr_group','int::',$csr_group, 0,XARVAR_NOT_REQUIRED)) return;
    
    if (!xarSecurityCheck('AdminDossier',0)) return false;

    $currentalias = xarModGetVar('dossier','aliasname');
    $newalias = trim($aliasname);
          /* Get rid of the spaces if any, it's easier here and use that as the alias*/
    if ( strpos($newalias,'_') === FALSE )
    {
        $newalias = str_replace(' ','_',$newalias);
    }
    $hasalias= xarModGetAlias($currentalias);
    $useAliasName= xarModGetVar('sitecontact','useModuleAlias');

    if (($useAliasName==1) && !empty($newalias)){
        /* we want to use an aliasname */
        /* First check for old alias and delete it */
        if (isset($hasalias) && ($hasalias =='sitecontact')){
            xarModDelAlias($currentalias,'sitecontact');
        }
        /* now set the new alias if it's a new one */
          xarModSetAlias($newalias,'dossier');
    }
    /* now set the alias modvar */
    xarModSetVar('dossier', 'useModuleAlias', $newalias);
    xarModSetVar('dossier', 'aliasname', $newalias);
    
    xarModSetVar('dossier', 'SupportShortURLs', $shorturls);
    
    xarModSetVar('dossier', 'csr_group', $csr_group);
    
    xarModSetVar('dossier', 'displaytitle', $displaytitle);
    xarModSetVar('dossier', 'itemsperpage', $itemsperpage);

    xarSessionSetVar('errormsg', xarML('Configuration saved!'));
    
    xarModCallHooks('module','updateconfig','dossier',
                    array('module' => 'dossier'));

//    if (!xarModFunc('dynamicdata','admin','update')) return; // throw back

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('dossier', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>
