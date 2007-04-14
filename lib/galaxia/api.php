<?php
// Load configuration of the Galaxia Workflow Engine
include_once (dirname(__FILE__) . '/config.php');

// @todo we dont need this, we can lazy load stuff

// Load different object hierarchies we need
include_once (GALAXIA_LIBRARY.'/api/process.php');
include_once (GALAXIA_LIBRARY.'/api/instance.php');
include_once (GALAXIA_LIBRARY.'/api/activity.php');

// This sucks a little.
$instance = new Instance();
?>
