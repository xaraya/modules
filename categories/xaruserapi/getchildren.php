<?php

/**
 * get direct children of a specific (list of) category
 *
 * @param $args['cid'] id of category to get children for, or
 * @param $args['cids'] array of category ids to get children for
 * @param $args['return_itself'] =Boolean= return the cid itself (default false)
 * @returns array
 * @return array of category info arrays, false on failure
 */
function categories_userapi_getchildren($args) {
    extract($args);

    if (!isset($cid) && !isset($cids)) {
       xarSessionSetVar('errormsg', xarML('Bad arguments for API function'));
       return false;
    }

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
    if (isset($cid)) {
        $SQLquery .= "WHERE xar_parent =".xarVarPrepForStore($cid);
        if (!empty($return_itself)) {
            $SQLquery .= " OR xar_cid =".xarVarPrepForStore($cid);
        }
    } else {
        $allcids = join(', ',$cids);
        $SQLquery .= "WHERE xar_parent IN (".xarVarPrepForStore($allcids).")";
        if (!empty($return_itself)) {
            $SQLquery .= " OR xar_cid IN (".xarVarPrepForStore($allcids).")";
        }
    }
    $SQLquery .= " ORDER BY xar_left";

    $result = $dbconn->Execute($SQLquery);
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
