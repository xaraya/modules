<?php
/**
 * Delete an item
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage window
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Johnny Robeson
 */

/**
 * Delete an item
 *
 * @access public
 *
 * @param int $args[itemid]
 */
function window_admin_delete($args)
{
    if (!xarSecurityCheck('AdminWindow')) return;

    if (!xarVarFetch('itemid', 'id', $itemid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('bluff', 'str', $bluff, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);

    //check the item
    $item = xarModAPIFunc('window', 'user', 'get', array('itemid' => $itemid));

    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */
    
    /* Check for confirmation. */
    if (empty($confirm)) {
         $data['authid'] = xarSecGenAuthKey();
         $data['itemid']=$itemid;
         $data['itemname']=$item['name'];
         return $data;
    }
    
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('window', 'admin', 'delete',
                 array('itemid' => $itemid))) {
        return;
    }

    xarResponseRedirect(xarModURL('window', 'admin', 'newurl'));
}
?>