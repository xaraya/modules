<?php
/**
 * TopBookmarks.php
 *
  *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage My Bookmarks module
 * @author Scot Gardner

/**
 * Initialise the block
 */
function mybookmarks_TopBookmarksblock_init()
{
    $statusmsg = xarSessionGetVar( 'mybookmarks_statusmsg_old' );
    return false;
}

/**
 * Show Information about the block
 */
function mybookmarks_TopBookmarksblock_info()
{
        // Values
    return array(
        'text_type' => 'TopBookmarks',
        'module'    => 'mybookmarks',
        'text_type_long' => 'Show Status Message',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true );
}

/**
 * Display the block
 */
function mybookmarks_TopBookmarksblock_display( $blockinfo )
{
    $blockinfo['content'] = array(
        'content'       => xarSessionGetVar( 'mybookmarks_statusmsg_old' )
#        ,'_bl_template'  => 'test1'
    );
    return $blockinfo;
}

/**
 * Update Block information
 */
function mybookmarks_TopBookmarksblock_help( $blockinfo )
{
    return "Hilfetext";
}

/*
 * END OF FILE
 */
?>