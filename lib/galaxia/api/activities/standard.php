<?php
include_once(GALAXIA_LIBRARY.'/api/activity.php');
/**
 * Class to handle workflow activities of type 'activity'
 *
 **/
class StandardActivity extends WorkflowActivity
{
      protected $type  = 'activity';
      protected $shape = 'box';
}
?>
