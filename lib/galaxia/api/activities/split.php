<?php

namespace Galaxia\Api\Activities;

include_once(GALAXIA_LIBRARY.'/api/activity.php');
use Galaxia\Api\WorkflowActivity;

/**
 * Class to handle workflow activities of type 'split'
 *
 **/
class SplitActivity extends WorkflowActivity
{
    protected $type  = 'split';
    protected $shape = 'triangle';
}
