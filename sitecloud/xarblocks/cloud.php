<?php
/**
 * File: $Id$
 * 
 * Xaraya Site Cloud
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Site Cloud Module
 * @author John Cox
*/

/**
 * Block init - holds security.
 */
function sitecloud_cloudblock_init()
{
    return true;
}

/**
 * Block info array
 */
function sitecloud_cloudblock_info()
{
    return array('text_type' => 'cloud',
         'text_type_long' => 'Site Cloud',
         'module' => 'sitecloud',
         'func_update' => 'sitecloud_cloudblock_insert',
         'allow_multiple' => true,
         'form_content' => false,
         'form_refresh' => false,
         'show_preview' => true);
}

/**
 * Display func.
 * @param $blockinfo array containing title,content
 */
function sitecloud_cloudblock_display($blockinfo)
{
    // Break out options from our content field
    $vars = unserialize($blockinfo['content']);
    $blockinfo['content'] = '';

    // The user API function is called
    $links = xarModAPIFunc('sitecloud',
                           'user',
                           'getall',
                           array( 'startnum' => 1,
                                  'numitems' => $vars['limit']));

    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        //$link['udated'] = time() - $link['date'];
        $links[$i]['updated'] = trim(xarLocaleFormatDate("%a, %d %b %Y %H:%M:%S %Z",($link['date'])));
        $links[$i]['when']    = time() - $link['date'];
    }
    $data['links'] = $links;

    if (empty($blockinfo['template'])) {
        $template = 'cloud';
    } else {
        $template = $blockinfo['template'];
    }
    $blockinfo['content'] = xarTplBlock('sitecloud',$template,  array('links'  => $data['links'],
                                                                      'blockid'      => $blockinfo['bid']));
    return $blockinfo;

}

/**
 * Updates the Block config from the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function sitecloud_cloudblock_insert($blockinfo) 
{
    xarVarFetch('limit', 'id', $vars['limit'], '10', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);
    // Define a default block title
    if (empty($blockinfo['title'])) {
        $blockinfo['title'] = xarML('sitecloud');
    }

    $blockinfo['content']= serialize($vars);
    return $blockinfo;
}

/**
 * Modify Function to the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function sitecloud_cloudblock_modify($blockinfo)
{
    // Break out options from our content field
    $vars = unserialize($blockinfo['content']);

    if (empty($vars['limit'])) {
        $vars['limit'] = 10;
    }

    $vars['blockid'] = $blockinfo['bid'];
    $content = xarTplBlock('sitecloud','cloudAdmin', $vars);
    return $content;
}

?>