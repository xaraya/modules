<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 *//**
 * Standard function to delete a forum
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_adminapi_delete($args)
{

    extract($args);
    if (!isset($fid) || !is_numeric($fid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'forum ID', 'admin', 'delete', 'crispbb');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $forum = xarModAPIFunc('crispbb', 'user', 'getforum', array('fid' => $fid));
    $topics = xarModAPIFunc('crispbb', 'user', 'gettopics', array('fid' => $fid));
    $tids = !empty($topics) ? array_keys($topics) : array();
    $posts = xarModAPIFunc('crispbb', 'user', 'getposts', array('fid' => $fid));
    $pids = !empty($posts) ? array_keys($posts) : array();

    //if (!xarSecurityCheck('DeleteExample', 1, 'Item', "$item[name]:All:$exid")) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $forumstable = $xartable['crispbb_forums'];
    $topicstable = $xartable['crispbb_topics'];
    $poststable = $xartable['crispbb_posts'];
    $itemtypestable = $xartable['crispbb_itemtypes'];
    $hookstable = $xartable['crispbb_hooks'];

    // remove posts
    if (!empty($pids)) {
        $query = "DELETE FROM $poststable WHERE xar_pid IN (" . join(',', $pids) . ")";
        $result = &$dbconn->Execute($query,array());
        if (!$result) return;
    }

    // remove topics
    if (!empty($tids)) {
        // first from topics table
        $query = "DELETE FROM $topicstable WHERE xar_tid IN (" . join(',', $tids) . ")";
        $result = &$dbconn->Execute($query,array());
        if (!$result) return;
        // then from hooks table
        $query = "DELETE FROM $hookstable WHERE xar_tid IN (" . join(',', $tids) . ")";
        $result = &$dbconn->Execute($query,array());
        if (!$result) return;
    }

    // remove forum itemtype
    $query = "DELETE FROM $itemtypestable WHERE xar_fid = ? AND xar_component = 'Forum'";
    $result = &$dbconn->Execute($query,array($fid));
    if (!$result) return;

    // finally, remove the forum itself
    $query = "DELETE FROM $forumstable WHERE xar_fid = ?";
    $result = &$dbconn->Execute($query,array($fid));
    if (!$result) return;


    $item['module'] = 'crispbb';
    $item['itemtype'] = $forum['itemtype'];
    $item['itemid'] = $fid;
    xarModCallHooks('item', 'delete', $fid, $item);

    // TODO: call some kind of itemtype delete hooks here, once we have those
    //xarModCallHooks('itemtype', 'delete', $forum['itemtype'],
    //                array('module' => 'crispbb',
    //                      'itemtype' => $forum['itemtype']));

    /* Let the calling process know that we have finished successfully */
    return true;
}
?>