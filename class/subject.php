<?php
/**
 * Workflow Module Test Subject for Symfony Workflow tests - could use xarWorkflowTransitionTrait too
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

sys::import('modules.workflow.class.traits.markingtrait');
sys::import('modules.workflow.class.traits.transitiontrait');

class xarWorkflowSubject implements xarWorkflowMarkingInterface
{
    use xarWorkflowMarkingTrait;

    // @checkme create minimal objectref object for use in getId()
    public $objectref;
    public $name = 'subject';

    public function __construct(string $objectName = 'dummy', int $itemId = 0)
    {
        $this->objectref = (object) ['name' => $objectName, 'itemid' => $itemId];
    }

    public function getObject(bool $build = true)
    {
        sys::import('modules.dynamicdata.class.objects.base');
        // @checkme create fake objectName for module:itemtype if no object is available for now?
        if ($build && !$this->objectref instanceof DataObject && strpos($this->objectref->name, ':') === false) {
            $objectref = DataObjectMaster::getObject(['name' => $this->objectref->name, 'itemid' => $this->objectref->itemid]);
            if (!empty($objectref)) {
                if (!empty($this->objectref->itemid)) {
                    $objectref->getItem();
                }
                $this->objectref = $objectref;
            }
        }
        return $this->objectref;
    }
}

class xarWorkflowSubjectWithTransitions extends xarWorkflowSubject implements xarWorkflowTransitionInterface
{
    use xarWorkflowTransitionTrait;

    public function getWorkflow(string $workflowName): mixed
    {
        // @todo get the actual workflow here instead of the workflowsproperty config
        return $this->workflows[$workflowName];
    }
}
