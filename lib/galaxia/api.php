<?php

// Load configuration of the Galaxia Workflow Engine
include_once (dirname(__FILE__) . '/config.php');

include_once (GALAXIA_LIBRARY.'/api/process.php');
include_once (GALAXIA_LIBRARY.'/api/instance.php');
include_once (GALAXIA_LIBRARY.'/api/activity.php');

// This sucks a little.
// Commented $process out. I suspect some workflows may fail, but if we
// want a common variable in the runtime workflow (perhaps we  should not)
// put these into the compiler pre/post files instead of here.

//$process  = new Process();
$instance = new Instance();
$baseActivity = new WorkflowActivity();

?>
