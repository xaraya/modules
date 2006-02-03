<?php
/**
 * Modify block settings
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */

/**
 * Julian Event Block - Modify block settings
 *
 * @author Julian Module development team
 * @author MichelV MichelV@xarayahosting.nl
 *
 * @access  public
 * @param   $blockinfo
 * @return  $blockinfo data array
*/
function julian_caleventblock_modify($blockinfo)
{
    // Break out options from our content field
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }
    // Security check. Need to be admin
    if (!xarSecurityCheck('AdminJulian', 1)) {
        return;
    }

    // Defaults
    if (!isset($vars['EventBlockDays'])) {
        $vars['EventBlockDays'] = 7;
    }
    /* Defaults */
    if (empty($vars['CatAware'])) {
        $vars['CatAware'] = false;
    }
    $vars['blockid'] = $blockinfo['bid'];
    return $vars;
}

/**
 * Updates the Block settings
 */
function julian_caleventblock_update($blockinfo)
{

    // Security check. Need to be admin
    if (!xarSecurityCheck('AdminJulian', 1)) {
        return;
    }

    $vars = array();
    if (!xarVarFetch('EventBlockDays', 'int', $vars['EventBlockDays'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('CatAware', 'checkbox', $vars['CatAware'], FALSE, XARVAR_NOT_REQUIRED)) return;

    $blockinfo['content'] = $vars;

    return $blockinfo;
}
?>
