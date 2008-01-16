<?php
/**
 * View items of the foo object
 *
 */
    function foo_admin_view($args)
    {
        if (!xarSecurityCheck('EditFoo')) return;

        $data['object'] = xarModApiFunc('dynamicdata','user','getobjectlist', array('name' => 'foo'));
        $data['object']->getItems();
        return $data;
    }
?>