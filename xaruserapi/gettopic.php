<?php

/**
 * Get info for a specific forum topic
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
 * get a specific link
 * @poaram $args['tid'] id of topic to get
 * @returns array
 * @return link array, or false on failure
 */

function xarbb_userapi_gettopic($args)
{
    extract($args);

    if (!isset($tid)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $xbbtopicstable = $xartable['xbbtopics'];
    $xbbforumstable = $xartable['xbbforums'];

    // Get link
    $categoriesdef = xarModAPIFunc(
        'categories','user','leftjoin',
        array('cids' => array(), 'modid' => xarModGetIDFromName('xarbb'))
    );
    if (empty($categoriesdef)) return;

    // CHECKME: this won't work for forums that are assigned to more (or less) than 1 category
    //          Do we want to support that in the future ?
    // make only one query to speed up
    // Get links
    $query = "SELECT xar_tid, $xbbtopicstable.xar_fid, xar_ttitle, xar_tpost, xar_tposter, xar_ttime,"
        . " xar_tftime, xar_treplies, xar_treplier, xar_tstatus, xar_thostname, xar_toptions,"
        . " xar_fname, xar_fdesc, xar_ftopics, xar_fposts, xar_fposter, xar_fpostid, xar_fstatus,"
        . " {$categoriesdef['cid']}"
        . " FROM $xbbtopicstable LEFT JOIN $xbbforumstable ON $xbbtopicstable.xar_fid = $xbbforumstable.xar_fid"
        . " LEFT JOIN {$categoriesdef['table']} ON {$categoriesdef['field']} = $xbbforumstable.xar_fid"
        . " {$categoriesdef['more']}"
        . " WHERE {$categoriesdef['where']} AND xar_tid = ?";

    $result =& $dbconn->Execute($query, array($tid));
    if (!$result) return;

    if ($result->EOF) {
        $msg = xarML('Topic with ID #(1) does not exist',$tid);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST', new SystemException($msg));
        return;
    }
    list($tid, $fid, $ttitle, $tpost, $tposter, $ttime, $tftime, $treplies,$treplier, $tstatus, $thostname, $toptions, $fname, $fdesc, $ftopics, $fposts, $fposter, $fpostid, $fstatus, $catid) = $result->fields;
    $result->Close();

    // Bug 4307
    // if (!xarSecurityCheck('ReadxarBB',0,'Forum',"$catid:$fid")) return;
    $topic = array(
        'tid'        => $tid,
        'fid'        => $fid,
        'ttitle'     => $ttitle,
        'tpost'      => $tpost,
        'tposter'    => $tposter,
        'ttime'      => $ttime,
        'tftime'     => $tftime,
        'treplies'   => $treplies,
        'tstatus'    => $tstatus,
        'treplier'   => $treplier,
        'thostname'  => $thostname,
        'toptions'   => $toptions,
        'fname'      => $fname,
        'fdesc'      => $fdesc,
        'ftopics'    => $ftopics,
        'fposts'     => $fposts,
        'fposter'    => $fposter,
        'fpostid'    => $fpostid,
        'fstatus'    => $fstatus,
        'catid'      => $catid
    );

    return $topic;
}

?>