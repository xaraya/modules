<?php

function navigator_userapi_count_articles_bycat($args)
{
    extract ($args);

    // Database information
    $dbconn    =& xarDBGetConn();
    $xartables =& xarDBGetTables();

    // Need this to ensure that xartables is populated
    // with the categories and articles table information
    xarModAPILoad('categories');
    xarModAPILoad('articles');

    // Get the field names and LEFT JOIN ... ON ... parts from articles
    // By passing on the $args, we can let leftjoin() create the WHERE for
    // the articles-specific columns too now
    $artTable     = $xartables['articles'];
    $catTable     = $xartables['categories'];
    $catLinkTable = $xartables['categories_linkage'];

    $articlesId   = xarModGetIDFromName('articles');
    $now          = time();

    $current_cids = xarModAPIFunc('navigator', 'user', 'get_current_cats');

    if (empty($current_cids)) {
        return array();
    } else {
        extract($current_cids);
    }

    // Get the cat ids from the cache and
    // make sure there is something to work with
    if (xarModGetVar('navigator', 'style.matrix')) {
        $matrix = TRUE;
    } else {
        $matrix = FALSE;
    }

    if ($matrix) {

        $tmpList = @unserialize(xarModGetVar('navigator', 'categories.list.secondary'));
        if (is_array($tmpList)&& count($tmpList)) {
            xarModAPIFunc('navigator', 'user', 'nested_tree_flatten', &$tmpList);
            foreach ($tmpList as $key => $item) {
                if (!isset($secondary_list)) {
                    $secondary_list = $item['cid'];
                } else {
                    $secondary_list .= ',' . $item['cid'];
                }
            }
        } else {
            $secondary_list = '0';
        }

        $query = "SELECT pri.xar_cid as primary,
                         sec.xar_cid as secondary,
                         COUNT(DISTINCT $artTable.xar_aid) AS total
                    FROM $artTable
               LEFT JOIN $catLinkTable as pri
                         ON pri.xar_iid = xar_articles.xar_aid
               LEFT JOIN $catLinkTable as sec
                         ON pri.xar_iid = sec.xar_iid AND pri.xar_modid = sec.xar_modid
                   WHERE $artTable.xar_status IN (3, 2)
                         AND $artTable.xar_pubdate < ?
                         AND pri.xar_modid = ?
                         AND pri.xar_cid = ?
                         AND sec.xar_cid IN ($secondary_list)
               GROUP BY  pri.xar_cid, sec.xar_cid";

        $query_args = array($now, $articlesId, $primary['id']);

    } else {

        $tmpList = @unserialize(xarModGetVar('navigator', 'categories.list.primary'));
        if (is_array($tmpList)&& count($tmpList)) {
            xarModAPIFunc('navigator', 'user', 'nested_tree_flatten', &$tmpList);
            foreach ($tmpList as $key => $item) {
                if (!isset($primary_list)) {
                    $primary_list = $item['cid'];
                } else {
                    $primary_list .= ',' . $item['cid'];
                }
            }
        } else {
            $primary_list = '0';
        }

        $query = "SELECT catlink.xar_cid as primary,
                         COUNT(DISTINCT art.xar_aid) AS total
                    FROM $artTable as art
               LEFT JOIN $catLinkTable AS catlink
                         ON catlink.xar_iid = art.xar_aid
                   WHERE art.xar_status IN (3, 2)
                         AND art.xar_pubdate < ?
                         AND catlink.xar_modid = ?
                         AND catlink.xar_cid IN ($primary_list)
                GROUP BY catlink.xar_cid";

        $query_args = array($now, $articlesId);
    }

    // Run the query - finally :-)
    $result =& $dbconn->Execute($query, $query_args);
    if (!$result) return;

    if ($result->EOF) {
        return array();
    }

    $list = array();

    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);

        if ($matrix) {
            $sec = &$row['secondary'];
            $total = &$row['total'];

            $list[$sec] = $total;
        } else {
            $pri = &$row['primary'];
            $total = &$row['total'];

            $list[$pri] = $total;
        }

        $result->MoveNext();
    }

    $result->Close();

    return $list;
}

?>
