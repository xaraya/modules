<?php
/**
 * Other Sample block
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Other Block - another sample block
 * modify block settings
 *
 * @author Example Module development team
 */
function example_othersblock_modify($blockinfo)
{ 
    /* Get current content */
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    /* Defaults */
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    } 

    /* Send content to template */
    return array(
        'numitems' => $vars['numitems'],
        'blockid' => $blockinfo['bid']
    );
} 

/**
 * update block settings
 */
function example_othersblock_update($blockinfo)
{
    $vars = array();
    if (!xarVarFetch('numitems', 'int:0', $vars['numitems'], 5, XARVAR_DONT_SET)) {return;}
    $blockinfo['content'] = $vars;
    return $blockinfo;
} 
?>