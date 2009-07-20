<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 *//**
 * Standard function to delete a forum
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_admin_delete($args)
{

    extract($args);
    if (!xarVarFetch('fid', 'id', $fid)) return;
    if (!xarVarFetch('sublink', 'str:1:', $sublink, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'enum:form:update', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'checkbox', $confirm, false, XARVAR_NOT_REQUIRED)) return;
    // allow return url to be over-ridden
    if (!xarVarFetch('returnurl', 'str:1:', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('crispbb', 'user', 'getforum', array('fid' => $fid, 'privcheck' => true));

    if ($data == 'NO_PRIVILEGES' || empty($data['deleteforumurl'])) {
        $errorMsg['message'] = xarML('You do not have the privileges required for this action');
        $errorMsg['return_url'] = xarModURL('crispbb', 'user', 'main');
        $errorMsg['type'] = 'NO_PRIVILEGES';
        $errorMsg['pageTitle'] = xarML('No Privileges');
        xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
        return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
    }

    $userLevel = $data['forumLevel'];
    $secLevels = $data['fprivileges'];
    $invalid = array();
    $now = time();
    $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));
    // End Tracking
    if (!empty($tracking)) {
        xarModSetUserVar('crispbb', 'tracking', serialize($tracking));
    }
    $pageTitle = xarML('Delete #(1)', $data['fname']);

    if ($phase == 'update' && $confirm) {
        if (empty($invalid)) {
            if (!xarSecConfirmAuthKey()) return;
            if (!xarModAPIFunc('crispbb', 'admin', 'delete',
                array('fid' => $fid))) return;
            xarSessionSetVar('crispbb_statusmsg', xarML('Forum #(1) deleted', $data['fname']));
            // if no returnurl specified, return to the modify function for the newly created forum
            if (empty($returnurl)) {
                $returnurl = xarModURL('crispbb', 'admin', 'view');
            }
            xarResponseRedirect($returnurl);
            return true;
        }
    }
    $data['pageTitle'] = $pageTitle;
    // populate the menulinks for this function
    $data['menulinks'] = xarModAPIFunc('crispbb', 'admin', 'getmenulinks',
        array(
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'delete',
            'current_sublink' => $sublink,
            'fid' => $fid,
            'catid' => $data['catid'],
            'secLevels' => $secLevels
        ));
    // set page title
    xarTPLSetPageTitle(xarVarPrepForDisplay($pageTitle));

    return $data;
}
?>