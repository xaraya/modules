<?php
/**
 * Display a term in the encyclopedia
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_user_displayterm()
{
    if(!xarVarFetch('id',   'int', $id)) {return;}
    if (xarSecurityCheck('ReadEncyclopedia',0,'Volume',"::" . $id))

    $objectid = xarModGetVar('encyclopedia','encyclopediaid');
    $object = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                            array('objectid' => $objectid));

    // Get this item based on the $itemid
    // This also loads the dynamic data API, which enables the next step
    $myobject = & Dynamic_Object_Master::getObject(array('objectid' => $objectid,
                                         'moduleid' => $object['moduleid'],
                                         'itemtype' => $object['itemtype'],
                                         'itemid'   => $id));
    //Load the item info
    $myobject->getItem();
    $data['object'] =& $myobject;

    // Assemble the information needed for the display
    $modinfo = xarModGetInfo($myobject->moduleid);
    $item = array();
    foreach (array_keys($myobject->properties) as $name) {
        $item[$name] = $myobject->properties[$name]->value;
    }
    $data['item'] = $item;
    $data['volume'] = xarModAPIFunc('categories','user','getcatinfo', array('cid' => $item['vid']));

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