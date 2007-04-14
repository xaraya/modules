<?php
/**
 * Workflow transitions
 *
 * @author Marcel van der Boom
 * @package modules
 * @subpackage workflow
 **/
include_once (GALAXIA_LIBRARY.'/common/base.php');

/**
 * Class modelling a transition between two activities
 *
**/
class Transition extends Base
{
    function __construct(WorkflowActivity $from, WorkflowActivity $to)
    {

    }
}
?>