<?php

/**
 * add new item
 */
function subitems_admin_item_delete($args)
{
    extract($args);

    if(!xarVarFetch('objectid','int:',$objectid)) return;
    if(!xarVarFetch('ddid','int:',$ddid)) return;
    if(!xarVarFetch('redirect','str:1',$redirect,xarServerGetVar('HTTP_REFERER'),XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('delete','str:1',$delete,'',XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('confirm','str:1',$confirm,'',XARVAR_NOT_REQUIRED)) return;

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object =& xarModAPIFunc('dynamicdata','user','getobject',
                             array('objectid' => $objectid,
                                   'itemid' => $ddid));
    if (!isset($object)) return;

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if(!xarSecurityCheck('DeleteDynamicDataItem',1,'Item',$object->moduleid.':'.$object->itemtype.':'.$ddid)) return;

    // get the values for this item
    $newid = $object->getItem();
    if (!isset($newid) || $newid != $ddid) return;

    if ($confirm) {
        // check the authorisation key
        if (!xarSecConfirmAuthKey()) return; // throw back

        if ($delete) {
            // delete the item here
            $ddid = $object->deleteItem();
            if (empty($ddid)) return; // throw back

            // detach ids -> write db
            if(!xarModAPIFunc('subitems','admin','dditem_detach',
                              array('ddid' => $ddid,
                                    'objectid' => $objectid))) return;
        } else {       // cancel
            // do nothing but return
        }

        // back to the caller module
        xarResponseRedirect($redirect);
        return true;
    }

    $data['preview'] = "1";
    $data['object'] = $object;
    $data['redirect'] = xarVarPrepHTMLDisplay($redirect);
    $data['ddid'] = $ddid;
    $data['objectid'] = $objectid;

    // Return the template variables defined in this function
    return $data;
}

?>
