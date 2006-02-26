<?php
/**
 * Modify Function to the Blocks Admin
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 */

/**
 * Modify Function to the Blocks Admin
 * @author Jo Dalle Nogare 
 * @author Jim McDonald
 * @param $blockinfo array containing title,content
 */
function registration_rloginblock_modify($blockinfo)
{
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['showlogout'])) {
        $vars['showlogout'] = 0;
    }
    if (empty($vars['logouttitle'])) {
        $vars['logouttitle'] = '';
    }

    $args['showlogout'] = $vars['showlogout'];
    $args['logouttitle'] = $vars['logouttitle'];

    $args['blockid'] = $blockinfo['bid'];
    return $args;
}

/**
 * Updates the Block config from the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function registration_rloginblock_update($blockinfo)
{
    if (!xarVarFetch('showlogout', 'int:0:1', $vars['showlogout'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('logouttitle', 'str', $vars['logouttitle'], '', XARVAR_NOT_REQUIRED)) return;

    $blockinfo['content'] = $vars;

    return $blockinfo;
}

?>