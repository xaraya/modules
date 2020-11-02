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
/**
 * Instead of triggering the scheduler by retrieving the web page
 * index.php?module=scheduler or by using a trigger block on your
 * site, you can also execute this script directly using the PHP
 * command line interface (CLI) : php run_scheduler.php
 */

/**
 * Redefine the paths to the Xaraya directories
 */
$systemConfiguration = array();
$systemConfiguration['rootDir'] = '../../../';          // The path from here to the Xaraya root directory
$systemConfiguration['libDir'] = 'lib/';                // The path to the lib directory relative to root
$systemConfiguration['webDir'] = '';                    // The path to the web directory relative to root
$systemConfiguration['codeDir'] = 'code/';              // The path to the code directory relative to root
$GLOBALS['systemConfiguration'] = $systemConfiguration;

/**
 * Load the Xaraya configuration files so we can get started
 * This needs to be hard coded. Everything else works off the system configuration paths above
 */
 include '../../../bootstrap.php';

/**
 * Load the Xaraya core
 */
sys::import('xaraya.core');
xarCore::xarInit(xarCore::SYSTEM_ALL);
$homedir = xarServer::getBaseURL();

// update the last run time
xarModVars::set('scheduler', 'lastrun', time());
xarModVars::set('scheduler', 'running', 1);

// call the API function to run the jobs
echo xarMod::apiFunc('scheduler', 'user', 'runjobs');
