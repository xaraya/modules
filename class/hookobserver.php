<?php
/**
 * Workflow Module Generic API/GUI Hook Observer
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

class xarWorkflowHookObserver extends HookObserver implements ixarEventObserver
{
    public $module = 'workflow';
    public $type   = 'admin';
    public $func   = 'OVERRIDE';

    public function __construct(array $args = [])
    {
        // nothing special to do here for now - maybe later...
        parent::__construct($args);
    }

    public function notify(ixarEventSubject $subject)
    {
        // @checkme Xaraya only fires one event per subject type, so subjectName = subjectType here
        $subjectName = $subject->getSubject();
        $this->logEvent($subject, $subjectName);

        // @todo pass along the event to the workflow dispatcher(s) where needed

        // for api hooks the subject expects an array of extrainfo
        if ($subject instanceof ApiHookSubject) {
            return $this->getApiResult($subject, $subjectName);
        }

        // for gui hooks the subject expects a string to display, return the display gui func
        if ($subject instanceof GuiHookSubject) {
            return $this->getGuiResult($subject, $subjectName);
        }

        throw new Exception('Unknown HookSubject class: ' . $subject::class);
    }

    public function getGuiResult(ixarEventSubject $subject, string $subjectName)
    {
        // get args from subject (array containing objectid, extrainfo)
        //$args = $subject->getArgs();
        // get extrainfo from subject (array containing module, module_id, itemtype, itemid)
        //$extrainfo = $subject->getExtrainfo();

        // for gui hooks the subject expects a string to display, return the display gui func
        //return xarMod::guiFunc('workflow', 'user', 'display', []);
        //return 'workflow was here...';
        // See lib/xaraya/structures/events/guiobserver.php
        return xarMod::guiFunc($this->module, $this->type, $this->func, $subject->getArgs());
    }

    public function getApiResult(ixarEventSubject $subject, string $subjectName)
    {
        // get args from subject (array containing objectid, extrainfo)
        //$args = $subject->getArgs();
        // get extrainfo from subject (array containing module, module_id, itemtype, itemid)
        //$extrainfo = $subject->getExtrainfo();

        // for api hooks the subject expects an array of extrainfo
        // return the merged array of extrainfo and the created/updated/deleted item
        //$item = ['workflow' => 'was here...'];
        //return $extrainfo += $item;
        // See lib/xaraya/structures/events/apiobserver.php
        return xarMod::apiFunc($this->module, $this->type, $this->func, $subject->getArgs());
    }

    public function logEvent(ixarEventSubject $subject, string $subjectName)
    {
        //$subjectName = $subject->getSubject();
        // get args from subject (array containing objectid, extrainfo)
        $args = $subject->getArgs();
        // get extrainfo from subject (array containing module, module_id, itemtype, itemid)
        //$extrainfo = $subject->getExtrainfo();
        $message = sprintf(
            '%s: Subject (id: "%s") had event "%s"',
            get_class($this),
            $args['objectid'] ?? '',
            $subjectName,
        );
        xarLog::message($message, xarLog::LEVEL_INFO);
    }
}
