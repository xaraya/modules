<?php
/**
 * Count the number of posts for a user
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 */

function xarbb_userapi_countposts($args)
{
    static $countcache = array();
    extract($args);

    if (!isset($uid)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (isset($countcache[$uid])) {
        return $countcache[$uid];
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $xbbtopicstable = $xartable['xbbtopics'];

    $query = "SELECT COUNT(1) FROM $xbbtopicstable WHERE xar_tposter = ?";
    $result =& $dbconn->Execute($query,array((int)$uid));
    if (!$result) return;

    list($topics) = $result->fields;
    $result->Close();

    // While we are here, how many replies have been made as well?
    $replies = xarModAPIFunc(
        'comments', 'user', 'get_author_count',
        array('modid' => xarModGetIdFromName('xarbb'), 'author' => $uid)
    );

    $total = $topics + $replies;
    $countcache[$uid] = $total;

    return $total;
}

?>