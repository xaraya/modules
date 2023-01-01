<?php
/**
 * Workflow Module
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

sys::import('modules.base.xarproperties.textarea');

class WorkflowsProperty extends TextAreaProperty
{
    public $id         = 18888;
    public $name       = 'workflows';
    public $desc       = 'Workflows';
    public $reqmodules = ['workflow'];

    private $marking   = [];
    private $context   = [];
    private $workflows = [];
    private $transitions = [];

    public function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->template   = 'workflows';
        $this->tplmodule  = 'workflow';
        $this->filepath   = 'modules/workflow/xarproperties';
        // We want a reference to the object here
        $this->include_reference = 1;

        // @checkme initialize workflows based on defaultvalue = {"loans":[]}
        $this->parseConfigValue($this->defaultvalue);
    }

    /**
     * The defaultvalue can be set to automatically load the workflows
     *
     * @param string $value the defaultvalue used to configure the workflows
     */
    public function parseConfigValue($value)
    {
        if (empty($value)) {
            return;
        }
        $this->workflows = @json_decode($value, true);
        // reset default value and current value after config parsing
        $this->defaultvalue = '';
        $this->value = $value;
    }

    /**
     * Show some default output for this property
     *
     * @param mixed $data['value'] value of the property (default is the current value)
     * @return string containing the HTML (or other) text to output in the BL template
     */
    public function showOutput(array $data = [])
    {
        if (empty($data['value'])) {
            $data['value'] = $this->workflows;
        }
        return parent::showOutput($data);
    }

    public function preList()
    {
        if (empty($this->objectref)) {
            return true;
        }

        return true;
    }

    // See https://write.vanoix.com/alexandre/creer-un-workflow-metier-avec-le-composant-symfony-workflow
    //
    // See https://github.com/symfony/symfony/blob/6.3/src/Symfony/Component/Workflow/Tests/Subject.php
    public function getMarking()
    {
        return $this->marking;
    }

    public function setMarking($marking, array $context = [])
    {
        $this->marking = $marking;
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }

    // See https://github.com/symfony/symfony/blob/6.3/src/Symfony/Component/Workflow/Registry.php
    public function hasWorkflow(string $workflowName)
    {
        return array_key_exists($workflowName, $this->workflows);
    }

    public function getWorkflow(string $workflowName)
    {
        return $this->workflows[$workflowName];
    }

    public function allWorkflows()
    {
        return $this->workflows;
    }

    // See https://github.com/symfony/symfony/blob/6.3/src/Symfony/Component/Workflow/Workflow.php
    public function canTransition(string $transitionName)
    {
        return array_key_exists($workflowName, $this->workflows);
    }

    public function applyTransition(string $transitionName, array $context = [])
    {
        return $this->workflows[$workflowName];
    }

    public function getEnabledTransitions()
    {
        return $this->workflows;
    }
}
