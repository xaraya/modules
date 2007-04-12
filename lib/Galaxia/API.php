<?php

// Load configuration of the Galaxia Workflow Engine
include_once (dirname(__FILE__) . '/config.php');

include_once (GALAXIA_LIBRARY.'/src/API/Process.php');
include_once (GALAXIA_LIBRARY.'/src/API/Instance.php');
include_once (GALAXIA_LIBRARY.'/src/API/BaseActivity.php');

$process = new Process();
$instance = new Instance();
$baseActivity = new BaseActivity();

?>
