<?php
/**
 * Handle getconfig hook calls
 *
 */

    function karma_adminapi_getconfighook($args)
    {
        extract($args);
        if (!isset($extrainfo['tabs'])) $extrainfo['tabs'] = array();
        $module = 'karma';
        $tabinfo = array(
                'module'  => $module,
                'configarea'  => 'general',
                'configtitle'  => xarML('Karma'),
                'configcontent' => ''
        );
        $extrainfo['tabs'][] = $tabinfo;
        return $extrainfo;
    }
?>