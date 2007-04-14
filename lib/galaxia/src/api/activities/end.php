<?php
include_once(GALAXIA_LIBRARY.'/src/api/activity.php');
//!! End
//! End class
/*!
This class handles activities of type 'end'
*/
class EndActivity extends WorkflowActivity
{
    protected $type  = 'end';
    protected $shape = 'doublecircle';
}
?>
