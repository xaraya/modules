<?php
/*
 * File: $Id: updateconfig.php,v 1.1 2003/09/19 09:16:03 jojodee Exp $
 *
 * SiteTools Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by Jo Dalle Nogare
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage SiteTools module
 * @author jojodee <http://xaraya.athomeandabout.com >
*/

/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function sitetools_admin_updateconfig()
{

   if (!xarVarFetch('adopath', 'str:4:128', $adopath, '')) return;
    if (!xarVarFetch('rsspath', 'str:4:128', $rsspath, '')) return;
    if (!xarVarFetch('templpath', 'str:4:128', $templpath,'')) return;
    if (!xarVarFetch('backuppath', 'str:4:128', $backuppath,'')) return;
    if (!xarVarFetch('lineterm', 'str:2:4', $lineterm,'')) return;
    if (!xarVarFetch('usetimestamp', 'int:1:', $usetimestamp,'')) return;
    if (!xarVarFetch('colnumber', 'int:1:', $colnumber,'')) return;
    if (!xarVarFetch('confirm', 'str:4:128', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;
    // Update module variables.  Note that the default values are set in 
    // xarVarFetch when recieving the incoming values, so no extra processing
    // is needed when setting the variables here.
    xarModSetVar('sitetools','adocachepath',$adopath);
    xarModSetVar('sitetools','rsscachepath', $rsspath);
    xarModSetVar('sitetools','templcachepath', $templpath);
    xarModSetVar('sitetools','backuppath', $backuppath);
    xarModSetVar('sitetools','lineterm', $lineterm);
    xarModSetVar('sitetools','timestamp', $usetimestamp);
    xarModSetVar('sitetools','colnumber',$colnumber);       

    xarModCallHooks('module','updateconfig','sitetools',
                   array('module' => 'sitetools'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('sitetools', 'admin', 'modifyconfig'));

    // Return
    return true;
}
?>
