<?php
/**
 * File: $Id$
 *
 * Random Bookmarks Block
 *
  *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage My Bookmarks module
 * @author Scot Gardner
*/

/**
 * initialise block
 *
 * @author  Jim McDonald
 * @access  public
 * @param   none
 * @return  nothing
 * @throws  no exceptions
 * @todo    nothing
*/
function mybookmarks_randombookmarksblock_init()
{
    // Security
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
function mybookmarks_randombookmarksblock_info()
{
    // Values
    return array('text_type' => 'mybookmarks',
                 'module' => 'mybookmarks',
                 'text_type_long' => 'Random Bookmark',
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false);
}

/**
 * display adminmenu block
 *
 * @author Jim McDonald, Scot Gardner, John Cox
 * @access  public
 * @param   none
 * @return  data array on success or void on failure
 * @throws  no exceptions
 * @todo    implement centre menu position
*/
function mybookmarks_randombookmarksblock_display($blockinfo)
{
    // Security check
    if(!xarSecurityCheck('Viewmybookmarks', 0)) return;

    // Get variables from content block
//    $vars = @unserialize($data['content']);
    $data = array();

    // Database information
    xarModDBInfoLoad('mybookmarks');
    $dbconn =& xarDBGetConn();
    $xartable =xarDBGetTables();
    $mybookmarkstable = $xartable['mybookmarks'];

    // Query
    mt_srand((double)microtime()*1000000);
    $total_result = $dbconn->Execute("SELECT count(xar_bm_id) FROM $mybookmarkstable");
    list($total) = $total_result->fields;
    if ($dbconn->ErrorNo() != 0) {
        return;
    }

    if (empty($total)){
        $emptymessage = xarML('No Bookmarks in Database');
        if (empty($blockinfo['template'])) {
            $template = 'mybookmarks';
        } else {
            $template = $blockinfo['template'];
        }
        $data['content'] = xarTplBlock('mybookmarks', $template, array('title' => '',
                                                                            'user_name' => '',
                                                                            'bm_url' => '',
                                                                            'content' => '',
                                                                            'nobookmarks' => $emptymessage));

        if (empty($blockinfo['title'])){
            $blockinfo['title'] = xarML('Random Bookmark');
        }
        $blockinfo['content'] = $data;
        return $blockinfo;
    }

    if ($total <= 1){
        $query = "SELECT xar_bm_id,
                         xar_user_name,
                         xar_bm_name,
                         xar_bm_url
                         FROM $mybookmarkstable
                         ORDER by xar_bm_id";
        $result = $dbconn->Execute($query);
        while(list($bm_id, $user_name, $bm_name, $bm_url) = $result->fields) {
            $result->MoveNext();
            if (empty($blockinfo['template'])) {
                $template = 'mybookmarks';
            } else {
                $template = $blockinfo['template'];
            }
            $data['content'] = xarTplBlock('mybookmarks',$template, array('title' => $blockinfo['title'],
                                                                         'bm_id' => $bm_id,
                                                                         'user_name' => $user_name,
                                                                         'bm_name' => $bm_name,
                                                                         'bm_url' => $bm_url,
                                                                         'content' => $content));
    }

    } else {
        $p = mt_rand(0, ($total - 1));


        $query = "SELECT xar_bm_id,
                         xar_user_name,
                         xar_bm_name,
                         xar_bm_url
                         FROM $mybookmarkstable
                         ORDER by xar_bm_id";
        $result = $dbconn->SelectLimit($query, 1,$p);
        while(list($bm_id, $user_name, $bm_name, $bm_url) = $result->fields) {
            $result->MoveNext();
            if (empty($blockinfo['template'])) {
                $template = 'RandomBookmarks';
            } else {
                $template = $blockinfo['template'];
            }
            $data = xarTplBlock('mybookmarks',$template, array('title' => $blockinfo['title'],
                                                                         'bm_id' => $bm_id,
                                                                         'user_name' => $user_name,
                                                                         'bm_name' => $bm_name,
                                                                         'bm_url' => $bm_url));
        }
    }

    if (empty($blockinfo['title'])){
        $blockinfo['title'] = xarML('Random Bookmark');
    }
    $blockinfo['content'] = $data;
    return $blockinfo;
}

?>
