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

    // @checkme create minimal objectref object for use in getId()
    public $objectref;

    public function __construct(string $objectName = 'dummy', int $itemId = 0)
    {
        $this->objectref = (object) ['name' => $objectName, 'itemid' => $itemId ?: spl_object_id($this)];
    }

    public function getObject(bool $build = true)
    {
        sys::import('modules.dynamicdata.class.objects.base');
        if ($build && !$this->objectref instanceof DataObject) {
            $objectref = DataObjectMaster::getObject(['name' => $this->objectref->name, 'itemid' => $this->objectref->itemid]);
            if (!empty($this->objectref->itemid)) {
                $objectref->getItem();
            }
            $this->objectref = $objectref;
        }
        return $this->objectref;
    }
}
