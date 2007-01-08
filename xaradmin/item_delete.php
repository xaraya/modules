<?php
/**
 * Subitems module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Subitems Module
 * @link http://xaraya.com/index.php/release/9356.html
 * @author Subitems Module Development Team
 */
/**
 * Delete an item
 */
function subitems_admin_item_delete($args)
{
    extract($args);

    // The subobject we're deleting an item from
    if(!xarVarFetch('objectid','int:',$subobjectid)) return;

    // The item id withing this subobject
    if(!xarVarFetch('ddid','int:',$ddid)) return;

    if(!xarVarFetch('redirect','str:1',$redirect,xarServerGetVar('HTTP_REFERER'),XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('delete','str:1',$delete,'',XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('confirm','str:1',$confirm,'',XARVAR_NOT_REQUIRED)) return;

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $subobject = xarModAPIFunc('dynamicdata','user','getobject',
                             array('objectid' => $subobjectid,
                                   'itemid' => $ddid));
    if (!isset($subobject)) return;

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if(!xarSecurityCheck('DeleteDynamicDataItem',1,'Item',$subobject->moduleid.':'.$subobject->itemtype.':'.$ddid)) return;

    // get the values for this item
    $newid = $subobject->getItem();
    if (!isset($newid) || $newid != $ddid) return;

    if ($confirm) {
        // check the authorisation key
        if (!xarSecConfirmAuthKey()) return; // throw back

        if ($delete) {
            // delete the item here
            $ddid = $subobject->deleteItem();
            if (empty($ddid)) return; // throw back

            // detach ids -> write db
            if(!xarModAPIFunc('subitems','admin','dditem_detach',
                              array('ddid' => $ddid,
                                    'objectid' => $subobjectid))) return;
        } else {       // cancel
            // do nothing but return
        }

        // back to the caller module
        xarResponseRedirect($redirect);
        return true;
    }

    $data['preview'] = "1";
    $data['object'] = $subobject;
    $data['redirect'] = xarVarPrepHTMLDisplay($redirect);
    $data['ddid'] = $ddid;
    $data['objectid'] = $subobjectid;

    // get the subitems link for this object
    $ddobjectlink = xarModAPIFunc('subitems','user','ddobjectlink_get',
                                  array('objectid' => $subobjectid));
    // nothing to see here
    if (empty($ddobjectlink)) return;

    // set the template if available
    foreach($ddobjectlink as $index => $subobjectlink) {
        $template = $subobject->name;
        if(!empty($subobjectlink['template']))
            $template = $subobjectlink['template'];
    }
    // Return the template variables defined in this function
    return xarTplModule('subitems','admin','item_delete',$data,$template);
}

?>
