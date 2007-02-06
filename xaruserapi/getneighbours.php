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
 * get info on neighbours based on left/right numbers
 * (easiest is to pass it a category array coming from getcat*)
 *
 * @param $args['left'] left number
 * @param $args['right'] right number
 * @param $args['parent'] parent id (optional)
 * @return array TODO
 */
function categories_userapi_getneighbours($args)
{
    extract($args);

    if (!isset($left) || !isset($right) || !is_numeric($left) || !is_numeric($right)) {
       xarSessionSetVar('errormsg', xarML('Bad arguments for API function'));
       return false;
    }

//    if (!isset($parent) || !is_numeric($parent)) {
//       $parent = 0;
//    }

// TODO: evaluate this
    // don't return neighbours unless we're at a leaf node
//    if ($left != $right - 1) {
//        return array();
//    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $categoriestable = $xartable['categories'];

    $SQLquery = "SELECT xar_cid,
                        xar_name,
                        xar_description,
                        xar_image,
                        xar_parent,
                        xar_left,
                        xar_right
                   FROM $categoriestable ";
// next at same level
    $SQLquery .= "WHERE xar_left =". ($right + 1);
// next at level higher
    $SQLquery .= " OR xar_right =". ($right + 1);
// next at level lower (if we accept non-leaf nodes)
    $SQLquery .= " OR xar_left =". ($left + 1);
// previous at same level
    $SQLquery .= " OR xar_right =". ($left - 1);
// previous at level higher
    $SQLquery .= " OR xar_left =". ($left - 1);
// previous at level lower (if we accept non-leaf nodes)
    $SQLquery .= " OR xar_right =". ($right - 1);
// parent node, just in case
//    if (!empty($parent)) {
//        $SQLquery .= " OR xar_cid =". $parent;
//    }

    $result = $dbconn->Execute($SQLquery);
    if (!$result) return;

    if ($result->EOF) {
        xarSessionSetVar('errormsg', xarML('Unknown Category'));
        return false;
    }

//    $curparent = $parent;
    $info = array();
    while (!$result->EOF) {
        list($cid, $name, $description, $image, $parent, $cleft, $cright) = $result->fields;
        if (!xarSecurityCheck('ViewCategories',0,'Category',"$name:$cid")) {
             $result->MoveNext();
             continue;
        }
//        if ($cid == $curparent) {
//            $link = 'parent';
//        } elseif ($cleft == $right + 1) {
        if ($cleft == $right + 1) {
            $link = 'next';
        } elseif ($cleft == $left - 1) {
            // Note: we'll never get here, actually - cfr. parent
            $link = 'previousup';
        } elseif ($cright == $right + 1) {
            // Note: we'll never get here, actually - cfr. parent
            $link = 'nextup';
        } elseif ($cleft == $left + 1) {
            $link = 'nextdown';
        } elseif ($cright == $left - 1) {
            $link = 'previous';
        } elseif ($cright == $right - 1) {
            $link = 'previousdown';
        }
        $info[$cid] = Array(
                            "cid"         => $cid,
                            "name"        => $name,
                            "description" => $description,
                            "image"       => $image,
                            "parent"      => $parent,
                            "left"        => $cleft,
                            "right"       => $cright,
                            "link"        => $link
                           );
        $result->MoveNext();
    }
    return $info;
}

?>
