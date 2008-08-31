<?php
/**
 * Handle getconfig hook calls
 *
 */

    function xarayatesting_adminapi_getconfighook($args)
    {
        extract($args);
        if (!isset($extrainfo['tabs'])) $extrainfo['tabs'] = array();
        $module = 'xarayatesting';
        $tabinfo = array(
                'module'  => $module,
                'configarea'  => 'general',
                'configtitle'  => xarML('Xarayatesting'),
                'configcontent' => ''
        );
        $extrainfo['tabs'][] = $tabinfo;
        return $extrainfo;
    }
?>