<?php
include_once(GALAXIA_LIBRARY.'/api/activity.php');
/**
 * Class to handle workflow activities of type 'end'
 *
 **/
class EndActivity extends WorkflowActivity
{
    protected $type  = 'end';
    protected $shape = 'doublecircle';
}
?>
