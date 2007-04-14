<?php
include_once(GALAXIA_LIBRARY.'/api/activity.php');
//!! Standalone
//! Standalone class
/*!
This class handles activities of type 'standalone'
*/
class StandaloneActivity extends WorkflowActivity
{
    protected $type  = 'standalone';
    protected $shape = 'hexagon';
}
?>
