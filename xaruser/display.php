<?php
/**
 * Cacher Module
 *
 * @package modules
 * @subpackage cacher
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2018 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Display an item of the cacher object
 *
 */
sys::import('modules.dynamicdata.class.objects.master');

function cacher_user_display()
{
    if (!xarSecurityCheck('ReadCacher')) return;
    xarTpl::setPageTitle('Display Cacher');

    if (!xarVarFetch('name',       'str',    $name,            'cacher_cacher', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid' ,    'int',    $data['itemid'] , 0 ,          XARVAR_NOT_REQUIRED)) return;

    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    $data['tplmodule'] = 'cacher';

    return $data;
}
?>