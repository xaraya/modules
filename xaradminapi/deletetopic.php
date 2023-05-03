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
function crispbb_adminapi_deletetopic($args)
{

    extract($args);
    if (!isset($tid) || !is_numeric($tid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('topic ID', 'admin', 'deletetopic', 'crispbb');
        throw new BadParameterException($vars, $msg);
        return;
    }

    $topic = xarMod::apiFunc('crispbb', 'user', 'gettopic', array('tid' => $tid));

    if (empty($topic['purgetopicurl'])) {
        $errorMsg['message'] = xarML('You do not have the privileges required for this action');
        $errorMsg['return_url'] = xarController::URL('crispbb', 'user', 'forum_index');
        $errorMsg['type'] = 'NO_PRIVILEGES';
        $errorMsg['pageTitle'] = xarML('No Privileges');
        xarTpl::setPageTitle(xarVar::prepForDisplay($errorMsg['pageTitle']));
        return xarTpl::module('crispbb', 'user', 'error', $errorMsg);
    }

    $posts = xarMod::apiFunc('crispbb', 'user', 'getposts', array('tid' => $tid, 'pstatus' => array(0,1,5)));
    $pids = !empty($posts) ? array_keys($posts) : array();


    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $topicstable = $xartable['crispbb_topics'];
    $poststable = $xartable['crispbb_posts'];
    $hookstable = $xartable['crispbb_hooks'];

    // remove posts
    if (!empty($pids)) {
        $query = "DELETE FROM $poststable WHERE id IN (" . join(',', $pids) . ")";
        $result = $dbconn->Execute($query,array());
        if (!$result) return;
        $item = array();
        $item['module'] = 'crispbb';
        foreach ($posts as $pid => $post) {
            $item['itemtype'] = $post['poststype'];
            $item['itemid'] = $post['pid'];
            xarModHooks::call('item', 'delete', $post['pid'], $item);
        }
    }

    // remove topic
    // first from topics table
    $query = "DELETE FROM $topicstable WHERE id = " . $tid;
    $result = $dbconn->Execute($query,array());
    if (!$result) return;
    // then from hooks table
    $query = "DELETE FROM $hookstable WHERE tid = " . $tid;
    $result = $dbconn->Execute($query,array());
    if (!$result) return;

    $item['module'] = 'crispbb';
    $item['itemtype'] = $topic['topicstype'];
    $item['itemid'] = $tid;
    xarModHooks::call('item', 'delete', $tid, $item);

    /* Let the calling process know that we have finished successfully */
    return true;
}
?>