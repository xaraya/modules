<?php
/**
 * Workflow Module Registry Trait for Symfony Workflow tests
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

trait xarWorkflowRegistryTrait
{
    protected $workflows = [];

    // See https://github.com/symfony/symfony/blob/6.3/src/Symfony/Component/Workflow/Registry.php
    public function hasWorkflow(string $workflowName)
    {
        return array_key_exists($workflowName, $this->workflows);
    }

    public function getWorkflow(string $workflowName)
    {
        return $this->workflows[$workflowName];
    }

    public function addWorkflow(string $workflowName, $workflow = [])
    {
        $this->workflows[$workflowName] = $workflow;
    }

    public function allWorkflows()
    {
        return $this->workflows;
    }

    public function supportsWorkflow(string $workflowName)
    {
        $this->workflows[$workflowName] ??= [];
        return true;
    }
}
