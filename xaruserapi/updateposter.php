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
 * Standard function to create or update a posters topic and reply count
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_userapi_updateposter($args)
{
    extract($args);

    if (empty($uid) || !is_numeric($uid)) {
        $uid = xarUserGetVar('uid');
    }

    $numreplies = xarModAPIFunc('crispbb', 'user', 'countposts',
        array('powner' => $uid, 'pstatus' => 0, 'tstatus' => array(0,1)));
    $numtopics = xarModAPIFunc('crispbb', 'user', 'counttopics',
        array('towner' => $uid, 'tstatus' => array(0,1)));
    $numreplies = !empty($numreplies) ? $numreplies - $numtopics : 0;

    // TODO : do we want to keep track of deleted and submitted topics and replies?
    // (I think submitted might be useful at least)

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $posterstable = $xartable['crispbb_posters'];

    // see if we already created an entry for this user
    if (!xarModAPIFunc('crispbb', 'user', 'getposter',
        array('uid' => $uid))) {
        // create poster
        $query = "INSERT INTO $posterstable (
                  xar_uid,
                  xar_numtopics,
                  xar_numreplies
                  )
                VALUES (?,?,?)";
        $bindvars = array();
        $bindvars[] = $uid;
        $bindvars[] = $numtopics;
        $bindvars[] = $numreplies;
        $result = &$dbconn->Execute($query,$bindvars);
        if (!$result) return;
        $uid = $dbconn->PO_Insert_ID($posterstable, 'xar_uid');
        return $uid;
    } else {
        // update poster
        $set = array();
        $bindvars = array();
        $set[] = 'xar_numtopics = ?';
        $bindvars[] = $numtopics;
        $set[] = 'xar_numreplies = ?';
        $bindvars[] = $numreplies;
        $query = "UPDATE $posterstable";
        $query .= " SET " . join(',', $set);
        $query .= " WHERE xar_uid = ?";
        $bindvars[] = $uid;
        $result = &$dbconn->Execute($query,$bindvars);
        if (!$result) return;
    }

    return true;
}
?>