<?php

namespace Galaxia\Api\Activities;

include_once(GALAXIA_LIBRARY.'/api/activity.php');
use Galaxia\Api\WorkflowActivity;

/**
 * Class to handle workflow activities of type 'activity'
 *
 **/
class StandardActivity extends WorkflowActivity
{
    protected $type  = 'activity';
    protected $shape = 'box';
}
