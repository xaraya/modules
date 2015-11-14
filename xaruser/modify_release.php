<?php
/**
 * Modify a release
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Modify a release by user
 *
 * @param $rid
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_user_modify_release($args)
{
    if (!xarSecurityCheck('EditRelease')) return;

    if (!xarVarFetch('name',       'str',      $name,            'release_notes', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid' ,    'int',      $data['itemid'] , 0 ,          XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'bool',     $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    $data['tplmodule'] = 'release';

    if ($data['confirm']) {
    
        // Check for a valid confirmation key
        if(!xarSecConfirmAuthKey()) return;

        // Get the data from the form
        $isvalid = $data['object']->checkInput();
        
        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTplModule('release','user','modify_release', $data);        
        } else {
            // Good data: create the item
            $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
            
            // Jump to the next page
            xarController::redirect(xarModURL('release','user','view_releases'));
            return true;
        }
    }
    return $data;
}
?>