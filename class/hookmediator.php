<?php
/**
 * Workflow Module Hook Mediator to direct Event/Hook Subjects from Xaraya to the right Workflow Hook Observer(s)
 * and/or Symfony EventDispatcher(s) (WIP)
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

//namespace Xaraya\Modules\Workflow\HookObservers;  // not supported by events.php yet
sys::import('xaraya.structures.hooks.observer');
sys::import('xaraya.structures.hooks.apisubject');
sys::import('xaraya.structures.hooks.guisubject');
sys::import('modules.workflow.class.hookobserver');

class xarWorkflowHookMediator extends xarWorkflowHookObserver implements ixarEventObserver
{
    private static $subjectNamePrefix = '';  // no prefix used by Xaraya event/hook subjects
    private static $registeredSubjects = [];
    private static $callbackFunctions = [];
    private static $subjectTypeMethods = [
        'ItemCreate' => 'onItemCreate',
        'ItemUpdate' => 'onItemUpdate',
        'ItemDelete' => 'onItemDelete',
        'ModuleRemove' => 'onModuleRemove',
        'ItemDisplay' => 'onItemDisplay',
    ];

    public function notify(ixarEventSubject $subject)
    {
        // @checkme Xaraya only fires one event per subject type, so subjectName = subjectType here
        $subjectName = $subject->getSubject();
        $this->logEvent($subject, $subjectName);
        $this->callBack($subject, $subjectName);

        // @todo pass along the event to the workflow dispatcher(s) where needed

        // for gui hooks the subject expects a string to display, return the display gui func
        if ($subject instanceof GuiHookSubject) {
            return $this->getGuiResult($subject, $subjectName);
        }

        // for api hooks the subject expects an array of extrainfo
        if ($subject instanceof ApiHookSubject) {
            return $this->getApiResult($subject, $subjectName);
        }

        throw new Exception('Unknown HookSubject class: ' . $subject::class);
    }

    public function callBack(ixarEventSubject $subject, string $subjectName)
    {
        if (empty(static::$callbackFunctions[$subjectName])) {
            return;
        }
        foreach (static::$callbackFunctions[$subjectName] as $callbackFunc) {
            $callbackFunc($subject, $subjectName);
        }
    }

    public function onItemCreate(ixarEventSubject $subject, string $subjectName)
    {
        //$subjectName = $subject->getSubject();
        //$this->logEvent($subject, $subjectName);
        //$this->callBack($subject, $subjectName);
    }

    public function onItemUpdate(ixarEventSubject $subject, string $subjectName)
    {
        //$subjectName = $subject->getSubject();
        //$this->logEvent($subject, $subjectName);
        //$this->callBack($subject, $subjectName);
    }

    public function onItemDelete(ixarEventSubject $subject, string $subjectName)
    {
        //$subjectName = $subject->getSubject();
        //$this->logEvent($subject, $subjectName);
        //$this->callBack($subject, $subjectName);
    }

    public function onModuleRemove(ixarEventSubject $subject, string $subjectName)
    {
        //$subjectName = $subject->getSubject();
        //$this->logEvent($subject, $subjectName);
        //$this->callBack($subject, $subjectName);
    }

    public function onItemDisplay(ixarEventSubject $subject, string $subjectName)
    {
        //$subjectName = $subject->getSubject();
        //$this->logEvent($subject, $subjectName);
        //$this->callBack($subject, $subjectName);
    }

    // @checkme Xaraya only fires one event per subject type, so subjectName = subjectType here
    public static function getSubjectName(string $subjectType, string $workflowName = '', string $specificName = '')
    {
        //workflow.guard
        if (empty($workflowName)) {
            $subjectName = static::$subjectNamePrefix . $subjectType;
        //workflow.[workflow name].guard
        } else {
            $subjectName = static::$subjectNamePrefix . $workflowName . '.' . $subjectType;
            //workflow.[workflow name].guard.[transition name]
            if (!empty($specificName)) {
                $subjectName .= '.' . $specificName;
            }
        }
        return $subjectName;
    }

    // @checkme Xaraya only fires one event per subject type, so subjectName = subjectType here
    public static function addRegisteredSubject(string $subjectType, string $workflowName = '', string $specificName = '', ?callable $callbackFunc = null)
    {
        $subjectName = static::getSubjectName($subjectType, $workflowName, $specificName);
        static::$registeredSubjects[$subjectName] = [static::$subjectTypeMethods[$subjectType]];
        if (!empty($callbackFunc)) {
            static::addCallbackFunction($subjectName, $callbackFunc);
        }
        return $subjectName;
    }

    public static function addCallbackFunction(string $subjectName, callable $callbackFunc)
    {
        static::$callbackFunctions[$subjectName] ??= [];
        // @checkme call only once per event even if specified several times?
        static::$callbackFunctions[$subjectName][] = $callbackFunc;
    }

    // @checkme for information purposes only, not loaded or called by dispatcher here
    public static function getRegisteredSubjects()
    {
        //return [
        //    'ItemCreate' => ['onItemCreate'],
        //];
        return static::$registeredSubjects;
    }
}
