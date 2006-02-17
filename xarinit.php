<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
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
    // this module can't work without the dynamicdata module
    $testmod = xarModIsAvailable('dynamicdata');
    if (!isset($testmod)) return; // some other exception got in our way [locale for instance :)]

    if (!$testmod) {
        $msg = xarML('Please activate the Dynamic Data module first...');
        xarErrorSet(XAR_USER_EXCEPTION, 'MODULE_NOT_ACTIVE',
                        new DefaultUserException($msg));
        return;
    }

    /**
     * import the object definition and properties from some XML file (exported from DD)
     * FIXME: this SUCCEEDS, but the objects are not correct. The loglevel property will
     * only be available after *this* module is active, so the import makes this a 'static text' property
     * Q: it should fail?
     * Q: with a slight modification, we could use the dd api function importprops to import the property
     *    before importing.
     */
    // Make sure we import our property
    $mypropdirs = array('modules/logconfig/xarproperties/');
    $result = xarModApiFunc('dynamicdata','admin','importpropertytypes',array('dirs' => $mypropdirs));

    $ids = array();
    $dir = "modules/logconfig/loggers/";
    $itemsnum = 0;

    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (substr($file, -4) == '.xml')
                {
                    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => "modules/logconfig/loggers/$file"));
                    if (empty($objectid)) return;
                    $ids[] = $objectid;
                    $itemsnum++;
                }
            }
            closedir($dh);
        }
    }

    // save the object ids for later
    //TODO: Review if this is needed
    xarModSetVar('logconfig','objectids',serialize($ids));

    //This is used in admin/view
    xarModSetVar('logconfig','itemstypenumber',$itemsnum);

    xarRegisterMask('AdminLogConfig','All','logconfig','Item','All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * upgrade the example module from an old version
 * This function can be called multiple times
 * @param string oldversion
 * @return bool true on success
 */
function logconfig_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.1.0':
            $logConfigFile = xarCoreGetVarDirPath() . '/cache/config.log.php';
            if (file_exists($logConfigFile)) unlink($logConfigFile);
            //When people turn it on again it will produce the config in the
            //new directory, no need to do it in here.
        case '1.0.0':
        case '2.0.0':
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
    // delete the dynamic objects and their properties
    $objectids = unserialize(xarModGetVar('logconfig','objectids'));
    foreach ($objectids as $objectid) {
        if (!empty($objectid)) {
            xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
        }
    }

    xarModDelVar('logconfig','objectids');
    xarModDelVar('logconfig','itemstypenumber');

    // Remove Masks and Instances
    xarRemoveMasks('logconfig');
    xarRemoveInstances('logconfig');

    // Deletion successful
    return true;
}

?>