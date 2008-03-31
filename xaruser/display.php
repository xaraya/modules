<?php
/**
 * Hitcount
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Hitcount Module
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */

/**
 * add a hit for a specific item, and display the hitcount (= display hook)
 *
 * (use xarVarSetCached('Hooks.hitcount','save', 1) to tell hitcount *not*
 * to display the hit count, but to save it in 'Hooks.hitcount', 'value')
 *
 * @param $args['objectid'] ID of the item this hitcount is for
 * @param $args['extrainfo'] may contain itemtype
 * @returns output
 * @return output with hitcount information
 */
function hitcount_user_display($args)
{
    extract($args);

    // Load API
    if (!xarModAPILoad('hitcount', 'admin')) return;

    // When called via hooks, modname will be empty, but we get it from the
    // extrainfo or from the current module
    if (empty($args['modname']) || !is_string($args['modname'])) {
        if (isset($extrainfo) && is_array($extrainfo) &&
            isset($extrainfo['module']) && is_string($extrainfo['module'])) {
            $args['modname'] = $extrainfo['module'];
        } else {
            $args['modname'] = xarModGetName();
        }
    }
    if (!isset($args['itemtype']) || !is_numeric($args['itemtype'])) {
         if (isset($extrainfo) && is_array($extrainfo) &&
             isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
             $args['itemtype'] = $extrainfo['itemtype'];
         } else {
             $args['itemtype'] = 0;
         }
    }

    if (xarVarIsCached('Hooks.hitcount','nocount') ||
        (xarSecurityCheck('AdminHitcount', 0) && xarModVars::get('hitcount', 'countadmin') == FALSE) ) {
        $hitcount = xarModAPIFunc('hitcount', 'user', 'get', $args);
    } else {
        $hitcount = xarModAPIFunc('hitcount', 'admin', 'update', $args);
    }

    if (isset($hitcount)) {
        // Display current hitcount or set the cached variable
        if (!xarVarIsCached('Hooks.hitcount','save') ||
            xarVarGetCached('Hooks.hitcount','save') == false ) {
            return '(' . $hitcount . ' ' . xarML('Reads') . ')';
        } else {
            xarVarSetCached('Hooks.hitcount','value',$hitcount);
        }
    }

    return '';
}

?>
