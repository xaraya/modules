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
 * get info on a specific (list of) category
 *
 * @param int   $args['cid'] id of category to get info, or
 * @param array $args['cids'] array of category ids to get info
 * @return array Category info array, or array of cat info arrays, false on failure
 */
function categories_userapi_getcatinfo($args)
{
    extract($args);

    if (!isset($cid) && !isset($cids)) {
       xarSessionSetVar('errormsg', xarML('Bad arguments for API function'));
       return false;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $categoriestable = $xartable['categories'];

    // TODO: simplify api by always using cids, if one cat, only 1 element in the array
    $SQLquery = "SELECT xar_cid,
                        xar_name,
                        xar_description,
                        xar_image,
                        xar_parent,
                        xar_left,
                        xar_right
                   FROM $categoriestable ";
    if (isset($cid)) {
        $SQLquery .= "WHERE xar_cid = ?";
        $bindvars = array($cid);
    } else {
        $bindmarkers = '?' . str_repeat(',?',count($cids)-1);
        $SQLquery .= "WHERE xar_cid IN ($bindmarkers)";
        $bindvars = $cids;
    }

    $result = $dbconn->Execute($SQLquery,$bindvars);
    if (!$result) return;

    if ($result->EOF) {
        xarSessionSetVar('errormsg', xarML('Unknown Category'));
        return false;
    }

    if (isset($cid)) {
        list($cid, $name, $description, $image, $parent, $left, $right) = $result->fields;
        $info = Array(
                      "cid"         => $cid,
                      "name"        => $name,
                      "description" => $description,
                      "image"       => $image,
                      "parent"      => $parent,
                      "left"        => $left,
                      "right"       => $right
                     );
        return $info;
    } else {
        $info = array();
        while (!$result->EOF) {
            list($cid, $name, $description, $image, $parent, $left, $right) = $result->fields;
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
}

?>
