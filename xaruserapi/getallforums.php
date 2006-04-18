<?php
/**
 * Get all forums
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
 * get all forums
 * @returns array
 * @return array of links, or false on failure
 */
function xarbb_userapi_getallforums($args)
{
    extract($args);

    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    // Security Check
    if (!xarSecurityCheck('ViewxarBB', 1, 'Forum')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $xbbforumstable = $xartable['xbbforums'];

    // FIXME: assume we will always be hooked to categories
    // FIXME: We can also only deal with ONE category assigned to a forum.
    $categoriesdef = xarModAPIFunc(
        'categories','user','leftjoin',
        array('cids' => (!empty($catid) ? array($catid) : array()), 'modid' => xarModGetIDFromName('xarbb'))
    );

    // Get links
    //<jojodee> Make sure we only get forums itemtype=1 else duplicates bug #2335 revisited
    //Fix for older xarbb versions
    $query = "SELECT DISTINCT xar_fid, xar_fname, xar_fdesc, xar_ftopics,"
        . " xar_fposts, xar_fposter, xar_fpostid, xar_fstatus, xar_foptions,"
        . " xar_forder, " . $categoriesdef['cid']
        . " FROM $xbbforumstable";

    if (!empty($categoriesdef)) {
        $query .= ' LEFT JOIN ' . $categoriesdef['table'];
        $query .= ' ON ' . $categoriesdef['field'] . ' = xar_fid';

        if (!empty($categoriesdef['more'])) {
            $query .= $categoriesdef['more'];
        }

        if (!empty($categoriesdef['where'])) {
            $query .= ' WHERE ' . $categoriesdef['where'];
        }
    }

    $query .= " ORDER BY xar_forder"; // Set her3 to ensure display of forum ordering by this column 

    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    $forums = array();
    for (; !$result->EOF; $result->MoveNext()) {
        list($fid, $fname, $fdesc, $ftopics, $fposts, $fposter, $fpostid, $fstatus, $foptions, $forder, $cid) = $result->fields;

        //if ((empty($catid) && xarSecurityCheck('ViewxarBB', 0, 'Forum', "All:$fid")) || (!empty($catid) && xarSecurityCheck('ViewxarBB', 0, 'Forum', "$catid:$fid"))) {
        if (xarSecurityCheck('ViewxarBB', 0, 'Forum', "$cid:$fid")) {
            $forums[] = array(
                'fid'     => $fid,
                'fname'   => $fname,
                'fdesc'   => $fdesc,
                'ftopics' => $ftopics,
                'fposts'  => $fposts,
                'fposter' => $fposter,
                'fpostid' => $fpostid,
                'fstatus' => $fstatus,
                'foptions'=> $foptions,
                'forder'  => $forder,
                'cid'     => $cid,
            );
        }
    }
    $result->Close();

    return $forums;
}

?>