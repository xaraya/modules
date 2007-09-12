<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * return the field names and correct values for joining on categories table
 * example : SELECT ..., $cid, ...
 *           FROM ...
 *           LEFT JOIN $table
 *               ON $field = <name of itemid field in your module>
 *           $more
 *           WHERE ...
 *               AND $where // this includes xar_modid = <your module ID>
 *
 * @param $args['modid'] your module ID (use xarModGetIDFromName('mymodule'))
 * @param $args['itemtype'] your item type (default is none) or array of itemtypes
 *
 * @param $args['iids'] optional array of item ids that we are selecting on
 * @param $args['cids'] optional array of cids we're counting for (OR/AND)
 * @param $args['andcids'] true means AND-ing categories listed in cids
 * @param $args['groupcids'] the number of categories you want items grouped by
 *
 * @param $args['cidtree'] get items in cid or anywhere below it (= slower than cids, usually)
 *
 * @return array('table' => 'xar_categories_linkage',
 *               'field' => 'xar_categories_linkage.xar_iid',
 *               'where' => 'xar_categories_linkage.xar_modid = ...
 *                           AND xar_categories_linkage.xar_cid IN (...)',
 *               'cid'   => 'xar_categories_linkage.xar_cid',
 *               ...
 *               'modid' => 'xar_categories_linkage.xar_modid')
 * @todo think about qstr() and bindvars here, this function return a string, so it's a bit harder
 * @todo any reason why the main join table cannot be an INNER JOIN, even if just for neatness?
 * @todo any table joined with conditions in the WHERE clause, is effectively an INNER JOIN, not a LEFT JOIN
 *
 * IMPORTANT NOTE: MySQL does not use indexes properly using the Celko model and BETWEEN. Do not
 * be tempted to replace the <= and >= conditions with BETWEEN.
 */
