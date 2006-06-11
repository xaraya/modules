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
 * Get all forums
 * @returns array
 * @return array of zero or more forums, or NULL on failure
 * @param fid integer Forum ID
 * @param fname string Forum name
 * @todo Support 'cids' and 'fids' arrays, 
 */

function xarbb_userapi_getallforums($args)
{
    extract($args);

    // Optional arguments
    if (!isset($startnum)) {$startnum = 1;}
    if (!isset($numitems)) {$numitems = -1;}

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

    // <jojodee> Make sure we only get forums itemtype=1 else duplicates bug #2335 revisited
    // Fix for older xarbb versions
    $query = "SELECT DISTINCT xar_fid, xar_fname, xar_fdesc, xar_ftopics,"
        . " xar_fposts, xar_fposter, xar_fpostid, xar_fstatus, xar_foptions,"
        . " xar_forder, " . $categoriesdef['cid']
        . " FROM $xbbforumstable";

    $where = array();
    $bind = array();

    if (!empty($categoriesdef)) {
        $query .= ' LEFT JOIN ' . $categoriesdef['table'];
        $query .= ' ON ' . $categoriesdef['field'] . ' = xar_fid';

        if (!empty($categoriesdef['more'])) {
            $query .= $categoriesdef['more'];
        }

        if (!empty($categoriesdef['where'])) {
            $where[] = $categoriesdef['where'];
        }
    }

    if (!empty($fid)) {
        $where[] = 'xar_fid = ?';
        $bind[] = (int)$fid;
    }

    if (!empty($fname)) {
        $where[] = 'xar_fname = ?';
        $bind[] = (string)$fname;
    }

    if (!empty($forder)) {
        $where[] = 'xar_forder = ?';
        $bind[] = (int)$forder;
    }

    if (!empty($where)) {
        $query .= ' WHERE ' . implode(' AND ', $where);
    }

    // Set to ensure display of forum ordering by this column
    $query .= " ORDER BY xar_forder";

    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bind);
    if (!$result) return;

    // Get the default settings for all forums.
    $global_settings = xarModGetVar('xarbb', 'settings');
    if (!empty($default_settings)) {
        $global_settings = unserialize($global_settings);
    } else {
        $global_settings = array();
    }

    // Now fill in any gaps in the default settings.
    // TODO: store these centrally, for use when resetting defaults.
    // This must be the 10th place these same defaults are stored ;-)
    $default_settings = array(
        'postsperpage' => 20,
        'postsortorder' => 'ASC',
        'topicsperpage' => 20,
        'topicsortby' => 'time',
        'topicsortorder' => 'DESC',
        'hottopic' => 20,
        'allowhtml' => false,
        'allowbbcode' => true,
        'editstamp' => 0,
        'showcats' => false,
        'nntp' => '',
    );

    $global_settings = array_merge($default_settings, $global_settings);

    $forums = array();
    for (; !$result->EOF; $result->MoveNext()) {
        list($fid, $fname, $fdesc, $ftopics, $fposts, $fposter, $fpostid, $fstatus, $foptions, $forder, $catid) = $result->fields;

        if (xarSecurityCheck('ViewxarBB', 0, 'Forum', "$catid:$fid")) {
            // Get the settings for this forum
            $settings = xarModGetVar('xarbb', 'settings.' . $fid);
            if (!empty($settings)) {
                $settings = unserialize($settings);
            } else {
                $settings = array();
            }

            // Add in any missing settings, by overlaying settings onto the global settings.
            $settings = array_merge($global_settings, $settings);

// TODO: merge forums assigned to several categories
            $forums[] = array(
                'fid'     => $fid,
                'fname'   => $fname,
                'fdesc'   => $fdesc,
                'ftopics' => $ftopics,
                'fposts'  => $fposts,
                'fposter' => $fposter,
                'fpostid' => $fpostid,
                'fstatus' => $fstatus,
                'foptions'=> $foptions, // TODO: unserialize this here
                'forder'  => $forder,
                'cid'     => $catid, // Deprecated - confused with comment IDs
                'catid'   => $catid,
                'settings' => $settings,
            );
        }
    }
    $result->Close();

    return $forums;
}

?>
