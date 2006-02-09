<?php
/**
 * Subitems module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Subitems Module
 * @link http://xaraya.com/index.php/release/9356.html
 * @author Subitems Module Development Team
 */
/**
 * modify an item
 */
function subitems_admin_item_modify($args)
{
    extract($args);

    // The subobject we're editting an item ofr
    if(!xarVarFetch('objectid','int:',$subobjectid)) return;

    // The item id within the subobject
    if(!xarVarFetch('ddid','int:',$ddid)) return;

    if(!xarVarFetch('redirect','str:1',$redirect,xarServerGetVar('HTTP_REFERER'),XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('create','str:1',$create,'',XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('confirm','str:1',$confirm,'',XARVAR_NOT_REQUIRED)) return;

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $subobject = xarModAPIFunc('dynamicdata','user','getobject',
                             array('objectid' => $subobjectid,
                                   'itemid' => $ddid));
    if (!isset($subobject)) return;

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if(!xarSecurityCheck('EditDynamicDataItem',1,'Item',$subobject->moduleid.':'.$subobject->itemtype.':'.$ddid)) return;

    // get the values for this item
    $newid = $subobject->getItem();
    if (!isset($newid) || $newid != $ddid) return;

    if($confirm)    {
        // check the authorisation key
        if (!xarSecConfirmAuthKey()) return; // throw back

        // check the input values for this object
        $isvalid = $subobject->checkInput();

        if($create && $isvalid)   {
            // create the item here
            $ddid = $subobject->updateItem();
            if (empty($ddid)) return; // throw back

            // back to the caller module
            xarResponseRedirect($redirect);
            return true;
        }
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
    return xarTplModule('subitems','admin','item_modify',$data,$template);
}

?>
