<?php
/**
 * Bookmarks Block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage My Bookmarks module
 * @author Scot Gardner
*/

/**
 * initialise block
 */
function mybookmarks_bookmarksblock_init()
{
    return true;
}

/**
 * get information on block
 *
 * @author  Jim McDonald
 * @access  public
 * @param   none
 * @return  data array
 * @throws  no exceptions
 * @todo    nothing
*/
function mybookmarks_bookmarksblock_info()
{
    // Values
    return array('text_type' => 'mybookmarks',
                 'module' => 'mybookmarks',
                 'text_type_long' => 'My Bookmarks',
                 'allow_multiple' => false,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false); }

/**
 * display mybookmarks block
 *
 * @author  Jim McDonald, Scot Gardner, John Cox
 * @access  public
 * @param   none
 * @return  data array on success or void on failure
 * @throws  no exceptions
 * @todo    implement centre menu position
*/
function mybookmarks_bookmarksblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('Viewmybookmarks',0)) return;

    // Get current content
    if (!empty($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = array();
    }

    if (!xarUserIsLoggedIn()) {
        return;
    }

    $args['uid'] = xarUserGetVar('uid');
    $args['url'] = xarServerGetCurrentURL();

    if (empty($blockinfo['title'])){
        $blockinfo['title'] = xarML('My Bookmarks');
    }

    // Used in the templates.
    $args['blockid'] = $blockinfo['bid'];
    $blockinfo['content'] = $args;
    return $blockinfo;
}
?>