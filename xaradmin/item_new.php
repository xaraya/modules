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
 * add new item
 */
function subitems_admin_item_new($args)
{
    extract($args);

    // The subobject we're adding an item for
    if(!xarVarFetch('objectid','int:',$subobjectid)) return;

    // The itemid of the parent to which we want to be linked
    if(!xarVarFetch('itemid','int:',$parentitemid)) return;

    if(!xarVarFetch('redirect','str:1',$redirect,xarServerGetVar('HTTP_REFERER'),XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('create','str:1',$create,'',XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('confirm','str:1',$confirm,'',XARVAR_NOT_REQUIRED)) return;

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $subobject = xarModAPIFunc('dynamicdata','user','getobject',
                             array('objectid' => $subobjectid));
    if (!isset($subobject)) return;


    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if(!xarSecurityCheck('AddDynamicDataItem',1,'Item',$subobject->moduleid.':'.$subobject->itemtype.':All')) return;

    if($confirm)    {
        // check the authorisation key
        if (!xarSecConfirmAuthKey()) return; // throw back

        // check the input values for this object
        $isvalid = $subobject->checkInput();

        if($create && $isvalid)   {
            // create the subitem here
            $ddid = $subobject->createItem();
            if (empty($ddid)) return; // throw back

            // connect ids -> write db
            if(!xarModAPIFunc('subitems','admin','dditem_attach',
                              array('itemid'   => $parentitemid,
                                    'ddid'     => $ddid,
                                    'objectid' => $subobjectid))) return;

            // back to the caller module
            xarResponseRedirect($redirect);
            return true;
        }
    }

    $data['preview'] = $confirm;
    $data['object'] = $subobject;
    $data['redirect'] = xarVarPrepHTMLDisplay($redirect);
    $data['itemid'] = $parentitemid;
    $data['objectid'] = $subobjectid;

    // get the subitems link for this object NOTE: nested subitems?
    // FIXME: suppose a nested subitem was defined, the data will not be
    // set for it then
    // FIXME: this is only for the template?
    $ddobjectlink = xarModAPIFunc('subitems','user','ddobjectlink_get',
                                  array('objectid' => $subobjectid));
    // nothing to see here
    if (empty($ddobjectlink)) return;

    foreach($ddobjectlink as $index => $subobjectlink) {
        // set the template if available
        $template = $subobject->name;
        if(!empty($subobjectlink['template']))
            $template = $subobjectlink['template'];
    }
    // Return the template variables defined in this function
    return xarTplModule('subitems','admin','item_new',$data,$template);
}

?>
