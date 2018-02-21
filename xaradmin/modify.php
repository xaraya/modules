<?php
/**
 * Cacher Module
 *
 * @package modules
 * @subpackage cacher
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2014 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Modify an item of the cacher object
 *
 */
    
function cacher_admin_modify()
{
    if (!xarSecurityCheck('EditCacher')) return;

    if (!xarVarFetch('name',       'str',      $name,            'cacher_cacher', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid' ,    'int',      $data['itemid'] , 0 ,          XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'checkbox', $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    $data['tplmodule'] = 'cacher';
    $data['authid'] = xarSecGenAuthKey('cacher');

    if ($data['confirm']) {
    
        // Check for a valid confirmation key
        if(!xarSecConfirmAuthKey()) return;

        // Get the data from the form
        $isvalid = $data['object']->checkInput();
        
        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTplModule('cacher','admin','modify', $data);        
        } else {
            // Good data: create the item
            $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
            
            // Jump to the next page
            xarController::redirect(xarModURL('cacher','admin','view'));
            return true;
        }
    }
    return $data;
}
?>