<?php

/**
 * get info on a specific (list of) category
 * @param $args['cid'] id of category to get info, or
 * @param $args['cids'] array of category ids to get info
 * @returns array
 * @return category info array, or array of cat info arrays, false on failure
 */
function categories_userapi_getcatinfo($args) {
    extract($args);

    if (!isset($cid) && !isset($cids)) {
       xarSessionSetVar('errormsg', xarML('Bad arguments for API function'));
       return false;
    }

    list($dbconn) = xarDBGetConn();
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
    if (isset($cid)) {
        $SQLquery .= "WHERE xar_cid =".xarVarPrepForStore($cid);
    } else {
        $allcids = join(', ',$cids);
        $SQLquery .= "WHERE xar_cid IN (".xarVarPrepForStore($allcids).")";
    }

    $result = $dbconn->Execute($SQLquery);
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
