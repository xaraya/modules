<?php
include_once(GALAXIA_LIBRARY.'/api/activity.php');
//!! SwitchActivity
//! SwitchActivity class
/*!
This class handles activities of type 'switch'
*/
class SwitchActivity extends WorkflowActivity
{
    protected $type  = 'switch';
    protected $shape = 'diamond';
}
?>
