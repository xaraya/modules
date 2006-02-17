<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Logconfig Module
 * @link http://xaraya.com/index.php/release/6969.html
 * @author Logconfig module development team
 */
/**
 * update an item
 */
function logconfig_admin_update($args)
{
    list($itemid,
         $objectid,
         $itemtype,
         $preview) = xarVarCleanFromInput('itemid',
                                          'objectid',
                                          'itemtype',
                                          'preview');

    extract($args);

    if (!empty($objectid)) {
        $itemid = $objectid;
    }

    // check the authorisation key
    if (!xarSecConfirmAuthKey()) return; // throw back

    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'update', 'logconfig');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module' => 'logconfig',
                                   'itemid' => $itemid,
                                   'itemtype' => $itemtype));
    if (!isset($object)) return;

    // get the values for this item
    $newid = $object->getItem();
    if (!isset($newid) || $newid != $itemid) return;

    // check the input values for this object
    $isvalid = $object->checkInput();

    // if we're in preview mode, or if there is some invalid input, show the form again
    if (!empty($preview) || !$isvalid) {
        $data = xarModAPIFunc('logconfig','admin','menu');

        $data['object'] = & $object;
        $data['itemid'] = $itemid;
        $data['itemtype'] = $itemtype;

        $data['preview'] = $preview;

        $item = array();
        $item['module'] = 'logconfig';

        return xarTplModule('logconfig','admin','modify', $data);
    }

    // update the item
    $itemid = $object->updateItem();

    if (empty($itemid)) return; // throw back

    //Update the Configuration file if Logging is on
     if (xarModAPIFunc('logconfig','admin','islogon')) {
        if (!xarModAPIFunc('logconfig','admin','saveconfig')) return;
     }

    // let's go back to the admin view
    xarResponseRedirect(xarModURL('logconfig', 'admin', 'view'));

    // Return
    return true;
}

?>