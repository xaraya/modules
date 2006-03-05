<?php
/**
 * Ephemerids
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ephemerids Module
 * @link http://xaraya.com/index.php/release/15.html
 * @author Volodymyr Metenchuk
 */
/**
 * Add new ephemerids to database.
 */
function ephemerids_admin_add()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('tid','int:1:',$tid)) return;
    if (!xarVarFetch('did','int:1:',$did)) return;
    if (!xarVarFetch('mid','int:1:',$mid)) return;
    if ($tid == 1) { // 1 - ephemerids, 2 - nameday
        if (!xarVarFetch('yid','int:1:',$yid)) return;
    } else {
        $yid = 0;
    }
    if (!xarVarFetch('content','str:1:',$content, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('elanguage','str:1:',$elanguage, 'ALL',XARVAR_NOT_REQUIRED)) return;

    // Confirm Auth
    if (!xarSecConfirmAuthKey()) return;

    // Security Check
    if(!xarSecurityCheck('AddEphemerids')) return;

    // The API function is called.
    $emp = xarModAPIFunc('ephemerids',
                         'admin',
                         'add',
                         array('tid' => $tid,
                               'did' => $did,
                               'mid' => $mid,
                               'yid' => $yid,
                               'content' => $content,
                               'elanguage' => $elanguage));

    // The return value of the function is checked here
    if (!isset($emp) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('ephemerids', 'admin', 'view'));
    // Return
    return true;
}

?>