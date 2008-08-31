<?php
/**
 * Create a new item of the xarayatesting object
 *
 */
    function xarayatesting_admin_new()
    {
        if (!xarSecurityCheck('AddXarayatesting')) return;

        $data['object'] = xarModApiFunc('dynamicdata','user','getobjectlist', array('name' => 'xarayatesting'));
        $data['tplmodule'] = 'xarayatesting';
        return $data;
    }

?>