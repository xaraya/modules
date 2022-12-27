<?php
/**
 * Release Module
 *
 * @package modules
 * @subpackage release
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2014 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Display an item of the release notes object
 *
 */
sys::import('modules.dynamicdata.class.objects.master');

function release_user_display_release()
{
    if (!xarSecurity::check('ReadRelease')) {
        return;
    }

    if (!xarVar::fetch('name', 'str', $name, 'release_notes', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'int', $data['itemid'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['object'] = DataObjectMaster::getObject(['name' => $name]);
    $data['object']->getItem(['itemid' => $data['itemid']]);

    $data['tplmodule'] = 'release';

    return $data;
}
