<?php
/**
 * Add a new release
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
 * Add a release and request an ID
 *
 * @param enum phase Phase we are at
 *
 * @return array
 * @author Release module development team
 */
function release_admin_new_release($args)
{
    if (!xarSecurityCheck('AddRelease')) {
        return;
    }

    if (!xarVarFetch('name', 'str', $name, 'release_notes', XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('confirm', 'bool', $data['confirm'], false, XARVAR_NOT_REQUIRED)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['tplmodule'] = 'release';

    if ($data['confirm']) {
    
        // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
        if (!xarVarFetch('preview', 'str', $preview, null, XARVAR_DONT_SET)) {
            return;
        }

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return;
        }
        
        // Get the data from the form
        $isvalid = $data['object']->checkInput();
        
        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTplModule('release', 'admin', 'new_release', $data);
        } else {
            // Good data: create the item
            $itemid = $data['object']->createItem();
            
            // Jump to the next page
            xarController::redirect(xarModURL('release', 'admin', 'view_releases'));
            return true;
        }
    }
    return $data;
}
