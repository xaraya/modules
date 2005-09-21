<?php
/**
 * Re-synchronise the totals and last posts of forums
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author mikespub
*/ 

/**
 * Re-synchronise forums and topics
 *
 * @param $args['fid'] int forum id (optional)
 * @param $args['withtopics'] bool update topics too (optional, default false)
 * @returns void
*/
function xarbb_admin_sync()
{
    // Security Check
    if (!xarSecurityCheck('EditxarBB',1,'Forum')) return;

    // Get parameters
    if (!xarVarFetch('fid','id', $fid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('withtopics','bool', $withtopics, false, XARVAR_NOT_REQUIRED)) return;

    // Pass arguments to the API function
    if (!xarModAPIFunc('xarbb','admin','sync',
                       array('fid'        => $fid,
                             'withtopics' => $withtopics))) {
        return;
    }

    // redirect
    xarResponseRedirect(xarModURL('xarbb', 'admin', 'view'));
    return true;
}
?>
