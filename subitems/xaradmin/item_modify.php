<?php

/**
 * add new item
 */
function subitems_admin_item_modify($args)
{
	extract($args);

	if(!xarVarFetch('objectid','int:',$objectid)) return;
    if(!xarVarFetch('ddid','int:',$ddid)) return;
    if(!xarVarFetch('redirect','str:1',$redirect,xarServerGetVar('HTTP_REFERER'),XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('create','str:1',$create,'',XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('confirm','str:1',$confirm,'',XARVAR_NOT_REQUIRED)) return;

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object =& xarModAPIFunc('dynamicdata','user','getobject',
                             array('objectid' => $objectid));
    if (!isset($object)) return;

    $object =& xarModAPIFunc('dynamicdata',
                   'user',
                   'getitem',
                   array(
                         'modid' => $object->moduleid,
                         'itemtype' => $object->itemtype,
                         'itemid' => $ddid,
                         'getobject' => true
                         ));
    if (!isset($object)) return;

    if($confirm)	{
        // check the authorisation key
	    if (!xarSecConfirmAuthKey()) return; // throw back

	    // check the input values for this object
	    $isvalid = $object->checkInput();

	    if($create && $isvalid)   {
               // create the item here
	            $ddid = $object->updateItem();
	            if (empty($ddid)) return; // throw back

               // back to the caller module
               xarResponseRedirect($redirect);

               return true;
	    }

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