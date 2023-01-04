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
/**
 * the test user function
 *
 * @author mikespub
 * @access public
 * @param no $ parameters
 * @return array empty
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function workflow_user_test()
{
    // Security Check
    if (!xarSecurity::check('ReadWorkflow')) {
        return;
    }

    // @checkme we need to require composer autoload here
    $root = sys::root();
    // flat install supporting symlinks
    if (empty($root)) {
        $vendor = realpath(dirname(realpath($_SERVER['SCRIPT_FILENAME'])) . '/../vendor');
    } else {
        $vendor = realpath($root . 'vendor');
    }
    if (!file_exists($vendor . '/autoload.php')) {
        return ['warning' => '<p>This test uses composer autoload<br/><code>$ composer require --dev symfony/workflow</code></p>'];
    }
    require_once $vendor .'/autoload.php';
    //sys::import('modules.workflow.class.process');
    xarVar::fetch('workflow', 'isset', $workflow, null, xarVar::NOT_REQUIRED);
    xarVar::fetch('trackerId', 'isset', $trackerId, null, xarVar::NOT_REQUIRED);
    xarVar::fetch('subject', 'isset', $subject, null, xarVar::NOT_REQUIRED);
    xarVar::fetch('place', 'isset', $place, null, xarVar::NOT_REQUIRED);
    xarVar::fetch('transition', 'isset', $transition, null, xarVar::NOT_REQUIRED);
    return ['workflow' => $workflow, 'trackerId' => $trackerId, 'subject' => $subject, 'place' => $place, 'transition' => $transition];
}
