<?php

/**
 * add new item
 */
function subitems_admin_item_new($args)
{
    extract($args);

    if(!xarVarFetch('objectid','int:',$objectid)) return;
    if(!xarVarFetch('itemid','int:',$itemid)) return;
    if(!xarVarFetch('redirect','str:1',$redirect,xarServerGetVar('HTTP_REFERER'),XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('create','str:1',$create,'',XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('confirm','str:1',$confirm,'',XARVAR_NOT_REQUIRED)) return;

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object =& xarModAPIFunc('dynamicdata','user','getobject',
                             array('objectid' => $objectid));
    if (!isset($object)) return;

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if(!xarSecurityCheck('AddDynamicDataItem',1,'Item',$object->moduleid.':'.$object->itemtype.':All')) return;

    if($confirm)    {
        // check the authorisation key
        if (!xarSecConfirmAuthKey()) return; // throw back

        // check the input values for this object
        $isvalid = $object->checkInput();

        if($create && $isvalid)   {
            // create the item here
            $ddid = $object->createItem();
            if (empty($ddid)) return; // throw back

            // connect ids -> write db
            if(!xarModAPIFunc('subitems','admin','dditem_attach',
                              array('itemid' => $itemid,
                                    'ddid' => $ddid,
                                    'objectid' => $objectid))) return;

            // back to the caller module
            xarResponseRedirect($redirect);
            return true;
        }
    }

    $data['preview'] = $confirm;
    $data['object'] = $object;
    $data['redirect'] = xarVarPrepHTMLDisplay($redirect);
    $data['itemid'] = $itemid;
    $data['objectid'] = $objectid;

    // Return the template variables defined in this function
    return $data;
}

?>
