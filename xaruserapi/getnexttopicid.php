<?php
/**
 * Get next (= more recent) topic id
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
 * get the next topic id in a forum
 * @param $args['tid'] id of previous topic, or
 * @param $args['fid'] id of the forum +
 * @param $args['ttime'] time of previous topic
 * @param $args['sort'] sort criteria in the forum (TODO - default ttime)
 * @returns array
 * @return link array, or false on failure
 */
function xarbb_userapi_getnexttopicid($args)
{
    extract($args);

    if (empty($tid) && (empty($fid) || empty($ttime))) {
        $msg = xarML('Invalid parameter count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xbbtopicstable = $xartable['xbbtopics'];

    if (!empty($fid) && !empty($ttime)) {
        $query = "SELECT xar_tid
                  FROM $xbbtopicstable
                  WHERE xar_fid = ? AND xar_ttime > ?
                  ORDER BY xar_ttime ASC";
        $result =& $dbconn->SelectLimit($query, 1, 0, array((int)$fid, (int)$ttime));
    } else {
        $query = "SELECT t1.xar_tid
                  FROM $xbbtopicstable t1, $xbbtopicstable t2
                  WHERE t2.xar_tid = ? AND t1.xar_fid = t2.xar_fid AND t1.xar_ttime > t2.xar_ttime
                  ORDER BY t1.xar_ttime ASC";
        $result =& $dbconn->SelectLimit($query, 1, 0, array((int)$tid));
    }
    if (!$result) return;

    if ($result->EOF) {
        // no next topic
        return 0;
    }

    $topicid = $result->fields[0];

    $result->Close();

    // let viewtopic worry about security check - we're only getting a topicid here
    //    if (!xarSecurityCheck('ReadxarBB',1,'Forum',"$catid:$fid")) return;

    return $topicid;
}

?>