<?php

namespace Galaxia\Api;

include_once(GALAXIA_LIBRARY.'/common/base.php');
use Galaxia\Common\Base;

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
    public $properties = [];
    public $started;
    public $ended;
    public $activity;
}
