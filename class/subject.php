<?php
/**
 * Workflow Module Test Subject for Symfony Workflow tests
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

class xarWorkflowSubject
{
    use xarWorkflowMarkingTrait;

    public $objectName = 'dummy';

    public function getId()
    {
        $itemId = spl_object_id($this);
        return implode('.', [$this->objectName, (string) $itemId]);
    }

    public function setObjectName(string $objectName)
    {
        $this->objectName = $objectName;
    }
}
