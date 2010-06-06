<?php
/**
 * Path Block modification
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Path Module
 * @link http://www.xaraya.com/index.php/release/eid/1150
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * modify block settings
 * This function is called via the blocks module. It shows a form in the block instance screen
 * @return array
 */
function path_firstblock_modify($blockinfo)
{
    // Get current path
    if (!is_array($blockinfo['path'])) {
        $vars = unserialize($blockinfo['path']);
    } else {
        $vars = $blockinfo['path'];
    }

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }

    // Send path to template
    return array('numitems' => $vars['numitems'], 'blockid' => $blockinfo['bid']);
}

/**
 * update block settings
 *
 * @param numitems The number of items to show
 * @return array $blockinfo with the information held by the block
 */
function path_firstblock_update($blockinfo)
{
    // We get the numitems. It is placed into $vars['numitems']
    if (!xarVarFetch('numitems', 'int:0', $vars['numitems'], 5, XARVAR_DONT_SET)) {return;}

    $blockinfo['path'] = $vars;

    return $blockinfo;
}

?>
