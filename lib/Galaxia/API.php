<?php

// Load configuration of the Galaxia Workflow Engine
include_once (dirname(__FILE__) . '/config.php');

include_once (GALAXIA_LIBRARY.'/src/API/Process.php');
include_once (GALAXIA_LIBRARY.'/src/API/Instance.php');
include_once (GALAXIA_LIBRARY.'/src/API/BaseActivity.php');

// This sucks a little.
// Commented $process out. I suspect some workflows may fail, but if we
// want a common variable in the runtime workflow (perhaps we  should not)
// put these into the compiler pre/post files instead of here.

//$process  = new Process();
$instance = new Instance();
$baseActivity = new BaseActivity();

?>
