<?php
include_once(GALAXIA_LIBRARY.'/api/activity.php');
/**
 * Class to handle workflow activities of type 'start'
 *
 * Characteristics of this type:
 * - no incoming transitions
 * - only one 'start' type activity per workflow
 **/
class StartActivity extends WorkflowActivity
{
    protected $type = 'start';
    protected $shape = 'circle';
}
?>
