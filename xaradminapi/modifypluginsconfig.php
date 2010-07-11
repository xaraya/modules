<?php

/**
 * Modify configuration for ckeditor plugins
 * @package ckeditor
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ckeditor 
 *  To be used whenever plugin configurations need to be modified by the ckeditor module
 */

function ckeditor_adminapi_modifypluginsconfig($args)
{
    extract($args);

    $pluginsConfigFile = sys::code() . 'modules/ckeditor/xartemplates/includes/ckeditor/plugins/pgrfilemanager/config.plugins.php';
    $config_php = join('', file($pluginsConfigFile));

    $config_php = preg_replace('/\[\''.$name.'\'\]\s*=\s*(\'|\")(.*)\\1;/', "['".$name."'] = '$value';", $config_php);

    $fp = fopen ($pluginsConfigFile, 'wb');
    fwrite ($fp, $config_php);
    fclose ($fp);

    return true;
}


?>