<?php
/**
 * Display an item of the foo object
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function foo_user_display()
    {
        if (!xarSecurityCheck('ReadFoo')) return;

        if (!xarVarFetch('name',       'str',    $name,            'foo_foo', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('itemid' ,    'int',    $data['itemid'] , 0 ,          XARVAR_NOT_REQUIRED)) return;

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['object']->getItem(array('itemid' => $data['itemid']));

        $data['tplmodule'] = 'foo';
        $data['authid'] = xarSecGenAuthKey('foo');

        return $data;
    }
?>