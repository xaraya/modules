<?php
/**
 * File: $Id:
 * 
 * Icecast "Now Playing" Block
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by Johnny Robeson
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link
 *
 * @subpackage icecast
 * @author Johnny Robeson 
 */

/**
 * Initialise block
 */
function icecast_nowplayingblock_init()
{
    return true;
} 

/**
 * Get information on block
 */
function icecast_nowplayingblock_info()
{ 
    // Values
    return array('text_type'      => 'Now Playing',
                 'module'         => 'icecast',
                 'text_type_long' => 'Now Playing',
                 'allow_multiple' => true,
                 'form_content'   => false,
                 'form_refresh'   => false,
                 'show_preview'   => true);
} 

/**
 * Display block
 */
function icecast_nowplayingblock_display($blockinfo)
{ 
    // Security check
    if (!xarSecurityCheck('ReadIcecastBlock', 1, 'Block', $blockinfo['title'])) return;

    // Get variables from content block.    
    //$vars = $blockinfo['content'];
    
    $stats = array();
    
    $stats = xarModAPIFunc('icecast', 'user', 'getnowplaying');
  
    $data['mounts'] = $stats; 
    //die(var_dump($data['mounts']));
    $data['blockid'] = $blockinfo['bid'];

    $blockinfo['content'] = $data;

    return $blockinfo;
} 

?>