function categories_userapi_leftjoin($args)
{
    // Get arguments from argument array
    extract($args);

    $dbconn =& xarDBGetConn();

    // Allow cross-module queries too
    if (!empty($modid) && !is_numeric($modid)) {
        $msg = xarML('Missing parameter #(1) for #(2)', 'modid', 'categories');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return array();
    }

    // Optional argument
    if (!empty($catid)) {
        if (strpos($catid, ' ')) {
            $cids = explode(' ', $catid);
            $andcids = true;
        } elseif (strpos($catid, '+')) {
            $cids = explode('+', $catid);
            $andcids = true;
        } elseif (strpos($catid, '-')) {
            $cids = explode('-', $catid);
            $andcids = false;
        } else {
            $cids = array($catid);
            $andcids = false;
        }
    }

    if (!isset($cids)) {
        $cids = array();
    } else {
        // Make sure the cids have continuous keys - we rely on that later.
        $cids = array_values($cids);
    }

    if (!isset($iids)) $iids = array();
    if (!isset($andcids)) $andcids = false;

    // Security check
    if (!xarSecurityCheck('ViewCategoryLink')) return;

    // Dummy cids array when we're going for x categories at a time
    if (isset($groupcids) && count($cids) == 0) {
        $andcids = true;
        $isdummy = 1;
        for ($i = 0; $i < $groupcids; $i++) $cids[] = $i;
    } else {
        $isdummy = 0;
    }

    // trick : cids = array(_NN) corresponds to cidtree = NN
    if (count($cids) == 1 && preg_match('/^_(\d+)$/', reset($cids), $matches)) {
        $cidtree = $matches[1];
        $cids = array();
    }

    // Table definition
    $xartable =& xarDBGetTables();
    $categorieslinkagetable = $xartable['categories_linkage'];
    $categoriestable = $xartable['categories'];

    $leftjoin = array();

    // Create list of tables we'll be left joining for AND
    if (count($cids) > 0 && $andcids) {
        $catlinks = array();
        for ($i = 0; $i < count($cids); $i++) {
            $catlinks[] = 'catlink' . $i;
        }
        $linktable = $catlinks[0];
    } else {
        $linktable = $categorieslinkagetable;
    }

    // Add available columns in the categories table
    $columns = array('cid', 'iid', 'modid', 'itemtype');
    foreach ($columns as $column) {
        $leftjoin[$column] = $linktable . '.xar_' . $column;
    }
    $leftjoin['field'] = $leftjoin['iid'];

    $where = array();

    // Specify LEFT JOIN ... ON ... [WHERE ...] parts
    if (count($cids) > 0 && $andcids) {
        for ($i = 0; $i < count($catlinks); $i++) {
            if ($i == 0) {
                // Main table
                $leftjoin['table'] = $categorieslinkagetable . ' ' . $catlinks[0];
                $leftjoin['more'] = ' ';
                $leftjoin['cids'] = array();
                $leftjoin['cids'][$i] = $catlinks[$i] . '.xar_cid';
            } else {
                // Remaining tables.

                // This is an INNER JOIN, since we join to it later either in the WHERE-clause or another INNER JOIN
                // unless we are grouping by categories.
                $jointype = ($isdummy ? 'LEFT JOIN' : 'INNER JOIN');
                $leftjoin['more'] .=
                    ' ' . $jointype . ' ' . $categorieslinkagetable . ' ' . $catlinks[$i]
                        . ' ON ' . $leftjoin['iid'] . ' = ' . $catlinks[$i] . '.xar_iid'
                        . ' AND ' . $leftjoin['modid'] . ' = ' . $catlinks[$i] . '.xar_modid ';

                // Note: only for non-zero itemtypes here
                if (!empty($itemtype)) {
                    $leftjoin['more'] .= ' AND ' . $leftjoin['itemtype'] . ' = '
                        . $catlinks[$i] . '.xar_itemtype ';
                }
                $leftjoin['cids'][$i] = $catlinks[$i] . '.xar_cid';
            }

            if ($isdummy) {
                $lastcid = '';
                foreach ($leftjoin['cids'] as $cid) {
                    if (!empty($lastcid)) $where[] .= $lastcid . ' < ' . $cid;
                    $lastcid = $cid;
                }
            } elseif (is_numeric($cids[$i])) {
                // For the main table, use the where-clause, otherwise use the JOIN table.
                if ($i == 0) {
                    $where[] = $catlinks[$i] . '.xar_cid = ' . $cids[$i];
                } else {
                    $leftjoin['more'] .= ' AND ' . $catlinks[$i] . '.xar_cid = ' . $cids[$i];
                }
            } elseif (preg_match('/^_(\d+)$/', $cids[$i], $matches)) {
                $tmpcid = $matches[1];
                $cat = xarModAPIFunc('categories', 'user', 'getcatinfo', array('cid' => $tmpcid));
                // We want to avoid bringing in this new table if we can.
                // If the tree we are checking contains just one category (it is a leaf node)
                // then don't left join to the categories table.
                if (!empty($cat)) {
                    if (($cat['left'] + 1) != $cat['right']) {
                        // Use 'INNER JOIN' since we are limiting the categories to a single tree.
                        $leftjoin['more'] .= ' INNER JOIN ' . $categoriestable . ' cattab' . $i
                            . ' ON cattab' . $i . '.xar_cid = ' .  $catlinks[$i] . '.xar_cid '
                            . ' AND cattab' . $i . '.xar_left >= ' . $cat['left']
                            . ' AND cattab' . $i . '.xar_left <= ' . $cat['right'];
                    } else {
                        // For the main table, use the where-clause, otherwise use the JOIN table.
                        if ($i == 0) {
                            $where[] = $catlinks[$i] . '.xar_cid = ' . $tmpcid;
                        } else {
                            $leftjoin['more'] .= ' AND ' . $catlinks[$i] . '.xar_cid = ' . $tmpcid;
                        }
                    }
                }
            }
        }

        // Include all cids here
        $leftjoin['cid'] = join(', ', $leftjoin['cids']);
    } elseif (!empty($cidtree)) {
        // TODO: why is 'cidtree' special? Could it be handled as a simple '_N' category tree?
        $leftjoin['table'] = $categorieslinkagetable;
        $cat = xarModAPIFunc('categories', 'user', 'getcatinfo', array('cid' => $cidtree));

        if (($cat['left'] + 1) != $cat['right']) {
            // Always use an INNER JOIN if limiting the whole query to a tree.
            // It is equivalent to using a LEFT JOIN and then putting conditions
            // into the WHERE-clause (but this is more efficient, noticable with
            // large tables).
            $leftjoin['more'] = ' INNER JOIN ' . $categoriestable
                . ' ON ' . $categoriestable . '.xar_cid = ' .  $leftjoin['cid']
                . ' AND ' . $categoriestable . '.xar_left >= ' . $cat['left']
                . ' AND ' . $categoriestable . '.xar_left <= ' . $cat['right'];
        } else {
            $where[] = $categorieslinkagetable . '.xar_cid = ' . $cidtree;
            $leftjoin['more'] = ' ';
        }
    } else {
        $leftjoin['table'] = $categorieslinkagetable;
        $leftjoin['more'] = ' ';
    }

    // Specify the WHERE part (some parts already defined further up though)
    if (!empty($modid) && is_numeric($modid)) {
        $where[] = $leftjoin['modid'] . ' = ' . $modid;
    }

    // Note: do not default to 0 here, because we want to be able to do things across item types
    if (isset($itemtype)) {
        if (is_numeric($itemtype)) {
            $where[] = $leftjoin['itemtype'] . ' = ' . $itemtype;
        } elseif (is_array($itemtype) && count($itemtype) > 0) {
            $seentype = array();
            foreach ($itemtype as $id) {
                if (empty($id) || !is_numeric($id)) continue;
                $seentype[$id] = 1;
            }
            if (count($seentype) == 1) {
                $itemtypes = array_keys($seentype);
                $where[] = $leftjoin['itemtype'] . ' = ' . $itemtypes[0];
            } elseif (count($seentype) > 1) {
                $itemtypes = join(', ', array_keys($seentype));
                $where[] = $leftjoin['itemtype'] . ' IN (' . $itemtypes . ')';
            }
        }
    }

    if (count($cids) > 0 && $andcids) {
        // MOVED
    } elseif (count($cids) > 0 && !$andcids) {
        // The categories are ORed, i.e. select items with ANY of the categories.
        $orcids = array();
        $tmpwhere = array();
        for ($i = 0; $i < count($cids); $i++) {
            if (is_numeric($cids[$i])) {
                $orcids[] = $cids[$i];
            } elseif (preg_match('/^_(\d+)$/', $cids[$i], $matches)) {
                $tmpcid = $matches[1];
                $cat = xarModAPIFunc('categories', 'user', 'getcatinfo', array('cid' => $tmpcid));
                if (!empty($cat)) {
                    // Only bring in the categories table if the category in our tree has any descendants.
                    if (($cat['left'] + 1) != $cat['right']) {
                        // Use 'LEFT [OUTER] JOIN' since we are ORing the categories.
                        $leftjoin['more'] .= ' LEFT JOIN ' . $categoriestable . ' cattab' . $i
                            . ' ON cattab' . $i . '.xar_cid = ' .  $leftjoin['cid']
                            . ' AND cattab' . $i . '.xar_left >= ' . $cat['left']
                            . ' AND cattab' . $i . '.xar_left <= ' . $cat['right'];
                    } else {
                        // FIXME: something not quite right here.
                        // This handles _N-M, where M is a leaf node.
                        // What exactly is that supposed to mean?
                        if (!empty($catlinks[$i])) $tmpwhere[] = $catlinks[$i] . '.xar_cid = ' . $tmpcid;
                    }
                }
            }
        }

        if (count($orcids) == 1) {
            $tmpwhere[] = $leftjoin['cid'] . ' = ' . $orcids[0];
        } elseif (count($orcids) > 1) {
            $allcids = join(', ', $orcids);
            $tmpwhere[] = $leftjoin['cid'] . ' IN (' . $allcids . ')';
        }

        if (count($tmpwhere) > 0) {
            $where[] = '(' . join(' OR ', $tmpwhere) . ')';
        }
    }

    if (count($iids) > 0) {
        $alliids = join(', ', $iids);
        $where[] = $leftjoin['iid'] . ' IN (' . $alliids . ')';
    }

    if (count($where) > 0) {
        $leftjoin['where'] = join(' AND ', $where);
    } else {
        $leftjoin['where'] = '';
    }

    return $leftjoin;
}

?>
