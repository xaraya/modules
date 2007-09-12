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
 * get direct children of a specific (list of) category
 *
 * @param $args['cid'] id of category to get children for, or
 * @param $args['cids'] array of category ids to get children for
 * @param $args['return_itself'] =Boolean= return the cid itself (default false)
 * @return array of category info arrays, false on failure
 */
function categories_userapi_getchildren($args)
{
    extract($args);

    if (!isset($cid) && !isset($cids)) {
       xarSessionSetVar('errormsg', xarML('Bad arguments for API function'));
       return false;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $categoriestable = $xartable['categories'];
    $bindvars = array();
    // TODO: simplify API by always using array of cids, optionally with one element
    $SQLquery = "SELECT xar_cid,
                        xar_name,
                        xar_description,
                        xar_image,
                        xar_parent,
                        xar_left,
                        xar_right
                   FROM $categoriestable ";
    if (isset($cid)) {
        $SQLquery .= "WHERE xar_parent =?";
        $bindvars[] = $cid;
        if (!empty($return_itself)) {
            $SQLquery .= " OR xar_cid =?";
            $bindvars[] = $cid;
        }
    } else {
        $bindmarkers = '?' . str_repeat(',?',count($cids)-1);
        $allcids = join(', ',$cids);
        $SQLquery .= "WHERE xar_parent IN ($bindmarkers)";
        $bindvars = $cids;
        if (!empty($return_itself)) {
            $SQLquery .= " OR xar_cid IN ($bindmarkers)";
            // bindvars could already hold the $cids
            $bindvars = array_merge($bindvars, $cids);
        }
    }
    $SQLquery .= " ORDER BY xar_left";

    $result = $dbconn->Execute($SQLquery,$bindvars);
    if (!$result) return;

    $info = array();
    while (!$result->EOF) {
        list($cid, $name, $description, $image, $parent, $left, $right) = $result->fields;
        if (!xarSecurityCheck('ViewCategories',0,'Category',"$name:$cid")) {
             $result->MoveNext();
             continue;
        }
        $info[$cid] = Array(
                            "cid"         => $cid,
                            "name"        => $name,
                            "description" => $description,
                            "image"       => $image,
                            "parent"      => $parent,
                            "left"        => $left,
                            "right"       => $right
                           );
        $result->MoveNext();
    }
    return $info;
}

?>
