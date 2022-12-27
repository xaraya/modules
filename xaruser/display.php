<?php
/**
 * EAV Module
 *
 * @package modules
 * @subpackage eav
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2013 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Display an item of the eav object
 *
 */
sys::import('modules.dynamicdata.class.objects.master');

function eav_user_display()
{
    if (!xarSecurity::check('ReadEAV')) {
        return;
    }

    if (!xarVar::fetch('name', 'str', $name, 'eav_eav', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'int', $data['itemid'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['object'] = DataObjectMaster::getObject(['name' => $name]);
    $data['object']->getItem(['itemid' => $data['itemid']]);

    $data['tplmodule'] = 'eav';
    $data['authid'] = xarSec::genAuthKey('eav');

    return $data;
}
