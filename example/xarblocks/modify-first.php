<?php
/**
 * File: $Id: first.php 1.24 04/01/28 12:16:10+00:00 dudleyc@pint.(none) $
 * 
 * Example Block
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage example
 * @author Example module development team 
 */

/**
 * modify block settings
 */
function example_firstblock_modify($blockinfo)
{ 
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    } 

    // Send content to template
    return array('numitems' => $vars['numitems'], 'blockid' => $blockinfo['bid']);
} 

/**
 * update block settings
 */
function example_firstblock_update($blockinfo)
{
    if (!xarVarFetch('numitems', 'int:0', $vars['numitems'], 5, XARVAR_DONT_SET)) {return;}

    $blockinfo['content'] = $vars;

    return $blockinfo;
} 

?>