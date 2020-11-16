<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Modify an item of the pubsub_events object
 *
 */
sys::import('modules.dynamicdata.class.objects.master');

function pubsub_admin_modify_event()
{
    // Xaraya security
    if (!xarSecurityCheck('ManagePubSub')) {
        return;
    }
    xarTpl::setPageTitle('Modify Event');
    
    if (!xarVarFetch('name', 'str', $name, 'pubsub_events', XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('itemid', 'int', $data['itemid'], 0, XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('confirm', 'bool', $data['confirm'], false, XARVAR_NOT_REQUIRED)) {
        return;
    }

    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    $data['tplmodule'] = 'pubsub';
    $data['authid'] = xarSecGenAuthKey('pubsub');

    if ($data['confirm']) {
    
        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return;
        }

        // Get the data from the form
        $isvalid = $data['object']->checkInput();
        
        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTpl::module('pubsub', 'admin', 'modify_event', $data);
        } else {
            // Good data: create the item
            $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
            
            // Jump to the next page
            xarController::redirect(xarModURL('pubsub', 'admin', 'view_events'));
            return true;
        }
    }
    return $data;
}
