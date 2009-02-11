<?php
function publications_adminapi_getconfighook($args)
{
    extract($args);
    if (!isset($extrainfo['tabs'])) $extrainfo['tabs'] = array();
    $module = 'publications';
    $tabinfo = array(
            'module'  => $module,
            'configarea'  => 'general',
            'configtitle'  => xarML('Publications'),
            'configcontent' => xarModFunc($module,'admin','modifyconfig_general'
            )
    );
    $extrainfo['tabs'][] = $tabinfo;
    return $extrainfo;
}
?>