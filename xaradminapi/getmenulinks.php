<?php
/**
 * Return the options for the admin menu
 *
 */

function xarayatesting_adminapi_getmenulinks()
{
    return xarMod::apiFunc('base', 'admin', 'menuarray', ['module' => 'xarayatesting']);
}
