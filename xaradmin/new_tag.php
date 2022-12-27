<?php
/**
 * Karma Module
 *
 * @package modules
 * @subpackage karma
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Create a new item of the tags object
 *
 */

function karma_admin_new_tag()
{
    if (!xarSecurity::check('AddKarma')) {
        return;
    }

    if (!xarVar::fetch('name', 'str', $name, 'karma_tags', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'bool', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => $name]);
    $data['tplmodule'] = 'karma';
    $data['authid'] = xarSec::genAuthKey('karma');

    if ($data['confirm']) {
        // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
        if (!xarVar::fetch('preview', 'str', $preview, null, xarVar::DONT_SET)) {
            return;
        }

        // Check for a valid confirmation key
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        // Get the data from the form
        $isvalid = $data['object']->checkInput();

        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTpl::module('karma', 'admin', 'new_tag', $data);
        } else {
            // Good data: create the item
            $itemid = xarMod::apiFunc('karma', 'admin', 'new_tag', );

            // Jump to the next page
            xarController::redirect(xarController::URL('karma', 'admin', 'view_tags'));
            return true;
        }
    }
    return $data;
}
