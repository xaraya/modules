<?php
/**
 * Modify an entry of the encyclopedia
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @link http://xaraya.com/index.php/release/221.html
 * @author Marc Lutolf
 */

function encyclopedia_admin_modifyentry()
{
//    if (!xarSecurityCheck('AddEncyclopedia',0,'Entry',$item['term'] . "::" . $id)) {return;}
    if (!xarVarFetch('itemid',      'isset', $itemid,     0, XARVAR_NOT_REQUIRED)) return;

    $objectid = xarModGetVar('encyclopedia','encyclopediaid');
    $object = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                            array('objectid' => $objectid));

    // Get this item based on the $itemid
    // This also loads the dynamic data API, which enables the next step
    $myobject = & Dynamic_Object_Master::getObject(array('objectid' => $objectid,
                                         'moduleid' => $object['moduleid'],
                                         'itemtype' => $object['itemtype'],
                                         'itemid'   => $itemid));
    //Load the item info
    // Send the item to the template
    $myobject->getItem();
    $data['object'] =& $myobject;

    // Assemble the information needed for the hooks
    $modinfo = xarModGetInfo($myobject->moduleid);
    $item = array();
    foreach (array_keys($myobject->properties) as $name) {
        $item[$name] = $myobject->properties[$name]->value;
    }
    $hooks = xarModCallHooks('item', 'new', $myobject->itemid, $item, $modinfo['name']);

    // Send the hooks info to the template
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    // Generate a security ID for the template
    $data['authid'] = xarSecGenAuthKey();

    return $data;
}
?>