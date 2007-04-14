<?php

include_once (GALAXIA_LIBRARY.'/common/base.php');
/**
 * Class representing workitems in a workflow
 *
 * @package workflow
 * @author Marcel van der Boom
 * @todo implement
**/
class WorkItem extends Base
{
    public $instance;
    public $properties = Array();
    public $started;
    public $ended;
    public $activity;

}
?>
