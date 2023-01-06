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
sys::import('modules.workflow.class.traits.markingtrait');
sys::import('modules.workflow.class.traits.transitiontrait');
sys::import('modules.workflow.class.config');
sys::import('modules.workflow.class.tracker');

class WorkflowsProperty extends TextAreaProperty
{
    use xarWorkflowMarkingTrait;
    use xarWorkflowTransitionTrait;

    public $id         = 18888;
    public $name       = 'workflows';
    public $desc       = 'Workflows';
    public $reqmodules = ['workflow'];

    public function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->template   = 'workflows';
        $this->tplmodule  = 'workflow';
        $this->filepath   = 'modules/workflow/xarproperties';
        // We want a reference to the object here
        $this->include_reference = 1;
        // Use the dummy datastore for this property
        $this->source = 'none';

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
        $data['subjectId'] = $this->getId();
        // pass along objectref for xarWorkflowTracker::getItems()
        $data['objectref'] = $this->objectref;
        // from xarWorkflowMarkingTrait
        $data['marking'] = $this->getMarking();
        $data['context'] = $this->getContext();
        // from xarWorkflowRegistryTrait
        $data['workflows'] = $this->allWorkflows();
        // from xarWorkflowTransitionTrait
        //$data['transitions'] = $this->getEnabledTransitions($workflow);
        return parent::showOutput($data);
    }

    public function preList()
    {
        if (empty($this->objectref)) {
            return true;
        }

        return true;
    }
}
