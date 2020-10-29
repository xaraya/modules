<?php
/**
 * modify the plugins config file
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com

 * @subpackage CKEditor Module
 * @link http://www.xaraya.com/index.php/release/eid/1166
 * @author Marc Lutolf <mfl@netspan.ch> and Ryan Walker <ryan@webcommunicate.net>
 */

function ckeditor_adminapi_modifypluginsconfig($args)
{
    extract($args);

    $pluginsConfigFile = sys::code() . 'modules/ckeditor/config.plugins.php';
    $config_php = join('', file($pluginsConfigFile));

    $config_php = preg_replace('/\[\''.$name.'\'\]\s*=\s*(\'|\")(.*)\\1;/', "['".$name."'] = '$value';", $config_php);

    $fp = fopen($pluginsConfigFile, 'wb');
    fwrite($fp, $config_php);
    fclose($fp);

    return true;
}
