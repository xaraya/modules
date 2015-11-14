<?php
/**
 * Delete a release
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Delete a release
 * 
 * @param $rid ID
 * 
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_user_delete_release($args)
{
    if (!xarSecurityCheck('ManageRelease')) return;

    if (!xarVarFetch('name',       'str:1',     $name,            'release_notes',     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid' ,    'int',       $data['itemid'] , '' ,          XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'checkbox',  $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    $data['tplmodule'] = 'release';
    $data['authid'] = xarSecGenAuthKey('release');

    if ($data['confirm']) {
    
        // Check for a valid confirmation key
        if(!xarSecConfirmAuthKey()) return;

        // Delete the item
        $item = $data['object']->deleteItem();
            
        // Jump to the next page
        xarController::redirect(xarModURL('release','user','view_releases'));
        return true;
    }
    return $data;
}
?>