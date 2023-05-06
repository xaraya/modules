<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Logconfig Module
 * @link http://xaraya.com/index.php/release/6969.html
 * @author Logconfig module development team
 */
/**
 * initialise the logconfig module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @return bool true on success
 */
function logconfig_init()
{
# --------------------------------------------------------
#
# Create DD objects
#
    PropertyRegistration::importPropertyTypes(false,array('modules/logconfig/xarproperties'));

    $module = 'logconfig';
    $objects = array(
                     'logconfig_errorlog',
                     'logconfig_html',
                     'logconfig_javascript',
                     'logconfig_mail',
                     'logconfig_mozilla',
                     'logconfig_simple',
                     'logconfig_sql',
                     'logconfig_syslog',
                     );
    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

    xarMasks::register('ManageLogConfig','All','logconfig','Item','All','ACCESS_DELETE');
    xarMasks::register('AdminLogConfig','All','logconfig','Item','All','ACCESS_ADMIN');

    // Initialisation successful
    return logconfig_upgrade('0.1.1');
}

/**
 * upgrade the logconfig module from an old version
 * This function can be called multiple times
 * @param string oldversion
 * @return bool true on success
 */
function logconfig_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.1.0':
            $logConfigFile = sys::varpath() . '/cache/config.log.php';
            if (file_exists($logConfigFile)) unlink($logConfigFile);
            //When people turn it on again it will produce the config in the
            //new directory, no need to do it in here.
        case '0.1.1':
            break;
    }

    // Update successful
    return true;
}

/**
 * delete the logconfig module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @return bool true on success
 */
function logconfig_delete()
{
    $module = 'logconfig';
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $module));
}

?>