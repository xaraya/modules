<?php
/**
 * File: $Id$
 * 
 * Get all forums
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
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
    if(!xarSecurityCheck('ViewxarBB',1,'Forum')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xbbforumstable = $xartable['xbbforums'];

    // Get links
    $query = "SELECT xar_fid,
                   xar_fname,
                   xar_fdesc,
                   xar_ftopics,
                   xar_fposts,
                   xar_fposter,
                   xar_fpostid,
                   xar_fstatus
            FROM $xbbforumstable";
    if (!empty($catid) && xarModIsHooked('categories','xarbb',1)) {
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                       array('cids' => array($catid),
                                            'modid' => xarModGetIDFromName('xarbb')));
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
    }
    $query .= " ORDER BY xar_fname";

    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    $forums = array();
    for (; !$result->EOF; $result->MoveNext()) {
        list($fid, $fname, $fdesc, $ftopics, $fposts, $fposter, $fpostid, $fstatus) = $result->fields;
        if (xarSecurityCheck('ViewxarBB', 0,'Forum',"$fid:All")) {
            $forums[] = array('fid'     => $fid,
                              'fname'   => $fname,
                              'fdesc'   => $fdesc,
                              'ftopics' => $ftopics,
                              'fposts'  => $fposts,
                              'fposter' => $fposter,
                              'fpostid' => $fpostid,
                              'fstatus' => $fstatus);
        }
    }

    $result->Close();

    return $forums;
}

?>
