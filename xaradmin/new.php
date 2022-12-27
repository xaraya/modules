<?php
/**
 * Create a new item of the xarayatesting object
 *
 */
function xarayatesting_admin_new()
{
    if (!xarSecurity::check('AddXarayatesting')) {
        return;
    }

    $data['object'] = xarMod::apiFunc('dynamicdata', 'user', 'getobjectlist', ['name' => 'xarayatesting']);
    $data['tplmodule'] = 'xarayatesting';
    return $data;
}
