<?php
/**
 * Classes to manage the output & variable cache system of Xaraya
 *
 * @package modules\xarcachemanager
 * @subpackage xarcachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182.html
 *
 * @author mikespub <mikespub@xaraya.com>
**/

class xarCache_Manager extends xarObject
{
    public static function init(array $args = [])
    {
    }

    /**
     * Gets caching configuration settings in the config file or modVars
     *
     * @author jsb <jsb@xaraya.com>
     * @access public
     * @param string $args['from'] source of configuration to get - file or db
     * @param array $args['keys'] array of config labels and values
     * @param boolean $args['tpl_prep'] prep the config for use in templates
     * @param boolean $args['viahook'] config value requested as part of a hook call
     * @return array of caching configuration settings
     * @throws MODULE_FILE_NOT_EXIST
     */
    public static function get_config($args)
    {
        extract($args);

        if (!isset($viahook)) {
            $viahook = false;
        }
        if (!$viahook) {
            if (!xarSecurity::check('AdminXarCache')) {
                return;
            }
        }
        if (!isset($from)) {
            $from = 'file';
        }
        if (!isset($tpl_prep)) {
            $tpl_prep = false;
        }

        // Make sure the caching configuration array is initialized
        // so we don't run into possible errors later.
        $cachingConfiguration = [];

        switch ($from) {

            case 'db':

                //get the modvars from the db
                if (!empty($keys)) {
                    foreach ($keys as $key) {
                        $value = xarModVars::get('xarcachemanager', $key);
                        if (substr($value, 0, 6) == 'array-') {
                            $value = substr($value, 6);
                            $value = unserialize($value);
                        }
                        if (is_numeric($value)) {
                            $value = intval($value);
                        }
                        $cachingConfiguration[$key] = $value;
                    }
                } else {
                    $modBaseInfo = xarMod::getBaseInfo('xarcachemanager');
                    //if (!isset($modBaseInfo)) return; // throw back

                    $dbconn = xarDB::getConn();
                    $tables = xarDB::getTables();

                    // Takes the right table basing on module mode
                    if ($modBaseInfo['mode'] == XARMOD_MODE_SHARED) {
                        $module_varstable = $tables['system/module_vars'];
                        $module_uservarstable = $tables['system/module_uservars'];
                    } elseif ($modBaseInfo['mode'] == XARMOD_MODE_PER_SITE) {
                        $module_varstable = $tables['site/module_vars'];
                        $module_uservarstable = $tables['site/module_uservars'];
                    }

                    $sql="SELECT $module_varstable.xar_name, $module_varstable.xar_value FROM $module_varstable WHERE $module_varstable.xar_modid = ?";
                    $result =& $dbconn->Execute($sql, [$modBaseInfo['systemid']]);
                    if (!$result) {
                        return;
                    }

                    while (!$result->EOF) {
                        [$name, $value] = $result->fields;
                        $result->MoveNext();
                        if (substr($value, 0, 6) == 'array-') {
                            $value = substr($value, 6);
                            $value = unserialize($value);
                        }
                        $cachingConfiguration[$name] = $value;
                    }
                }

                break;

            default:

                if (!isset($cachingConfigFile)) {
                    $cachingConfigFile = sys::varpath() . '/cache/config.caching.php';
                }

                if (!file_exists($cachingConfigFile)) {
                    // try to restore the missing file
                    if (!self::restore_config()) {
                        $msg=xarMLS::translate('The #(1) file is missing.  Please restore #(1)
                                    from backup, or the xarcachemanager/config.caching.php.dist
                                    file.', $cachingConfigFile);
                        throw new Exception($msg);
                        return false;
                    }
                }

                include $cachingConfigFile;

                // if we only want specific keys, reduce the array
                if (!empty($keys)) {
                    foreach ($keys as $key) {
                        $filteredConfig[$key] = $cachingConfiguration[$key];
                    }
                    $cachingConfiguration = $filteredConfig;
                }
        }

        if ($tpl_prep) {
            $settings = self::config_tpl_prep(
                $cachingConfiguration
            );
        } else {
            $settings = $cachingConfiguration;
        }

        return $settings;
    }

    /**
     * Save configuration settings in the config file and modVars
     *
     * @author jsb <jsb@xaraya.com>
     * @access public
     * @param $args['config'] array of config labels and values
     * @throws FUNCTION_FAILED
     */
    public static function save_config($args)
    {
        extract($args);

        if (!xarSecurity::check('AdminXarCache')) {
            return;
        }
        if (empty($configSettings) || !is_array($configSettings)) {
            return false;
        }

        if (!isset($cachingConfigFile)) {
            $cachingConfigFile = sys::varpath() . '/cache/config.caching.php';
        }

        if (!is_writable($cachingConfigFile)) {
            $msg=xarMLS::translate('The caching configuration file is not writable by the web server.
                   #(1) must be writable by the web server for
                   the output caching to be managed by xarCacheManager.', $cachingConfigFile);
            throw new Exception($msg);
            return false;
        }

        $cachingConfig = join('', file($cachingConfigFile));

        // place the settings in modvars for safe keeping
        // and replace the cachingConfig with the new values
        foreach ($configSettings as $configKey => $configValue) {
            if (is_numeric($configValue)) {
                xarModVars::set('xarcachemanager', $configKey, $configValue);
                $cachingConfig = preg_replace('/\[\'' . $configKey . '\'\]\s*=\s*(|\")(.*)\\1;/', "['$configKey'] = $configValue;", $cachingConfig);
            } elseif (is_array($configValue)) {
                xarModVars::set('xarcachemanager', $configKey, 'array-' . serialize($configValue));
                $configValue = str_replace("'", "\\'", $configValue);
                if (!empty($configValue)) {
                    $keyslist = array_keys($configValue);
                    // support basic associative array too
                    if (!is_numeric($keyslist[0])) {
                        $keyValue = [];
                        foreach ($keyslist as $key) {
                            $keyValue[] = $key . "' => '" . $configValue[$key];
                        }
                        $configValue = "'" . implode("','", $keyValue) . "'";
                    } else {
                        $configValue = "'" . implode("','", $configValue) . "'";
                    }
                } else {
                    $configValue = "'" . implode("','", $configValue) . "'";
                }
                $cachingConfig = preg_replace('/\[\'' . $configKey . '\'\]\s*=\s*array\s*\((.*)\)\s*;/i', "['$configKey'] = array($configValue);", $cachingConfig);
            } else {
                xarModVars::set('xarcachemanager', $configKey, $configValue);
                $configValue = str_replace("'", "\\'", $configValue);
                $cachingConfig = preg_replace('/\[\'' . $configKey . '\'\]\s*=\s*(\'|\")(.*)\\1;/', "['$configKey'] = '$configValue';", $cachingConfig);
            }
        }

        // write the new settings to the config file
        $fp = fopen($cachingConfigFile, 'wb');
        fwrite($fp, $cachingConfig);
        fclose($fp);
    }

    /**
     * Restore the caching configuration file
     *
     * @author jsb <jsb@xaraya.com>
     * @access public
     * @throws FUNCTION_FAILED
     * @return boolean
     */
    public static function restore_config()
    {
        $varCacheDir = sys::varpath() . '/cache';
        $defaultConfigFile = sys::code() . 'modules/xarcachemanager/config.caching.php.dist';
        $cachingConfigFile = $varCacheDir . '/config.caching.php';

        $configSettings = self::get_config(
            ['from' => 'db',
                                          'cachingConfigFile' => $cachingConfigFile, ]
        );

        // Confirm the cache directory is writable
        if (!is_writable($varCacheDir)) {
            $msg=xarMLS::translate('The #(1) directory is not writable by the web
                   web server. The #(1) directory must be writable by the web
                   server process owner for output caching to work.
                   Please change the permission on the #(1) directory
                   so that the web server can write to it.', $varCacheDir);
            throw new Exception($msg);
            return false;
        }

        // Confirm the config file is writable
        if (file_exists($cachingConfigFile) && !is_writable($cachingConfigFile)) {
            $msg=xarMLS::translate('The #(1) file is not writable by the web
                   web server. The #(1) file must be writable by the web
                   server process owner for output caching to be configured
                   via the xarCacheManager module.
                   Please change the permission on the #(1) file
                   so that the web server can write to it.', $cachingConfigFile);
            throw new Exception($msg);
            return false;
        }

        if (file_exists($cachingConfigFile)) {
            @unlink($cachingConfigFile);
        }
        copy($defaultConfigFile, $cachingConfigFile);
        self::save_config(
            ['configSettings' => $configSettings]
        );

        return true;
    }

    /**
     * Save configuration settings in the config file and modVars
     *
     * @author jsb <jsb@xaraya.com>
     * @access public
     * @param array $cachingConfiguration cachingConfiguration to be prep for a template
     * @return array of cachingConfiguration with '.' removed from keys or void
     */
    public static function config_tpl_prep($cachingConfiguration)
    {
        if (empty($cachingConfiguration) || !is_array($cachingConfiguration)) {
            return;
        }

        $keyslist = str_replace('.', '', array_keys($cachingConfiguration));
        $valueslist = array_values($cachingConfiguration);
        $settings = [];

        $arraysize = sizeof($keyslist);
        for ($i=0;$i<$arraysize;$i++) {
            $settings[$keyslist[$i]] = $valueslist[$i];
        }

        return $settings;
    }

    /**
     * Update the configuration parameters of the module based on data from the modification form
     *
     * @author Jon Haworth
     * @author jsb <jsb@xaraya.com>
     * @access public
     * @param string $args['starttime'] (seconds or hh:mm:ss)
     * @param string $args['direction'] (from or to)
     * @return string $convertedtime (hh:mm:ss or seconds)
     * @throws nothing
     * @todo maybe add support for days?
     */
    public static function convertseconds($args)
    {
        extract($args);

        $convertedtime = '';

        // if the value is set to zero, we can leave it that way
        if ($starttime === 0) {
            return $starttime;
        }

        switch ($direction) {
            case 'from':
                // convert to hours
                $hours = intval(intval($starttime) / 3600);
                // add leading 0
                $convertedtime .= str_pad($hours, 2, '0', STR_PAD_LEFT). ':';
                // get the minutes
                $minutes = intval(($starttime / 60) % 60);
                // then add to $hms (with a leading 0 if needed)
                $convertedtime .= str_pad($minutes, 2, '0', STR_PAD_LEFT). ':';
                // get the seconds
                $seconds = intval($starttime % 60);
                // add to $hms, again with a leading 0 if needed
                $convertedtime .= str_pad($seconds, 2, '0', STR_PAD_LEFT);
                break;
            case 'to':
                // break apart the time elements
                $elements = explode(':', $starttime);
                // make sure it's all there
                $allelements = array_pad($elements, -3, 0);
                // calculate the total seconds
                $convertedtime = (($allelements[0] * 3600) + ($allelements[1] * 60) + $allelements[2]);
                // make sure we're sending back an integer
                settype($convertedtime, 'integer');
                break;
        }

        return $convertedtime;
    }
}
