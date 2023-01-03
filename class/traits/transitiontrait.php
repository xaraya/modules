<?php
/**
 * Workflow Module Transition Trait for Symfony Workflow tests
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */

sys::import('modules.workflow.class.traits.registrytrait');

trait xarWorkflowTransitionTrait
{
    use xarWorkflowRegistryTrait;

    // See https://github.com/symfony/symfony/blob/6.3/src/Symfony/Component/Workflow/Workflow.php
    public function canTransition(string $workflowName, string $transitionName)
    {
        $workflow = $this->getWorkflow($workflowName);
        return $workflow->can($transitionName);
    }

    public function applyTransition(string $workflowName, string $transitionName, array $context = [])
    {
        $workflow = $this->getWorkflow($workflowName);
        return $workflow->apply($transitionName, $context);
    }

    public function getEnabledTransitions(string $workflowName)
    {
        $workflow = $this->getWorkflow($workflowName);
        return $workflow->getEnabledTransitions();
    }
}
