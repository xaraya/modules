<?php
/*
 *
 * Keywords Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @author mikespub
*/

/**
 * modify existing keywords assignment
 */
function keywords_admin_modify($args)
{ 
    extract($args);

    if (!xarVarFetch('itemid', 'id', $itemid)) return;
    if (!xarVarFetch('confirm',  'isset', $confirm,  NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarSecurityCheck('AdminKeywords')) return;

    $data = array();
    $data['object'] =& xarModAPIFunc('dynamicdata','user','getobject',
                                     array('module' => 'keywords'));
    if (!isset($data['object'])) return;

    // Get current item
    $newid = $data['object']->getItem(array('itemid' => $itemid));
    if (empty($newid) || $newid != $itemid) return;

    if (!empty($confirm)) {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return; 

        // check the input values for this object
        $isvalid = $data['object']->checkInput();
        if ($isvalid) {
            // update the item here
            $itemid = $data['object']->updateItem();
            if (empty($itemid)) return; // throw back

            // let's go back to the admin view
            xarResponseRedirect(xarModURL('keywords', 'admin', 'view'));
            return true;
        }
    }

    $item = array();
    $item['module'] = 'keywords';
    $hooks = xarModCallHooks('item','modify',$itemid,$item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    $data['itemid'] = $itemid;
    $data['authid'] = xarSecGenAuthKey();
    $data['confirm'] = xarML('Update');
    return $data;
}

?>
