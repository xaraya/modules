<?php
 /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage shop Module
 * @link http://www.xaraya.com/index.php/release/eid/1031
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  Delete an item
 */
function shop_admin_delete()
{
    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarFetch(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    if (!xarVarFetch('name',    'str',   $name, false,       XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid' ,     'int',    $data['itemid'] , '' ,          XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;

    // Show an error when the itemid is still not set
    if (empty($data['itemid'])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'delete', 'dyn_example');
        throw new Exception($msg);
    }

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing.  However,
    // in this case we had to wait until we could obtain the item name to
    // complete the instance information so this is the first chance we get to
    // do the check
    if (!xarSecurityCheck('Deleteshop',1,'Item',$data['itemid'])) return;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
    // Get the object we'll be working with
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));
    
    if ($data['confirm']) {

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

        $item = $data['object']->deleteItem();

        $viewfunc = str_replace('shop_','',$name);
        
        // Jump to the next page
        xarController::redirect(xarModURL('shop','admin',$viewfunc));
        return true;
    }
    return $data;
}

?>