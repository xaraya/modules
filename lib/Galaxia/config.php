<?php

/**
 * Configuration of the Galaxia Workflow Engine for Xaraya
 */

// Common prefix used for all database table names, e.g. xar_workflow_
if (!defined('GALAXIA_TABLE_PREFIX')) {
    define('GALAXIA_TABLE_PREFIX', xarDBGetSiteTablePrefix() . '_workflow_');
}

// Directory containing the Galaxia library, e.g. lib/Galaxia
if (!defined('GALAXIA_LIBRARY')) {
    define('GALAXIA_LIBRARY', dirname(__FILE__));
}

// Directory where the Galaxia processes will be stored, e.g. lib/Galaxia/processes
if (!defined('GALAXIA_PROCESSES')) {
    // Note: this directory must be writeable by the webserver !
    //define('GALAXIA_PROCESSES', GALAXIA_LIBRARY . '/processes');
    define('GALAXIA_PROCESSES', 'var/processes');
}

// Directory where a *copy* of the Galaxia activity templates will be stored, e.g. templates
// Define as '' if you don't want to copy templates elsewhere
if (!defined('GALAXIA_TEMPLATES')) {
    // Note: this directory must be writeable by the webserver !
    //define('GALAXIA_TEMPLATES', 'templates');
    define('GALAXIA_TEMPLATES', '');
}

// Default header to be added to new activity templates
if (!defined('GALAXIA_TEMPLATE_HEADER')) {
    //define('GALAXIA_TEMPLATE_HEADER', '{*Smarty template*}');
    define('GALAXIA_TEMPLATE_HEADER', '');
}

// File where the ProcessManager logs for Galaxia will be saved, e.g. lib/Galaxia/log/pm.log
// Define as '' if you don't want to use logging
if (!defined('GALAXIA_LOGFILE')) {
    // Note: this file must be writeable by the webserver !
    //define('GALAXIA_LOGFILE', GALAXIA_LIBRARY . '/log/pm.log');
    define('GALAXIA_LOGFILE', '');
}

// Directory containing the GraphViz 'dot' and 'neato' programs, in case
// your webserver can't find them via its PATH environment variable
if (!defined('GRAPHVIZ_BIN_DIR')) {
    define('GRAPHVIZ_BIN_DIR', '');
    //define('GRAPHVIZ_BIN_DIR', 'd:/wintools/ATT/GraphViz/bin');
}

/**
 * Xaraya-specific adaptations
 */

// Database handler
global $dbGalaxia;
if (!isset($dbGalaxia)) {

    // Set the fetch mode to assoc by default (needed by lib/Galaxia)
    if(defined('ADODB_FETCH_ASSOC')) {
        $dbGalaxia = xarDBNewConn();
        
        // Adodb can set fetch mode on the whole connection, so lets do that in any case
        $oldmode = $dbGalaxia->SetFetchMode(ADODB_FETCH_ASSOC);
        define('GALAXIA_FETCHMODE',ADODB_FETCH_ASSOC);
    } elseif(defined('XARCORE_GENERATION') && XARCORE_GENERATION == 2) {
        $dbGalaxia =& xarDBGetConn();

        // This means we're in the 2 series of Xaraya
        // To prevent parse errors in php 4, we can not use the class constant here, even if this code doesnt
        // actually run, because php errors out with a parse error.
        // ResultSet::FETCHMODE_ASSOC has the value 1
        $ResultSet__FETCHMODE_ASSOC=1;
        define('GALAXIA_FETCHMODE',$ResultSet__FETCHMODE_ASSOC);
    } else {
        // Hope that everything works out :-)
    }
 }

// Specify how error messages should be shown (for use in compiler and activity code)
if (!function_exists('galaxia_show_error')) {
    function galaxia_show_error($msg)
    {
        // TODO: clean this up
        trigger_error(xarML($msg),E_USER_ERROR);
    }
}

// Specify how to execute a non-interactive activity (for use in src/API/Instance.php)
if (!function_exists('galaxia_execute_activity')) {
    function galaxia_execute_activity($activityId = 0, $iid = 0, $auto = 1)
    {
        $result = xarModAPIFunc('workflow','user','run_activity',
                                array('activityId' => $activityId,
                                      'iid' => $iid,
                                      'auto' => $auto));
    }
}

// Translate strings and variables
if (!function_exists('tra')) {
    function tra($what)
    {
        return xarML($what);
    }
}

?>
