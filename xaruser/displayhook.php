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
 * @author Marc Lutolf <mfl@netspan.ch>
 */

function workflow_user_displayhook($args)
{
    //return var_export($args, true);
    extract($args);

    // everything is already validated in HookSubject, except possible empty objectid/itemid for create/display
    $modname = $extrainfo['module'];
    $itemtype = $extrainfo['itemtype'];
    $itemid = $extrainfo['itemid'];
    $modid = $extrainfo['module_id'];

    // Symfony Workflow transition
    //return 'Workflow user displayhook was here for Symfony Workflow transition...';
    // Galaxia Workflow activity
    //return 'Workflow user displayhook was here for Galaxia Workflow activity...';
    return '';
}
