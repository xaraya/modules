<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
function headlines_admin_updateconfig()
{
    if (!xarSecConfirmAuthKey()) return;
    if (!xarSecurityCheck('AdminHeadlines')) return;
    if (!xarVarFetch('itemsperpage', 'str:1:', $itemsperpage, '20', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    //if (!xarVarFetch('magpie', 'checkbox', $magpie, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('parser', 'enum:default:magpie:simplepie', $parser, 'default', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('importpubtype', 'id', $importpubtype, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('uniqueid', 'str:1:', $uniqueid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias', 'checkbox', $modulealias, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname', 'str:1:', $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showcomments', 'checkbox', $showcomments, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showratings', 'checkbox', $showratings, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showhitcount', 'checkbox', $showhitcount, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showkeywords', 'checkbox', $showkeywords, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('feeditemsperpage', 'int:1', $feeditems, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxdescription', 'int:1', $maxdescription, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('adminajax', 'checkbox', $adminajax, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('userajax', 'checkbox', $userajax, 0, XARVAR_NOT_REQUIRED)) return;
    xarModSetVar('headlines', 'itemsperpage', $itemsperpage);
    xarModSetVar('headlines', 'SupportShortURLs', $shorturls);
    xarModSetVar('headlines', 'useModuleAlias', $modulealias);
    xarModSetVar('headlines', 'feeditemsperpage', $feeditems);
    xarModSetVar('headlines', 'maxdescription', $maxdescription);
    xarModSetVar('headlines', 'adminajax', $adminajax);
    xarModSetVar('headlines', 'userajax', $userajax);

    // The magpie var is no longer needed
    if (xarModGetVar('headlines', 'magpie')) xarModDelVar('headlines', 'magpie');

    xarModSetVar('headlines', 'parser', $parser);
    xarModSetVar('headlines', 'importpubtype', $importpubtype);
    xarModSetVar('headlines', 'uniqueid', $uniqueid);
    
    if (!xarModIsAvailable('comments') || !xarModIsHooked('comments', 'headlines')) {
        $showcomments = 0;
    }
    xarModSetVar('headlines', 'showcomments', $showcomments);
    if (!xarModIsAvailable('ratings') || !xarModIsHooked('ratings', 'headlines')) {
        $showratings = 0;
    }
    xarModSetVar('headlines', 'showratings', $showratings);
    if (!xarModIsAvailable('hitcount') || !xarModIsHooked('hitcount', 'headlines')) {
        $showhitcount = 0;
    }
    xarModSetVar('headlines', 'showhitcount', $showhitcount);
    if (!xarModIsAvailable('keywords') || !xarModIsHooked('keywords', 'headlines')) {
        $showkeywords = 0;
    }
    xarModSetVar('headlines', 'showkeywords', $showkeywords);
    // Module alias for short URLs
   $currentalias = xarModGetVar('headlines','aliasname');
   $newalias = trim($aliasname);
   /* Get rid of the spaces if any, it's easier here and use that as the alias*/
   if ( strpos($newalias,'_') === FALSE )
   {
       $newalias = str_replace(' ','_',$newalias);
   }
   $hasalias= xarModGetAlias($currentalias);
   $useAliasName= xarModGetVar('headlines','useModuleAlias');

   if (($useAliasName==1) && !empty($newalias)){
       /* we want to use an aliasname */
       /* First check for old alias and delete it */
       if (isset($hasalias) && ($hasalias =='headlines')){
           xarModDelAlias($currentalias,'headlines');
       }
       /* now set the new alias if it's a new one */
       xarModSetAlias($newalias,'headlines');
   } elseif (!empty($currentalias) && $useAliasName==0) {
       /* we're not using an aliasname, delete the old alias */
       xarModDelAlias($currentalias,'headlines');
   }
   /* now set the alias modvar */
   xarModSetVar('headlines', 'aliasname', $newalias);

    xarModCallHooks('module','updateconfig','headlines', array('module' => 'headlines'));
    xarResponseRedirect(xarModURL('headlines', 'admin', 'modifyconfig'));
    return true;
}

?>
