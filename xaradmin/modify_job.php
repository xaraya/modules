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
 * Modify a job in the process queue
 *
 */
sys::import('modules.dynamicdata.class.objects.master');

function pubsub_admin_modify_job()
{
    // Xaraya security
    if (!xarSecurity::check('ManagePubSub')) {
        return;
    }
    xarTpl::setPageTitle('Modify Job');

    if (!xarVar::fetch('name', 'str', $name, 'pubsub_process', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'int', $data['itemid'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'bool', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['object'] = DataObjectMaster::getObject(['name' => $name]);
    $data['object']->getItem(['itemid' => $data['itemid']]);

    $data['tplmodule'] = 'pubsub';
    $data['authid'] = xarSec::genAuthKey('pubsub');

    if ($data['confirm']) {
        // Check for a valid confirmation key
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        // Get the data from the form
        $isvalid = $data['object']->checkInput();

        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTpl::module('pubsub', 'admin', 'modify_process', $data);
        } else {
            // Good data: create the item
            $itemid = $data['object']->updateItem(['itemid' => $data['itemid']]);

            // Jump to the next page
            xarController::redirect(xarController::URL('pubsub', 'admin', 'view_queue'));
            return true;
        }
    }
    return $data;
}
