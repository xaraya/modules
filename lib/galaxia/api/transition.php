<?php
/**
 * Workflow transitions
 *
 * @author Marcel van der Boom
 * @package modules
 * @subpackage workflow
 **/

namespace Galaxia\Api;

include_once(GALAXIA_LIBRARY.'/common/base.php');
use Galaxia\Common\Base;

/**
 * Class modelling a transition between two activities
 *
**/
class Transition extends Base
{
    private $from = null;
    private $to   = null;

    public function __construct(WorkflowActivity $from, WorkflowActivity $to)
    {
    }
}
