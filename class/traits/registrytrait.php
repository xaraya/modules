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

/**
 * For documentation purposes only - available via xarWorkflowRegistryTrait
 */
interface xarWorkflowRegistryInterface
{
    public function hasWorkflow(string $workflowName): bool;
    public function getWorkflow(string $workflowName): mixed;
    public function addWorkflow(string $workflowName, $workflow = []): void;
    public function allWorkflows(): array;
    public function supportsWorkflow(string $workflowName): bool;
}

trait xarWorkflowRegistryTrait
{
    protected $workflows = [];

    // See https://github.com/symfony/symfony/blob/6.3/src/Symfony/Component/Workflow/Registry.php
    public function hasWorkflow(string $workflowName): bool
    {
        return array_key_exists($workflowName, $this->workflows);
    }

    public function getWorkflow(string $workflowName): mixed
    {
        return $this->workflows[$workflowName];
    }

    public function addWorkflow(string $workflowName, $workflow = []): void
    {
        $this->workflows[$workflowName] = $workflow;
    }

    public function allWorkflows(): array
    {
        return $this->workflows;
    }

    public function supportsWorkflow(string $workflowName): bool
    {
        $this->workflows[$workflowName] ??= [];
        return true;
    }
}
