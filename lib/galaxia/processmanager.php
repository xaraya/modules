<?php

// Load configuration of the Galaxia Workflow Engine
include_once (dirname(__FILE__) . '/config.php');

include_once (GALAXIA_LIBRARY.'/managers/processes.php');
include_once (GALAXIA_LIBRARY.'/managers/instances.php');
include_once (GALAXIA_LIBRARY.'/managers/roles.php');
include_once (GALAXIA_LIBRARY.'/managers/activities.php');
include_once (GALAXIA_LIBRARY.'/managers/graphviz.php');

/// $roleManager is the object that will be used to manipulate roles.
$roleManager = new RoleManager();
/// $activityManager is the object that will be used to manipulate activities.
$activityManager = new ActivityManager();
/// $processManager is the object that will be used to manipulate processes.
$processManager = new ProcessManager();
/// $instanceManager is the object that will be used to manipulate instances.
$instanceManager = new InstanceManager();

if (defined('GALAXIA_LOGFILE') && GALAXIA_LOGFILE) {
    include_once (GALAXIA_LIBRARY.'/observers/logger.php');

    $logger = new Logger(GALAXIA_LOGFILE);
    $processManager->attach_all($logger);
    $activityManager->attach_all($logger);
    $roleManager->attach_all($logger);
}

?>
