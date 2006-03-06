<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
/**
 * get all headlines
 * @return array of links, or false on failure
 */

function headlines_userapi_getall($args)
{
    extract($args);

    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $links = array();

    // Security Check
    if(!xarSecurityCheck('OverviewHeadlines')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $headlinestable = $xartable['headlines'];

    // Get links
    $query = "SELECT xar_hid,
                     xar_title,
                     xar_desc,
                     xar_url,
                     xar_order,
                     xar_string,
                     xar_date
            FROM $headlinestable";

    if (!empty($catid) && xarModIsHooked('categories','headlines')) {
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                       array('cids' => array($catid),
                                            'modid' => xarModGetIDFromName('headlines')));
        if (!empty($categoriesdef)) {
            $query .= ' LEFT JOIN ' . $categoriesdef['table'];
            $query .= ' ON ' . $categoriesdef['field'] . ' = xar_hid';
            if (!empty($categoriesdef['more'])) {
                $query .= $categoriesdef['more'];
            }
            if (!empty($categoriesdef['where'])) {
                $query .= ' WHERE ' . $categoriesdef['where'];
            }
        }
    }

    $query .= " ORDER BY xar_order";

    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($hid, $title, $desc, $url, $order, $string, $date) = $result->fields;
        if (xarSecurityCheck('OverviewHeadlines')) {
            $links[] = array('hid'      => $hid,
                             'title'    => $title,
                             'desc'     => $desc,
                             'url'      => $url,
                             'order'    => $order,
                             'string'   => $string,
                             'date'     => $date);
        }
    }
    $result->Close();
    return $links;
}
?>