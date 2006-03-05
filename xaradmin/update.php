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
function ephemerids_admin_update($args)
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('tid','int:1:',$tid)) return;
    if (!xarVarFetch('did','int:1:',$did)) return;
    if (!xarVarFetch('mid','int:1:',$mid)) return;
    if ($tid == 1) {
        if (!xarVarFetch('yid','int:1:',$yid)) return;
    } else {
        $yid = 0;
    }
    if (!xarVarFetch('content','str:1:',$content, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('elanguage','str:1:',$elanguage, 'ALL',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('eid','int:1:',$eid)) return;
    if (!xarVarFetch('objectid','str:1:',$objectid,$eid,XARVAR_NOT_REQUIRED)) return;
    extract($args);
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // The API function is called.
    if(!xarModAPIFunc('ephemerids',
                    'admin',
                    'update',
                    array('eid' => $eid,
                          'tid' => $tid,
                          'did' => $did,
                          'mid' => $mid,
                          'yid' => $yid,
                          'content' => $content,
                          'elanguage' => $elanguage))) {
        return; // throw back
    }
    //Redirect
    xarResponseRedirect(xarModURL('ephemerids', 'admin', 'view'));
    // Return
    return true;
}
?>