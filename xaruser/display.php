<?php
/**
 * Otp Module
 *
 * @package modules
 * @subpackage otp
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2017 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Display an item of the otp object
 *
 */
sys::import('modules.dynamicdata.class.objects.master');

function otp_user_display()
{
    if (!xarSecurity::check('ReadOtp')) return;
    xarTpl::setPageTitle('Display Otp');

    if (!xarVar::fetch('name',       'str',    $name,            'otp_otp', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('itemid' ,    'int',    $data['itemid'] , 0 ,          xarVar::NOT_REQUIRED)) return;

    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    $data['tplmodule'] = 'otp';

    return $data;
}
?>