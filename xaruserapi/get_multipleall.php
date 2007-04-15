<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Get a list of comments from one or several modules + item types
 *
 * @author Andrea Moro modified from Carl P. Corliss (aka rabbitt) userapi
 * @access public
 * @param array    $modarray   array of module names + itemtypes to look for
 * @param string   $order      sort order (ASC or DESC date)
 * @param integer  $howmany    number of comments to retrieve
 * @param integer  $first      start number
 * @returns array     an array of comments or an empty array if no comments
 *                   found for the particular modules, or raise an
 *                   exception and return false.
 */
function comments_userapi_get_multipleall($args)
{
    extract($args);
    // $modid
    if (!isset($modarray) || empty($modarray) || !is_array($modarray)) {
        $modarray=array('all');
    }
    if (empty($order) || $order != 'ASC') {
        $order = 'DESC';
    } else {
        $order = 'ASC';
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ctable = &$xartable['comments_column'];
    $commentlist = array();

    $query = "SELECT  $ctable[title] AS subject,
                      $ctable[comment] AS text,
                      $ctable[cdate] AS datetime,
                      $ctable[author] AS author,
                      $ctable[id] AS id,
                      $ctable[status] AS status,
                      $ctable[postanon] AS postanon,
                      $ctable[modid] AS modid,
                      $ctable[itemtype] AS itemtype,
                      $ctable[objectid] AS objectid
                FROM  $xartable[comments]
               WHERE  $ctable[status]="._COM_STATUS_ON." ";

    if (count($modarray) > 0 && $modarray[0] != 'all' ) {
        $where = array();
        foreach ($modarray as $modname) {
            if (strstr($modname,'.')) {
                list($module,$itemtype) = explode('.',$modname);
                $modid = xarModGetIDFromName($module);
                if (empty($itemtype)) {
                    $itemtype = 0;
                }
                $where[] = "($ctable[modid] = $modid AND $ctable[itemtype] = $itemtype)";
            } else {
                $modid = xarModGetIDFromName($modname);
                $where[] = "($ctable[modid] = $modid)";
            }
        }
        if (count($where) > 0) {
            $query .= " AND ( " . join(' OR ', $where) . " ) ";
        }
    }

    $query .= " ORDER BY datetime $order ";

    if (empty($howmany) || !is_numeric($howmany)) {
        $howmany = 5;
    }
    if (empty($first) || !is_numeric($first)) {
        $first = 1;
    }

    $result = $dbconn->SelectLimit($query, $howmany, $first - 1);
    if (!$result) return;

    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        // FIXME delete after date output testing
        // $row['date'] = xarLocaleFormatDate("%B %d, %Y %I:%M %p",$row['datetime']);
        $row['date'] = $row['datetime'];
        $row['author'] = xarUserGetVar('name',$row['author']);
        $commentlist[] = $row;
        $result->MoveNext();
    }
    $result->Close();

    return $commentlist;
}

?>
