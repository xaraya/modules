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

class xarWorkflowProcess extends xarObject
{
    private $definition;
    private $markingStore;
    private $dispatcher;
    private $name;
    private $eventsToDispatch;

    //public function __construct(Definition $definition, MarkingStoreInterface $markingStore = null, EventDispatcherInterface $dispatcher = null, string $name = 'unnamed', array $eventsToDispatch = null)
    public function __construct($definition, $markingStore = null, $dispatcher = null, string $name = 'unnamed', array $eventsToDispatch = null)
    {
        $this->definition = $definition;
        //$this->markingStore = $markingStore ?? new MethodMarkingStore();
        $this->markingStore = $markingStore;
        $this->dispatcher = $dispatcher;
        $this->name = $name;
        $this->eventsToDispatch = $eventsToDispatch;
    }
}
