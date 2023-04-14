<?php
/**
 * Reminders Module
 *
 * @package modules
 * @subpackage reminders
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Display an item of the reminders object
 *
 */
sys::import('modules.dynamicdata.class.objects.master');

function reminders_user_display()
{
    if (!xarSecurity::check('ReadReminders')) return;
    xarTpl::setPageTitle('Display Reminders');

    if (!xarVar::fetch('name',       'str',    $name,            'reminders_reminders', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('itemid' ,    'int',    $data['itemid'] , 0 ,          xarVar::NOT_REQUIRED)) return;

    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    $data['tplmodule'] = 'reminders';

    return $data;
}
?>