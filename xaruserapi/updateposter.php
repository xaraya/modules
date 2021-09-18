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
        $uid = xarUser::getVar('id');
    }

    $numreplies = xarMod::apiFunc(
        'crispbb',
        'user',
        'countposts',
        ['powner' => $uid, 'pstatus' => 0, 'tstatus' => [0,1]]
    );
    $numtopics = xarMod::apiFunc(
        'crispbb',
        'user',
        'counttopics',
        ['towner' => $uid, 'tstatus' => [0,1]]
    );
    $numreplies = !empty($numreplies) ? $numreplies - $numtopics : 0;

    // TODO : do we want to keep track of deleted and submitted topics and replies?
    // (I think submitted might be useful at least)

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $posterstable = $xartable['crispbb_posters'];

    // see if we already created an entry for this user
    if (!xarMod::apiFunc(
        'crispbb',
        'user',
        'getposter',
        ['uid' => $uid]
    )) {
        // create poster
        $query = "INSERT INTO $posterstable (
                  id,
                  numtopics,
                  numreplies
                  )
                VALUES (?,?,?)";
        $bindvars = [];
        $bindvars[] = $uid;
        $bindvars[] = $numtopics;
        $bindvars[] = $numreplies;
        $result = $dbconn->Execute($query, $bindvars);
        if (!$result) {
            return;
        }
    } else {
        // update poster
        $set = [];
        $bindvars = [];
        $set[] = 'numtopics = ?';
        $bindvars[] = $numtopics;
        $set[] = 'numreplies = ?';
        $bindvars[] = $numreplies;
        $query = "UPDATE $posterstable";
        $query .= " SET " . join(',', $set);
        $query .= " WHERE id = ?";
        $bindvars[] = $uid;
        $result = $dbconn->Execute($query, $bindvars);
        if (!$result) {
            return;
        }
    }

    return true;
}
