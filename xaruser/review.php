<?php
/**
 * @package modules
 * @copyright (C) 2008 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage registration
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
 */
sys::import('modules.dynamicdata.class.objects.master');

function registration_user_review()
{
    if (!xarSecurityCheck('ReadRegistration')) return;

    // Check if thsi is allowed
    if (!xarUserIsLoggedIn() || !xarModVars::get('registration','allowreview')) {
        xarResponseRedirect(xarModURL('roles', 'user', 'account'));
        return true;
    }

    // Set a return url
    $modulename = 'registration';
    xarSession::setVar('ddcontext.' . $modulename, array('return_url' => xarServerGetCurrentURL()));

    xarTplSetPageTitle(xarML('Review Profile'));
    if (!xarVarFetch('phase','str:1:100',$phase,'request',XARVAR_NOT_REQUIRED)) return;

    // Get the object we need
    $data['object'] = DataObjectMaster::getObject(array('name' => xarModVars::get('registration', 'reviewobject')));
    $item = $data['object']->getItem(array('itemid' => xarSession::getVar('role_id')));
    $data['authid'] = xarSecGenAuthKey('dynamicdata');

    return $data;
}
?>