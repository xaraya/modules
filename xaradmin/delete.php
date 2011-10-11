<?php
/**
 * Ephemerids Module
 *
 * @package modules
 * @subpackage ephemerids module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/15.html
 * @author Volodymyr Metenchuk
 */
/**
 * Delete selected ephemerids
 */
function ephemerids_admin_delete($args)
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('eid','int:1:',$eid)) return;
    if (!xarVarFetch('obid','str:1:',$obid,$eid,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm','str:1:',$confirm,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirmation','str:1:',$confirmation,'',XARVAR_NOT_REQUIRED)) return;

    extract ($args);
    // Security Check
    if(!xarSecurityCheck('DeleteEphemerids')) return;

    // Check for confirmation.
    if (empty($confirmation)) {
    $data['eid'] = $eid;
    $data['authid'] = xarSecGenAuthKey();
    return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    // The API function is called
    if (xarModAPIFunc('ephemerids',
                      'admin',
                      'delete',
                      array('eid' => $eid))) {

    }
    xarController::redirect(xarModURL('ephemerids', 'admin', 'view'));
    // Return
    return true;
}

?>