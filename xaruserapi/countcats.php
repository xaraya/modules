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
 * count number of categories (optionally below some category)
 * Usage : $num = xarModAPIFunc('categories', 'user', 'countcats', $cat);
 *         $total = xarModAPIFunc('categories', 'user', 'countcats', array());
 *
 * @param $args['cid'] The ID of the category you are counting for (optional)
 * @param $args['left'] The left value for that category (optional)
 * @param $args['right'] The right value for that category (optional)
 * @returns int
 * @return number of categories
 */
function categories_userapi_countcats($args)
{
    // Get arguments from argument array
    extract($args);

    // Security check
    if(!xarSecurityCheck('ViewCategories')) return;

    // Database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $categoriestable = $xartable['categories'];
    $bindvars = array();

    // Get number of categories
    if (!empty($left) && is_numeric($left) &&
        !empty($right) && is_numeric($right)) {
        $sql = "SELECT COUNT(xar_cid) AS childnum
                  FROM $categoriestable
                 WHERE xar_left
               BETWEEN ? AND ?";
        $bindvars[] = $left; $bindvars[] = $right;
    } elseif (!empty($cid) && is_numeric($cid)) {
        $sql = "SELECT COUNT(P2.xar_cid) AS childnum
                  FROM $categoriestable AS P1,
                       $categoriestable AS P2
                 WHERE P1.xar_cid = ?
                   AND P2.xar_left >= P1.xar_left
                   AND P2.xar_left <= P1.xar_right";

        $bindvars[] = $cid;
/* this is terribly slow, at least for MySQL 3.23.49-nt
               BETWEEN P1.xar_left AND
                       P1.xar_right
                   AND P1.xar_cid
                        = ".xar Var Prep For Store($cid); // making my greps happy <mrb>
*/
    } else {
        $sql = "SELECT COUNT(xar_cid) AS childnum
                  FROM $categoriestable";
    }

    $result = $dbconn->Execute($sql,$bindvars);
    if (!$result) return;

    $num = $result->fields[0];

    $result->Close();

    return $num;
}

?>
