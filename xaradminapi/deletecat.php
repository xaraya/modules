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
 * delete a category
 * @param $args['cid'] the ID of the category
 * @returns bool
 * @return true on success, false on failure
 */
function categories_adminapi_deletecat($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check
    if (empty($cid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'cid', 'admin', 'deletecat', 'categories');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return false;
    }

    // Obtain current information on the reference category
    $args = Array(
                  'cid' => $cid,
                  'getparents' => false,
                  'getchildren' => true,
                  'return_itself' => true
                 );
    $cat = xarModAPIFunc('categories', 'user', 'getcatinfo', $args);
    if ($cat == false) {
        $msg = xarML('Category does not exist. Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'category', 'admin', 'deletecat', 'categories');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // These are set to be used later on
    $right = $cat['right'];
    $left = $cat['left'];
    $deslocation_inside = $right - $left + 1;
    $categories = xarModAPIFunc('categories',
                                'user',
                                'getcat',
                                $args);
    if ($categories == false || count($categories) == 0) {
        $msg = xarML('Category does not exist. Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'category', 'admin', 'deletecat', 'categories');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // Useful Variables set...

    // Security check
    // Don?t check by name anything! That?s evil... Unique ID is the way to go.
    if(!xarSecurityCheck('DeleteCategories',1,'Category',"All:$cid")) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Deleting a category

    //There are two possibilities when deleting a set:
    //1 - Destroy every child inside it
    //2 - Destroy the parent, and make the parent?s parent inherity the children
    //As this model has the moving feature, i think the best option is '1'

    // This part was mostly taken from Joe Celko?s article SQL for Smarties on DBMS, April 1996

    // So deleting all the subtree


    // TODO: Hooks

    // Remove linkage in the category and its sub-tree
    $categorieslinkagetable = $xartable['categories_linkage'];

    $catlist = array();
    foreach ($categories as $mycat) {
        $catlist[] = $mycat['cid'];
    }
    $cats_comma_separated = implode (',', $catlist);

    $sql = "DELETE FROM $categorieslinkagetable
            WHERE xar_cid IN (" . $cats_comma_separated . ")";
    $result = $dbconn->Execute($sql);
    if (!$result) return;

    // Remove the category and its sub-tree
    $categoriestable = $xartable['categories'];

    $SQLquery = "DELETE FROM $categoriestable
                 WHERE xar_left
                 BETWEEN $left AND $right";

    $result = $dbconn->Execute($SQLquery);
    if (!$result) return;

    // Now close up the the gap
    $SQLquery = "UPDATE $categoriestable
                 SET xar_left =
                 CASE WHEN xar_left > $left
                      THEN xar_left - $deslocation_inside
                      ELSE xar_left
                 END,
                     xar_right =
                 CASE WHEN xar_right > $left
                      THEN xar_right - $deslocation_inside
                      ELSE xar_right
                 END
                 ";
    $result = $dbconn->Execute($SQLquery);
    if (!$result) return;
    // Call delete hooks
    $args['module'] = 'categories';
    $args['itemtype'] = 0;
    $args['itemid'] = $cid;
    xarModCallHooks('item', 'delete', $cid, $args);
    return true;
}
?>