<?php
/**
 * Workflow Module Marking Trait for Symfony Workflow tests
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

trait xarWorkflowMarkingTrait
{
    protected $_workflowMarking;  // array for workflow or string for state_machine
    protected $_workflowContext;

    //public function getId()
    //{
    //    return spl_object_id($this);
    //}

    // See https://write.vanoix.com/alexandre/creer-un-workflow-metier-avec-le-composant-symfony-workflow
    //
    // See https://github.com/symfony/symfony/blob/6.3/src/Symfony/Component/Workflow/Tests/Subject.php
    public function getMarking()
    {
        return $this->_workflowMarking;
    }

    public function setMarking($marking, array $context = [])
    {
        $this->_workflowMarking = $marking;
        $this->_workflowContext = $context;
    }

    public function getContext()
    {
        return $this->_workflowContext;
    }
}
