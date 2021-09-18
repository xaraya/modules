<?php
/**
 * Handle getconfig hook calls
 *
 */

    function ckeditor_adminapi_getconfighook($args)
    {
        extract($args);
        if (!isset($extrainfo['tabs'])) {
            $extrainfo['tabs'] = [];
        }
        $module = 'ckeditor';
        $tabinfo = [
                'module'  => $module,
                'configarea'  => 'general',
                'configtitle'  => xarML('CKEditor'),
                'configcontent' => '',
        ];
        $extrainfo['tabs'][] = $tabinfo;
        return $extrainfo;
    }
