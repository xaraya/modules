<?php

namespace Galaxia\Api\Activities;

include_once(GALAXIA_LIBRARY.'/api/activity.php');
use Galaxia\Api\WorkflowActivity;

/**
 * Class to handle workflow activities of type 'switch'
 *
 **/
class SwitchActivity extends WorkflowActivity
{
    protected $type  = 'switch';
    protected $shape = 'diamond';
}
