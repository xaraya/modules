<?php
include_once(GALAXIA_LIBRARY.'/src/api/activity');
//!! Split
//! Split class
/*!
This class handles activities of type 'split'
*/
class SplitActivity extends WorkflowActivity
{
    protected $type  = 'split';
    protected $shape = 'triangle';
}
?>
