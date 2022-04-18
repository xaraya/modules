<?php

namespace Galaxia\Api\Activities;

include_once(GALAXIA_LIBRARY.'/api/activity.php');
use Galaxia\Api\WorkflowActivity;

/**
 * Class to handle workflow activities of type 'join'
 *
 **/
class JoinActivity extends WorkflowActivity
{
    protected $type  = 'join';
    protected $shape = 'invtriangle';
}
