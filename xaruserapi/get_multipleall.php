<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
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

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $commentlist = array();

    $query = "SELECT  title AS subject,
                      comment AS text,
                      date AS datetime,
                      author AS author,
                      id]AS id,
                      status AS status,
                      postanon AS postanon,
                      modid AS modid,
                      itemtype AS itemtype,
                      objectid AS objectid
                FROM  $xartable[comments]
               WHERE  status="._COM_STATUS_ON." ";

    if (count($modarray) > 0 && $modarray[0] != 'all' ) {
        $where = array();
        foreach ($modarray as $modname) {
            if (strstr($modname,'.')) {
                list($module,$itemtype) = explode('.',$modname);
                $modid = xarMod::getRegID($module);
                if (empty($itemtype)) {
                    $itemtype = 0;
                }
                $where[] = "(modid = $modid AND itemtype = $itemtype)";
            } else {
                $modid = xarMod::getRegID($modname);
                $where[] = "(modid = $modid)";
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
