<?php
/**
 * Handle getconfig hook calls
 *
 */

    function ckeditor_adminapi_getconfighook($args)
    {
        extract($args);
        if (!isset($extrainfo['tabs'])) $extrainfo['tabs'] = array();
        $module = 'ckeditor';
        $tabinfo = array(
                'module'  => $module,
                'configarea'  => 'general',
                'configtitle'  => xarML('CKEditor'),
                'configcontent' => ''
        );
        $extrainfo['tabs'][] = $tabinfo;
        return $extrainfo;
    }
?>