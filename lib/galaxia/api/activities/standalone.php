<?php
include_once(GALAXIA_LIBRARY.'/api/activity.php');
/**
 * Class to handle workflow activities of type 'standalone'
 *
 * Characteristics of this type:
 * - no incoming, nor outgoing transitions
 **/
class StandaloneActivity extends WorkflowActivity
{
    protected $type  = 'standalone';
    protected $shape = 'hexagon';
}
?>
