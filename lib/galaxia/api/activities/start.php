<?php
include_once(GALAXIA_LIBRARY.'/api/activity.php');
/**
 * Class to handle workflow activities of type 'start'
 *
 **/
class StartActivity extends WorkflowActivity
{
    protected $type = 'start';
    protected $shape = 'circle';
}
?>
