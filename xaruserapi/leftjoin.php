<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
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
 */
function categories_userapi_leftjoin($args)
{
    // Get arguments from argument array
    extract($args);

    $dbconn =& xarDBGetConn();

    // Required argument ?
    if (!isset($modid) || !is_numeric($modid)) {
        $msg = xarML('Missing parameter #(1) for #(2)',
                    'modid','categories');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return array();
    }

    // Optional argument
    if (!empty($catid)) {
        if (strpos($catid,' ')) {
            $cids = explode(' ',$catid);
            $andcids = true;
        } elseif (strpos($catid,'+')) {
            $cids = explode('+',$catid);
            $andcids = true;
        } elseif (strpos($catid,'-')) {
            $cids = explode('-',$catid);
            $andcids = false;
        } else {
            $cids = array($catid);
            $andcids = false;
        }
    }
    if (!isset($cids)) {
        $cids = array();
    }
    if (!isset($iids)) {
        $iids = array();
    }
    if (!isset($andcids)) {
        $andcids = false;
    }

    // Security check
    if (!xarSecurityCheck('ViewCategoryLink')) return;

/*
    if (count($cids) > 0) {
        if (count($iids) > 0) {
            foreach ($cids as $cid) {
                foreach ($iids as $iid) {
                    if(!xarSecurityCheck('ViewCategoryLink',1,'Link',"$modid:All:$iid:$cid")) return;
                }
            }
        } else {
            foreach ($cids as $cid) {
                if(!xarSecurityCheck('ViewCategoryLink',1,'Link',"$modid:All:All:$cid")) return;
            }
        }
    } elseif (count($iids) > 0) {
    // Note: your module should be checking security for the iids too !
        foreach ($iids as $iid) {
            if(!xarSecurityCheck('ViewCategoryLink',1,'Link',"$modid:All:$iid:All")) return;
        }
    } else {
        if(!xarSecurityCheck('ViewCategoryLink',1,'Link',"$modid:All:All:All")) return;
    }
*/

    // dummy cids array when we're going for x categories at a time
    if (isset($groupcids) && count($cids) == 0) {
        $andcids = true;
        $isdummy = 1;
        for ($i = 0; $i < $groupcids; $i++) {
            $cids[] = $i;
        }
    } else {
        $isdummy = 0;
    }

    // trick : cids = array(_NN) corresponds to cidtree = NN
    if (count($cids) == 1 && preg_match('/^_(\d+)$/',$cids[0],$matches)) {
        $cidtree = $matches[1];
        $cids = array();
    }

    // Table definition
    $xartable =& xarDBGetTables();
    $categorieslinkagetable = $xartable['categories_linkage'];
    $categoriestable = $xartable['categories'];

    $leftjoin = array();

    // create list of tables we'll be left joining for AND
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
    $columns = array('cid','iid','modid','itemtype');
    foreach ($columns as $column) {
        $leftjoin[$column] = $linktable . '.xar_' . $column;
    }

    // Specify LEFT JOIN ... ON ... [WHERE ...] parts
    if (count($cids) > 0 && $andcids) {
        $leftjoin['table'] = $categorieslinkagetable . ' ' . $catlinks[0];
        $leftjoin['more'] = ' ';
        $leftjoin['cids'] = array();
        $leftjoin['cids'][] = $catlinks[0] . '.xar_cid';
        for ($i = 1; $i < count($catlinks); $i++) {
            $leftjoin['more'] .= ' LEFT JOIN ' . $categorieslinkagetable .
                                     ' ' . $catlinks[$i] .
                                 ' ON ' . $leftjoin['iid'] . ' = ' .
                                     $catlinks[$i] . '.xar_iid' .
                                 ' AND ' . $leftjoin['modid'] . ' = ' .
                                     $catlinks[$i] . '.xar_modid ';
            // Note: only for non-0 itemtypes here
            if (!empty($itemtype)) {
                $leftjoin['more'] .= ' AND ' . $leftjoin['itemtype'] . ' = ' .
                                     $catlinks[$i] . '.xar_itemtype ';
            }
            $leftjoin['cids'][] = $catlinks[$i] . '.xar_cid';
        }
    } elseif (!empty($cidtree)) {
        $leftjoin['table'] = $categorieslinkagetable;
        $leftjoin['more'] = ' LEFT JOIN ' . $categoriestable .
                            ' ON ' . $categoriestable . '.xar_cid = ' .  $leftjoin['cid'] . ' ';
    } else {
        $leftjoin['table'] = $categorieslinkagetable;
        $leftjoin['more'] = '';
    }
    $leftjoin['field'] = $leftjoin['iid'];

    // Specify the WHERE part
    $where = array();
    if (!empty($modid) && is_numeric($modid)) {
        $where[] = $leftjoin['modid'] . ' = ' . $modid;
    }
    // Note : do not default to 0 here, because we want to be able to do things across item types
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
    if (count($cids) > 0) {
        if ($andcids) {
            // select only the 1-2-4 combination, not the 2-1-4, 4-2-1, etc.
            if ($isdummy) {
                $oldcid = '';
                foreach ($leftjoin['cids'] as $cid) {
                    if (!empty($oldcid)) {
                        $where[] .= $oldcid . ' < ' . $cid;
                    }
                    $oldcid = $cid;
                }
            // select the categories you wanted
            } else {
                for ($i = 0; $i < count($cids); $i++) {
                    if (is_numeric($cids[$i])) {
                        $where[] = $catlinks[$i] . '.xar_cid = ' . $cids[$i];
                    } elseif (preg_match('/^_(\d+)$/',$cids[$i],$matches)) {
                        $tmpcid = $matches[1];
                        $cat = xarModAPIFunc('categories','user','getcatinfo',Array('cid' => $tmpcid));
                        if (!empty($cat)) {
                            $leftjoin['more'] .= ' LEFT JOIN ' . $categoriestable . ' cattab' . $i .
                                                 ' ON cattab' . $i . '.xar_cid = ' .  $catlinks[$i] . '.xar_cid ';
                            $where[] = 'cattab' . $i . '.xar_left >= ' . $cat['left'];
                            $where[] = 'cattab' . $i . '.xar_left <= ' . $cat['right'];
                        }
                    } else {
                        // hmmm, what's this ?
                    }
                }
            }
            // include all cids here
            $leftjoin['cid'] = join(', ',$leftjoin['cids']);
        } else {
            $orcids = array();
            $tmpwhere = array();
            for ($i = 0; $i < count($cids); $i++) {
                if (is_numeric($cids[$i])) {
                    $orcids[] = $cids[$i];
                } elseif (preg_match('/^_(\d+)$/',$cids[$i],$matches)) {
                    $tmpcid = $matches[1];
                    $cat = xarModAPIFunc('categories','user','getcatinfo',Array('cid' => $tmpcid));
                    if (!empty($cat)) {
                        $leftjoin['more'] .= ' LEFT JOIN ' . $categoriestable . ' cattab' . $i .
                                             ' ON cattab' . $i . '.xar_cid = ' .  $leftjoin['cid'];
                        $tmpwhere[] = '(cattab' . $i . '.xar_left >= ' . $cat['left'] .
                                      ' AND ' .
                                      'cattab' . $i . '.xar_left <= ' . $cat['right'] . ')';
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
    }
    if (!empty($cidtree)) {
        $cat = xarModAPIFunc('categories','user','getcatinfo',Array('cid' => $cidtree));
        if (!empty($cat)) {
            $where[] = $categoriestable . '.xar_left >= ' . $cat['left'];
            $where[] = $categoriestable . '.xar_left <= ' . $cat['right'];
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
