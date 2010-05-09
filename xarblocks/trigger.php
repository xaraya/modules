<?php
/**
 * Scheduler module
 *
 * @package modules
 * @copyright (C) 2002-2008 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Scheduler Module
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class Scheduler_TriggerBlock extends BasicBlock implements iBlock
{
    public $name                = 'TriggerBlock';
    public $module              = 'scheduler';
    public $text_type           = 'Trigger';
    public $text_type_long      = 'Trigger for the scheduler (using an external trigger is better)';
    public $allow_multiple      = false;
    public $nocache             = 1;
    public $pageshared          = 0;
    public $usershared          = 0;

    public $showstatus          = false;

/**
 * Display func.
 * @param $data array containing title,content
 */
    function display(Array $data=array())
    {
        $data = parent::display($data);
        if (empty($data)) return;

        // check if we have the right trigger
        $trigger = xarModVars::get('scheduler','trigger');
        if (empty($trigger) || $trigger != 'block') {
            $data['content']['msg'] = xarML('Wrong trigger');
            return $data;
        }

        // check when we last ran the scheduler
        $lastrun = xarModVars::get('scheduler', 'lastrun');
        $now = time() + 60; // add some margin here
        if (!empty($lastrun) && $lastrun > $now - 60*60) {
            if (empty($vars['showstatus'])) {
                return;
            } else {
                $diff = time() - $lastrun;
                $data['content']['msg'] = xarML('Last run was #(1) minutes #(2) seconds ago', intval($diff / 60), $diff % 60);
                return $data;
            }
        }

        // let's run without interruptions for a while :)
        @ignore_user_abort(true);
        @set_time_limit(15*60);

        // update the last run time
        xarModVars::set('scheduler','lastrun',$now - 60); // remove the margin here
        xarModVars::set('scheduler','running',1);

    // TODO: this won't work on NFS-mounted or FAT (Win98) file systems, and ISAPI may do weird things too !
    //       So we need to find some better way to see if we're really the only ones playing here...

        // let's see if we're the only ones trying to run jobs at this moment
        $GLOBALS['xarScheduler_LockFileHandle'] = fopen(xarCoreGetVarDirPath().'/cache/templates/scheduler.lock','w+');
        if (empty($GLOBALS['xarScheduler_LockFileHandle']) || !flock($GLOBALS['xarScheduler_LockFileHandle'], LOCK_EX | LOCK_NB)) {
            fclose($GLOBALS['xarScheduler_LockFileHandle']);
            if (empty($vars['showstatus'])) {
                return;
            } else {
                $data['content']['msg'] = xarML('Some other process is running jobs right now');
                return $data;
            }
        }

        // For some reason, PHP thinks it's in the Apache root during shutdown functions,
        // so we save the current base dir here - otherwise xarMod::apiFunc() will fail
        $GLOBALS['xarScheduler_BaseDir'] = realpath('.');

        // register the shutdown function that will execute the jobs after this script finishes
        register_shutdown_function('scheduler_triggerblock_runjobs');

        if (empty($vars['showstatus'])) {
            return;
        } else {
            $data['content']['msg'] = xarML('Running Jobs');
            return $data;
        }
    }

/**
 * Modify Function to the Blocks Admin
 * @param $data array containing title,content
 */
    public function modify(Array $data=array())
    {
        return parent::modify($data);
    }

/**
 * Updates the Block config from the Blocks Admin
 * @param $data array containing title,content
 */
    public function update(Array $data=array())
    {
        $data = parent::update($data);
        if (empty($data)) return;
        $vars = array();
        if(!xarVarFetch('showstatus',  'isset', $vars['showstatus'],  NULL, XARVAR_DONT_SET)) {return;}
        if (empty($vars['showstatus'])) {
            $vars['showstatus'] = 0;
        }

        $data['content'] = $vars;
        return $data;
    }
}

/**
 * run scheduler jobs when the script is finished
 */
function scheduler_triggerblock_runjobs()
{
    // For some reason, PHP thinks it's in the Apache root during shutdown functions,
    // so we move back to our own base dir first - otherwise xarMod::apiFunc() will fail
    if (!empty($GLOBALS['xarScheduler_BaseDir'])) {
        chdir($GLOBALS['xarScheduler_BaseDir']);
    }
    $output = xarMod::apiFunc('scheduler','user','runjobs');

    // Normally, open files should be closed at the end by PHP anyway, but let's be polite :)
    if (!empty($GLOBALS['xarScheduler_LockFileHandle'])) {
        fclose($GLOBALS['xarScheduler_LockFileHandle']);
    }
}

?>