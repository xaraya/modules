<?php
/**
 * File: $Id$
 *
 * Bookmarks Block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage My Bookmarks module
 * @author Scot Gardner
*/


function mybookmarks_bookmarksblock_init() 
{
  $mod = xarRequestGetVar('module');
    if($mod == "roles")
    {
        return true;
    }
    else
    {
        return false;
     }
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
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false); }

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
function mybookmarks_bookmarksblock_display($blockinfo)
{

    $mod = xarRequestGetVar('module');
    if($mod == "roles" || $mod == "mybookmarks"){
    // Security check
    if(!xarSecurityCheck('Viewmybookmarks', 0)) return;
    }
    else
    {
    return;
    }

    // Database information
    xarModDBInfoLoad('mybookmarks');
    $dbconn =& xarDBGetConn();
    $xartable =xarDBGetTables();
    $mybookmarkstable = $xartable['mybookmarks'];
    $prefix = xarDBGetSiteTablePrefix();


    $blockinfo = array();


    // Query
    $total_result = $dbconn->Execute("SELECT count(".$prefix."_bm_id) FROM $mybookmarkstable");
    list($total) = $total_result->fields;
    if ($dbconn->ErrorNo() != 0) {
        return;
    }

    if (empty($total)){
        $blockinfo['emptymarks'] = xarML('No Bookmarks in Database');
        $blockinfo['content'] = 'No Bookmarks in Database';
      if (empty($blockinfo['title'])){
            $blockinfo['title'] = xarML('My Bookmarks');
        }
         return $blockinfo;
    }
    $uid = xarUserGetVar('uid');
    $bookmarks = array();
    if ($total <= 1){
        $query = "SELECT ".$prefix."_bm_id,
                         ".$prefix."_user_name,
                         ".$prefix."_bm_name,
                         ".$prefix."_bm_url
                         FROM $mybookmarkstable
                         WHERE ".$prefix."_user_name = $uid
                         ORDER by ".$prefix."_bm_id";
    }
    else {
         $query = "SELECT ".$prefix."_bm_id,
                          ".$prefix."_user_name,
                          ".$prefix."_bm_name,
                          ".$prefix."_bm_url
                          FROM $mybookmarkstable
                          WHERE ".$prefix."_user_name = $uid
                          ORDER by ".$prefix."_bm_name";
    }
    $result = $dbconn->Execute($query);
    for (; !$result->EOF; $result->MoveNext()) {
        list($bm_id, $user_name, $bm_name, $bm_url) = $result->fields;
        $bookmarks[] = array('bm_id' => $bm_id,
                             'user_name' => $user_name,
                             'bm_name' => $bm_name,
                             'bm_url' => $bm_url);
        }


    $blockinfo['content']['bookmarks'] = $bookmarks;

    if (empty($blockinfo['title'])){
        $blockinfo['title'] = xarML('My Bookmarks');
    }

    return $blockinfo;
}

?>
