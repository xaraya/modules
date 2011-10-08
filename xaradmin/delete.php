<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * delete existing keywords assignment
 */
function keywords_admin_delete($args)
{
    extract($args);

    if (!xarVarFetch('itemid', 'id', $itemid)) return;
    if (!xarVarFetch('confirm',  'isset', $confirm,  NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarSecurityCheck('AdminKeywords')) return;

    $data = array();
    $data['object'] = xarModAPIFunc('dynamicdata','user','getobject',
                                     array('module' => 'keywords'));
    if (!isset($data['object'])) return;

    // Get current item
    $newid = $data['object']->getItem(array('itemid' => $itemid));
    if (empty($newid) || $newid != $itemid) return;

    if (!empty($confirm)) {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;

        // delete the item here
        $itemid = $data['object']->deleteItem();
        if (empty($itemid)) return; // throw back

        // let's go back to the admin view
        xarController::redirect(xarModURL('keywords', 'admin', 'view'));
        return true;
    }

    $data['itemid'] = $itemid;
    $data['authid'] = xarSecGenAuthKey();
    $data['confirm'] = xarML('Delete');
    return $data;
}

?>
