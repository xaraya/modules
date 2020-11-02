<?php
/**
 * Scheduler Module
 *
 * @package modules
 * @subpackage scheduler module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class Scheduler_TriggerBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'trigger';
    protected $module           = 'scheduler'; // module block type belongs to, if any
    protected $text_type        = 'Scheduler Trigger';  // Block type display name
    protected $text_type_long   = 'Trigger for the scheduler (external trigger is recommended)'; // Block type description
    // Additional info, supplied by developer, optional
    protected $type_category    = 'block'; // options [(block)|group]
    protected $author           = '';
    protected $contact          = '';
    protected $credits          = '';
    protected $license          = '';
    
    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    // @todo: drop the show_help flag, and go back to checking if help method is declared
    protected $show_help    = false; // let the subsystem know if this block type has a help() method

    public $showstatus          = false;

    /**
     * Display func.
     * @param $data array containing title,content
     */
    public function display(array $data=array())
    {
        $vars = $this->getContent();
        /*
                // check if we have the right trigger
                $trigger = xarModVars::get('scheduler','trigger');
                if (empty($trigger) || $trigger != 'block') {
                    $vars['msg'] = xarML('Wrong trigger');
                    return $vars;
                }
        */
        // Check when we last ran the scheduler
        $lastrun = xarModVars::get('scheduler', 'lastrun');
        $now = time() + 60; // add some margin here
        if (!empty($lastrun) && $lastrun > $now - 60*60) {
            if (empty($vars['showstatus'])) {
                return;
            } else {
                $diff = time() - $lastrun;
                $vars['msg'] = xarML('Last run was #(1) minutes #(2) seconds ago', intval($diff / 60), $diff % 60);
                return $vars;
            }
        }

        // Let's run without interruptions for a while :)
        @ignore_user_abort(true);
        @set_time_limit(15*60);

        // update the last run time
        xarModVars::set('scheduler', 'lastrun', $now - 60); // remove the margin here
        xarModVars::set('scheduler', 'running', 1);

        // TODO: this won't work on NFS-mounted or FAT (Win98) file systems, and ISAPI may do weird things too !
        //       So we need to find some better way to see if we're really the only ones playing here...

        // let's see if we're the only ones trying to run jobs at this moment
        $GLOBALS['xarScheduler_LockFileHandle'] = fopen(sys::varpath().'/cache/templates/scheduler.lock', 'w+');
        if (empty($GLOBALS['xarScheduler_LockFileHandle']) || !flock($GLOBALS['xarScheduler_LockFileHandle'], LOCK_EX | LOCK_NB)) {
            fclose($GLOBALS['xarScheduler_LockFileHandle']);
            if (empty($vars['showstatus'])) {
                return;
            } else {
                $vars['msg'] = xarML('Some other process is running jobs right now');
                return $vars;
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
            $vars['msg'] = xarML('Running Jobs');
            return $vars;
        }
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
    $output = xarMod::apiFunc('scheduler', 'user', 'runjobs');

    // Normally, open files should be closed at the end by PHP anyway, but let's be polite :)
    if (!empty($GLOBALS['xarScheduler_LockFileHandle'])) {
        fclose($GLOBALS['xarScheduler_LockFileHandle']);
    }
}
