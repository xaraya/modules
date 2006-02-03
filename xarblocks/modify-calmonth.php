<?php
/**
 * Modify block settings
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */

/**
 * Julian Month Block - Modify block settings
 *
 * @author Julian Module development team
 * @author MichelV MichelV@xarayahosting.nl
 */
function julian_calmonthblock_modify($blockinfo)
{
    // Security check. Need to be admin
    if (!xarSecurityCheck('AdminJulian', 1)) {
        return;
    }

    /* Get current content */
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    /* Defaults */
    if (empty($vars['CatAware'])) {
        $vars['CatAware'] = false;
    }
    $vars['blockid'] = $blockinfo['bid'];

    /* Send content to template */
    return $vars;
}

/**
 * Update block settings
 */
function julian_calmonthblock_update($blockinfo)
{
    // Security check. Need to be admin
    if (!xarSecurityCheck('AdminJulian', 1)) {
        return;
    }

    $vars = array();
    if (!xarVarFetch('CatAware', 'checkbox', $vars['CatAware'], FALSE, XARVAR_NOT_REQUIRED)) return;
    $blockinfo['content'] = $vars;
    $vars['blockid'] = $blockinfo['bid'];
    return $blockinfo;
}
?>