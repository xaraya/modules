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
    xarSessionSetVar('statusmsg', '');
    // Pass arguments to the API function
    if (!xarModAPIFunc('xarbb','admin','sync',
                       array('fid'        => $fid,
                             'withtopics' => $withtopics))) {
       if ($withtopics) {
          xarSessionSetVar('statusmsg', xarML('Problem syncing forum topics!'));
       } else {
          xarSessionSetVar('statusmsg', xarML('Problem syncing forum!'));
       }

        return;
    }
    if ($withtopics) {
        xarSessionSetVar('statusmsg', xarML('Forum topics successfully synced'));
       } else {
        xarSessionSetVar('statusmsg', xarML('Forum successfully synced'));
       }
    // redirect
    xarResponseRedirect(xarModURL('xarbb', 'admin', 'modify',array('fid'=> $fid)));
    return true;
}
?>
