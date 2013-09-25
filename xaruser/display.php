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
        if (!xarSecurityCheck('ReadEAV')) return;

        if (!xarVarFetch('name',       'str',    $name,            'eav_eav', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('itemid' ,    'int',    $data['itemid'] , 0 ,          XARVAR_NOT_REQUIRED)) return;

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['object']->getItem(array('itemid' => $data['itemid']));

        $data['tplmodule'] = 'eav';
        $data['authid'] = xarSecGenAuthKey('eav');

        return $data;
    }
?>