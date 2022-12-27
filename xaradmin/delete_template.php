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
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Delete a biller template
 *
 */

function pubsub_admin_delete_template()
{
    if (!xarSecurity::check('ManageBiller')) {
        return;
    }

    if (!xarVar::fetch('name', 'str:1', $name, 'pubsub_templates', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'int', $data['itemid'], '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'str:1', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => $name]);
    $data['object']->getItem(['itemid' => $data['itemid']]);

    $data['tplmodule'] = 'pubsub';
    $data['authid'] = xarSec::genAuthKey('pubsub');

    if ($data['confirm']) {
        // Check for a valid confirmation key
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        // Delete the item
        $item = $data['object']->deleteItem();

        // Jump to the next page
        xarController::redirect(xarController::URL('pubsub', 'admin', 'view_templates'));
        return true;
    }
    return $data;
}
