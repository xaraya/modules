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

    // Get the object we need
    $listobject = DataObjectMaster::getObjectList(array('name' => xarModVars::get('registration', 'reviewobject')));
    $where ='id eq '. xarSession::getVar('role_id');
    $responses = $listobject->getItems(
                             array('where'    => $where,
                                   ));
                                   
    // Check if thsi is allowed
    if (!xarUserIsLoggedIn() || !xarModVars::get('registration','allowreview') || count($responses) == 0) {
        xarResponseRedirect(xarModURL('roles', 'user', 'account'));
        return true;
    }

    // Set a return url
    $modulename = 'registration';
    xarSession::setVar('ddcontext.' . $modulename, array('return_url' => xarServerGetCurrentURL()));

    xarTplSetPageTitle(xarML('Review Profile'));

    // Get the object we need
    $data['object'] = DataObjectMaster::getObject(array('name' => xarModVars::get('registration', 'reviewobject')));
    $item = current($responses);
    $item = $data['object']->getItem(array('itemid' => $item['id']));

    $data['authid'] = xarSecGenAuthKey('dynamicdata');
    $data['return_url'] = xarServerGetCurrentURL();
    return $data;
}
?>