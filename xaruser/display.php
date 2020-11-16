<?php
/**
 * Hitcount Module
 *
 * @package modules
 * @subpackage hitcount module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */

/**
 * add a hit for a specific item, and display the hitcount (= display hook)
 *
 * (use xarVar::setCached('Hooks.hitcount','save', 1) to tell hitcount *not*
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
    if (!xarMod::apiLoad('hitcount', 'admin')) {
        return;
    }

    // When called via hooks, modname will be empty, but we get it from the
    // extrainfo or from the current module
    if (empty($args['modname']) || !is_string($args['modname'])) {
        if (isset($extrainfo) && is_array($extrainfo) &&
            isset($extrainfo['module']) && is_string($extrainfo['module'])) {
            $args['modname'] = $extrainfo['module'];
        } else {
            $args['modname'] = xarMod::getName();
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
    if (xarVar::isCached('Hooks.hitcount', 'nocount') ||
        (xarSecurity::check('AdminHitcount', 0) && xarModVars::get('hitcount', 'countadmin') == false)) {
        $hitcount = xarMod::apiFunc('hitcount', 'user', 'get', $args);
    } else {
        $hitcount = xarMod::apiFunc('hitcount', 'admin', 'update', $args);
    }

    // @fixme: this function should return output to a template, not directly as a string!
    if (isset($hitcount)) {
        // Display current hitcount or set the cached variable
        if (!xarVar::isCached('Hooks.hitcount', 'save') ||
            xarVar::getCached('Hooks.hitcount', 'save') == false) {
            return '(' . $hitcount . ' ' . xarML('Reads') . ')';
        } else {
            xarVar::setCached('Hooks.hitcount', 'value', $hitcount);
        }
    }

    return '';
}
