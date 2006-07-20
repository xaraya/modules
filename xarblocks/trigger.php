<?php
/**
 * Scheduler module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
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
function scheduler_triggerblock_init()
{
    return true;
}

/**
 * get information on block
 * @return array
 */
function scheduler_triggerblock_info()
{
    // Values
    return array('text_type' => 'trigger',
        'module' => 'scheduler',
        'text_type_long' => 'Trigger for the scheduler (using an external trigger is better)',
        'allow_multiple' => false,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => false);
}

/**
 * display block
 */
function scheduler_triggerblock_display($blockinfo)
{
    // Get current content
    if (!empty($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = array();
    }
    // Defaults
    if (empty($vars['showstatus']) || !xarSecurityCheck('AdminScheduler',0)) {
        $vars['showstatus'] = 0;
    }

    // check if we have the right trigger
    $trigger = xarModGetVar('scheduler','trigger');
    if (empty($trigger) || $trigger != 'block') {
        $blockinfo['content'] = xarML('Wrong trigger');
        return $blockinfo;
    }

    // check when we last ran the scheduler
    $lastrun = xarModGetVar('scheduler', 'lastrun');
    $now = time() + 60; // add some margin here
    if (!empty($lastrun) && $lastrun > $now - 60*60) {
        if (empty($vars['showstatus'])) {
            return;
        } else {
            $diff = time() - $lastrun;
            $blockinfo['content'] = xarML('Last run was #(1) minutes #(2) seconds ago', intval($diff / 60), $diff % 60);
            return $blockinfo;
        }
    }

    // let's run without interruptions for a while :)
    @ignore_user_abort(true);
    @set_time_limit(15*60);

    // update the last run time
    xarModSetVar('scheduler','lastrun',$now - 60); // remove the margin here
    xarModSetVar('scheduler','running',1);

// TODO: this won't work on NFS-mounted or FAT (Win98) file systems, and ISAPI may do weird things too !
//       So we need to find some better way to see if we're really the only ones playing here...

    // let's see if we're the only ones trying to run jobs at this moment
    $GLOBALS['xarScheduler_LockFileHandle'] = fopen(xarCoreGetVarDirPath().'/cache/templates/scheduler.lock','w+');
    if (empty($GLOBALS['xarScheduler_LockFileHandle']) || !flock($GLOBALS['xarScheduler_LockFileHandle'], LOCK_EX | LOCK_NB)) {
        fclose($GLOBALS['xarScheduler_LockFileHandle']);
        if (empty($vars['showstatus'])) {
            return;
        } else {
            $blockinfo['content'] = xarML('Some other process is running jobs right now');
            return $blockinfo;
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
        $blockinfo['content'] = xarML('Running Jobs');
        return $blockinfo;
    }
}

/**
 * modify block settings
 */
function scheduler_triggerblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    if (empty($vars['showstatus'])) {
        $vars['showstatus'] = 0;
    }
    $vars['blockid'] = $blockinfo['bid'];

    // Return output
    return xarTplBlock('scheduler','triggerAdmin',$vars);
}

/**
 * update block settings
 */
function scheduler_triggerblock_update($blockinfo)
{
    $vars = array();
    if(!xarVarFetch('showstatus',  'isset', $vars['showstatus'],  NULL, XARVAR_DONT_SET)) {return;}
    if (empty($vars['showstatus'])) {
        $vars['showstatus'] = 0;
    }

    $blockinfo['content'] = serialize($vars);
    return $blockinfo;
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
    $output = xarModAPIFunc('scheduler','user','runjobs');

    // Normally, open files should be closed at the end by PHP anyway, but let's be polite :)
    if (!empty($GLOBALS['xarScheduler_LockFileHandle'])) {
        fclose($GLOBALS['xarScheduler_LockFileHandle']);
    }
}

?>