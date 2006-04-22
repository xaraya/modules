<?php
/**
 * Get all topics in a forum
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
 * get all topics
 *
 * @param $args['fid'] forum id, or
 * @param $args['tids'] array of topic ids
 * @returns array
 * @return array of links, or false on failure
 */
function xarbb_userapi_getalltopics_byip($args)
{
    extract($args);

     // Optional argument
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    } 
    if (empty($cids)) {
        $cids = array();
    }

    if (empty($ip)) {
        $msg = xarML('Invalid Parameter Count', '', 'userapi', 'getalltopics_byuid', 'xarbb');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xbbtopicstable = $xartable['xbbtopics'];
    $xbbforumstable = $xartable['xbbforums'];

    if (!xarModAPILoad('categories', 'user')) return;

    // Get link
    $categoriesdef = xarModAPIFunc(
        'categories','user','leftjoin',
        array('cids' => $cids, 'modid' => xarModGetIDFromName('xarbb'))
    );
  
    // CHECKME: this won't work for forums that are assigned to more (or less) than 1 category
    // Do we want to support that in the future ?
    // make only one query to speed up
    // Get links
    $query = "SELECT xar_tposter, xar_thostname FROM $xbbtopicstable ";

    // Get by UID
    $query .= "WHERE $xbbtopicstable.xar_thostname = ?";
    $bindvars = array($ip);

    // FIXME we should add possibility change sorting order
    $query .= " ORDER BY xar_ttime DESC";

    // Need to run the query and add $numitems to ensure pager works
    if (isset($numitems) && is_numeric($numitems)) {
        $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1,$bindvars);
    } else {
        $result =& $dbconn->Execute($query,$bindvars);
    }

    $topics = array();
    for (; !$result->EOF; $result->MoveNext()) {
        list($tposter, $thostname) = $result->fields;

        $topics[] = array('tposter'    => $tposter, 'thostname'  => $thostname);
    }

    $result->Close();
    return $topics;
}

?>