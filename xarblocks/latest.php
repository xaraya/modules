<?php
/**
 * Latest Block
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsgroups
 * @author mikespub
 */

/**
 * initialise block
 */
function newsgroups_latestblock_init()
{
    return array(
        'group' => '',
        'numitems' => 5,
        'nocache' => 0, // cache by default (if block caching is enabled)
        'pageshared' => 1, // share across pages
        'usershared' => 1, // share across group members
        'cacheexpire' => 1800 // 30 minutes
    );
}

/**
 * get information on block
 */
function newsgroups_latestblock_info()
{ 
    // Values
    return array(
        'text_type' => 'Latest',
        'module' => 'newsgroups',
        'text_type_long' => 'Show latest messages from a newsgroup',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
} 

/**
 * display block
 */
function newsgroups_latestblock_display($blockinfo)
{ 
    // Security check
    if (!xarSecurityCheck('ReadNewsGroups',0)) return;

    // Get variables from content block.
    // Content is a serialized array for legacy support, but will be
    // an array (not serialized) once all blocks have been converted.
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    if (empty($vars['group'])) {
        // nothing to show here
        return;
    } 

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    } 

    $data = xarModAPIFunc('newsgroups','user','getoverview',
                          array('group'    => $vars['group'],
                                'numitems' => $vars['numitems'],
                                'sortby'   => 'article'));
    if (!isset($data)) return;

    $data['blockid'] = $blockinfo['bid'];

    // Now we need to send our output to the template.
    // Just return the template data.
    $blockinfo['content'] = $data;

    return $blockinfo;
} 

?>
