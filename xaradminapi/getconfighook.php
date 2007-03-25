<?php
function vendors_adminapi_getconfighook($args)
{
    extract($args);
    if (!isset($extrainfo['tabs'])) $extrainfo['tabs'] = array();
    $module = 'vendors';
    $tabinfo = array(
            'module'  => $module,
            'configarea'  => 'general',
            'configtitle'  => xarML('Vendors'),
            'configcontent' => xarModFunc($module,'admin','modifyconfig_general'
            )
    );
    $extrainfo['tabs'][] = $tabinfo;
    return $extrainfo;
}
?>