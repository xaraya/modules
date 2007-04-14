<?php
include_once(GALAXIA_LIBRARY.'/src/api/activity.php');
//!! Activity
//!
/*!
This class handles activities of type 'activity'
*/
class StandardActivity extends WorkflowActivity
{
      protected $type  = 'activity';
      protected $shape = 'box';
}
?>
