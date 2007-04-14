<?php
include_once(GALAXIA_LIBRARY.'/api/activity');
/**
 * Class to handle workflow activities of type 'split'
 *
 **/
class SplitActivity extends WorkflowActivity
{
    protected $type  = 'split';
    protected $shape = 'triangle';
}
?>
