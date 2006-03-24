<?php
/*
 * Get caching config settings
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
/**
 * Gets caching configuration settings in the config file or modVars
 * 
 * @author jsb <jsb@xaraya.com>
 * @access public 
 * @param string $args['from'] source of configuration to get - file or db 
 * @param array $args['keys'] array of config labels and values
 * @param boolean $args['tpl_prep'] prep the config for use in templates
 * @param boolean $args['viahook'] config value requested as part of a hook call
 * @returns array
 * @returns array of caching configuration settings
 * @throws MODULE_FILE_NOT_EXIST
 */
function xarcachemanager_adminapi_get_cachingconfig($args)
{
    extract($args);
    
    if (!isset($viahook)) {
        $viahook = FALSE;
    }
    if (!$viahook) {
        if (!xarSecurityCheck('AdminXarCache')) { return; }
    }
    if (!isset($from)) {
        $from = 'file';
    }
    if (!isset($tpl_prep)) {
       $tpl_prep = FALSE;
    }

    // Make sure the caching configuration array is initialized 
    // so we don't run into possible errors later.
    $cachingConfiguration = array();

    switch ($from) {

    case 'db':

        //get the modvars from the db
        if (!empty($keys)) {

            foreach ($keys as $key) {
                $value = xarModGetVar('xarcachemanager', $key);
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
        
            $modBaseInfo = xarMod_getBaseInfo('xarcachemanager');
            //if (!isset($modBaseInfo)) return; // throw back
        
            $dbconn =& xarDBGetConn();
            $tables =& xarDBGetTables();
        
            // Takes the right table basing on module mode
            if ($modBaseInfo['mode'] == XARMOD_MODE_SHARED) {
                $module_varstable = $tables['system/module_vars'];
                $module_uservarstable = $tables['system/module_uservars'];
            } elseif ($modBaseInfo['mode'] == XARMOD_MODE_PER_SITE) {
                $module_varstable = $tables['site/module_vars'];
                $module_uservarstable = $tables['site/module_uservars'];
            }
            
            $sql="SELECT $module_varstable.xar_name, $module_varstable.xar_value FROM $module_varstable WHERE $module_varstable.xar_modid = ?";
            $result =& $dbconn->Execute($sql,array($modBaseInfo['systemid']));
            if(!$result) { return; }
            
            while (!$result->EOF) {
                list($name, $value) = $result->fields;
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
             $cachingConfigFile = xarCoreGetVarDirPath() . '/cache/config.caching.php';
        }
    
        if (!file_exists($cachingConfigFile)) {
            // try to restore the missing file
            if (!xarModAPIFunc('xarcachemanager', 'admin', 'restore_cachingconfig')) {
                $msg=xarML('The #(1) file is missing.  Please restore #(1)   
                            from backup, or the xarcachemanager/config.caching.php.dist 
                            file.', $cachingConfigFile);
                xarErrorSet(XAR_SYSTEM_EXCEPTION,'MODULE_FILE_NOT_EXIST',
                                new SystemException($msg));
                    
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
        $settings = xarModAPIFunc('xarcachemanager', 'admin', 'config_tpl_prep',
                                  $cachingConfiguration);
    } else {
        $settings = $cachingConfiguration;
    }

    return $settings;
}

?>
