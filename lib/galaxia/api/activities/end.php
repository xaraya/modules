<?php
include_once(GALAXIA_LIBRARY.'/api/activity.php');
/**
 * Class to handle workflow activities of type 'end'
 *
 * Characteristics of this type:
 * - no outgoing transitions
 * - only one 'end' type activity per workflow
 *
 **/
class EndActivity extends WorkflowActivity
{
    protected $type  = 'end';
    protected $shape = 'doublecircle';
}
?>
