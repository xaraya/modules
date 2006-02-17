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
 * create a new logger
 */
function logconfig_admin_create($args)
{
    extract($args);

    if (!xarVarFetch('itemtype','id',$itemtype)) return;
    if (!xarSecurityCheck('AdminLogConfig')) return;
    if (!xarSecConfirmAuthKey()) return; // throw back

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module' => 'logconfig',
                                   'itemtype' => $itemtype));
    if (!isset($object)) return;

    // check the input values for this object
    $valid = $object->checkInput();

    // if we're in preview mode, or if there is some invalid input, show the form again
    if (!$valid) {
        $data = xarModAPIFunc('logconfig','admin','menu');

        $data['object'] = & $object;
        $data['itemtype'] = $itemtype;

        return xarTplModule('logconfig','admin','new', $data);
    }

    // create the item here
    $itemid = $object->createItem();
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