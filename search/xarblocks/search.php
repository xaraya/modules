<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 * 
 * Search System
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * @subpackage search module
 * @author Johnny Robeson 
 */

/**
 * initialise block
 * 
 * @author Johnny Robeson 
 * @access public 
 * @param none $ 
 * @return nothing 
 * @throws no exceptions
 * @todo nothing
 */
function search_searchblock_init()
{
    return true;
} 

/**
 * get information on block
 * 
 * @author Johnny Robeson 
 * @access public 
 * @param none $ 
 * @return data array
 * @throws no exceptions
 * @todo nothing
 */
function search_searchblock_info()
{ 
    // Values
    return array('text_type'        => 'Search',
        'module'                    => 'search',
        'text_type_long'            => 'Search Block',
        'allow_multiple'            => false,
        'form_content'              => false,
        'form_refresh'              => false,
        'show_preview'              => true);
} 

/**
 * display search block
 * 
 * @author Johnny Robeson 
 * @access public 
 * @param none $ 
 * @return data array on success or void on failure
 * @throws no exceptions
 * @todo implement centre menu position
 */
function search_searchblock_display($blockinfo)
{ 
    // Security Check
    if (!xarSecurityCheck('ReadSearch', 0)) return;

    if (empty($blockinfo['title'])) {
        $blockinfo['title'] = xarML('Search');
    } 

    $args['name'] = xarUserGetVar('name');

    $args['blockid'] = $blockinfo['bid'];
    if (empty($blockinfo['template'])) {
        $template = 'search';
    } else {
        $template = $blockinfo['template'];
    }
    $blockinfo['content'] = xarTplBlock('search', $template, $args);

    return $blockinfo;
} 

?>
