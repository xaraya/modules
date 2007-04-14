<?php
include_once(GALAXIA_LIBRARY.'/src/api/activity.php');
//!! Join
//! Join class
/*!
This class handles activities of type 'join'
*/
class JoinActivity extends WorkflowActivity
{
    protected $type  = 'join';
    protected $shape = 'invtriangle';
}
?>
