<?php
/**
 * Scheduler module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Scheduler Module
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * initialise block
 * @return bool true on success
 */
// Inherit properties and methods from BasicBlock class
sys::import('xaraya.structures.containers.blocks.basicblock');

class TriggerBlock extends BasicBlock implements iBlock
{
    public $name                = 'TriggerBlock';
    public $module              = 'scheduler';
    public $text_type           = 'trigger';
    public $text_type_long      = 'Trigger for the scheduler (using an external trigger is better)';
    public $xarversion          = '2.2.0';

    public $form_content        = false;
    public $form_refresh        = false;

    public $showstatus          = false;

/**
 * Display func.
 * @param $data array containing title,content
 */
    function display(Array $data=array())
    {
        $data = parent::display($data);
        if (empty($data)) return;

        if (empty($data['showstatus'])) $data['showstatus'] = $this->showstatus;

    // TODO: this won't work on NFS-mounted or FAT (Win98) file systems, and ISAPI may do weird things too !
    //       So we need to find some better way to see if we're really the only ones playing here...

        // let's see if we're the only ones trying to run jobs at this moment
        $GLOBALS['xarScheduler_LockFileHandle'] = fopen(xarCoreGetVarDirPath().'/cache/templates/scheduler.lock','w+');
        if (empty($GLOBALS['xarScheduler_LockFileHandle']) || !flock($GLOBALS['xarScheduler_LockFileHandle'], LOCK_EX | LOCK_NB)) {
            fclose($GLOBALS['xarScheduler_LockFileHandle']);
            if (empty($data['showstatus'])) {
                return;
            } else {
                $data['content'] = xarML('Some other process is running jobs right now');
                return $data;
            }
        }

        // For some reason, PHP thinks it's in the Apache root during shutdown functions,
        // so we save the current base dir here - otherwise xarModAPIFunc() will fail
        $GLOBALS['xarScheduler_BaseDir'] = realpath('.');

        // register the shutdown function that will execute the jobs after this script finishes
        register_shutdown_function('scheduler_triggerblock_runjobs');

        if (empty($vars['showstatus'])) {
            return;
        } else {
            $data['content'] = xarML('Running Jobs');
            return $data;
        }
    }
}


/**
 * run scheduler jobs when the script is finished
 */
function scheduler_triggerblock_runjobs()
{
    // For some reason, PHP thinks it's in the Apache root during shutdown functions,
    // so we move back to our own base dir first - otherwise xarModAPIFunc() will fail
    if (!empty($GLOBALS['xarScheduler_BaseDir'])) {
        chdir($GLOBALS['xarScheduler_BaseDir']);
    }
    $output = xarModAPIFunc('scheduler','user','runjobs',array('trigger' => 2));

    // Normally, open files should be closed at the end by PHP anyway, but let's be polite :)
    if (!empty($GLOBALS['xarScheduler_LockFileHandle'])) {
        fclose($GLOBALS['xarScheduler_LockFileHandle']);
    }
}

?>