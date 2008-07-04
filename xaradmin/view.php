<?php
/**
 * View items of the karma object
 *
 */
    function karma_admin_view($args)
    {
        if (!xarSecurityCheck('EditKarma')) return;

        $data['object'] = xarModApiFunc('dynamicdata','user','getobjectlist', array('name' => 'karma'));
        $data['object']->getItems();
        return $data;
    }
?>