<?php

include_once (GALAXIA_DIR.'/src/common/Observable.php');

include_once (GALAXIA_DIR.'/src/common/Observer.php');
include_once (GALAXIA_DIR.'/src/Observers/Logger.php');
include_once (GALAXIA_DIR.'/src/API/Base.php');
include_once (GALAXIA_DIR.'/src/API/BaseActivity.php');
include_once (GALAXIA_DIR.'/src/API/Process.php');
include_once (GALAXIA_DIR.'/src/API/Instance.php');
include_once (GALAXIA_DIR.'/src/API/activities/Activity.php');
include_once (GALAXIA_DIR.'/src/API/activities/Start.php');
include_once (GALAXIA_DIR.'/src/API/activities/End.php');
include_once (GALAXIA_DIR.'/src/API/activities/Standalone.php');
include_once (GALAXIA_DIR.'/src/API/activities/Start.php');
include_once (GALAXIA_DIR.'/src/API/activities/End.php');
include_once (GALAXIA_DIR.'/src/API/activities/SwitchActivity.php');
include_once (GALAXIA_DIR.'/src/API/activities/Split.php');
include_once (GALAXIA_DIR.'/src/API/activities/Join.php');
include_once (GALAXIA_DIR.'/src/API/Instance.php');
$baseActivity = new BaseActivity($dbTiki);
$process = new Process($dbTiki);
$instance = new Instance($dbTiki);

?>
